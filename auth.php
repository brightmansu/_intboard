<? /*

User authenticication library for Intellect Board 2

(c) 2004-2006, XXXX Pro, United Open Project
Visit us online: http://intboard.ru
*/

// ������� auth_process -- �������� ����������� ������������.
// �������� $lang -- ��� �����, ���� �� ������� ����� URL
// ������ ������� �������� �� �������� ������,
// ���������� ���������� ����������. � �������� ���������� ������� ������
// ���������� ������ � ������������, ����������� �� �� � ���� ����

function& auth_process() {
  global $link;
  session_name('IB2XPnew'.$GLOBALS['DBprefix']); // ��� ������
  if (isset($_COOKIE['IB2XPnew'.$GLOBALS['DBprefix']]) ||
      isset($_GET['IB2XPnew'.$GLOBALS['DBprefix']]) ||
      isset($_POST['IB2XPnew'.$GLOBALS['DBprefix']]) ||
      isset($_COOKIE['IB2XP'.$GLOBALS['DBprefix'].'uid']) || getvar('inusername')) session_start();
  $uid=intval($_SESSION['uid']);
  if (!$uid) $uid=intval($_COOKIE['IB2XP'.$GLOBALS['DBprefix'].'uid']);
  $key=addslashes($_SESSION['key']);
  if (!$key) $key=addslashes($_COOKIE['IB2XP'.$GLOBALS['DBprefix'].'key']);
  if ($lang) $langdata = "ln.ln_file=\"".db_slashes($lang)."\"";
  else $langdata="ln.ln_id=u.u_lnid";
  $sqldata="SELECT u.*, ln.*, st.*, u__pmcount AS pmcount, u__warnings AS uw_count ".
   "FROM ".$GLOBALS['DBprefix']."User u ".
   "LEFT JOIN ".$GLOBALS['DBprefix']."Language ln ON ($langdata)".
   "LEFT JOIN ".$GLOBALS['DBprefix']."StyleSet st ON (st.st_id=u.u_stid) ".
   "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=0) ";
  $inusername =&getvar("inusername"); // ��������� ����� ������������ � ������ � ������ �������� �����
  $inpassword =&getvar("inpassword");
  if ($uid && $key && !$inusername && !$inpassword) { // ���� � ������������� ������������ � cookies ��� ������ � �� �������� ��� ������������ � ������
     $sql = "SELECT o_udata FROM ".$GLOBALS['DBprefix']."Online WHERE o_uid=".$uid." AND o_key=\"".$key."\" LIMIT 1";
     $res=&db_query($sql,$link);
     if (db_num_rows($res)) { // ���� ������� ���-�� ������� �� ����
       list($data)=db_fetch_row($res);
       $udata = unserialize($data);
       $udata['pass_checked']=1;
       db_free_result($res);
       $prev_warnings = $udata['uw_count'];
       $udata['uw_count']=check_warnings($udata);
       if ($udata['uw_count']==$prev_warnings) return $udata;
       else {
         remove_cached_user($uid);
         $sql=$sqldata.' WHERE u_id="'.$uid.'"';
         $res=&db_query($sql,$link);
         $udata =&db_fetch_array($res);
         db_free_result($res);
         return put_user($udata,$key);
       }
       return $udata;
     }
     else {
       $sql=$sqldata.' WHERE u_id="'.$uid.'"';
       $res=&db_query($sql,$link);
       $udata =&db_fetch_array($res);
       db_free_result($res);
       return put_user($udata,$key);
     }
  }
  elseif ($inusername && $inpassword) {
    $sql=$sqldata.' WHERE u__name="'.$inusername.'"';
    $res=&db_query($sql,$link);
    $udata =&db_fetch_array($res);
    db_free_result($res);
    if ($udata['u_encrypted']) $inpassword=md5($inpassword);
    if ($inpassword==$udata['u__password']) {
       $key=generate_key($udata);
       // ��������, ��� ������������ ��� ��� � ���� ������
       return put_user($udata,$key);  // ����� -- ��������� ��� ����
    }
    else {
      return load_guest($lang);
    }
  }
  else {
    return load_guest($lang);
  }
}

// ��������� �����, ������� � ���������� ������������� � cookies
function generate_key(&$udata) {
  if (!$mode=&getvar("login_mode")) $mode=$_COOKIE["IB2XP".$GLOBALS['DBprefix'].'mode'];
  $buffer='';
  if ($GLOBALS['opt_secbrowser']==1) $buffer.=$_SERVER['HTTP_USER_AGENT'];
  if ($mode==1) $buffer.=$_SERVER['REMOTE_ADDR'];
  return md5(md5($udata['u__key']).'==+=='.$udata['u__password'].$udata['u_id'].$buffer);
}

// �������� ������������ � ������ �������������� � ������� Online
function& put_user(&$udata,$key) {
   global $link;
   if (generate_key($udata)==$key && time()>$udata['u__lastlogin']+$GLOBALS['opt_brutetimeout']) {
     $sql = "SELECT o_uid FROM ".$GLOBALS['DBprefix']."Online WHERE o_uid=".$udata['u_id'].' AND o_key="'.$key.'"';
     $res=&db_query($sql,$link);
     
     $udata['uw_count']=check_warnings($udata);
     if (intval($udata['uw_count'])<=(-$GLOBALS['opt_warnstoban']) && $GLOBALS['opt_warnstoban']>0) {
       $GLOBALS['inuserlevel']=-1;
       $udata['u__level']=-1;
     }

     // ��������� ������ ����������� ��� ��������� ��������
     $sql2 = "SELECT f_id, ua_level, f_lview FROM ".$GLOBALS['DBprefix']."Forum".
     " LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ON (fid=f_id AND uid=".$udata['u_id'].")";
     $res2=&db_query($sql2,$link);
     $rest_array=array();
     $levels_array=array();
     while ($restricted=db_fetch_row($res2)) {
       $levels_array[$restricted[0]]=$restricted[1];
       if ((!$restricted[1] && $udata['u__level']<$restricted[2]) ||
         ($restricted[1] && $restricted[1]<$restricted[2])) array_push($rest_array,$restricted[0]);
     }
     $udata['forum_noaccess']=join(',',$rest_array);
     $udata['forum_levels']=&$levels_array;
     db_free_result($res2);

     // ��������� ������ ��������, ����������� �� "������������" � "�������������"
     $sql3 = "SELECT gid FROM ".$GLOBALS["DBprefix"]."UGroupMember WHERE uid=".intval($udata['u_id']); 
     $res3=&db_query($sql3,$link);
     $groups_array=array();
     while ($group=db_fetch_row($res3)) {
       array_push($groups_array,$group[0]);
     }
     $udata['user_groups']=join(',',$groups_array);
     db_free_result($res3);
     $sql2 = "SELECT fid FROM ".$GLOBALS['DBprefix']."ForumIgnore WHERE uid=".$udata['u_id'];
     $res2=&db_query($sql2,$link);
     $ignored_array=array();
     while ($ignored=db_fetch_row($res2)) {
       array_push($ignored_array,$ignored[0]);
     }
     $udata['forum_ignored']=join(',',$ignored_array);
     db_free_result($res2);

     if (db_num_rows($res)==0) { // ���� ������������ ����
       $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Online (o_uid,o_key,o_udata) ".
       'VALUES ('.$udata['u_id'].',"'.$key.'","'.addslashes(serialize($udata)).'")';
        $res=&db_query($sql,$link);
     }
     $udata['pass_checked']=1;
     $mode=&getvar("login_mode");
     if (!isset($_POST['login_mode']) && !isset($_GET['login_mode'])) $mode=$_COOKIE["IB2XP".$GLOBALS['DBprefix'].'mode'];
     $_SESSION['uid']=$udata['u_id'];
     $_SESSION['key']=$key;
     $_SESSION['login_time']=time();
     if ($mode==2) {
       setcookie("IB2XP".$GLOBALS['DBprefix'].'uid',$udata['u_id'],time()+180*24*60*60,'/');
       setcookie("IB2XP".$GLOBALS['DBprefix'].'key',$key,time()+180*24*60*60,'/');
       setcookie("IB2XP".$GLOBALS['DBprefix'].'mode',$mode,time()+180*24*60*60,'/');
     }
     elseif ($mode==1) {
       setcookie("IB2XP".$GLOBALS['DBprefix'].'uid',$udata['u_id'],time()+30*60,'/');
       setcookie("IB2XP".$GLOBALS['DBprefix'].'key',$key,time()+30*60,'/');
       setcookie("IB2XP".$GLOBALS['DBprefix'].'mode',$mode,time()+30*60,'/');
     }
     else {
       setcookie("IB2XP".$GLOBALS['DBprefix'].'uid',$udata['u_id'],0,'/');
       setcookie("IB2XP".$GLOBALS['DBprefix'].'key',$key,0,'/');
       setcookie("IB2XP".$GLOBALS['DBprefix'].'mode','0',0,'/');
     }
     return $udata;
   }
   else {
     if ($udata['u_id']) {
       $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__lastlogin=".time()." WHERE u_id=".$udata['u_id'];
       $res=&db_query($sql,$link);
     }
     setcookie("IB2XP".$GLOBALS['DBprefix'].'uid','',time()-3600,'/');
     setcookie("IB2XP".$GLOBALS['DBprefix'].'key','',time()-3600,'/');
     return load_guest($lang);
   }
}

function& load_guest($lang) { // �������� ������� �����
  global $link;
  if ($lang) $langdata = "ln.ln_file=\"".db_slashes($lang)."\"";
  else $langdata="ln.ln_id=u.u_lnid";
  if (is_file($GLOBALS['opt_dir'].'/config/guest.txt')) {
    $udata=unserialize(file_get_contents($GLOBALS['opt_dir'].'/config/guest.txt'));
  }
  else {
    $sql="SELECT u.*, ln.*, st.*, u__pmcount AS pmcount, u__warnings AS uw_count ".
      "FROM ".$GLOBALS['DBprefix']."User u ".
      "LEFT JOIN ".$GLOBALS['DBprefix']."Language ln ON ($langdata)".
      "LEFT JOIN ".$GLOBALS['DBprefix']."StyleSet st ON (st.st_id=u.u_stid) ".
      "WHERE u_id=1";
    $res=&db_query($sql,$link);
    $udata =&db_fetch_array($res);
     // ��������� ������ ����������� ��� ��������� ��������
     $sql = 'SELECT f_id FROM '.$GLOBALS['DBprefix'].'Forum LEFT JOIN '.$GLOBALS['DBprefix'].'UserAccess ON (fid=f_id AND uid='.$udata['u__level'].') WHERE f_lview>COALESCE(ua_level,'.$udata['u__level'].')';
     $res=&db_query($sql,$link);
     $rest_array=array();
     while ($restricted=db_fetch_row($res)) {
       array_push($rest_array,$restricted[0]);
     }
     $udata['forum_noaccess']=join(',',$rest_array);
     db_free_result($res);

     $sql2 = "SELECT fid FROM ".$GLOBALS['DBprefix']."ForumIgnore WHERE uid=".$udata['u_id'];
     $res2=&db_query($sql2,$link);
     $ignored_array=array();
     while ($ignored=db_fetch_row($res2)) {
       array_push($ignored_array,$ignored[0]);
     }
     $udata['forum_ignored']=join(',',$ignored_array);
     db_free_result($res2);

    $fh=fopen($GLOBALS['opt_dir'].'/config/guest.txt','w');
    fputs($fh,serialize($udata));
    fclose($fh);
  }
  return $udata;
}

// ������� auth_checkpass() -- �������� ������������ ������
// ������������ ��������:
// -1 -- ��������� IP-����� ������������
// -2 -- ������������ ���������
// -3 -- ������������ ����� ��� ������
// 1  -- �������� ������ �������

function auth_checkpass(&$inuser) {
  global $link;
  $inpassword=&getvar('inpassword'); // ���� ���� ������� �����
  $inuserid=$inuser['u_id'];
  if ($GLOBALS['opt_secbrowser']) $useragent=$_SERVER['HTTP_USER_AGENT'];

  if (!$inuser['pass_checked'] && $inpassword) {
    $_SESSION['uid']=1;
    $_SESSION['key']="";
    return -3;
  }

  if ($inuser['u__active']!=1) {
    $_SESSION['uid']=1;
    $_SESSION['key']="";
    return -2;
  }

  return 1;
}

// ������� auth_login -- ���������� ��� ����� ������������ �� ����� ����� ����� �����,
// � ������� ������������� ����������� ������ ������ �����. ��� ������� ����� (��������,
// ��� �������� ���������, ��� ������� �� ����������)

function auth_login() {
  if ($GLOBALS['inuserid']==1 || $GLOBALS['inuserid']==3) {
    $_SESSION['uid']=0;
    $_SESSION['password']="";
    $_SESSION['salt']=0;
    error(MSG_e_u_nosuchuser);
  }
  $mode=&getvar("login_mode");
  if ($mode==2) {
    setcookie("IB2XP".$GLOBALS['DBprefix'].'mode',$mode,time()+180*24*60*60);
  }
  else {
    setcookie("IB2XP".$GLOBALS['DBprefix'].'mode',$mode);
  }
  if ($mode==1) $_SESSION['ipaddr']=getip();
}

// ������� auth_logout -- ���������� ��� ������ � ������
function auth_logout() {
  setcookie('IB2XP'.$GLOBALS['DBprefix'].'uid','',time()-3600,'/');
  setcookie('IB2XP'.$GLOBALS['DBprefix'].'key','',time()-3600,'/');
  $_SESSION['uid']=0;
  $_SESSION['key']='';
}

// ������� auth_register -- ���������� ��������������� ����� ����������� ������ ������������
// �������� u_data -- ���, ����� �������� ������������� ����� � ������� prefix_User
// ����������: �������� ������ � �������� ������� prefix_User �������� � ������� profile.php,
// ����� ��� ��������� �� ���������.
function auth_register(&$udata) {
}

// ������� auth_editprofile -- ���������� ��������������� ����� ����������� ������ ������������
// �������� u_data -- ���, ����� �������� ������������� ����� � ������� prefix_User
// ����������: �������� ������ � �������� ������� prefix_User �������� � ������� profile.php,
// ����� ��� ��������� �� ���������.
function auth_editprofile(&$udata) {
  global $link;
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=&db_query($sql,$link);
  $key=generate_key($udata);
  if ($_COOKIE["IB2XP".$GLOBALS['DBprefix'].'mode']==2) {
    setcookie("IB2XP".$GLOBALS['DBprefix'].'key',$key,time()+180*24*60*60);
  }
  else {
    setcookie("IB2XP".$GLOBALS['DBprefix'].'key',$key);
  }
}

// ������� restore_pass -- ��������� ��������� ������ ������ � �������� ��� � ��.
// ������� ������ �������������� �� ������� profile.php
// ������� ���������� ��� $udata, ���������� ���� u_id, u__name, u__email, u__password,
// u_ecncrypted �� ��������������� �������� ��, � ����� ���� newkey � newpassword, ����������
// ����� ������ (� ������ ���� ������ �������� � ����������� ����) � ���� ��� ��� ���������
function auth_restorepass($login,$email,$number) {
  global $link;
  if ($login) $sql = "SELECT u_id,u__name,u__email,u__password,u_encrypted,u__key FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"$login\"";
  elseif($email) $sql = "SELECT u_id,u__name,u__email,u__password,u_encrypted,u__key FROM ".$GLOBALS['DBprefix']."User WHERE u__email=\"$email\"";
  elseif($number) $sql = "SELECT u_id,u__name,u__email,u__password,u_encrypted,u__key FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$number\"";
  else error(MSG_e_u_filldata);
  $res =&db_query($sql,$link);
  $udata =&db_fetch_array($res);
  db_free_result($res);
  if ($udata['u_encrypted']==1) {
    $trash = rand();
    $udata['newpassword']=substr(crypt(md5($trash)),3,8);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__newpassword=\"".db_slashes($udata['newpassword'])."\" WHERE u_id=".$udata['u_id'];
    $res =&db_query($sql,$link);
    $udata['newkey']=md5($udata['newpassword'].$udata['u__key']);
  }
  return $udata;
}

// ������� auth_activate -- ��������� ��������� ��������������������� ������������
// ������������ ��������:
// -1 -- ��������� ����� Email �� ������ �� ������������
// -2 -- ������������ � ����� ������� ��� � ���� ������
// -3 -- ���� �������

function auth_activate() {
  $link = $GLOBALS['link'];
  if ($GLOBALS['opt_activate']!=1) return -1;
  $uid = intval(getvar("u"));
  $key =&getvar("key");

  $sql = "SELECT u__key,u__password FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
  $newmail=&getvar('newmail');
  $res =&db_query($sql,$link);
  if (db_num_rows($res)!=1) return -2;
  $udata=&db_fetch_array($res);
  db_free_result($res);
  if ($key!=md5($udata['u__password'].$udata['u__key'].$newmail)) return -3;
  if ($newmail) $sqldata=", u__email=\"$newmail\"";

  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__active=1 $sqldata WHERE u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=&db_query($sql,$link);
  $_POST['refpage']="index.php?a=login&m=profile&refpage=index.php";
  return 0;
}

// ������� auth_actpass -- ��������� ��������� ������ ������ ��� ��� ����� � �������������� ����� Email (� ������������ ����� Email)
// ������������ ��������:
// -1 -- ��������� ����� Email �� ������ �� ������������
// -2 -- ������������ � ����� ������� ��� � ���� ������
// -3 -- ���� �������

function auth_actpass() {
  $uid = intval($_GET['u']);
  global $link;
  $key = $_GET['key'];
  $sql = "SELECT u__key,u__newpassword,u__active FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)!=1) return -1;
  $udata=&db_fetch_array($res);
  db_free_result($res);
  if ($key!=md5($udata['u__newpassword'].$udata['u__key'])) return -2;
  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__password=MD5(u__newpassword), u__newpassword=\"\" WHERE u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=&db_query($sql,$link);
}
