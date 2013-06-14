<? /*

Subsribe script for Intellect Board 2 Project
 
(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function do_subscr() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_sub_noguest);
  if ($GLOBALS['intopic']['subscr']) error(MSG_e_sub_already);
  global $link;
  
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription VALUES(\"".$GLOBALS['inuserid']."\",\"".$GLOBALS['topic'].
         "\",\"".$GLOBALS['forum']."\")";
  $res =&db_query($sql,$link);
  message(MSG_sub_subscribed,1);
}

function do_unsubscr() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_sub_noguest);
  if (!$GLOBALS['intopic']['subscr']) error(MSG_e_sub_none);
  global $link;
  
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=".$GLOBALS['inuserid']." AND tid=".$GLOBALS['topic']."";
  $res =&db_query($sql,$link);
  message(MSG_sub_unsubscribed,1);	
}

function view() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_sub_noguest);
  global $link;
  $forumsel = build_forum_select('f_lread');
  subscr_forum_select($forumsel);
  subscr_subfunctions();  
}

function list_subscr() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_sub_noguest);
  global $link;
  $inuserid=$GLOBALS['inuserid'];
  $forum=$GLOBALS['forum'];
  
	$sql = "SELECT t.t_id, t.t_title, t_link, ".$forum." AS f_id, \"".addslashes($GLOBALS['inforum']['f_link'])."\" f_link, COUNT(sub.uid) AS subscr FROM ".$GLOBALS['DBprefix']."Topic t ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Subscription sub ON (sub.uid=$inuserid AND t.t_id=sub.tid) ".
  "WHERE t.t_fid=$forum GROUP BY t.t_id, t.t_title";
  $res =&db_query($sql,$link);
  
  subscr_list_start();
  while ($tdata=&db_fetch_array($res)) {
	  subscr_list_entry($tdata);
  }
  db_free_result($res);
  $sql = "SELECT tid FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=$inuserid AND fid=$forum AND tid>=4294967294";
  $res =&db_query($sql,$link);
  while ($tmp=db_fetch_row($res)) {
	  if (floatval($tmp[0])==4294967294) $inform=1;
	  if (floatval($tmp[0])==4294967295) $autosub=1;
  }
  db_free_result($res);
  subscr_list_end($inform,$autosub);
}

function do_process() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_sub_noguest);
  global $link;
  $inuserid = $GLOBALS['inuserid'];
  $forum = $GLOBALS['forum'];

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=$inuserid AND fid=$forum";
  $res =&db_query($sql,$link);
  
  if (is_array($_POST['subscr'])) foreach ($_POST['subscr'] as $curtopic=>$curvalue) if (is_numeric($curtopic)) {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription (uid,tid,fid) VALUES ($inuserid,".intval($curtopic).",$forum)";
    $res=&db_query($sql,$link);
  }
  message(MSG_sub_saved,1);
}

function do_process_all() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_sub_noguest);
  global $link;
  
  $subact = getvar('sa');
  if ($subact=='unsub_all') {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=".$GLOBALS['inuserid'];
    $res=&db_query($sql,$link);    
  }
  elseif ($subact=='unsub_notify') {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=".$GLOBALS['inuserid'].' AND tid=4294967294';
    $res=&db_query($sql,$link);    
  }
  elseif ($subact=='unsub_auto') {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=".$GLOBALS['inuserid'].' AND tid=4294967295';
    $res=&db_query($sql,$link);    
  }
  elseif ($subact=='unsub_topics') {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=".$GLOBALS['inuserid'].' AND tid<4294967294';
    $res=&db_query($sql,$link);    
  }
  if ($subact=='sub_notify' || $subact=='sub_all' || $subact=='sub_auto' || $subact=='sub_topics') {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=".$GLOBALS['inuserid'];
    $res=&db_query($sql,$link);    
  }
  if ($subact=='sub_notify') {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription (uid,fid,tid) SELECT ".$GLOBALS['inuserid'].",f_id,4294967294 FROM ".$GLOBALS['DBprefix']."Forum WHERE ".check_access('f_id',true);
    $res=&db_query($sql,$link);    
  }
  if ($subact=='sub_auto' || $subact=='sub_all') {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription (uid,fid,tid) SELECT ".$GLOBALS['inuserid'].",f_id,4294967295 FROM ".$GLOBALS['DBprefix']."Forum WHERE ".check_access('f_id',true);
    $res=&db_query($sql,$link);
  }
  if ($subact=='sub_topics' || $subact=='sub_all') {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription (uid,fid,tid) SELECT ".$GLOBALS['inuserid'].",t_fid,t_id FROM ".$GLOBALS['DBprefix']."Topic WHERE ".check_access('t_fid',true);
    $res=&db_query($sql,$link);    
  }
  message(MSG_sub_saved,1);  
}

function locations($locations) {
	if ($GLOBALS['action']=="view") array_push($locations,MSG_sub_params);
	elseif ($GLOBALS['action']=="list_subscr") {
		array_push($locations,"<a href=\"index.php?f=".$GLOBALS['forum']."\">".$GLOBALS['inforum']['f_title']."</a>");
		array_push($locations,MSG_sub_params);
  }	
  return $locations;
}
