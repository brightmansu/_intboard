<? /*

Bookmark script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function do_add() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_bmk_noguests);
  if ($GLOBALS['intopic']['bmk']) error(MSG_e_bmk_alreadyexists);
  global $link;

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Bookmark VALUES(".$GLOBALS['inuserid'].",".$GLOBALS['topic'].")";
  $res =&db_query($sql,$link);
  message(MSG_bmk_added,1);
}

function view() {
  if ($GLOBALS['inuserid']<=3) error(MSG_e_bmk_noguests);
  global $link;
  $sql = "SELECT t.*, f.*, p1.p_uname AS first_name, p1.p_uid AS first_uid, p1.p__time AS first_time, ".
         "p2.p_uname AS last_name, p2.p_uid AS last_uid, p2.p__time AS last_time ".
         "FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Bookmark bmk, "
         .$GLOBALS['DBprefix']."Post p1, ".$GLOBALS['DBprefix']."Post p2, "
         .$GLOBALS['DBprefix']."Forum f WHERE t.t_id=bmk.tid ".
         " AND f.f_id=t.t_fid AND bmk.uid=".$GLOBALS['inuserid'].
         " AND ".check_access('f_id')." AND p1.p_id=t.t__startpostid AND p2.p_id=t.t__lastpostid ORDER BY f.f_sortfield";
  $res =&db_query($sql,$link);
  bmk_list_start();
  $oldforum=0;
  if (db_num_rows($res)==0) bmk_no_entries();
  while ($bmkdata=&db_fetch_array($res)) {
    if ($bmkdata['f_id']!=$oldforum) {
      bmk_forum_entry($bmkdata);
      $oldforum=$bmkdata['f_id'];
    }
    $pages=&build_pages($bmkdata['t__pcount'],-1,$GLOBALS['inuser']['u_mperpage'],"index.php?t=".$bmkdata['t_id']);
    bmk_list_entry($bmkdata,$maxdata,$pages);
  }
  bmk_list_end();
}

function delbmk() {
  load_style('message.php');
  if ($GLOBALS['inuserid']<=3) error(MSG_e_bmk_noguests);
  global $link;
  if (is_array($_POST['delbmk'])) foreach ($_POST['delbmk'] as $curtopic=>$curvalue) {
    $counter++;
    if ($sqldata) $sqldata.=" OR ";
    $sqldata .= "tid=\"".intval($curtopic)."\"";
  }
  if ($sqldata) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Bookmark WHERE uid=".$GLOBALS['inuserid']." AND ($sqldata)";
    $res =&db_query($sql,$link);
  }
  message(MSG_bmk_deleted." ".$counter,1);
}

function locations($locations) {
  global $action;
  if ($action=="view") array_push($locations,MSG_bmk_list);
  return $locations;
}
