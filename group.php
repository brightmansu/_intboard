<? /*

User Groups script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function view() {
  global $link;
  if ($GLOBALS['inuserid']<=3) error(MSG_e_g_noguests);
  $sql_c = "SELECT g.g_id, COUNT(gm.uid) AS gm_count FROM ".$GLOBALS['DBprefix']."UGroup g ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UGroupMember gm ON (gm.gid=g.g_id) GROUP BY g.g_id,g.g_title,g.g_setlevel,g.g_ljoin,g.g_lview,g.g_lautojoin,g.g_descr,g.g_allowquit";
  $res_c = db_query($sql_c,$link);
  while ($gdata_c=db_fetch_array($res_c)) {
    $gcount[$gdata_c['g_id']] = $gdata_c['gm_count'];
  }
  db_free_result($res_c);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UGroup g ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UGroupMember gm ON (gm.gid=g.g_id AND gm.uid=".$GLOBALS['inuserid'].") ".
  "WHERE g.g_lview<=".$GLOBALS['inuserbasic']." ORDER BY gm.gm_status DESC";
  $res =&db_query($sql,$link);
  group_list_start();
  while ($gdata=&db_fetch_array($res)) {
      $gdata['gm_count']=$gcount[$gdata['g_id']];
      group_list_entry($gdata);
  }
  if (db_num_rows($res)==0) group_list_noentries();
  group_list_end();
}

function sendjoin() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_g_noguests);
  $link = $GLOBALS['link'];
  $gid =&getvar("g");
  $sql = "SELECT g_id,g_ljoin,g_title FROM ".$GLOBALS['DBprefix']."UGroup WHERE g_id=\"$gid\" AND g_ljoin<=".$GLOBALS['inuserbasic'];
  $res =&db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_g_norights);
  $gdata =&db_fetch_array($res);
  db_free_result($res);
  group_join($gdata);
}

function do_sendjoin() {
  global $link;
  if ($GLOBALS['inuserid']<=3) error(MSG_e_g_noguests);
  $gid=&getvar("g");
  $sql = "SELECT g_ljoin,g_title FROM ".$GLOBALS['DBprefix']."UGroup g WHERE g_id=\"$gid\" AND g_ljoin<=".$GLOBALS['inuserbasic'];
  $res =&db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_g_norights);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UGroupMember VALUES(\"$gid\",".$GLOBALS['inuserid'].",0)";
  $res =&db_query($sql,$link);
  $subj = MSG_g_message." ".$tmp[1];
  $text = MSG_g_message." ".$tmp[1]."\n".getvar("text")."\n".MSG_g_adduser.": ".$GLOBALS['opt_url']."/index.php?m=group&a=do_add&g=$gid&u=".$GLOBALS['inuserid'];
  $sql = "SELECT uid FROM ".$GLOBALS['DBprefix']."UGroupMember WHERE gid=\"$gid\" AND gm_status=2";
  $res =&db_query($sql,$link);
  while ($mod_uid=db_fetch_row($res)) {
    send_pm($mod_uid[0],$GLOBALS['inuserid'],$text,$subj,"");
  }
  $GLOBALS['refpage']="index.php?m=group";
  message(MSG_g_message_send,1);
}

function do_autojoin() {
  global $link;
  if ($GLOBALS['inuserid']<=3) error(MSG_e_g_noguests);
  $gid=&getvar("g");
  $sql = "SELECT g_lautojoin,g_title,g_setlevel FROM ".$GLOBALS['DBprefix']."UGroup WHERE g_id=\"$gid\"";
  $res =&db_query($sql,$link);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  if ($tmp[0]>$GLOBALS['inuserbasic']) error(MSG_e_g_norights);
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UGroupMember VALUES(\"$gid\",".$GLOBALS['inuserid'].",1)";
  $res =&db_query($sql,$link);
  update_level($gid,$uid,$tmp[2]);
  $GLOBALS['refpage']="index.php?m=group";
  message(MSG_g_joined,1);
}

function show() {
  global $link;
  $gid=&getvar("g");
  if ($GLOBALS['inuserid']<=3) error(MSG_e_g_noguests);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UGroup g ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UGroupMember gm ON (g_id=gid AND uid=".$GLOBALS['inuserid'].") ".
  "WHERE g_lview<=".$GLOBALS['inuserbasic']." AND g_id=\"$gid\"";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_g_norights);
  $gdata=&db_fetch_array($res);
  db_free_result($res);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UGroupMember gm, ".$GLOBALS['DBprefix']."User u WHERE gm.uid=u.u_id AND gm.gid=\"$gid\"";
  $res =&db_query($sql,$link);
  group_show_start($gdata);
  while ($udata=&db_fetch_array($res)) {
    group_show_entry($udata,$gdata['gm_status']);
  }
  group_show_end($gdata['gm_status'],$gdata['g_id']);
}

function do_quit() {
  global $link;
  $gid=&getvar("g");
  if ($GLOBALS['inuserid']<=3) error(MSG_e_g_noguests);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UGroup g, ".$GLOBALS['DBprefix']."UGroupMember gm WHERE gm.gid=\"$gid\" AND gm.gid=g_id AND gm.uid=".
         $GLOBALS['inuserid']." AND gm_status>0 AND g.g_allowquit=1";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)!=1) error(MSG_e_g_norights);
  db_free_result($res);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupMember WHERE uid=".$GLOBALS['inuserid']." AND gid=\"$gid\"";
  $res =&db_query($sql,$link);
  $GLOBALS['refpage']="index.php?m=group";
  message(MSG_g_quited,1);
}

function is_group_moder($gid) {
  global $link;
  if ($GLOBALS['inuserid']<=3) error(MSG_e_g_noguests);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UGroup g, ".$GLOBALS['DBprefix']."UGroupMember gm WHERE gm.gid=\"$gid\" AND gm.uid=".
         $GLOBALS['inuserid']." AND gm_status=2 AND gm.gid=g.g_id";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)!=1) error(MSG_e_g_norights);
  $GLOBALS['gdata']=&db_fetch_array($res);
  db_free_result($res);
}

function update_level($gid,$uid,$level) {
  global $link;
  if ($level) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__level=".$GLOBALS['gdata']['g_setlevel']." WHERE u_id=$uid AND u__level<=".$level;
    $res =&db_query($sql,$link);
  }
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UGroupAccess WHERE gid=\"$gid\"";
  $res =&db_query($sql,$link);
  while ($gldata=&db_fetch_array($res)) {
    $sql2="DELETE FROM ".$GLOBALS['DBprefix']."UserAccess WHERE uid=$uid AND fid=".$gldata['fid'];
    $res2 =&db_query($sql2,$link);
    $sql2="INSERT INTO ".$GLOBALS['DBprefix']."UserAccess SET uid=$uid, fid=".$gldata['fid'].", ua_level=".$gldata['ga_level'];
    $res2 =&db_query($sql2,$link);
  }
  db_free_result($res);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res =&db_query($sql,$link);
}

function do_add() {
  $gid=&getvar("g");
  global $link;
  is_group_moder($gid);

  $uid =&getvar("u");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."UGroupMember SET gm_status=1 WHERE gid=\"$gid\" AND uid=\"$uid\" AND gm_status=0";
  $res =&db_query($sql,$link);
  $GLOBALS['refpage']="index.php?m=group&a=show&g=$gid";
  update_level($gid,$uid,$GLOBALS['gdata']['g_setlevel']);
  message(MSG_g_added,1);
}

function do_delete() {
  global $link;
  $gid=&getvar("g");
  is_group_moder($gid);

  $uid =&getvar("u");
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupMember WHERE gid=\"$gid\" AND uid=\"$uid\" AND gm_status<2";
  $res =&db_query($sql,$link);
  $GLOBALS['refpage']="index.php?m=group&a=show&g=$gid";
  message(MSG_g_deleted,1);
}

function do_forceadd() {
  global $link;
  $gid=&getvar("g");
  is_group_moder($gid);

  $uname =&getvar("u__name");
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User u ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UGroupMember gm ON (gm.uid=u.u_id AND gm.gid=\"$gid\") WHERE u__name=\"$uname\"";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_u_nosuchuser);
  $udata =&db_fetch_array($res);
  db_free_result($res);
  if ($udata['u_count']>0) error(MSG_e_g_alreadyadded);
  $uid=$udata['u_id'];
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UGroupMember VALUES (\"$gid\",$uid,1)";
  $res =&db_query($sql,$link);
  $GLOBALS['refpage']="index.php?m=group&a=show&g=$gid";
  update_level($gid,$uid,$GLOBALS['gdata']['g_setlevel']);
  message(MSG_g_added,1);
}

function mailsend() {
  global $link;
  $gid=&getvar("g");
  is_group_moder($gid);

  group_send_form();
}

function do_mailsend() {
  $link = $GLOBALS['link'];
  $gid =&getvar("g");
  is_group_moder($gid);

  $subj =$_POST["subj"];
  $GLOBALS['text']=$_POST["text"];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."UGroupMember gm, ".$GLOBALS['DBprefix']."UGroup g ".
  " WHERE gm.gid=g.g_id AND gm.uid=u.u_id AND g.g_id=\"$gid\" AND gm.gm_status>0 AND u.u_nomails=0";
  $res =&db_query($sql,$link);
  while ($udata=&db_fetch_array($res)) {
    $GLOBALS['username']=$udata['u__name'];
    $GLOBALS['group']=$udata['g_title'];
    $buffer=load_mail("group.txt");
    replace_mail($buffer,$udata['u__email'],"Рассылка по группе ".$GLOBALS['group']);
  }
  $GLOBALS['refpage']="index.php?m=group&a=show&g=$gid";
  message(MSG_g_sent,1);
}

function locations($locations) {
  if ($GLOBALS['action']=="view") array_push($locations,MSG_g_groups);
  elseif ($GLOBALS['action']=="show") {
    array_push($locations,"<a href=\"index.php?m=group\">".MSG_groups."</a>");
    array_push($locations,MSG_g_view);
  }
  return $locations;
}
