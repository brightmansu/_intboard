<? /*

Drafts list script for Intellect Board 2 Project

(C) 2004-2006, XXXX Pro, United Open Project (http://www.openproj.ru)
Visit us online: http://intboard.ru

*/

function view() {
  global $link;
  $sql = "SELECT COUNT(*) ".
  "FROM ".$GLOBALS['DBprefix']."Forum, ".$GLOBALS['DBprefix']."Draft dr ".
  " LEFT JOIN ".$GLOBALS['DBprefix']."Topic t ON (t_id=dr_tid) ".
  "WHERE dr_uid=".$GLOBALS['inuserid']." AND dr_fid=f_id";
  $res=&db_query($sql,$link);
  list($count)=db_fetch_row($res);
  db_free_result($res);

  $perpage =&getvar('perpage');
  if (!$perpage) $perpage=$GLOBALS['inuser']['u_mperpage'];
  $start=&getvar('st');
  if ($start!="all") $limit = " LIMIT ".intval($start).",".intval($perpage);

  $pages=&build_pages($count,$start,$perpage,"index.php?m=drafts&perpage=$perpage&o=$sort&desc=$desc");

  $sql = "SELECT dr.*, t_id, t_title, t_link, f_id, f_title, f_link ".
  "FROM ".$GLOBALS['DBprefix']."Forum, ".$GLOBALS['DBprefix']."Draft dr ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Topic t ON (t_id=dr_tid) ".
  "WHERE dr_uid=".$GLOBALS['inuserid']." AND dr_fid=f_id ".
  "ORDER BY f_sortfield, t_id DESC ".$limit  ;
  $res=&db_query($sql,$link);
  drafts_start($pages,$perpage);
  while ($draftdata=&db_fetch_array($res)) {
    $draftdata=&array_merge($draftdata,unserialize($draftdata['dr_text']));
    drafts_entry($draftdata);
  }
  if (db_num_rows($res)==0) drafts_noentries();
  drafts_end($pages);
}

function do_delete() {
  global $link;
  $sqlarray=array();
  if (is_array($_POST['draft'])) {
    foreach ($_POST['draft'] as $curdraft) {
     list($fid,$tid)=explode(':',$curdraft);
      $sqlarray[]='(dr_fid="'.db_slashes($fid).'" AND dr_tid="'.db_slashes($tid).'") ';
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Draft WHERE dr_uid=".$GLOBALS['inuserid'].' AND ('.join(' OR ',$sqlarray).')';
    $res=&db_query($sql,$link);
    message(MSG_dr_deleted,1);
  }
  else message(MSG_dr_noselected,1);
}

function locations(&$locations) {
  array_push($locations,MSG_dr_draftlist);
  return $locations;
}
