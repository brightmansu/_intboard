<? /*

File list script for Intellect Board 2 Project

(C) 2004-2006, XXXX Pro, United Open Project (http://www.openproj.ru)
Visit us online: http://intboard.ru

*/

function view() {
  global $link;
  $sort =&getvar('o');
  $desc=&getvar('desc');
  $sql = "SELECT COUNT(*) ".
  "FROM ".$GLOBALS['DBprefix']."Post p, ".
  $GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum".
  " LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=".$GLOBALS['inuserid']." AND fid=f_id) ".
  " WHERE p_attach<>0 AND p_tid=t_id AND f_id=t_fid".
  " AND COALESCE(ua_level,".$GLOBALS['inuserbasic'].")>=f_lread ";
  $res=&db_query($sql,$link);
  list($count)=db_fetch_row($res);
  db_free_result($res);

  $perpage =&getvar('perpage');
  if (!$perpage) $perpage=$GLOBALS['inuser']['u_mperpage'];
  $start=&getvar('st');
  if ($start!="all") $limit = " LIMIT ".intval($start).",".intval($perpage);

  $pages=&build_pages($count,$start,$perpage,"index.php?m=filelist&perpage=$perpage&o=$sort&desc=$desc");

  if (!$sort) $sort='p__time DESC';

  $sql = "SELECT fl.*, p.*, t_id, t_title, t_link, f_id, f_title, f_link ".
  "FROM ".$GLOBALS['DBprefix']."File fl, ".$GLOBALS['DBprefix']."Post p, ".
  $GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=".$GLOBALS['inuserid']." AND fid=f_id) ".
  " WHERE p_attach!=0 AND file_id=p_attach AND p_tid=t_id AND f_id=t_fid ".
  " AND COALESCE(ua_level,".$GLOBALS['inuserbasic'].")>=f_lread ".
  "ORDER BY ".$sort.$limit  ;
  $res=&db_query($sql,$link);
  filelist_start($pages,$perpage);
  while ($filedata=&db_fetch_array($res)) {
    filelist_entry($filedata);
  }
  if (db_num_rows($res)==0) filelist_noentries();
  filelist_end($pages);
}

function locations(&$locations) {
  array_push($locations,MSG_fl_filelist);
  return $locations;
}
