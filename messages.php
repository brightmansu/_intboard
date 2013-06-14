<? /*

Personal messages script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function view() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  global $link;

  pm_head();

  $sql = "SELECT u.u_id, u__name, pm_subj, COUNT(*) AS pm_count, MIN(pm__senddate) AS pm_start, ".
  "MAX(pm__senddate) AS pm_last, MIN(pm_id) AS pm_id, COUNT(*)-COUNT(NULLIF(pm__readdate+pm__box,0)) AS pm_unread ".
  "FROM ".$GLOBALS['DBprefix']."PersonalMessage pm ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."User u ON (pm.pm__correspondent=u.u_id) ".
  "WHERE pm__owner=".$GLOBALS['inuserid']." AND pm__box!=2 ".
  "GROUP BY u.u_id, u__name, pm_subj ".
  "ORDER BY pm_last DESC";
  $res=db_query($sql,$link);
  pm_newlist_start();
  while ($data=db_fetch_array($res)) {
    if (!$data['pm_subj']) $data['pm_subj']='&lt;'.MSG_pm_nosubject.'&gt;';    
    pm_newlist_entry($data);
  }
  db_free_result($res);
  pm_newlist_end();
}

function view_msg_list() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  global $link;
  pm_head();

  $startid = getvar('pm_id');
  $sql = "SELECT pm__correspondent, pm_subj FROM ".$GLOBALS['DBprefix']."PersonalMessage pm ".
  "WHERE pm_id=\"$startid\"";
  $res=db_query($sql,$link);
  list($cor,$subj)=db_fetch_row($res);
  db_free_result($res);

  $sql = "SELECT pm.*, u_id, u__name, u_status ".
  "FROM ".$GLOBALS['DBprefix']."PersonalMessage pm ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."User u ON (pm.pm__correspondent=u.u_id) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."AddrBook ab ON (pm.pm__correspondent=ab.u_partner AND ab.u_owner=".$GLOBALS['inuserid'].") ".
  "WHERE pm__owner=".$GLOBALS['inuserid']." AND pm__correspondent=".$cor." AND ".
  "pm_subj=\"".addslashes($subj)."\"";
  if ($GLOBALS['inuser']['u_sortposts']==1) $sql.=" ORDER BY pm__senddate DESC";
  else $sql.=" ORDER BY pm__senddate";
  $res=db_query($sql,$link);
  pm_msglist_start($subj);
  while ($data=db_fetch_array($res)) {
    if (!$data['pm_subj']) $data['pm_subj']='&lt;'.MSG_pm_nosubject.'&gt;';
    pm_msglist_entry($data);
    if ($data['u_id']!=$GLOBALS['inuserid']) $name=$data['u__name'];
  }
  db_free_result($res);
  pm_msglist_end();

  $curtime=$GLOBALS['curtime'];
  $sql = "UPDATE ".$GLOBALS['DBprefix']."PersonalMessage SET pm__readdate=$curtime WHERE pm__owner=".$GLOBALS['inuserid']." AND pm__correspondent=".$cor." AND ".
  "pm_subj=\"".addslashes($subj)."\" AND pm__box=0 AND pm__readdate=0";
  $res=db_query($sql,$link);
  $changed = db_affected_rows($res);
  if ($changed>0) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__pmcount=u__pmcount-".$changed." WHERE u_id=".$GLOBALS['inuserid'];
    $res=db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
    $res=db_query($sql,$link);
  }
  $sql = "UPDATE ".$GLOBALS['DBprefix']."PersonalMessage SET pm__readdate=$curtime WHERE pm__correspondent=".$GLOBALS['inuserid']." AND pm__correspondent=".$cor." AND ".
  "pm_subj=\"".addslashes($subj)."\" AND pm__box=1 AND pm__readdate=0";
  $res=db_query($sql,$link);

  $pmdata['pm_bcode']=1;
  $pmdata['pm_smiles']=$GLOBALS['inuser']['u_usesmiles'];
  $pmdata['pm_subj']=$subj;
  $pmdata['u__name']=$name;
  pm_edit($pmdata);
}

function viewbox() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  $link = $GLOBALS['link'];
  $box = getvar("box");

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."PersonalMessage pm
  LEFT JOIN ".$GLOBALS['DBprefix']."User u ON (pm.pm__correspondent=u.u_id) WHERE pm__owner=\"".
         $GLOBALS['inuserid']."\" AND pm__box=\"$box\" ORDER BY pm__senddate DESC";
  $res = db_query($sql,$link);
  pm_head();
  pm_list_start();
  if (db_num_rows($res)==0) pm_list_noentries();
  while ($pmdata=db_fetch_array($res)) {
    if (!$pmdata['pm_subj']) $pmdata['pm_subj']='&lt;'.MSG_pm_nosubject.'&gt;';    
    pm_list_entry($pmdata);
  }
  pm_list_end();
}

function viewmsg() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  $link = $GLOBALS['link'];
  $msg=getvar("msg");

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."PersonalMessage pm ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."User u ON (pm.pm__correspondent=u.u_id) WHERE pm__owner=\"".$GLOBALS['inuserid']."\" AND pm_id=\"$msg\"";
  $res = db_query($sql,$link);
  $pmdata = db_fetch_array($res);
  if ($pmdata['pm__box']==0 && !$pmdata['pm__readdate']) {
    $curtime = $GLOBALS['curtime'];
    $sql = "UPDATE ".$GLOBALS['DBprefix']."PersonalMessage SET pm__readdate=$curtime WHERE (pm_id=".$pmdata['pm_id'];
    if ($pmdata['pm_pair']) $sql.=" OR ".$pmdata['']."pm_id=".$pmdata['pm_pair'];
    $sql.=")";
    $res = db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__pmcount=u__pmcount-1 WHERE u_id=".$GLOBALS['inuserid'];
    $res = db_query($sql,$link);
    remove_cached_user($GLOBALS['inuserid']);
  }
  pm_head();
  pm_message($pmdata);
}

function reply() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  $link = $GLOBALS['link'];
  $msg=getvar("msg");
  $reply=getvar("reply");
  if ($msg) {
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."PersonalMessage pm, ".$GLOBALS['DBprefix']."User u WHERE pm__owner=\"".
         $GLOBALS['inuserid']."\" AND pm_id=\"$msg\" AND pm.pm__correspondent=u.u_id ";
    $res = db_query($sql,$link);
    $pmdata = db_fetch_array($res);
  }
  elseif ($reply) {
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."PersonalMessage pm, ".$GLOBALS['DBprefix']."User u WHERE pm__owner=\"".
         $GLOBALS['inuserid']."\" AND pm_id=\"$reply\" AND pm.pm__correspondent=u.u_id ";
    $res = db_query($sql,$link);
    $pmdata = db_fetch_array($res);
  }
  pm_head();
  pm_edit($pmdata);

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."PersonalMessage pm ".
          "WHERE pm__owner=".$GLOBALS['inuserid'];
  $res=db_query($sql,$link);
  $tmp=db_fetch_row($res);
  db_free_result($res);
  if ($tmp>300) {
  error('Превышен лимит сообщений в ящике! Чтобы отправить новое сообщение, удалите часть отправленных или полученных вами! (Лимит 300 сообщений)');
  }
}

function newmsg() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);

  pm_head();
  $pmdata=$GLOBALS['inuser'];
  $pmdata['pm_bcode']=1;
  $pmdata['pm_smiles']=$GLOBALS['inuser']['u_usesmiles'];
  unset($pmdata['u__name']);
  $uid=getvar('u');
  if ($uid) {
    global $link;
    $sql = "SELECT u__name FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
    $res = db_query($sql,$link);
    $tmp=db_fetch_row($res);
    $pmdata['u__name']=$tmp[0];
  }
  pm_edit($pmdata);
  
  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."PersonalMessage pm ".
          "WHERE pm__owner=".$GLOBALS['inuserid'];
  $res=db_query($sql,$link);
  $tmp=db_fetch_row($res);
  db_free_result($res);
  if ($tmp>300) {
  error('Превышен лимит сообщений в ящике! Чтобы отправить новое сообщение, удалите часть отправленных или полученных вами! (Лимит 300 сообщений)');
  }
}

function do_send() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  if ($GLOBALS['inuserbasic']<0) error(MSG_e_pm_banned); 
  $link = $GLOBALS['link'];
  $_POST['pm_text']=&$_POST['p_text'];

  $uname=getvar("u__name");
  if (!$uname) $uname=getvar('name2');
  $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"".$uname."\" AND u_id>3";
  $res = db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_u_nosuchuser);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  $uid=$tmp[0];
  
  $sql = "SELECT u_status FROM ".$GLOBALS['DBprefix']."AddrBook WHERE u_owner=".$uid." AND u_partner=".$GLOBALS['inuserid'];
  $res = db_query($sql,$link);
  if (db_num_rows($res)==0) $status=0;
  else list($status)=db_fetch_row($res);
  db_free_result($res);
  if ($status==-1) error(MSG_e_pm_ignored);

  if (!getvar("pm_text") || !getvar("pm_subj")) error(MSG_e_pm_empty);
  $curtime = $GLOBALS['curtime'];

  $subj = getvar("pm_subj");
  unset($_POST['pm_subj']);
  $text = getvar("pm_text");
  $oldtext = $_POST['pm_text'];
  unset($_POST['pm_text']);
  $sqldata = build_sql("pm_");
  if ($sqldata) $sqldata2=', '.$sqldata;
  if (getvar("drafts")) $box=2;
  else $box=1;
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."PersonalMessage SET pm__box=$box, pm__owner=".$GLOBALS['inuserid'].", pm__senddate=$curtime, pm__readdate=0, pm__correspondent=$uid, pm_text=\"$text\", pm_subj=\"$subj\" ";
  $res = db_query($sql.$sqldata2,$link);
  $pair = db_insert_id($res);

  if (!getvar("drafts")) {
    $_POST['pm_text']=$oldtext;
    $sqldata=" pm_pair=$pair".$sqldata2;
    $newpair = send_pm($uid,$GLOBALS['inuserid'],$text,$subj,$sqldata);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."PersonalMessage SET pm_pair=".$newpair." WHERE pm_id=$pair";
    $res = db_query($sql,$link);
    $result = MSG_pm_sended;
  }
  else $result = MSG_pm_draft;
  $GLOBALS['refpage']=$GLOBALS['opt_url'].'/index.php?m=messages&a=view_msg_list&pm_id='.$pair;

  message($result,1);
  
  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."PersonalMessage pm ".
          "WHERE pm__owner=".$GLOBALS['inuserid'];
  $res=db_query($sql,$link);
  $tmp=db_fetch_row($res);
  db_free_result($res);
  if ($tmp>300) {
  error('Превышен лимит сообщений в ящике! Чтобы отправить новое сообщение, удалите часть отправленных или полученных вами! (Лимит 300 сообщений)');
  }
}

function do_move() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  $link = $GLOBALS['link'];

  $msg = getvar("msg");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."PersonalMessage SET pm__box=3 WHERE pm_id=\"$msg\" AND pm__owner=".$GLOBALS['inuserid'];
  $res = db_query($sql,$link);

  message(MSG_pm_moved,1);
}

function do_delete() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  $link = $GLOBALS['link'];

  $msg = getvar("msg");
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."PersonalMessage WHERE pm_id=\"$msg\" AND pm__owner=".$GLOBALS['inuserid'];
  $res = db_query($sql,$link);

  $GLOBALS['refpage']="index.php?m=messages&a=view";
  message(MSG_pm_deleted,1);
}

function do_delall() {
  if ($GLOBALS['inuserid']<=3)  error(MSG_e_pm_noguests);
  $link = $GLOBALS['link'];

  $delete = $_POST['delete'];
  foreach ($delete as $curid=>$curvalue) {
    if ($sqldata) $sqldata.=" OR ";
    $sqldata .= "pm_id=\"".db_slashes($curid)."\"";
  }
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."PersonalMessage WHERE pm__owner=".$GLOBALS['inuserid']." AND (".$sqldata.")";
  $res = db_query($sql,$link);

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."PersonalMessage WHERE pm__owner=".$GLOBALS['inuserid']." AND pm__box=0 AND pm__readdate=0";
  $res = db_query($sql,$link);
  list($unread)=db_fetch_row($res);
  db_free_result($res);

  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__pmcount=".intval($unread)." WHERE u_id=".$GLOBALS['inuserid'];
  $res=db_query($sql,$link);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);

  $GLOBALS['refpage']="index.php?m=messages&a=view";
  message(MSG_pm_deleted_all,1);
}

function msg_friend_list() {
  global $link;
  
  $sql = 'SELECT u__name FROM '.$GLOBALS['DBprefix'].'User u, '.$GLOBALS['DBprefix'].'AddrBook ab '.
  'WHERE u.u_id=ab.u_partner AND u_owner='.$GLOBALS['inuserid'].' ORDER BY u__name';
  $buffer='<select name="name2" onChange="this.form.elements[\'u__name\'].value=this.value"><option value="">'.MSG_ab_choose_friend;
  $res = db_query($sql,$link);
  while ($tmp=db_fetch_row($res)) {
    $buffer.='<option value="'.htmlspecialchars($tmp[0]).'">'.htmlspecialchars($tmp[0]).'</option>';
  }
  $buffer.='</select>';
  return $buffer;
}

function locations($locations) {
  if ($GLOBALS['action']=="view") array_push($locations,MSG_pm);
  else {
    array_push($locations,"<a href=\"index.php?m=messages\">".MSG_pm."</a>");
    if ($GLOBALS['action']=="reply") array_push($locations,MSG_pm_reply);
    if ($GLOBALS['action']=="viewmsg") array_push($locations,MSG_pm_view);
    if ($GLOBALS['action']=="viewbox") {
      if (getvar("box")==0) { array_push($locations,MSG_pm_inbox); }
      elseif (getvar("box")==1) { array_push($locations,MSG_pm_outbox); }
      elseif (getvar("box")==2) { array_push($locations,MSG_pm_drafts); }
      elseif (getvar("box")==3) { array_push($locations,MSG_pm_archive); }
    }
  }
  return $locations;
}
