<? /*

Title page script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function view() {
  $link = $GLOBALS['link'];

  $category=&getvar("ct");
  $oldcat = 0;
  if ($GLOBALS['inuser']['u_multilang']==0) {
    $lang="(f_lnid=0 OR f_lnid=".$GLOBALS['inuser']['ln_id'].") AND ";
  }
  if (!$GLOBALS['opt_fixviews']) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."LastVisit SET lv_time2=lv_time1 WHERE uid=".$GLOBALS['inuserid']." AND lv_time1<".($GLOBALS['curtime']-$GLOBALS['opt_heretime']*60);
    $res =&db_query($sql,$link);
  }

  if ($GLOBALS['opt_foreword'] && !$category && !$GLOBALS['forum']) main_foreword($GLOBALS['opt_foreword']);

  if (!$category && !$GLOBALS['forum'] && $GLOBALS['opt_summary']==1) main_show_stats();
  $parent=$GLOBALS['forum'];
  if (!$parent) $parent=0;

  if(!isset($sqldata)) $sqldata = "";

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Category $sqldata ORDER BY ct_sortfield";
  $res=&db_query($sql,$link);
  while ($ctdata=&db_fetch_array($res)) {
    $catlist[]=$ctdata;
  }
  db_free_result($res);

  if ($GLOBALS['opt_fixviews'] && $GLOBALS['inuserid']>3) {
    if ($GLOBALS['DBdriver']=='mysql' && (($GLOBALS['DBheavyload'] & 2)==2)) {
      $sql = "LOCK TABLES ".$GLOBALS['DBprefix']."ForumView WRITE, ".
        $GLOBALS['DBprefix']."Forum AS f READ, ".$GLOBALS['DBprefix']."LastVisit AS lv READ, ".
        $GLOBALS['DBprefix']."Post AS p1 READ, ".$GLOBALS['DBprefix']."Post AS p2 READ, ".
        $GLOBALS['DBprefix']."ForumType AS tp READ, ".$GLOBALS['DBprefix']."TopicView AS tv READ, ".
        $GLOBALS['DBprefix']."Topic READ";
      $res=&db_query($sql,$link);
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."ForumView";
      $res =&db_query($sql,$link);
    }
    else {
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."ForumView WHERE uid=".$GLOBALS['inuserid'];
      $res =&db_query($sql,$link);
    }

    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."ForumView (uid,fid,fv_count) SELECT ".$GLOBALS['inuserid'].", t_fid, COUNT(tid) FROM ".$GLOBALS['DBprefix']."TopicView tv, ".$GLOBALS['DBprefix']."Topic WHERE t_id=tid AND tv.uid=".$GLOBALS['inuserid']." AND (".check_access('t_fid').") GROUP BY t_fid";
    $res =&db_query($sql,$link);
  }

  $sql = "SELECT f.*, f__views, lv.*, tp_template, tp_container, tp_external, f__tcount AS tf_tcount, ".
  "f__pcount AS tf_pcount, lv_time2, p1.p__time AS tf_lasttime, p1.p_uname, p1.p_uid, ".
  "p2.p__time AS tf_laststart, f__views AS tf_views, COALESCE(fv_count,0)+COALESCE(lv_markcount,0) AS tf_visited ".
  ($GLOBALS['opt_last_post']?", t_title lp_title, t_id lp_id, t_link lp_link ":"").
  "FROM ".$GLOBALS['DBprefix']."ForumType tp, ".$GLOBALS['DBprefix']."Forum f ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."ForumVC fvc ON (fvc.fid=f_id) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=f_id AND lv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."ForumView ON (".$GLOBALS['DBprefix']."ForumView.fid=lv.fid AND ".$GLOBALS['DBprefix']."ForumView.uid=lv.uid) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Post p1 ON (p1.p_id=f.f__lastpostid) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Post p2 ON (p2.p_id=f.f__startpostid) ".
  ($GLOBALS['opt_last_post']?"LEFT JOIN ".$GLOBALS['DBprefix']."Topic t ON (p1.p_tid=t.t_id) ":"").
  "WHERE tp_id=f_tpid AND $lang ".check_access('f_id')." AND f_hidden=0 ";
  if ($category) $sql .= " AND f_ctid=\"$category\"";
  $sql.=" ORDER BY f_sortfield";
  $res =&db_query($sql,$link);

  if ($GLOBALS['opt_fixviews'] && $GLOBALS['inuserid']>3) {
    if ($GLOBALS['DBdriver']=='mysql' && (($GLOBALS['DBheavyload'] & 2)==2)) {
      $sql = "UNLOCK TABLES";
      $res2=&db_query($sql,$link);
    }
  }
  $GLOBALS['forumlist']=array();
  while ($fdata=&db_fetch_array($res)) {
    $GLOBALS['forumlist'][$fdata['f_ctid']][]=$fdata;
  }

  main_list_start();
  $IBOARD=1;
  load_style("titles.php");
  require_once($GLOBALS['opt_dir']."/titles.php");

  global $oldcat;
  $oldcat=0;
  if (is_array($catlist)) foreach ($catlist as $curcat) {
    if (is_array($GLOBALS['forumlist'][$curcat['ct_id']])) foreach ($GLOBALS['forumlist'][$curcat['ct_id']] as $fdata) if ($fdata['f_parent']==$parent) {
      if ($fdata['f_ctid']!=$oldcat) {
        main_category($curcat);
        $oldcat=$fdata['f_ctid'];
      }
      build_mod_list($fdata['f_id'],$fdata['f_lmoderate']);

      if ($fdata['tp_external']) {
        require_once($GLOBALS['opt_dir']."/titles/".$fdata['tp_template'].".php");
        load_style("titles/".$fdata['tp_template'].".php");
      }
      if (!$fdata['t_count']) $fdata['p_count']=0;
      call_user_func($fdata['tp_template']."_title",$fdata);
    }
  }
  main_list_end();

  if (!$category && !$GLOBALS['forum'] && $GLOBALS['opt_summary']==2) main_show_stats();
}

function contnr_view() {
  view();
}

function contnr_locations($locations) {
  return locations($locations);
}

function main_show_stats() {
  global $link;
  $sql = "SELECT SUM(f__pcount), SUM(f__tcount) FROM ".$GLOBALS['DBprefix']."Forum ".
  "WHERE f_nostats=0 AND $lang ".check_access('f_id');
  $res =&db_query($sql,$link);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  $p_total=$tmp[0];
  $t_total=$tmp[1];

  $sql = "SELECT COUNT(u_id)-3 AS u_total FROM ".$GLOBALS['DBprefix']."User u";
  $res =&db_query($sql,$link);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  $u_total=$tmp[0];

  $sql = "SELECT u_id, u__name FROM ".$GLOBALS['DBprefix']."User u ORDER BY u_id DESC LIMIT 1";
  $res =&db_query($sql,$link);
  $ucount = db_fetch_assoc($res);
  $ucount['u_total'] = $u_total;
  db_free_result($res);

  main_statistics($t_total,$p_total,$ucount);
}

function locations($locations) {
  $ctid=&getvar("ct");
  global $link;
  if ($ctid) {
    $sql = "SELECT ct_name FROM ".$GLOBALS['DBprefix']."Category WHERE ct_id=\"$ctid\"";
    $res =&db_query($sql,$link);
    $ctname = db_fetch_row($res);
    push_parents($locations,$GLOBALS['forum']);
    array_push($locations,$ctname[0]);
  }
  elseif ($GLOBALS['forum']) {
   /*array_push($locations,"<a href=\"index.php?ct=".$fdata['ct_id']."\">".$GLOBALS['inforum']['ct_name']."</a>");*/
    push_parents($locations,$GLOBALS['inforum']['f_parent']);
    array_push($locations,$GLOBALS['inforum']['f_title']);
  }
  return $locations;
}
