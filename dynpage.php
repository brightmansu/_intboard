<? /*

Dynpageations script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

require_once("main.php");

function dynpage_view() {
  $link = $GLOBALS['link'];
  if (strpos($GLOBALS['inforum']['f_url'],'http://')!==false) $GLOBALS['inforum']['f_url']='';
    
  dynpage_text($GLOBALS['inforum']);
  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Forum f ".
//  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (uid=".$GLOBALS['inuserid']." AND fid=f_id) ".
  "WHERE f_parent=".$GLOBALS['forum']." AND ".check_access('f_id');
  $res =&db_query($sql,$link);
  list($count)=db_fetch_row($res);
  if ($count) view();
}

function dynpage_locations($locations) {
  return locations($locations);
}
