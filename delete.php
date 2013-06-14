<?
function do_delete_topic($topic,$maxtime) {
  global $link;

  $userdif=build_diff_list($topic);

  $sql = "SELECT t_fid FROM ".$GLOBALS['DBprefix']."Topic WHERE t_id=\"$topic\"";
  $res =&db_query($sql,$link);
  list($fid)=db_fetch_row($res);
  db_free_result($res);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Topic WHERE t_id=\"$topic\"";
  $res =&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicVC WHERE tid=\"$topic\"";
  $res =&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Bookmark WHERE tid=\"$topic\"";
  $res =&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE tid=\"$topic\"";
  $res =&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicView WHERE tid=\"$topic\"";
  $res =&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicRate WHERE tid=\"$topic\"";
  $res =&db_query($sql,$link);
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Poll WHERE pl_tid=\"$topic\"";
  $res =&db_query($sql,$link);

  if (db_num_rows($res)>0) {
      $plid=db_fetch_row($res);
      db_free_result($res);
      $sql = "SELECT pv_id FROM ".$GLOBALS['DBprefix']."PollVariant WHERE pv_plid=".$plid[0];
      $res =&db_query($sql,$link);
      while ($pvid=db_fetch_row($res)) {
          if ($sqldata) $sqldata.=" OR ";
          $sqldata = "pvid=".$pvid[0];
      }
      db_free_result($res);
      if ($sqldata) {
        $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Vote WHERE $sqldata";
        $res =&db_query($sql,$link);
      }
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."PollVariant WHERE pv_plid=".$plid[0];
      $res =&db_query($sql,$link);
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Poll WHERE pl_id=".$plid[0];
      $res =&db_query($sql,$link);
  }

  if (!$maxtime) {
    $sql = "SELECT MAX(p__time) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=\"$topic\"";
    $res=&db_query($sql,$link);
    list($maxtime)=db_fetch_row($res);
    db_free_result($res);
  }

  if ($maxtime) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."LastVisit SET lv_markcount=lv_markcount-1 WHERE lv_markall>=$maxtime AND fid=".$GLOBALS['forum'];
    $res=&db_query($sql,$link);
  }

  $sql = "SELECT p_attach FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=\"$topic\" AND p_attach!=0";
  $res =&db_query($sql,$link);
  $attachcount=db_num_rows($res);
  if ($attachcount) {
    while ($pnumber=db_fetch_row($res)) {
      if ($sqldata) $sqldata.=" OR ";
      $sqldata.="file_id=".$pnumber[0];
      }
      db_free_result($res);
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE $sqldata";
      $res =&db_query($sql,$link);
  }
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=\"$topic\"";
  $res=&db_query($sql,$link);
  user_substr($userdif,$fid);
}

function do_delete_user($uid) {
    global $link;

    $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET p_uid=1 WHERE p_uid=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupMember WHERE uid=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserRating WHERE uid=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserWarning WHERE uw_id=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."PersonalMessage WHERE pm__owner=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicView WHERE uid=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserAccess WHERE uid=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."LastVisit WHERE uid=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserStat WHERE uid=$uid";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."AddrBook WHERE u_owner=$uid OR u_partner=$uid";
    $res =& db_query($sql,$link);

    $sql = "SELECT u__pavatar_id,u__photo_id FROM ".$GLOBALS['DBprefix']."User WHERE u_id=$uid";
    $res =&db_query($sql,$link);
    list($avatar,$photo)=db_fetch_row($res);
    db_free_result($res);

    if ($avatar) {
        $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE file_id=\"$avatar\"";
        $res =&db_query($sql,$link);
    }
    if ($photo) {
        $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE file_id=\"$photo\"";
        $res =&db_query($sql,$link);
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
    $res =&db_query($sql,$link);

    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online WHERE o_uid=\"$uid\"";
    $res =&db_query($sql,$link);

    clear_mod_cache();
}

function do_delete_post($pid) {
  global $link;
  $sql = "SELECT u.u__level,ua.ua_level,p.p_attach,p.p_tid,p_uid,t_fid,t__pcount ".
  "FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Topic t,  ".$GLOBALS['DBprefix']."User u ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u.u_id AND ua.fid=".$GLOBALS['forum'].") ".
  "WHERE p_id=\"$pid\" AND p_tid=t_id AND u.u_id=p.p_uid";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)!=1) error(MSG_e_p_notfound);
  $pdata=&db_fetch_array($res);
  db_free_result($res);
  if ($pdata['p_uid']!=$GLOBALS['inuserid'] && check_moderate($pdata,$GLOBALS['inuserlevel'])) error(MSG_e_mod_subordinate);
  if ($pdata['p_attach']) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE file_id=".$pdata['p_attach'];
    $res =&db_query($sql,$link);
  }
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE p_id=\"$pid\"";
  $res =&db_query($sql,$link);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."UserStat SET us_count=us_count-1 WHERE uid=".$pdata['p_uid']." AND fid=".$pdata['t_fid'];
  $res =&db_query($sql,$link);
  if ($pdata['t__pcount']==1) { 
    delete_topic($pdata['p_tid']);
    return true;
  }
  return false;
}

function clear_mod_cache() {
  $dh=opendir($GLOBALS['opt_dir'].'/config/');
  while ($file=readdir($dh)) {
    if (is_file($GLOBALS['opt_dir'].'/config/'.$file) && strpos($file,'moders')===0) unlink($GLOBALS['opt_dir'].'/config/'.$file);
  }
  closedir($dh);
}
