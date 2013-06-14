<? /*

Address book script for Intellect Board 2 Project

(C) 2007, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function process_ab_change($status) {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_ab_noguests);
  global $link;
  
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."AddrBook ".
  "WHERE u_owner=".$GLOBALS['inuserid']." AND u_partner=\"".getvar('uid')."\"";
  $res =&db_query($sql,$link);

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."AddrBook (u_owner,u_partner,u_status) ".
  "VALUES(".$GLOBALS['inuserid'].",\"".getvar('uid')."\",".$status.")";
  $res =&db_query($sql,$link);
}

function do_ignore() {
  process_ab_change(-1);
  message(MSG_ab_ignored,1);
}

function do_friend() {
  process_ab_change(1);  
  message(MSG_ab_friended,1);
}

function view() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_ab_noguests);
  global $link;

  addrbook_start();
  addrbook_friends_start();
  $sql = "SELECT u_id,u__name FROM ".$GLOBALS['DBprefix']."AddrBook ab, ".$GLOBALS['DBprefix']."User u ".
  "WHERE u_owner=".$GLOBALS['inuserid']." AND u.u_id=u_partner AND u_status=1 ".
  "ORDER BY u__name";
  $res = & db_query($sql,$link);
  while ($data=db_fetch_row($res)) {
    addrbook_friends_entry($data);
  }
  if (db_num_rows($res)==0) addrbook_friends_noentries();
  db_free_result($res);
  addrbook_friends_end();
  addrbook_separator();

  addrbook_enemies_start();
  $sql = "SELECT u_id,u__name FROM ".$GLOBALS['DBprefix']."AddrBook ab, ".$GLOBALS['DBprefix']."User u ".
  "WHERE u_owner=".$GLOBALS['inuserid']." AND u.u_id=u_partner AND u_status=-1 ".
  "ORDER BY u__name";
  $res = & db_query($sql,$link);
  while ($data=db_fetch_row($res)) {
    addrbook_enemies_entry($data);
  }
  if (db_num_rows($res)==0) addrbook_enemies_noentries();
  db_free_result($res);
  addrbook_enemies_end();

  addrbook_end();
}

function do_view() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_ab_noguests);
  global $link;

  $delete_array =array();
  if (is_array($_POST['delete'])) foreach ($_POST['delete'] as $curid=>$value) {
    $delete_array[]=intval($curid);
  }

  $new_f_array=array();
  $friends_id=array();
  if ($_POST['new_friends']) {
    $f_array=explode(',',$_POST['new_friends']);
    if (is_array($f_array)) for ($i=0, $count=count($f_array); $i<$count; $i++) {
      $new_f_array[]='"'.db_slashes(trim($f_array[$i])).'"';
    }
    if ($count>0) {
      $sql = 'SELECT u_id FROM '.$GLOBALS['DBprefix'].'User WHERE u__name IN ('.join(',',$new_f_array).')';
      $res = db_query($sql,$link);
      while ($tmp=db_fetch_row($res)) $friends_id[]=$tmp[0];
      db_free_result($res);
    }
  }

  $new_e_array=array();
  $enemies_id=array();
  if ($_POST['new_enemies']) {
    $e_array=explode(',',$_POST['new_enemies']);
    if (is_array($e_array)) for ($i=0, $count=count($e_array); $i<$count; $i++) {
      $new_e_array[]='"'.db_slashes(trim($e_array[$i])).'"';
    }
    if ($count>0) {
      $sql = 'SELECT u_id FROM '.$GLOBALS['DBprefix'].'User WHERE u__name IN ('.join(',',$new_e_array).')';
      $res = db_query($sql,$link);
      while ($tmp=db_fetch_row($res)) $enemies_id[]=$tmp[0];
      db_free_result($res);
    }
  }

  $all_array=array_merge($delete_array,$friends_id,$enemies_id);
  if (is_array($all_array) && count($all_array)) {
    $sql = 'DELETE FROM '.$GLOBALS['DBprefix'].'AddrBook '.
    " WHERE u_owner=".$GLOBALS['inuserid']." AND u_partner IN (".join(',',$all_array).") ";
    $res = db_query($sql,$link);

    $sqldata=array();
    for ($i=0, $count=count($friends_id); $i<$count; $i++) {
      $sqldata[]='('.$GLOBALS['inuserid'].','.$friends_id[$i].',1)';
    }
    for ($i=0, $count=count($enemies_id); $i<$count; $i++) {
      $sqldata[]='('.$GLOBALS['inuserid'].','.$enemies_id[$i].',-1)';
    }
    if (count($sqldata)>0) {
      $sql = 'INSERT INTO '.$GLOBALS['DBprefix'].'AddrBook (u_owner,u_partner,u_status) VALUES '.
      join(',',$sqldata);
      $res = db_query($sql,$link);
    }
  }
  message(MSG_ab_saved,1);
}

function locations($locations) {
  array_push($locations,MSG_ab_list);
  return $locations;
}
