<? /*

User registration & profile script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function rules() {
  user_rules();
}

function register() {
  global $link;
  $newaction = "do_register";
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."Language ln,".
      $GLOBALS['DBprefix']."StyleSet st WHERE ln.ln_id=u.u_lnid AND st.st_id=u.u_stid AND u_id=3";
  $res =&db_query($sql,$link);
  $udata =&db_fetch_array($res);
  db_free_result($res);
  $langselect = build_select("SELECT * FROM ".$GLOBALS['DBprefix']."Language ",$udata['u_lnid']);
  $styleselect = build_select("SELECT * FROM ".$GLOBALS['DBprefix']."StyleSet WHERE st_show=1",$udata['u_stid']);
  unset($udata['u__name']);
  unset($udata['u__email']);
  if (!$GLOBALS['opt_nameletters']) $GLOBALS['opt_nameletters']='\w\d ;,+\\-*\/=А-Яа-я';
  user_profile($newaction,"profile",$udata,$styleselect,$langselect,build_avatar_select());
}

function do_register() {
  check_post();
  if ($GLOBALS['opt_ddos']==1 || $GLOBALS['opt_ddos']==2) check_ddos("code");
  global $link;
  $quick =&getvar("q");
  $password1 =&getvar("password1");
  $password2 =&getvar("password2");
  if ($password1!=$password2) error(MSG_e_u_passnotmatch);
  if (!$password1) error(MSG_e_u_emptypass);
  if (!getvar("u__email")) error(MSG_e_u_emptymail);
  $newname = trim(getvar("u__name"));
  if (!$newname) error(MSG_e_u_noname);

  check_user_params(true);

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User WHERE u_id=3";
  $res =&db_query($sql,$link);
  $udata = db_fetch_assoc($res);
  db_free_result($res);

  unset($udata['u__avatar']);
  unset($udata['u_id']);

  if ($inuserlevel>=$GLOBALS['opt_ltitle']) $udata['u__title']=$_POST['u__title'];

  if ($GLOBALS['opt_activate']==0) $udata['u__active']=1;
  else $udata['u__active']=0;

  $udata['u__name']=$newname;
  $udata['u__canonical']=$new_canon;
  $udata['u__email']=&getvar("u__email");
  $curtime = $GLOBALS['curtime'];
  $udata['u__regdate']=$curtime;
  $udata['u__profileupdate']=$curtime;

  $trash=rand();
  $newkey=db_slashes(substr(crypt($trash),0,12));
  $udata['u_encrypted'] =&getvar("u_encrypted");
  if ($GLOBALS['opt_encrypted']!=2) $udata['u_encrypted'] = $GLOBALS['opt_encrypted'];
  if ($udata['u_encrypted']) $udata['u__password'] = md5($password1);
  else $udata['u__password']=$password1;
  $avatartype=&getvar("u_avatartype");
  $udata['u__key']=$newkey;

  if ($avatartype<3 && !is_uploaded_file($_FILES['avatar3']['tmp_name'])) {
    $udata['u__avatar']=&getvar("avatar".$avatartype);
    if (!$udata['u__avatar']) $avatartype==0;
    if ($avatartype==2 && $udata['u__avatar'] && strtolower(substr($udata['u__avatar'],0,7))!="http://" && substr($udata['u__avatar'],0,1)!="/") error(MSG_e_u_avatarurl);
    $pavatar=0;
  }
  else {
    $udata['u__avatar']="";
    check_image("avatar3",$GLOBALS['opt_maxavatarsize'],$GLOBALS['opt_maxavatarx'],$GLOBALS['opt_maxavatary'],MSG_e_av_badfilesize,MSG_e_av_badtype,MSG_e_av_badsize);
    $udata['u__pavatar_id']=handle_upload($_FILES['avatar3'],0,false,true);
  }
  if (is_uploaded_file($_FILES['photo1']['tmp_name'])) {
    check_image("photo1",$GLOBALS['opt_maxphoto'],$GLOBALS['opt_maxphotox'],$GLOBALS['opt_maxphotoy'],MSG_e_ph_badfilesize,MSG_e_ph_badtype,"");
    $udata['u__photo_id']=handle_upload($_FILES['photo1'],0,false,true);
  }

  foreach ($udata as $curparam=>$curvalue) {
    if (substr($curparam,0,2)=="u_" && $curparam!="u_id") {
      if (strpos($curparam,"__")===false && getvar($curparam)) {
        if ($sqldata) $sqldata.=", ";
        if (is_numeric($udata[$curparam])) $sqldata .= "$curparam=\"".intval(getvar($curparam))."\"";
        else $sqldata .= "$curparam=\"".getvar($curparam)."\"";
        $udata[$curparam]=&getvar($curparam);
      }
      else {
        if ($sqldata) $sqldata.=", ";
        $sqldata .= "$curparam=\"".db_slashes($udata[$curparam])."\"";
      }
    }
  }

  auth_register($udata);

  $userip=getip();
  $userproxy=array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
  if (file_exists($GLOBALS['opt_dir']."/config/lastreg.txt")) $regdata=file($GLOBALS['opt_dir']."/config/lastreg.txt");
  if (is_array($regdata)) foreach ($regdata as $curstr) {
    list($time,$ip,$proxy)=explode("|",trim($curstr));
    if ($time>$GLOBALS['curtime']-30) {
      $newipdata.=$curstr;
      if ($ip==$userip || ($userproxy && $userproxy==$proxy)) error("MSG_e_u_regblocked");
    }
  }
  $newipdata.=$GLOBALS['curtime']."|$userip|$userproxy\n";
  $fh=fopen($GLOBALS['opt_dir']."/config/lastreg.txt","w");
  flock($fh,LOCK_EX);
  fputs($fh,$newipdata);
  fclose($fh);

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."User SET $sqldata";
  $res =&db_query($sql,$link);
  $newid = db_insert_id($res);
  $salt = rand();

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."LastVisit (uid,fid,lv_time1,lv_time2,lv_markall,lv_markcount) SELECT ".$newid.",f_id,".$GLOBALS['curtime'].",".$GLOBALS['curtime'].",".$GLOBALS['curtime'].", f__tcount FROM ".$GLOBALS['DBprefix']."Forum ".$forumlimit;
  $res =&db_query($sql,$link);

  if (getvar("claim")==1) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET p_uid=$newid WHERE p_uname=\"$newname\" AND p_uid<=3";
    $res =&db_query($sql,$link);
  }

  if ($GLOBALS['opt_activate']==2 || $GLOBALS['opt_reginfo']) {
    if ($GLOBALS['opt_activate']==2) $GLOBALS['activate']=MSG_u_need_activate;
    $GLOBALS['newname']=$newname;
    $GLOBALS['password']=$password2;
    $GLOBALS['email']=&getvar("u__email");
    $GLOBALS['ip']=getip();
    process_mail("anewuser.txt",$GLOBALS['opt_mailout'],MSG_u_newuser);
  }

  if ($GLOBALS['opt_activate']==0) {
    $udata['u_id']=$newid;
    $key=generate_key($udata);
    $_SESSION['uid']=$newid;
    $_SESSION['key']=$key;
    setcookie("IB2XP".$GLOBALS['DBprefix'].'uid',$newid,0,'/');
    setcookie("IB2XP".$GLOBALS['DBprefix'].'key',$key,0,'/');
    setcookie("IB2XP".$GLOBALS['DBprefix'].'mode','0',0,'/');
    message(MSG_u_registered,1);
  }
  elseif ($GLOBALS['opt_activate']==1) {
    if ($udata['u_encrypted']) $password1=md5($password1);
    $GLOBALS['newlink']=$GLOBALS['opt_url']."/agent.php?u=$newid&a=activate&key=".md5($password1.$newkey);
    $GLOBALS['username']=$newname;
    $GLOBALS['password']=$_POST['password2'];
    message(MSG_u_needactivate);
    process_mail("newuser.txt",$udata['u__email'],MSG_u_confirm);
  }
  elseif($GLOBALS['opt_activate']==2) message(MSG_u_needadminactivate);
}

function edit() {
  $link = $GLOBALS['link'];
  if ($GLOBALS['inuser']['u__noedit']==1) error(MSG_e_u_noedit);  
  if ($GLOBALS['inuserid']<=3) error(MSG_e_u_sysprofile);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."Language ln,".
      $GLOBALS['DBprefix']."StyleSet st WHERE ln.ln_id=u.u_lnid AND st.st_id=u.u_stid AND u_id=".$GLOBALS['inuserid'];
  $res =&db_query($sql,$link);
  $udata =&db_fetch_array($res);
  db_free_result($res);
  $langselect = build_select("SELECT * FROM ".$GLOBALS['DBprefix']."Language",$udata['u_lnid']);
  if ($GLOBALS['inuserlevel']<1000) $sqldata="WHERE st_show=1";
  else $sqldata="";
  $styleselect = build_select("SELECT * FROM ".$GLOBALS['DBprefix']."StyleSet $sqldata",$udata['u_stid']);
  if (!$GLOBALS['opt_nameletters']) $GLOBALS['opt_nameletters']='\w\d ;,+\\-*\/=А-Яа-я';
  user_profile("do_edit","profile",$udata,$styleselect,$langselect,build_avatar_select());
}

function do_edit() {
  check_post();
  global $link;
  if ($GLOBALS['inuserid']<=3) error(MSG_e_u_sysprofile);
  if ($GLOBALS['inuser']['u__noedit']==1) error(MSG_e_u_noedit);

  $password1 =&getvar("password1");
  $password2 =&getvar("password2");
  $oldpassword =&getvar("oldpassword");
  if (!getvar("u__email")) error(MSG_e_u_emptymail);
  if ($password1 && $password1!=$password2) error(MSG_e_u_passnotmatch);
  if ($oldpassword) $newname = trim(getvar("u__name"));
  else $newname=$GLOBALS['inuser']['u__name'];

  if (!$password1) $_POST['u_encrypted']=$GLOBALS['inuser']['u_encrypted'];

  check_user_params($newname && $newname!=$GLOBALS['inuser']['u__name']);

  $newmail=trim(getvar("u__email"));
  
  if ($newmail && $newmail!=$GLOBALS['inuser']['u__email']) {
    $inuserid = $GLOBALS['inuserid'];
    $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE u_id<>\"".$GLOBALS['inuserid']."\" AND (u__name=\"$newname\" OR u__email=\"$newmail\")";
    $res =&db_query($sql,$link);
    if (db_num_rows($res)>0) error(MSG_e_u_alreadyused);
    db_free_result($res);
  }

  $udata = $GLOBALS['inuser'];
  if ($udata['u_encrypted']) $oldpassword=md5($oldpassword);
  if ($password1 && $oldpassword!=$GLOBALS['inuser']['u__password']) error(MSG_e_u_badoldpass);
  if (($_POST['u__email'] || $_POST['u__name']) && ($_POST['u__email']!=$GLOBALS['inuser']['u__email'] || $_POST['u__name']!=$GLOBALS['inuser']['u__name']) &&
       $oldpassword!=$GLOBALS['inuser']['u__password']) error(MSG_e_u_badoldpass);
  $udata['u__name']=$newname;
  if ($new_canon) $udata['u__canonical']=$new_canon;

  if ($GLOBALS['opt_activate']!=1) $udata['u__email']=&getvar("u__email");

  if ($GLOBALS['inuserlevel']>=$GLOBALS['opt_ltitle']) $udata['u__title']=&getvar("u__title");
  
  $udata['u_encrypted'] = &getvar("u_encrypted");
  if ($GLOBALS['opt_encrypted']!=2) $udata['u_encrypted'] = $GLOBALS['opt_encrypted'];
  
  if ($password1) {
    if ($udata['u_encrypted']) $udata['u__password'] = md5($password1);
    else $udata['u__password']=$password1;
  }

  $avatartype=&getvar("u_avatartype");

  if ($avatartype<3) {
    $udata['u__avatar']=&getvar("avatar".$avatartype);
    if ($avatartype==2 && $udata['u__avatar'] && strtolower(substr($udata['u__avatar'],0,7))!="http://" && substr($udata['u__avatar'],0,1)!="/") error(MSG_e_u_avatarurl);
  }
  else {
    $udata['u__avatar']="";
    if (is_uploaded_file($_FILES['avatar3']['tmp_name'])) {
      check_image("avatar3",$GLOBALS['opt_maxavatarsize'],$GLOBALS['opt_maxavatarx'],$GLOBALS['opt_maxavatary'],MSG_e_av_badfilesize,MSG_e_av_badtype,MSG_e_av_badsize);
    }
  }
  $udata['u__pavatar_id']=handle_upload($_FILES['avatar3'],$udata['u__pavatar_id'],$udata['u_avatartype']!=3,true);

  if (is_uploaded_file($_FILES['photo1']['tmp_name'])) {
    check_image("photo1",$GLOBALS['opt_maxphoto'],$GLOBALS['opt_maxphotox'],$GLOBALS['opt_maxphotoy'],MSG_e_ph_badfilesize,MSG_e_ph_badtype,MSG_e_ph_badsize);
  }
  $udata['u__photo_id']=handle_upload($_FILES['photo1'],$udata['u__photo_id'],getvar("photo_del"),true);
  $udata['u__profileupdate']=$GLOBALS['curtime'];

  foreach ($udata as $curparam=>$curvalue) {
    if (substr($curparam,0,2)=="u_" && $curparam!="u_id") {
      if (strpos($curparam,"__")===false && $curparam!="u_encrypted") {
        if ($sqldata) $sqldata.=", ";
        if (is_numeric($udata[$curparam])) $sqldata .= "$curparam=\"".intval(getvar($curparam))."\"";
        else $sqldata .= "$curparam=\"".getvar($curparam)."\"";
      }
      elseif ($curparam!="u__active") {
        if ($sqldata) $sqldata.=", ";
        $sqldata .= "$curparam=\"".db_slashes($udata[$curparam])."\"";
      }
    }
  }

  if ($GLOBALS['opt_activate']==1 && $newmail!=$GLOBALS['inuser']['u__email']) {
    if (!$password1) $password1=$udata['u__password'];
    elseif ($udata['u_encrypted']) $password1=md5($password1);
    $GLOBALS['username']=$udata['u__name'];
    $GLOBALS['newlink']=$GLOBALS['opt_url']."/agent.php?u=".$udata['u_id']."&a=activate&newmail=$newmail&key=".md5($password1.$udata['u__key'].$newmail);
    $GLOBALS['username']=$newname;
    process_mail("chmail.txt",$newmail,MSG_u_changemail);
    $sqldata.=", u__active=0";
  }

  auth_editprofile($udata);

  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET $sqldata WHERE u_id=".$GLOBALS['inuserid'];
  $res =&db_query($sql,$link);
  if ($newname!=$GLOBALS['inuser']['u__name']) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET p_uname=\"$newname\" WHERE p_uid=".$GLOBALS['inuserid'];
    $res=&db_query($sql,$link);
    
    if ($GLOBALS['inuser']['u__blog_fid']) {
      $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_link=\"blog_".$newname."\" WHERE f_id=".$GLOBALS['inuser']['u__blog_fid'];
      $res=&db_query($sql,$link);
    }
    if ($GLOBALS['inuser']['u__gallery_fid']) {
      $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_link=\"gallery_".$newname."\" WHERE f_id=".$GLOBALS['inuser']['u__gallery_fid'];
      $res=&db_query($sql,$link);
    }
    
  }
  if ($password1) {
    $salt = rand();
  }
  if ($GLOBALS['opt_activate']==1 && $newmail!=$GLOBALS['inuser']['u__email']) message(MSG_u_changeconfirm,1);
  else message(MSG_u_changed,1);
}

function login() {
  if($GLOBALS['inuserid']>3) redirect();
  else user_big_login();
}

function do_login() {
  global $link;
  auth_login();
  redirect("ref");
}

function do_logout() {
  global $link;
  auth_logout();
  redirect();
}

function listusers() {
  global $link;
  $start =&getvar("st");
  $order =&getvar("o");
  $desc =&getvar("desc");

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."User WHERE u_id>3";
  $res =&db_query($sql,$link);
  $count = db_fetch_row($res);
  db_free_result($res);

  if ($order=="u__password" || $order=="u__newpassword" || $order=="u__key") unset($order);
  if (!$order) $order="u__regdate";
  $order = " ORDER BY $order";
  if ($desc) $order.=" DESC";
  if ($start!="all") $limit=" LIMIT ".intval($start).",".($GLOBALS['inuser']['u_mperpage']);

  $pages=&build_pages($count[0],$start,$GLOBALS['inuser']['u_mperpage'],"index.php?m=profile&a=listusers&o=".getvar("o")."&desc=$desc");

  $sql = "SELECT uid, SUM(us_count) FROM ".$GLOBALS['DBprefix']."UserStat, ".$GLOBALS['DBprefix']."Forum
  WHERE fid=f_id AND f_nostats=0 AND ".check_access('f_id')." GROUP BY uid";
  $res =&db_query($sql,$link);
  while ($tmp=db_fetch_row($res)) {
    $ustats[$tmp[0]]=$tmp[1];
  }
  db_free_result($res);

  $sql = "SELECT u.*,l.l_title, lv_time1 AS lastvisit, ".
  "u__rating AS urating, u__warnings ".
  "FROM ".$GLOBALS['DBprefix']."UserLevel l, ".$GLOBALS['DBprefix']."User u ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (u.u_id=lv.uid AND lv.fid=0) ".
  "WHERE u.u_id>3 AND u.u__level=l.l_level ".$order.$limit;
  $res =&db_query($sql,$link);

  user_list_start($pages);
  while ($udata=&db_fetch_array($res)) {
    $udata['pcount']=$ustats[$udata['u_id']];
    user_list_entry($udata);
  }
  user_list_end();
}

function view() {
  global $link;
  $uid = intval(getvar("u"));
  $uname =&getvar('u_name');
  $sql = "SELECT l.l_title, l_pic, u.*, u__rating AS u_rating, u__warnings AS uw_count FROM  ".
    $GLOBALS['DBprefix']."UserLevel l, ".$GLOBALS['DBprefix']."User u ".
    "WHERE u.u__level=l.l_level";
  if ($uname) $sql.=' AND u__name="'.db_slashes($uname).'"';
  else $sql.=" AND u.u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_u_nosuchuser);
  $udata =&db_fetch_array($res);
  db_free_result($res);
  $udata['uw_count']=check_warnings($udata);
  $uid=$udata['u_id'];

  $sql = "SELECT SUM(us_count) FROM ".$GLOBALS['DBprefix']."UserStat us, ".$GLOBALS['DBprefix']. "Forum f ".
  " WHERE us.fid=f_id AND f_nostats=0 AND us.uid=$uid AND ".check_access('fid');
  $res =&db_query($sql,$link);
  $tmp = db_fetch_row($res);
  $udata['u_pcount'] = $tmp[0];
  db_free_result($res);

  if ($GLOBALS['opt_topiccount']!=1) {
    $sql = "SELECT t.*, f_id,f_title,f_link,p_id FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Forum f ".
       " WHERE p.p_tid=t.t_id AND t.t_fid=f.f_id AND p.p__premoderate=0 AND f.f_nostats=0 AND ".check_access('f_id').
       " AND p.p_uid=\"$uid\" ORDER BY p.p_id DESC LIMIT 1";
    $res =&db_query($sql,$link);
    $lmsg =&db_fetch_array($res);
    db_free_result($res);

    $sql = "SELECT COUNT(t_id), COUNT(pl_id) FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."Topic t ".
       "LEFT JOIN ".$GLOBALS['DBprefix']."Poll pl ON (pl_tid=t_id) ".
    " WHERE p.p_uid=\"$uid\" AND p.p__premoderate=0 AND p.p_tid=t.t_id AND p.p_id=t.t__startpostid AND t.t_fid=f.f_id AND f.f_nostats=0 AND ".check_access('f_id');
    $res=&db_query($sql,$link);
    list($tcount,$plcount)=db_fetch_row($res);
    db_free_result($res);
    $udata['u_tcount']=$tcount;
    $udata['u_plcount']=$plcount;
  }

//  if ($GLOBALS['inuserlevel']>=1000) db_explain($sql);

  $sql = "SELECT MAX(lv_time1) FROM ".$GLOBALS['DBprefix']."LastVisit WHERE uid=\"$uid\"";
  $res=&db_query($sql,$link);
  list($lvtime)=db_fetch_row($res);
  db_free_result($res);
  $udata['u_lastvisit']=$lvtime;

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Vote WHERE uid=\"$uid\"";
  $res=&db_query($sql,$link);
  list($vcount)=db_fetch_row($res);
  db_free_result($res);
  $udata['u_vcount']=$vcount;

  $addrbook=array();
  $sql = "SELECT u_id, u__name, u_status,u_owner FROM ".$GLOBALS['DBprefix']."User, ".$GLOBALS['DBprefix']."AddrBook ".
  "WHERE (u_owner=$uid  AND u_id=u_partner) OR (u_partner=$uid AND u_id=u_owner)";
  $res=&db_query($sql,$link);
  while ($ab=db_fetch_row($res)) {
    if ($ab[3]==$uid) {
      if ($ab[2]==-1) $addrbook['ignore'][]=user_out($ab[1],$ab[0]);
      elseif ($ab[2]==1) $addrbook['friends'][]=user_out($ab[1],$ab[0]);
    }
    else {
      if ($ab[2]==-1) {
        $addrbook['ignored'][]=user_out($ab[1],$ab[0]);
        if ($ab[3]==$GLOBALS['inuserid']) $addrbook['is_ignored']=1;
      }
      elseif ($ab[2]==1) {
        $addrbook['friended'][]=user_out($ab[1],$ab[0]);
        if ($ab[3]==$GLOBALS['inuserid']) $addrbook['is_friend']=1;
      }
    }
  }

  $ratetime = time()-$GLOBALS['opt_ratetime']*24*60*60;
  $sql = "SELECT COUNT(uid) FROM ".$GLOBALS['DBprefix']."UserRating WHERE uid=\"$uid\" AND ".
  "ur_rated=\"".$GLOBALS['inuserid']."\" AND ur_time>=$ratetime";
  $res =&db_query($sql,$link);
  $tmp = db_fetch_row($res);
  $allowrate=($tmp[0]==0);
  if ($GLOBALS['opt_hurl']) {
    if ($udata['u__blog_fid']) $udata['blog_link'] = 'blogs/'.urlencode($udata['u__name']).'/';
    if ($udata['u__gallery_fid']) $udata['gallery_link'] = 'gallerys/'.urlencode($udata['u__name']).'/';
  }
  else {
    if ($udata['u__blog_fid']) $udata['blog_link'] = 'index.php?f='.$udata['u__blog_fid'];
    if ($udata['u__gallery_fid']) $udata['gallery_link'] = 'index.php?f='.$udata['u__gallery_fid'];
  }

  user_profile_start($udata,$lmsg,$allowrate,$addrbook);

  $sql = "SELECT ct.*,f.*, us_count AS f_count, lv.lv_time1 AS lv_time, l_title FROM ".$GLOBALS['DBprefix']."Category ct, ".$GLOBALS['DBprefix']."Forum f ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserStat us ON (us.fid=f_id AND us.uid=$uid)".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua2 ON (ua2.fid=f_id AND ua2.uid=$uid)".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserLevel l ON (l_level=ua2.ua_level) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=f.f_id AND lv.uid=$uid) ".
  "WHERE ".check_access('f_id')." AND us_count>0 AND f_ctid=ct_id ".
  "ORDER BY us_count DESC";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)) {
    user_profile_list();
    while ($fdata=&db_fetch_array($res)) {
      if ($flist) $flist.="&";
      $flist.="fs[".$fdata['f_id']."]=1";
      user_profile_entry($fdata,$udata['u_pcount']);
    }
    user_profile_finish();
  }
  user_profile_end($udata,$flist,$addrbook);
}

function online() {
  $link = $GLOBALS['link'];
  $time = $GLOBALS['curtime']-$GLOBALS['opt_heretime']*60;
  online_start();
  out_online("",$time,$GLOBALS['curtime'],1);
  online_end();
}

function password() {
  get_password_form();
}

function do_password() {
  $login =&getvar("login");
  $email =&getvar("email");
  $number =&getvar("number");
  $userip=getip();
  $userproxy=array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
  if (file_exists($GLOBALS['opt_dir']."/config/lastsend.txt")) $regdata=file($GLOBALS['opt_dir']."/config/lastsend.txt");
  if (is_array($regdata)) foreach ($regdata as $curstr) {
    list($time,$ip,$proxy)=explode("|",trim($curstr));
    if ($time>$GLOBALS['curtime']-300) {
      $newipdata.=$curstr;
      if ($ip==$userip || ($userproxy && $userproxy==$proxy)) error("MSG_e_u_restoreblocked");
    }
  }
  if ($GLOBALS['opt_ddos']>0) check_ddos('code');
  $udata=auth_restorepass($login,$email,$number);
  if (!$udata) error(MSG_e_u_notfound);
  if ($udata['u_id']<3) error(MSG_e_u_sysuser);

  $newipdata.=$GLOBALS['curtime']."|$userip|$userproxy\n";
  $fh=fopen($GLOBALS['opt_dir']."/config/lastsend.txt","w");
  flock($fh,LOCK_EX);
  fputs($fh,$newipdata);
  fclose($fh);

  $GLOBALS['username']=$udata['u__name'];
  $GLOBALS['ip']=getip();
  if (!$udata['u_encrypted']) {
    $GLOBALS['password']=$udata['u__password'];
    process_mail("password.txt",$udata['u__email'],MSG_u_password);
    message(MSG_u_pass_sent);
  }
  else {
    $GLOBALS['password']=$udata['newpassword'];
    $GLOBALS['newlink']=$GLOBALS['opt_url']."/agent.php?a=pass&u=".$udata['u_id']."&key=".$udata['newkey'];
    process_mail("pass_cr.txt",$udata['u__email'],MSG_u_password);
    message(MSG_u_pass_generated);
  }
}

function do_user_rate() {
  $uid =&getvar("u");
  if ($GLOBALS['inuserid']<=3 || $uid<=3) error(MSG_e_u_guest_no_rate);
  $link = $GLOBALS['link'];
  if ($GLOBALS['inuserid']==$uid) error(MSG_e_u_selfrate);
  if ($GLOBALS['inuserlevel']<$GLOBALS['opt_ratinglevel'] || $GLOBALS['inuser']['u__rating'] <=0) error(MSG_e_u_level_rate);
  $ratetime = time()-$GLOBALS['opt_ratetime']*24*60*60;
  $sql = "SELECT COUNT(uid) FROM ".$GLOBALS['DBprefix']."UserRating WHERE uid=\"$uid\" AND ".
  "ur_rated=\"".$GLOBALS['inuserid']."\" AND ur_time>=$ratetime";
  $res =&db_query($sql,$link);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  if ($tmp[0]>0) error(MSG_e_u_rated);
  
  if ($GLOBALS['opt_norateperiod']) {
    $sql = "SELECT MAX(p__time) FROM ".$GLOBALS['DBprefix']."Post WHERE p_uid=\"".$GLOBALS['inuserid']."\"";
    $res =&db_query($sql,$link);
    list($lastpost)=db_fetch_row($res);
    db_free_result($res);
    if ($lastpost<$GLOBALS['curtime']-$GLOBALS['opt_norateperiod']*24*60*60) error(MSG_e_u_norateperiod); 
  }

  if (getvar("dir")=="pro") $direct=1;
  else $direct=-1;
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UserRating (uid,ur_value,ur_rated,ur_time) VALUES(\"$uid\",$direct,\"".$GLOBALS['inuserid']."\",".time().")";
  $res =&db_query($sql,$link);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__rating=u__rating+$direct WHERE u_id=$uid";
  $res =&db_query($sql,$link);
  message(MSG_u_rated,1);
}

function list_warn() {
  $uid =&getvar("u");
  global $link;

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UserWarning uw, ".$GLOBALS['DBprefix']."User u WHERE uw_uid=\"$uid\" AND u.u_id=uw.uw_warner";
  $res =&db_query($sql,$link);
  $count = db_num_rows($res);
  warn_form_start($pdata,$count);
  while ($wdata=&db_fetch_array($res)) {
    warn_form_entry($wdata);
  }
  if (db_num_rows($res)==0) warn_form_noentries();
  warn_form_end();
}

function warn() {
  if ($GLOBALS['inuserbasic']<$pdata['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;
  $uid = intval(getvar("u"));
  $sql = "SELECT u.u__level,ua.ua_level FROM ".$GLOBALS['DBprefix']."User u ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=\"$uid\" AND ua.fid=\"".$GLOBALS['forum']."\") ".
  "WHERE u.u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  $level = $tmp[1];
  if (!$tmp[1]) $level = $tmp[0];
  if ($GLOBALS['inuserlevel']<=$level) error(MSG_e_mod_subordinate);

  list_warn();
  warn_form_input();
}

function do_warn() {
  $comment=&getvar("comment");
  if (!$comment) error(MSG_e_warn_nocomment);
  global $link;
  if ($GLOBALS['inuserlevel']<$pdata['f_lmoderate'] && $pdata['ua_level']<$pdata['f_lmoderate'] && (($GLOBALS['forum']!=$GLOBALS['inuser']['u__blog_fid'] && $GLOBALS['forum']!=$GLOBALS['inuser']['u__gallery_fid']) || $GLOBALS['inuserbasic']>=1000)) error(MSG_e_mod_norights);

  $uid =&getvar("u");
  $sql = "SELECT u.u__level,ua.ua_level FROM ".$GLOBALS['DBprefix']."User u ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=\"$uid\" AND ua.fid=\"".$GLOBALS['forum']."\") ".
  "WHERE u.u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  $level = $tmp[1];
  if (!$tmp[1]) $level = $tmp[0];
  if ($GLOBALS['inuserlevel']<=$level) error(MSG_e_mod_subordinate);

  $enddate = get_date_field("enddate");
  if (getvar("valid")=="endless") $enddate="0";
  if (getvar("mode")=="warn") {
    $value=-1;
    $subj = MSG_warn_warning;
  }
  elseif (getvar("mode")=="award") {
    $value=1;
    $subj = MSG_warn_award;
  }
  elseif (getvar("mode")=="ban") {
    $value=-$GLOBALS['opt_warnstoban'];
    $subj = MSG_warn_ban;
  }
  else error(MSG_e_warn_value);
  $subj .= " ".MSG_warn_from." ".$GLOBALS['inuser']['u__name'];
  $comment .= db_slashes("\n\nТема: \"".$GLOBALS['intopic']['t_title']."\" в разделе \"".$GLOBALS['inforum']['f_title']."\"\n".
     $GLOBALS['opt_url']."/index.php?t=".$GLOBALS['topic']);
  if ($enddate!=0) $comment.="\n".MSG_warn_validtill." ".short_date_out($enddate);
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UserWarning(uw_uid,uw_value,uw_warner,uw_validtill,uw_comment) VALUES(\"$uid\",$value,".$GLOBALS['inuserid'].",$enddate,\"$comment\")";
  $res =&db_query($sql,$link);
  send_pm($uid,$GLOBALS['inuserid'],$comment,$subj,"pm_signature=".$GLOBALS['inuser']['u_usesignature'].", ".
         "pm_smiles=".$GLOBALS['inuser']['u_usesmiles'].", pm_bcode=1, pm_pair=0");

  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__warnings=u__warnings+$value, u__warntime=GREATEST(u__warntime,$enddate) WHERE u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  remove_cached_user($uid);
  $GLOBALS['refpage']=build_url($GLOBALS['intopic']);
  message(MSG_warn_done,1);
}

function resend() {
  resend_form();
}

function do_resend() {
  if ($GLOBALS['opt_ddos']>0) check_ddos("code");
  $link = $GLOBALS['link'];
  $uname=&getvar('uname');
  $newmail=&getvar('newmail');
  $password1=&getvar('password1');
  $sql = "SELECT u_id, u__key, u__password, u_encrypted FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"$uname\" AND u__active=0";
  $res=&db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_u_nouser_resend);
  $udata=&db_fetch_array($res);
  db_free_result($res);

  if ($GLOBALS['opt_activate']!=1) error(MSG_e_u_badactmode);
  else {
    if ($udata['u_encrypted']) $password1=md5($password1);
    if ($password1!=$udata['u__password']) error(MSG_e_badpassword);
    $GLOBALS['newlink']=$GLOBALS['opt_url']."/agent.php?u=".$udata['u_id']."&a=activate&newmail=$newmail&key=".md5($udata['u__password'].$udata['u__key'].$newmail);
    $GLOBALS['username']=$uname;
    process_mail("react.txt",$newmail,MSG_resend_done);
  }
  message(MSG_u_reactivated);
}

function check_user_params($changename) {
  global $link;
  
  if ($changename) {
    $newname = trim(getvar("u__name"));
    $newname = preg_replace('/  +/',' ',$newname);    
    
    if (!$GLOBALS['opt_nameletters']) $GLOBALS['opt_nameletters']='\w\d ;,+\\-*\/=А-Яа-я';
    if (!preg_match('/^['.$GLOBALS['opt_nameletters'].']+$/',$newname)) error(MSG_e_u_cyrforbidden);
    if ($newname=="Guest" || $newname=="System" || $newname=="NewUser") error(MSG_e_u_reservedname);
    require('canonize.php');
    $new_canon = canonize_name($newname);
    $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE (u__name=\"$newname\" OR u__email=\"".trim(getvar("u__email"))."\" OR u__canonical=\"".$new_canon.'") AND u_id<>'.$GLOBALS['inuserid'];
    $res =&db_query($sql,$link);
    if (db_num_rows($res)>0) error(MSG_e_u_alreadyused);
    db_free_result($res);
    
    $sql = "SELECT w_bad FROM ".$GLOBALS['DBprefix']."BadWord";
    $res = db_query($sql,$link);
    $words=array();
    while ($badword=db_fetch_row($res)) {
      $badword[0]=str_replace('*','\S*',$badword[0]);
      if ($badword[0] && preg_match('/^'.$badword[0].'/i',$newname) || preg_match('/\s+'.$badword[0].'/i',$newname)) error(MSG_e_u_badword);
    }
    db_free_result($res);
  }
  
  if ($_POST['u_homepage'] && substr(strtolower($_POST['u_homepage']),0,7)!="http://") $_POST['u_homepage']="http://".$_POST['u_homepage'];
  if ($_POST['u_diary'] && substr(strtolower($_POST['u_diary']),0,7)!="http://") $_POST['u_diary']="http://".$_POST['u_diary'];
  if ($GLOBALS['opt_mustfields']) {
    $GLOBALS['opt_mustfields']=str_replace(',',';',$GLOBALS['opt_mustfields']);
    $GLOBALS['opt_mustfields']=str_replace(' ','',$GLOBALS['opt_mustfields']);
    $fields=explode(";",$GLOBALS['opt_mustfields']);
    foreach ($fields as $curfield) {
      if ($curfield[0]=='u' && $curfield[1]=='_' && $curfield[2]!='_') {
        if (getvar($curfield)=="") error($GLOBALS['opt_mustmsg']);
      }
    }
  }
}

function user_search() {
  $levels=build_level_select();
  user_search_form($levels);
}

function user_search_result() {
  global $link;
  $sqlarray=array();
  if ($name=&getvar('name')) {
    $mode=&getvar('mode');
    if (!$mode) array_push($sqlarray,"u__name=\"$name\"");
    elseif ($mode==1) array_push($sqlarray,"u__name LIKE \"$name%\"");
    elseif ($mode==2) array_push($sqlarray,"u__name LIKE \"%$name%\"");
  }
  if (getvar('photo')) array_push($sqlarray,"u__photo_id<>0");
  if (getvar('icq')) array_push($sqlarray,"u_icq<>0");
  if (getvar('site')) array_push($sqlarray,"u_homepage!=\"\"");
  if ($intr=&getvar('interests')) {
    $intarray=explode(",",$intr);
    foreach ($intarray as $curint) array_push($sqlarray,"u_interests LIKE \"%".trim($curint)."%\"");
  }
  if (getvar('email')) array_push($sqlarray,"u_showmail<>0");
  if ($level=&getvar('level')) array_push($sqlarray,"u__level>=\"$level\"");

  if (count($sqlarray)==0) error(MSG_us_nodata);
  $sqldata = join(" AND ",$sqlarray);

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."User WHERE $sqldata";
  $res=&db_query($sql,$link);
  list($count)=db_fetch_row($res);
  db_free_result($res);

  $start=&getvar("st");
  if (!$start) $start="0";
  $perpage=$GLOBALS['inuser']['u_tperpage'];
  $pages=&build_pages($count,$start,$perpage,"index.php?f=$forum&perpage=$perpage&filter=$filter&o=$order&time=$time");


  user_result_start($pages);
  if ($start!="all") $limit = " LIMIT $start,".$perpage;
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User, ".$GLOBALS['DBprefix']."UserLevel WHERE u__level=l_level AND $sqldata $limit";
  $res=&db_query($sql,$link);
  while ($udata=&db_fetch_array($res)) {
    user_list_entry($udata);
  }
  user_result_end($pages);

  $levels=build_level_select();
  user_search_form($levels);
}

function change_blog() {
  global $link;
  if ($GLOBALS['inuserid']<=3) error(MSG_e_u_noblogguest);
  if ($GLOBALS['inuser']['u__blog_fid']!=0) {
    $sql = 'SELECT * FROM '.$GLOBALS['DBprefix'].'Forum WHERE f_id='.$GLOBALS['inuser']['u__blog_fid'];
    $res =& db_query($sql,$link);
    $blogdata=db_fetch_array($res);
    db_free_result($res);
  }
  else {
    if ($GLOBALS['inuserbasic']<$GLOBALS['opt_blog_level'] && $GLOBALS['inuser']['u__blog_fid']==0) error(MSG_e_u_nobloglevel);
    $blogdata['f_lpost']=100;
    $blogdata['f_lpremod']=100;
    $blogdata['f_lread']=-1;
    $blogdata['f_lview']=-1;
  }
  $levelselect = build_level_select();
  change_blog_form($blogdata,$levelselect);
}

function do_change_blog() {
  global $link;
  $sqldata = build_sql('f_');
  if ($GLOBALS['inuserid']<=3) error(MSG_e_u_noblogguest);
  $sqldata.=', f_ltopic=999, f_lmoderate=999, f_bcode=1, f_smiles=1, f_lhtml=1024, f_lattach=999, f_lip=999';
  if ($GLOBALS['inuser']['u__blog_fid']!=0) {
    $sql =  'SELECT f_id FROM '.$GLOBALS['DBprefix'].'Forum WHERE f_id='.$GLOBALS['inuser']['u__blog_fid'];
    $res =& db_query($sql,$link);
    if (db_num_rows($res)==0) $GLOBALS['inuser']['u__blog_fid']=0;
    db_free_result($res);
  }
  if ($GLOBALS['inuser']['u__blog_fid']!=0) {
    $sql = 'UPDATE '.$GLOBALS['DBprefix'].'Forum SET '.$sqldata.' WHERE f_id='.$GLOBALS['inuser']['u__blog_fid'];
    $res =& db_query($sql,$link);
    $blog_id=$GLOBALS['inuser']['u__blog_fid'];
  }
  else {
    if ($GLOBALS['inuserbasic']<$GLOBALS['opt_blog_level'] && $GLOBALS['inuser']['u__blog_fid']==0) error(MSG_e_u_nobloglevel);
    if ($GLOBALS['opt_blog_container']==0) $sqldata.=', f_hidden=1';
    else $sqldata.=', f_parent='.$GLOBALS['opt_blog_container'];
    $sqldata.=', f_link="blog_'.$GLOBALS['inuser']['u__name'].'", f_ctid='.$GLOBALS['opt_blog_cat'].', f_rate=1, f_rules=""';
    $sql = 'INSERT INTO '.$GLOBALS['DBprefix'].'Forum SET '.$sqldata.', f_tpid=11';
    $res =& db_query($sql,$link);
    $blog_id = db_insert_id($sql,$link);
    $sql = 'UPDATE '.$GLOBALS['DBprefix'].'User SET u__blog_fid='.$blog_id.' WHERE u_id='.$GLOBALS['inuserid'];
    $res =& db_query($sql,$link);
  }
  if ($GLOBALS['inuserbasic']<999) {
    $sql = 'DELETE FROM '.$GLOBALS['DBprefix'].'UserAccess WHERE uid='.$GLOBALS['inuserid'].' AND fid='.$blog_id;
    $res =& db_query($sql,$link);
    $sql = 'INSERT INTO '.$GLOBALS['DBprefix'].'UserAccess (uid,fid,ua_level) VALUES ('.$GLOBALS['inuserid'].','.$blog_id.',999)';
    $res =& db_query($sql,$link);
    $sql = 'DELETE FROM '.$GLOBALS['DBprefix'].'Online';
    $res =& db_query($sql,$link);
  }
  
  message(MSG_u_blogsaved);
}

function change_gallery() {
  global $link;
  if ($GLOBALS['inuserid']<=3) error(MSG_e_u_nogalleryguest);
  if ($GLOBALS['inuser']['u__gallery_fid']!=0) {
    $sql = 'SELECT * FROM '.$GLOBALS['DBprefix'].'Forum WHERE f_id='.$GLOBALS['inuser']['u__gallery_fid'];
    $res =& db_query($sql,$link);
    $gallerydata=db_fetch_array($res);
    db_free_result($res);
  }
  else {
    if ($GLOBALS['inuserbasic']<$GLOBALS['opt_gallery_level'] && $GLOBALS['inuser']['u__gallery_fid']==0) error(MSG_e_u_nogallerylevel);
    $gallerydata['f_lpost']=100;
    $gallerydata['f_lpremod']=100;
    $gallerydata['f_lread']=-1;
    $gallerydata['f_lview']=-1;
  }
  $levelselect = build_level_select();
  change_gallery_form($gallerydata,$levelselect);
}

function do_change_gallery() {
  global $link;
  $sqldata = build_sql('f_');
  if ($GLOBALS['inuserid']<=3) error(MSG_e_u_nogalleryguest);
  $sqldata.=', f_ltopic=999, f_lmoderate=999, f_bcode=1, f_smiles=1, f_lhtml=1024, f_lattach=999, f_lip=999';
  if ($GLOBALS['inuser']['u__gallery_fid']!=0) {
    $sql =  'SELECT f_id FROM '.$GLOBALS['DBprefix'].'Forum WHERE f_id='.$GLOBALS['inuser']['u__gallery_fid'];
    $res =& db_query($sql,$link);
    if (db_num_rows($res)==0) $GLOBALS['inuser']['u__gallery_fid']=0;
    db_free_result($res);
  }
  if ($GLOBALS['inuser']['u__gallery_fid']!=0) {
    $sql = 'UPDATE '.$GLOBALS['DBprefix'].'Forum SET '.$sqldata.' WHERE f_id='.$GLOBALS['inuser']['u__gallery_fid'];
    $res =& db_query($sql,$link);
  }
  else {
    if ($GLOBALS['inuserbasic']<$GLOBALS['opt_gallery_level'] && $GLOBALS['inuser']['u__gallery_fid']==0) error(MSG_e_u_nogallerylevel);
    if ($GLOBALS['opt_gallery_container']==0) $sqldata.=', f_hidden=1';
    else $sqldata.=', f_parent='.$GLOBALS['opt_gallery_container'];
    $sqldata.=', f_link="gallery_'.$GLOBALS['inuser']['u__name'].'", f_ctid='.$GLOBALS['opt_gallery_cat'].', f_rate=1';
    $sql = 'INSERT INTO '.$GLOBALS['DBprefix'].'Forum SET '.$sqldata.', f_tpid=12';
    $res =& db_query($sql,$link);
    $gallery_id = db_insert_id($sql,$link);
    $sql = 'UPDATE '.$GLOBALS['DBprefix'].'User SET u__gallery_fid='.$gallery_id.' WHERE u_id='.$GLOBALS['inuserid'];
    $res =& db_query($sql,$link);
    if ($GLOBALS['inuserbasic']<999) {
      $sql = 'INSERT INTO '.$GLOBALS['DBprefix'].'UserAccess (uid,fid,ua_level) VALUES ('.$GLOBALS['inuserid'].','.$gallery_id.',999)';
      $res =& db_query($sql,$link);
    }
    $sql = 'DELETE FROM '.$GLOBALS['DBprefix'].'Online';
    $res =& db_query($sql,$link);
  }
  message(MSG_u_gallerysaved);
}

function self_delete() {
  if ($GLOBALS['inuser']['u__noedit']==1) error(MSG_e_u_noedit);
  if ($GLOBALS['inuserid']<=3) error(MSG_e_u_sysprofile);  
  self_del_confirm_form();
}

function do_self_delete() {
  if ($GLOBALS['inuser']['u__noedit']==1) error(MSG_e_u_noedit);
  if ($GLOBALS['inuserid']<=3) error(MSG_e_u_sysprofile);  
  $pass=getvar('pass');
  if ($GLOBALS['inuser']['u_encrypted']) $pass=md5($pass);
  if ($pass!=$GLOBALS['inuser']['u__password']) error(MSG_e_badpassword);
  require('delete.php');
  delete_user($GLOBALS['inuserid']);
  $GLOBALS['refpage']='index.php';
  message(MSG_user_deleted);
}

function forums_ignore() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_sub_noguest);
  global $link;
  $inuserid=$GLOBALS['inuserid'];
  $forum=$GLOBALS['forum'];
  
	$sql = "SELECT f_id, f_title, f_sortfield, ct_id, ct_sortfield, COALESCE(fid,0) AS ignored FROM ".$GLOBALS['DBprefix']."Category ct, ".$GLOBALS['DBprefix']."Forum f  ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."ForumIgnore ON (uid=".$GLOBALS['inuserid']." AND f_id=fid) ".
  "WHERE f_ctid=ct_id AND ".check_access('f_id')." ORDER BY ct_sortfield";
  $res =&db_query($sql,$link);
  
  $forums = array();
  while ($fdata=&db_fetch_array($res)) $forums[]=$fdata;
  $forums=sort_forums_recurse($forums);  
  
  ignore_list_start();
  foreach ($forums as $fdata) {
	  ignore_list_entry($fdata);
  }
  db_free_result($res);
  ignore_list_end();
}

function do_forums_ignore() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_sub_noguest);
  $link = $GLOBALS['link'];
  $inuserid = $GLOBALS['inuserid'];

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."ForumIgnore WHERE uid=$inuserid";
  $res =&db_query($sql,$link);
  
  if (is_array($_POST['ignore'])) foreach ($_POST['ignore'] as $curforum=>$curvalue) if (is_numeric($curforum)) {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."ForumIgnore (uid,fid) VALUES (".$inuserid.",".intval($curforum).")";
    $res=&db_query($sql,$link);
  }
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=&db_query($sql,$link);
  message(MSG_ignore_saved,1);
}


function locations($locations) {
  if ($GLOBALS['action']=="register") {
    array_push($locations,MSG_u_register);
  }
  elseif ($GLOBALS['action']=="edit") {
    array_push($locations,MSG_u_editprofile);
  }
  elseif ($GLOBALS['action']=="rules") {
    array_push($locations,MSG_u_rules);
  }
  elseif ($GLOBALS['action']=="listusers") {
    array_push($locations,MSG_u_list);
  }
  elseif ($GLOBALS['action']=="view") {
    array_push($locations,MSG_u_profile_view);
  }
  elseif ($GLOBALS['action']=="online") {
    array_push($locations,MSG_u_online);
  }
  elseif ($GLOBALS['action']=="password") {
    array_push($locations,MSG_u_password);
  }
  elseif ($GLOBALS['action']=="warn") {
    array_push($locations,MSG_warn);
  }
  elseif ($GLOBALS['action']=="login") {
    array_push($locations,MSG_login);
  }
  elseif ($GLOBALS['action']=="list_warn") {
    array_push($locations,MSG_warnlist);
  }
  elseif ($GLOBALS['action']=="resend") {
    array_push($locations,MSG_resend);
  }
  elseif ($GLOBALS['action']=="user_search") {
    array_push($locations,MSG_us_extsearch);
  }
  elseif ($GLOBALS['action']=="user_search_result") {
    array_push($locations,MSG_us_result);
  }
  elseif ($GLOBALS['action']=="change_blog") {
    array_push($locations,MSG_u_changeblog);
  }
  elseif ($GLOBALS['action']=="change_gallery") {
    array_push($locations,MSG_u_changegallery);
  }
  elseif ($GLOBALS['action']=="self_delete") {
    array_push($locations,MSG_user_delete);
  }
  elseif ($GLOBALS['action']=='forums_ignore') {
    array_push($locations,MSG_ignore_loc);
  }
  return $locations;
}
