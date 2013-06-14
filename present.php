<? /*

Presentations script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

require_once("main.php");

function present_view() {
  $link = $GLOBALS['link'];
  present_text($GLOBALS['inforum']);
  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Forum f ".
//  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (uid=".$GLOBALS['inuserid']." AND fid=f_id) ".
  "WHERE f_parent=".$GLOBALS['forum']." AND ".check_access('f_id');
  $res =&db_query($sql,$link);
  list($count)=db_fetch_row($res);
  if ($count) view();
}

function present_locations($locations) {
  return locations($locations);
}

function present_edit() {
  present_edit_form($GLOBALS['inforum']);
}

function do_present_edit() {
  check_post();
  global $link;

  $text=db_slashes($_POST["p_text"]);
  $url =&getvar("f_url");
  if (getvar("update")) $time=", f_update=".$GLOBALS['curtime'];

  $sql="UPDATE ".$GLOBALS['DBprefix']."Forum SET f_text=\"$text\", f_url=\"$url\" $time WHERE f_id=".$GLOBALS['forum'];
  $res =&db_query($sql,$link);

  $GLOBALS['refpage']="index.php?f=".$GLOBALS['forum'];
  message(MSG_pr_saved,1);
}

function do_get() {
  $link= $GLOBALS['link'];
  $fid = $GLOBALS['forum'];
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_downloads=f_downloads+1 WHERE f_id=$fid";
  $res =&db_query($sql,$link);
  $sql = "SELECT f_url FROM ".$GLOBALS['DBprefix']."Forum WHERE f_id=$fid";
  $res =&db_query($sql,$link);
  list($url)=db_fetch_row($res);
  header("Location: $url");
  exit();
}
