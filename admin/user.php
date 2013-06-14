<? /*

User administration script for Intellect Board 2

(c) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru
*/

if (!$IBOARD) die("Hack attempt!");

function u_select() {
  ad_u_select();
  global $link;
  $levels = build_userlevel_select();
  user_level_start($levels);
  $sql = "SELECT f_id,f_title FROM ".$GLOBALS['DBprefix']."Forum";
  $res = db_query($sql,$link);
  while ($fdata=db_fetch_array($res)) {
    user_level_forum($fdata,$levels,0);
  }
  user_level_end();
}

function u_edit() {
  global $link;
  $uname = getvar("uname");
  if ($uname=="System") error(MSG_e_nosystem);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix'].'User WHERE u__name="'.$uname.'"';
  $res = db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_nouser);
  $udata = db_fetch_array($res);
  $newaction = "u_save";
  $newmodule = "user";
  $levels = build_userlevel_select();
  $langselect = build_select("SELECT * FROM ".$GLOBALS['DBprefix']."Language",$udata['u_lnid']);
  $styleselect = build_select("SELECT * FROM ".$GLOBALS['DBprefix']."StyleSet",$udata['u_stid']);
  load_style("profile.php");

  user_profile("u_save","user",$udata,$styleselect,$langselect,"","user");
  user_params_start($uname,$levels,$udata['u__level']);
  $sql = "SELECT f_id,f_title,ua_level FROM ".$GLOBALS['DBprefix']."Forum f LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.fid=f.f_id AND ua.uid=".$udata['u_id'].")";
  $res = db_query($sql,$link);
  while ($fdata=db_fetch_array($res)) {
    if (!$fdata['ua_level']) $fdata['ua_level']="common";
    user_level_forum($fdata,$levels,$fdata['ua_level']);
  }
  user_level_end();
}

function u_setlevel() {
  check_post();
  global $link;
  $unames = getvar("unames");
  if (!$unames) error(MSG_e_ad_nouser);
  ereg_replace(";",",",$unames);
  preg_replace("/\s+,/",",",$unames);
  $ulevel = getvar("u__level");
  if (isset($_POST['uforum'])) $uforums = $_POST["uforum"];
  else $uforums=array();
  $unames = explode(",",$unames);
  foreach ($unames as $curname) {
    if ($curname!="Guest" && $curname!="System") {
      if ($userlist) $userlist .= 'OR u__name="'.$curname.'"';
      else $userlist = 'u__name="'.$curname.'"';
    }
  }
  $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE $userlist";
  $res = db_query($sql,$link);
  $uids = array();
  while ($num=db_fetch_row($res)) {
    array_push($uids,$num[0]);
    if ($uidlist) $uidlist.=" OR ";
    $uidlist.="uid=".$num[0];
  }
  db_free_result($res);
  foreach ($uforums as $curforum=>$curlevel) {
    if ($curlevel && $curlevel!="0") {
      if ($uidlist) {
        $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserAccess WHERE fid=\"".addslashes($curforum)."\" AND ($uidlist)";
        $res = db_query($sql,$link);
      }
      $sqldata="";
      if ($curlevel!="common") {
        foreach ($uids as $curid) {
          if ($sqldata) $sqldata.=", ";
          $sqldata.="($curid,".addslashes($curforum).",".addslashes($curlevel).")";
        }
        $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UserAccess VALUES $sqldata";
        $res = db_query($sql,$link);
      }
    }
  }
  if ($ulevel && $ulevel!="0") {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__level=\"$ulevel\" WHERE $userlist";
    $res = db_query($sql,$link);
  }
  if ($uid==1 && file_exists($GLOBALS['opt_dir'].'/config/guest.txt')) unlink($GLOBALS['opt_dir'].'/config/guest.txt');
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);
  require_once('../delete.php');
  clear_mod_cache();
  ad_message(MSG_u_rights_set,MSG_u_edit,"admin/index.php?m=user&a=u_select");
}

function u_save() {
  check_post();
  $uid = getvar("u");
  if ($uid==2) error(MSG_e_nosystem);
  $password1 = getvar("password1");
  $password2 = getvar("password2");
  $uname=trim(getvar("u__name"));
  if (strpos($uname,",")!==false || strpos($uname,";")!==false) error(MSG_e_u_badchars);

  $link = $GLOBALS['link'];
  $sql = "SELECT COUNT(u_id) FROM ".$GLOBALS['DBprefix']."User WHERE u_id!=\"$uid\" AND (u__name=\"$uname\" OR u__email=\"".getvar("u__email")."\")";
  $res = db_query($sql,$link);
  $count=db_fetch_row($res);
  db_free_result($res);
  if ($count[0]>0) error(MSG_e_u_alreadyused);
  unset($_POST['u__name']);

  $sqldata = build_sql_all("u_");
  if (getvar('photo_del') || getvar('avatar_del')) {
    $sql = "SELECT u__pavatar_id, u__photo_id FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
    $res=db_query($sql,$link);
    list($avatar,$photo)=db_fetch_row($res);
    db_free_result($res);

    if (getvar('avatar_del')) {
      handle_upload("",$avatar,1);
      $sqldata.=", u__pavatar_id=0, u_avatartype=0";
    }
    if (getvar('photo_del')) {
      handle_upload("",$photo,1);
      $sqldata.=", u__photo_id=0";
    }
  }

  if ($password1 && $password1!=$password2) error(MSG_e_u_passnotmatch);
  if ($password1) {
    if ($GLOBALS['opt_encrypted']!=2) $encrypt=$GLOBALS['opt_encrypted'];
    else $encrypt = getvar('u_encrypted');
    if ($encrypt) $password1 = md5($password1);
    if ($password1) $sqldata.=", u__password=\"$password1\", u_encrypted=\"$encrypt\"";
  }

  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__name=\"$uname\", $sqldata WHERE u_id=\"$uid\"";
  $res = db_query($sql,$link);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET p_uname=\"$uname\" WHERE p_uid=\"".$uid.'"';
  $res=db_query($sql,$link);
  if ($uid==1) unlink($GLOBALS['opt_dir'].'/config/guest.txt');
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);
  ad_message(MSG_u_saved,MSG_u_select,"admin/index.php?m=user&a=u_select");
}

function u_confirm() {
  $params['uname'] = $_GET['uname'];
  $newaction="u_delete";
  $newmodule="user";
  confirm($newmodule,$newaction,$params,MSG_u_delete." ".$_GET['uname'],"admin/index.php?m=user&a=user_list&ltt=".substr(getvar("uname"),0,1));
}

function u_level_list() {
  $link = $GLOBALS['link'];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UserLevel ORDER BY l_level";
  $res = db_query($sql,$link);
  ad_u_level_start();
  while ($ldata=db_fetch_array($res)) {
    ad_u_level_entry($ldata);
  }
  ad_u_level_end();
  $newaction="u_level_add";
  ad_u_level_edit($newaction,array('l_custom'=>1),MSG_l_create);
}

function u_level_add() {
  check_post();
  $llevel = getvar("l_level");
  $link = $GLOBALS['link'];
  $sql = "SELECT l_level FROM ".$GLOBALS['DBprefix']."UserLevel WHERE l_level=\"$llevel\"";
  $res = db_query($sql,$link);
  if (db_num_rows($res)>0) error(MSG_e_levelexists);
  db_free_result($res);
  if ($llevel <1 || $llevel > 1023) error(MSG_e_invalidlevel);
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UserLevel VALUES(\"".$llevel."\",\"".getvar("l_title")."\",\"".getvar("l_minpost")."\",\"".getvar("l_custom")."\",\"".getvar('l_pic')."\")";
  $res = db_query($sql,$link);
  ad_message(MSG_l_created,MSG_l_select,"admin/index.php?m=user&a=u_level_list");
}

function u_level_edit() {
  $link = $GLOBALS['link'];
  $level = getvar("l");
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UserLevel WHERE l_level=\"$level\"";
  $res = db_query($sql,$link);
  $ldata = db_fetch_array($res);
  $newaction="u_level_save";
  ad_u_level_edit($newaction,$ldata,MSG_l_edit." \"".$ldata['l_title']."\"");
}

function u_level_save() {
  check_post();
  $link = $GLOBALS['link'];
  $llevel = getvar("l");
  if ($llevel <1 || $llevel > 1023) error(MSG_e_invalidlevel);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."UserLevel SET l_title=\"".getvar("l_title")."\", l_minpost=\"".getvar("l_minpost")."\", l_custom=\"".getvar("l_custom")."\", l_pic=\"".getvar('l_pic')."\" WHERE l_level=$llevel";
  $res = db_query($sql,$link);
  ad_message(MSG_l_saved,MSG_l_select,"admin/index.php?m=user&a=u_level_list");
}

function u_level_confirm() {
  $params['l']=getvar("l");
  confirm("user","u_level_delete",$params,MSG_u_levelconfirm."?","admin/index.php?m=user&a=u_level_list");
}

function u_level_delete() {
  check_post();
  $link = $GLOBALS['link'];
  $llevel = getvar("l");
  if ($llevel <1 || $llevel > 1023) error(MSG_e_invalidlevel);
  $sql = "SELECT l_custom,l_minpost FROM ".$GLOBALS['DBprefix']."UserLevel WHERE l_level=$llevel";
  $res = db_query($sql,$link);
  $lcustom = db_fetch_row($res);
  db_free_result($res);
  if ($lcustom[0]) {
    $sql = "SELECT u.u_id,COUNT(p.p_id) FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."Post p WHERE u.u__level=$llevel AND u.u_id=p.p_uid GROUP BY p.p_uid";
    $res = db_query($sql,$link);
    $ulevels = array();
    while ($tmplevel=db_fetch_row($res)) { $ulevels[$tmplevel[0]] = $tmplevel[1]; }
    foreach ($ulevels as $uid=>$curlevel) {
      $sql = "SELECT MAX(l_level) FROM ".$GLOBALS['DBprefix']."UserLevel WHERE l_minpost<".$curlevel." GROUP BY l_level";
      $res = db_query($sql,$link);
      $newlevel = db_fetch_row($res);
      db_free_result($res);
      $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__level=".$newlevel[0]." WHERE u_id=$uid";
      $res = db_query($sql,$link);
    }
  }
  else {
    $sql = "SELECT MAX(l_level) FROM ".$GLOBALS['DBprefix']."UserLevel WHERE l_minpost<".$lcustom[1]." AND l_custom=0";
    $res = db_query($sql,$link);
    $newlevel = db_fetch_row($res);
    db_free_result($res);
    if ($newlevel[0]<1) error(MSG_e_invalidlevel);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__level=".$newlevel[0]." WHERE u__level=$llevel";
    $res = db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."UGroup SET g_setlevel=".$newlevel[0]." WHERE g_setlevel=$llevel";
    $res = db_query($sql,$link);
  }
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserLevel WHERE l_level=$llevel";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserAccess WHERE ua_level=\"$llevel\"";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupAccess WHERE ga_level=\"$llevel\"";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);
  ad_message(MSG_l_delete,MSG_l_select,"admin/index.php?m=user&a=u_level_list");
}

function user_list() {
  global $link;
  $sql = "SELECT DISTINCT SUBSTRING(UPPER(u__name),1,1) AS letter FROM ".$GLOBALS['DBprefix']."User WHERE u_id>3 ORDER BY letter";
  $res = db_query($sql,$link);
  user_letter_start();
  $ltt=getvar("ltt");
  while ($letter=db_fetch_row($res)) {
    if (!$ltt) $ltt=$letter[0];
    user_letter_entry($letter[0]);
  }
  user_letter_end();

  $sql = "SELECT uid,SUM(us_count) FROM ".$GLOBALS['DBprefix']."UserStat GROUP BY uid";
  $res = db_query($sql,$link);
  while ($usdata=db_fetch_row($res)) {
    $ustats[$usdata[0]]=$usdata[1];
  }
  db_free_result($res);

  $sql = "SELECT uid,MAX(lv_time1) FROM ".$GLOBALS['DBprefix']."LastVisit GROUP BY uid";
  $res = db_query($sql,$link);
  while ($lvdata=db_fetch_row($res)) {
    $lvstats[$lvdata[0]]=$lvdata[1];
  }
  db_free_result($res);

  $sql = "SELECT u.* FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."UserLevel ".
  "WHERE UPPER(SUBSTRING(LTRIM(u__name),1,1))=\"$ltt\" AND u_id>3 AND u__level=l_level ".
  "ORDER BY u__name";
  $res = db_query($sql,$link);
  user_lst_start(MSG_u_letterlist."\"".getvar("ltt")."\"");
  while ($udata=db_fetch_array($res)) {
    $udata['u_count']=$ustats[$udata['u_id']];
    $udata['u_lastvisit']=$lvstats[$udata['u_id']];
    user_lst_entry($udata);
  }
  user_lst_end();
}

function banned_list() {
  global $link;

  $sql = "SELECT uid,SUM(us_count) FROM ".$GLOBALS['DBprefix']."UserStat GROUP BY uid";
  $res = db_query($sql,$link);
  while ($usdata=db_fetch_row($res)) {
    $ustats[$usdata[0]]=$usdata[1];
  }
  db_free_result($res);

  $sql = "SELECT uid,MAX(lv_time1) FROM ".$GLOBALS['DBprefix']."LastVisit GROUP BY uid";
  $res = db_query($sql,$link);
  while ($lvdata=db_fetch_row($res)) {
    $lvstats[$lvdata[0]]=$lvdata[1];
  }
  db_free_result($res);

  $sql = "SELECT u.*,l_title FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."UserLevel ".
  "WHERE u__level=-1 AND u_id>3 AND u__level=l_level ".
  "ORDER BY u__name";
  $res = db_query($sql,$link);
  user_lst_start(MSG_user_bannedlist);
  while ($udata=db_fetch_array($res)) {
    user_lst_entry($udata);
  }
  if (db_num_rows($res)==0) user_lst_noentries(MSG_user_nobanned);
  user_lst_end();
}

function inactive_list() {
  global $link;

  $sql = "SELECT uid,SUM(us_count) FROM ".$GLOBALS['DBprefix']."UserStat GROUP BY uid";
  $res = db_query($sql,$link);
  while ($usdata=db_fetch_row($res)) {
    $ustats[$usdata[0]]=$usdata[1];
  }
  db_free_result($res);

  $sql = "SELECT uid,MAX(lv_time1) FROM ".$GLOBALS['DBprefix']."LastVisit GROUP BY uid";
  $res = db_query($sql,$link);
  while ($lvdata=db_fetch_row($res)) {
    $lvstats[$lvdata[0]]=$lvdata[1];
  }
  db_free_result($res);

  $sql = "SELECT u.*,l_title FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."UserLevel ".
  "WHERE u__active=0 AND u_id>3 AND u__level=l_level ".
  "ORDER BY u__name";
  $res = db_query($sql,$link);
  user_lst_start(MSG_user_inactivelist);
  while ($udata=db_fetch_array($res)) {
    user_lst_entry($udata);
  }
  if (db_num_rows($res)==0) user_lst_noentries(MSG_user_noinactive);
  user_lst_end();
}

function u_delete() {
  check_post();
  global $link;
  $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"".getvar("uname")."\"";
  $res = db_query($sql,$link);
  $tmp = db_fetch_row($res);
  $uid = $tmp[0];
  db_free_result($res);

  delete_user($uid);

  ad_message(MSG_u_deleted,MSG_u_go,"admin/index.php?m=user&a=user_list&ltt=".substr(getvar("uname"),0,1));
}

function u_clear() {
  user_clear_form();
}

function u_clear_confirm() {
  global $link;

  $curtime=time();
  if ($lvdays=getvar("lvtime")) $lvtime=$curtime-$lvdays*60*60*24;
  $pcount=intval(getvar("pcount"));
  if ($lpdays=getvar("lptime")) $lptime=$curtime-$lpdays*60*60*24;

  if (!$lvtime && !$pcount && $pcount!="0" && !$lpdays) error(MSG_e_u_noparams);

  if (getvar('inactive')) $sqldata2=" AND u__active=0 ";
  else $sqldata2='';

  if ($lvtime) {
    $array1=array();
    $sql = "SELECT uid,MAX(lv_time1) AS lvtime FROM ".$GLOBALS['DBprefix']."LastVisit lv ".
    "WHERE uid>3 AND lv_time1<".$lvtime." ".
    "GROUP BY uid HAVING lvtime<".$lvtime;
    $res = db_query($sql,$link);
    while ($udata=db_fetch_row($res)) $array1[]=$udata[0];
    db_free_result($res);
  }

  if ($pcount || $pcount=="0") {
    $array2=array();
    $sql = "SELECT uid, SUM(us_count) AS pcount FROM ".$GLOBALS['DBprefix']."UserStat us ".
    "WHERE uid>3 ".
    "GROUP BY uid HAVING pcount<=".$pcount;
    $res = db_query($sql,$link);
    while ($udata=db_fetch_row($res)) $array2[]=$udata[0];
    db_free_result($res);
  }

  if ($lpdays) {
    $array3 = array();
    $sql = "SELECT p_uid, MAX(p__time) AS lastpost FROM ".$GLOBALS['DBprefix']."Post p ".
    "GROUP BY p_uid HAVING lastpost<=".$lptime;
    $res = db_query($sql,$link);
    while ($udata=db_fetch_row($res)) $array3[]=$udata[0];
    db_free_result($res);
  }
  
  $array_res=array();
  if (is_array($array1)) $array_res=&$array1;
  elseif (is_array($array2)) $array_res=&$array2;
  elseif (is_array($array3)) $array_res=&$array3;

  if (is_array($array_res)) {
    if (is_array($array2)) $array_res=array_intersect($array_res,$array2);
    if (is_array($array3)) $array_res=array_intersect($array_res,$array3);
  }

  $users=array();  
  if (count($array_res)>0) {
    $sql = "SELECT u_id,u__name FROM ".$GLOBALS['DBprefix']."User ".
    "WHERE u_id IN (".join(',',$array_res).") ".$sqldata2;
    $res = db_query($sql,$link);
    while ($udata=db_fetch_row($res)) {
      $users["uid[".$udata[0]."]"]=$udata[0];
      if ($userlist) $userlist.=", ";
      $userlist.=user_out($udata[1],$udata[0]);
    }
  }

  confirm("user","u_clear_process",$users,MSG_u_clearlist.":<br>".$userlist,"admin/index.php?m=user&a=u_clear");
}

function u_clear_process() {
  if (is_array($_POST['uid'])) foreach ($_POST['uid'] as $curuid) {
    delete_user(addslashes($curuid));
  }
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);
  ad_message(MSG_u_cleared,MSG_u_go,"admin/index.php?m=user&a=u_select");
}

function u_change() {
  global $link;

  $sa=getvar("sa");
  if ($sa==1) $sqldata="u__active=1";
  elseif ($sa==2) $sqldata="u__level=100";
  elseif ($sa==3) $sqldata="u__level=-1";
  $uid=getvar("uid");

  $sql="UPDATE ".$GLOBALS['DBprefix']."User SET $sqldata WHERE u_id=\"$uid\"";
  $res = db_query($sql,$link);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);
  ad_message(MSG_user_saved,MSG_user_backlist,$_SERVER['HTTP_REFERER']);
}

function uw_form() {
  uw_input_form();
}

function uw_list() {
  global $link;

  $user=getvar("uname");
  $user=str_replace(";",",",$user);
  $user=str_replace(", ",",",$user);
  if ($user) { $sqldata=" AND (u__name=\"".str_replace(",","\" OR u__name=\"",$user)."\")"; }
  $curtime=time();
  if (getvar("active")) {
    $sqldata.=" AND (uw_validtill=0 OR uw_validtill>$curtime) ";
  }

  $sql = "SELECT uw.*,u__name,u_id FROM ".$GLOBALS['DBprefix']."UserWarning uw, ".$GLOBALS['DBprefix']."User ".
  "WHERE uw_uid=u_id $sqldata ORDER BY uw_id DESC";
  $res = db_query($sql,$link);

  uw_list_start();
  while ($uwdata=db_fetch_array($res)) {
    uw_list_entry($uwdata);
  }
  uw_list_end();
  uw_input_form();
}

function uw_delete() {
  $link = $GLOBALS['link'];
  $uwid=getvar("uwid");

  $sql = "SELECT uw_uid FROM ".$GLOBALS['DBprefix']."UserWarning WHERE uw_id=\"$uwid\"";
  $res = db_query($sql,$link);
  list($uid)=db_fetch_row($res);
  db_free_result($res);

  $sql="DELETE FROM ".$GLOBALS['DBprefix']."UserWarning WHERE uw_id=\"$uwid\"";
  $res = db_query($sql,$link);
  $uname=getvar("uname");
  $active=getvar("active");

  $sql = "SELECT SUM(uw_value),MAX(uw_validtill) FROM ".$GLOBALS['DBprefix']."UserWarning WHERE uw_uid=".$uid." AND (uw_validtill=0 OR uw_validtill>".time().")";
  $res = db_query($sql,$link);
  list($uw_value,$uw_validtill)=db_fetch_row($res);
  db_free_result($res);
  if ($uw_validtill<$GLOBALS['curtime']) $uw_validtill="0";
  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__warnings=".intval($uw_value).", u__warntime=".intval($uw_validtill)." WHERE u_id=".$uid;
  $res = db_query($sql,$link);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);
  ad_message(MSG_uw_deleted,MSG_uw_list,"admin/index.php?m=user&a=uw_list&uname=$uname&active=$active");
}

function u_del_byname() {
  del_byname_form();
}

function u_del_process() {
  check_post();
  $link = $GLOBALS['link'];

  $uname= getvar("uname");
  $delmsg = getvar("delmsg");
  $delguest = getvar("delguest");

  $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"$uname\"";
  $res = db_query($sql,$link);
  list($uid)=db_fetch_row($res);

  if ($uid) delete_user($uid);
  if ($delmsg && $uid) {
    $sql = "SELECT p_attach FROM ".$GLOBALS['DBprefix']."Post WHERE p_uid=$uid AND p_attach<>0";
    $res = db_query($sql,$link);
    while ($attach=db_fetch_row($res)) {
      if ($sqldata) $sqldata.=" OR ";
      $sqldata.="file_id=".$attach[0];
    }
    if ($sqldata) {
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE $sqldata";
      $res = db_query($sql,$link);
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE p_uid=$uid";
    $res = db_query($sql,$link);
  }
  if ($delguest) {
    $sqldata="";
    $sql = "SELECT p_attach FROM ".$GLOBALS['DBprefix']."Post WHERE p_uname=\"$uname\" AND p_attach<>0";
    $res = db_query($sql,$link);
    while ($attach=db_fetch_row($res)) {
      if ($sqldata) $sqldata.=" OR ";
      $sqldata.="file_id=".$attach[0];
    }
    if ($sqldata) {
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE $sqldata";
      $res = db_query($sql,$link);
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE p_uname=\"$uname\"";
    $res = db_query($sql,$link);
  }
  if ($delmsg || $delguest) {
    $sql = "SELECT t_id,COUNT(p_id) AS pcount FROM ".$GLOBALS['DBprefix']."Topic ".
            "LEFT JOIN ".$GLOBALS['DBprefix']."Post ON (t_id=p_tid) ".
            "GROUP BY t_id HAVING pcount=0";
    $res = db_query($sql,$link);
    while ($tdata=db_fetch_row($res)) {
      delete_topic($tdata[0]);
    }
  }

  ad_message(MSG_u_deleted,MSG_u_go,"admin/index.php?m=user&a=user_list&ltt=".substr(getvar("uname"),0,1));
}

function u_create() {
  global $link;
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User WHERE u_id=3";
  $res = db_query($sql,$link);
  $udata = db_fetch_array($res);
  $newaction = "u_docreate";
  $newmodule = "user";
  $levels = build_userlevel_select();
  $langselect = build_select("SELECT * FROM ".$GLOBALS['DBprefix']."Language",$udata['u_lnid']);
  $styleselect = build_select("SELECT * FROM ".$GLOBALS['DBprefix']."StyleSet",$udata['u_stid']);
  load_style("profile.php");

  user_profile("u_docreate","user",$udata,$styleselect,$langselect,"","user");
}

function u_docreate() {
  check_post();
  global $link;
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User WHERE u_id=3";
  $res = db_query($sql,$link);
  $udata = db_fetch_array($res);

  if (!($email=getvar("u__email"))) error(MSG_e_u_emptymail);
  $newname = trim(getvar("u__name"));

  if (preg_match("/[\x80-\xFF]/",$newname)>0 && !$GLOBALS['opt_cyrillic']) error(MSG_e_u_cyrforbidden);
  if (strpos($newname,",")!==false || strpos($newname,";")!==false) error(MSG_e_u_badchars);
  if ($newname=="Guest" || $newname=="System" || $newname=="NewUser") error(MSG_e_u_reservedname);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"$newname\" OR u__email=\"".trim($email)."\"";
  $res = db_query($sql,$link);
  if (db_num_rows($res)>0) error(MSG_e_u_alreadyused);
  db_free_result($res);

  $password1 = getvar("password1");
  $password2 = getvar("password2");
  if ($password1!=$password2) error(MSG_e_u_passnotmatch);
  if (!$password1) error(MSG_e_u_emptypass);
  if (getvar('u_encrypted')) $password1=md5($password1);

  $udata['u__password']=$password1;

  foreach ($udata as $key=>$value) {
    if ($key!="u_id" && $key!="u__password" && isset($_POST[$key])) $udata[$key]=getvar($key);
  }

  foreach ($udata as $curparam=>$curvalue) {
    if (substr($curparam,0,2)=="u_" && $curparam!="u_id" && $curparam!="u__regdate" && $curparam!="u__profileupdate") {
        if ($sqldata) $sqldata.=", ";
        $sqldata .= "$curparam=\"".htmlspecialchars($udata[$curparam])."\"";
    }
  }

  $curtime = time();
  $sqldata.=", u__regdate=$curtime, u__profileupdate=$curtime";

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."User SET $sqldata";
  $res = db_query($sql,$link);

  ad_message(MSG_user_created,MSG_u_go,"admin/index.php?m=user&a=user_list&ltt=".substr(getvar("u__name"),0,1));
}

function canonize() {
  global $link;
  require('../canonize.php');
  $sql = "LOCK TABLES ".$GLOBALS['DBprefix']."User WRITE";
  $res =&db_query($sql,$link);
  $sql = "SELECT u__name,u_id FROM ".$GLOBALS['DBprefix']."User";
  $res =&db_query($sql,$link);
  while ($udata=db_fetch_row($res)) {
    $sql2 = "UPDATE ".$GLOBALS['DBprefix']."User SET u__canonical=\"".addslashes(canonize_name($udata[0]))."\" WHERE u_id=".$udata[1];
    $res2 =&db_query($sql2,$link);
  }
  db_free_result($res);
  $sql = "UNLOCK TABLES";
  $res =&db_query($sql,$link);
  ad_message(MSG_user_canonized,MSG_go_stats,"admin/index.php");
}
