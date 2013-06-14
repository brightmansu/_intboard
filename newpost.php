<? /*

New posts script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function recurse_forums($fid) {
  global $link;
  $sql = "SELECT f_id, tp_container ".
  "FROM ".$GLOBALS['DBprefix']."Forum, ".$GLOBALS['DBprefix']."ForumType ".
  "WHERE tp_id=f_tpid AND f_parent=".$fid;
  $res = db_query($sql,$link);
  $result = array(intval($fid));
  while ($data=db_fetch_row($res)) {
    $result[]=$data[0];
    if ($data[1]) $result=array_merge($result,recurse_forums($data[0]));
  }
  return $result;
}

function view() {
  global $link;
  if ($GLOBALS['inuserid']<=3) error(MSG_e_new_noguest);

  if ($GLOBALS['opt_fixviews']) $sqldata=" AND t__lasttime>=IFNULL(lv.lv_markall,0) AND tv.tid IS NULL ";
  else $sqldata=" AND t__lasttime>=IFNULL(lv.lv_time2,0) AND p_uid<>".$GLOBALS['inuserid']." ";
  if (getvar("fs")) $forumlimit=" AND f_id IN (".join(',',recurse_forums(getvar("fs"))).") ";
  $time=&getvar("time");

  if (!isset($_GET['time'])) $time=$GLOBALS['inuser']['u_timelimit'];
  else $timelimit=$GLOBALS['curtime']-$time*24*60*60;

  $sql = "SELECT f_title, f_id, f_link, f_lread, ua_level, lv_time2 AS lvtime, f_sortfield, ct_sortfield, f_parent ".
  "FROM ".$GLOBALS['DBprefix']."Category, ".$GLOBALS['DBprefix']."Forum ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=f_id AND lv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.fid=f_id AND ua.uid=".$GLOBALS['inuserid'].") ".
  "WHERE f_ctid=ct_id ".$forumlimit." AND ".check_access('f_id',true)." AND COALESCE(ua_level,".$GLOBALS['inuserbasic'].")>=f_lread ORDER BY ct_sortfield";
  $res=&db_query($sql,$link);
  $forums = array();
  $fids=array();
  while ($tmp=&db_fetch_array($res)) {
    if (($tmp['ua_level'] && $tmp['f_lread']<=$tmp['ua_level'])
    || $tmp['f_lread']<=$GLOBALS['inuserbasic']) {
      $forums[]=$tmp;
      $fids[]=$tmp['f_id'];
    }
  }
  if (count($fids)) $flist=' AND t_fid IN ('.join(',',$fids).')';
  if ($timelimit>0) $timelimit=' AND p__time>'.$timelimit;
  $forums=sort_forums_recurse($forums);

  $sql = "SELECT p_uname,p_uid,p__time,t_title,t_link,t_id,t_fid, SUBSTRING(p_text,1,".(intval($GLOBALS['opt_hinttext'])+1).") AS hint FROM ".
  $GLOBALS['DBprefix']."Topic ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=t_fid AND lv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."TopicView tv ON (tv.tid=t_id AND tv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Post p ON (p_id=t__lastpostid) ".
  "WHERE t__pcount>0 $timelimit ".$flist.' '.
  $sqldata." ORDER BY t__lasttime DESC";
  $res =&db_query($sql,$link);

  new_start($time,MSG_newposts);
  $oldforum=0;
  $newtopics=array();
  while ($newdata=&db_fetch_array($res)) {
    $newtopics[]=$newdata;
  }
  db_free_result($res);
  $fcount=count($forums);
  for ($i=0;$i<$fcount; $i++) {
     $f=0;
     $tcount=count($newtopics);
     for ($j=0;$j<$tcount; $j++) {
       if ($newtopics[$j]['t_fid']==$forums[$i]['f_id']) {
         if ($f==0) { new_forum($forums[$i]); $f=1; }
         new_entry(array_merge($newtopics[$j],$forums[$i]));
       }
     }
  }
  if (count($newtopics)==0) new_noentries();
  new_end();
}

function view_unanswered() {
  global $link;

  if (getvar("fs")) $forumlimit=" AND f_id IN (".join(',',recurse_forums(getvar("fs"))).") ";
  $time=&getvar("time");

  if (!isset($_GET['time'])) $time=$GLOBALS['inuser']['u_timelimit'];
  if ($time>0) $timelimit=' AND p__time>'.($GLOBALS['curtime']-$time*24*60*60);

  $sql = "SELECT f_title, f_id, f_link, f_lread, ua_level, lv_time2 AS lvtime ".
  "FROM ".$GLOBALS['DBprefix']."Category, ".$GLOBALS['DBprefix']."Forum ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=f_id AND lv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.fid=f_id AND ua.uid=".$GLOBALS['inuserid'].") ".
  "WHERE f_ctid=ct_id ".$forumlimit." ORDER BY ct_sortfield, f_sortfield";
  $res=&db_query($sql,$link);
  $forums = array();
  $fids=array();
  while ($tmp=&db_fetch_array($res)) {
    if (($tmp['ua_level']!=NULL && $tmp['f_lread']<=$tmp['ua_level'])
    || $tmp['f_lread']<=$GLOBALS['inuserbasic']) {
      $forums[]=$tmp;
      $fids[]=$tmp['f_id'];
    }
  }
  if (count($fids)) $flist=' t_fid IN ('.join(',',$fids).')';

  $sql = "SELECT p_uname,p_uid,p__time,t_title,t_link,t_id,t_fid, SUBSTRING(p_text,1,".(intval($GLOBALS['opt_hinttext'])+1).") AS hint FROM ".
  $GLOBALS['DBprefix']."Topic ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."TopicView tv ON (tv.tid=t_id AND tv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=t_fid AND lv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Post p ON (p_id=t__lastpostid) ".
  "WHERE ".$flist." AND t__pcount=1 $timelimit ORDER BY t__lasttime DESC";
  $res =&db_query($sql,$link);
  new_start($time,MSG_t_unanswered);
  $oldforum=0;
  $newtopics=array();
  while ($newdata=&db_fetch_array($res)) {
    $newtopics[]=$newdata;
  }
  db_free_result($res);
  $fcount=count($forums);
  for ($i=0;$i<$fcount; $i++) {
     $f=0;
     $tcount=count($newtopics);
     for ($j=0;$j<$tcount; $j++) {
       if ($newtopics[$j]['t_fid']==$forums[$i]['f_id']) {
         if ($f==0) { new_forum($forums[$i]); $f=1; }
         new_entry(array_merge($newtopics[$j],$forums[$i]));
       }
     }
  }
  if (count($newtopics)==0) new_noentries();
  new_end();
}

function do_mark_read() {
  global $link;
  $forum =&getvar("fs");
  if ($GLOBALS['inuserid']<=3) error(MSG_e_new_noguest);

  if ($GLOBALS['opt_fixviews']==1) {
    if ($forum) {
      $sql = "SELECT t_id FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=\"$forum\"";
      $res =&db_query($sql,$link);
      while ($tmp=db_fetch_row($res)) {
        if ($forumlimit) $forumlimit.=" OR ";
        $forumlimit.="tid=".$tmp[0];
      }
      db_free_result($res);
      $forumlimit="AND ($forumlimit)";
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicView WHERE uid=".$GLOBALS['inuserid']." $forumlimit";
    $res =&db_query($sql,$link);
  }
  if ($forum) {
    $forumlimit=" AND fid=\"$forum\"";
    $forumlimit2=" WHERE f_id=\"$forum\"";
  }
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."LastVisit WHERE uid=".$GLOBALS['inuserid'].$forumlimit;
  $res =&db_query($sql,$link);

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."LastVisit (uid,fid,lv_time1,lv_time2,lv_markall,lv_markcount) ".
    "SELECT ".$GLOBALS['inuserid'].",f_id,".$GLOBALS['curtime'].",".$GLOBALS['curtime'].",".$GLOBALS['curtime'].", f__tcount FROM ".$GLOBALS['DBprefix']."Forum ".$forumlimit2;
  $res =&db_query($sql,$link);

  message(MSG_f_marked,1);
}

function view_updated() {
  global $link;

  if (getvar("fs")) $forumlimit=" AND t_fid=\"".getvar("fs")."\" ";
  $time=&getvar("time");

  if (!isset($_GET['time'])) $time=$GLOBALS['inuser']['u_timelimit'];
  if (!$GLOBALS['opt_updated_time']) $GLOBALS['opt_updated_time']=30;
  if (!$time) $time=$GLOBALS['opt_updated_time'];
  $timelimit=$GLOBALS['curtime']-$time*24*60*60;

  $sql = "SELECT COUNT(*)  FROM ".$GLOBALS['DBprefix']."Topic ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=t_fid AND lv.uid=".$GLOBALS['inuserid'].") ".
  "WHERE t__lasttime>$timelimit AND ".check_access('t_fid',true)." AND t__pcount>0 ".
  $sqldata.$forumlimit;
  $res=&db_query($sql,$link);
  list($count)=db_fetch_row($res);
  db_free_result($res);

  $start=&getvar('st');
  $perpage=$GLOBALS['inuser']['u_tperpage'];
  $pages=&build_pages($count,$start,$perpage,"index.php?m=newpost&a=view_updated&time=$time&fs=$forum");
  if ($start!="all") $limit="LIMIT ".intval($start).",$perpage";

  $sql = "SELECT t.*, f_id, f_title, f_link, p1.p_uid, p1.p_uname, p1.p__time AS fp__time, p2.p_uname AS lp_uname, ".
  "p2.p__html, p2.p__bcode, p2.p__smiles, lv_markall, t__views, ".
  "p2.p_uid AS lp_uid, p2.p__time AS lp__time, t.t__ratingsum/NULLIF(t__ratingcount,0) AS trating, ".
  "tv.tid AS visited, t__pcount AS tl_count, SUBSTRING(p2.p_text,1,".(intval($GLOBALS['opt_hinttext'])+20).") AS hint ".
  "FROM ".$GLOBALS['DBprefix']."Topic t ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."TopicVC tvc ON (tvc.tid=t_id) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Forum ON (t_fid=f_id) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."TopicView tv ON (tv.uid=".$GLOBALS['inuserid']." AND tv.tid=t_id) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."LastVisit lv ON (lv.fid=t_fid AND lv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Post p1 ON (p1.p_id=t__startpostid) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Post p2 ON (p2.p_id=t__lastpostid) ".
  "WHERE t__lasttime>$timelimit AND ".check_access('t_fid',true)." AND t__pcount>0 ".
  $sqldata.$forumlimit.
  "ORDER BY t__lasttime DESC $limit";
  $res =&db_query($sql,$link);
  upd_start($time,$pages);
  while ($newdata=&db_fetch_array($res)) {
    $tpages = build_pages_hurl($newdata['t__pcount'],-2,$GLOBALS['inuser']['u_mperpage'],$newdata,"");
    upd_entry($newdata,$tpages);
  }
  if (db_num_rows($res)==0) upd_noentries();
  db_free_result($res);
  upd_end($pages);
}

function do_unmark_read() {
  global $link;
  $tid = intval(getvar('ts'));
  $sql = 'SELECT t__lasttime,t_fid FROM '.$GLOBALS['DBprefix'].'Topic WHERE t_id='.$tid;
  $res=db_query($sql,$link);
  list($lasttime,$fid)=db_fetch_row($res);
  db_free_result($res);
  $sql = 'DELETE FROM '.$GLOBALS['DBprefix'].'TopicView WHERE tid='.$tid.' AND uid='.$GLOBALS['inuserid'];
  $res=&db_query($sql,$link);
  $sql = 'UPDATE '.$GLOBALS['DBprefix'].'LastVisit SET lv_markcount=lv_markcount-1 '.
  'WHERE uid='.$GLOBALS['inuserid'].' AND fid='.$fid.' AND lv_markall>'.$lasttime;
  $res=&db_query($sql,$link);
  $GLOBALS['refpage']=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['inforum']);
  message(MSG_t_unmarked,1);
}

function locations(&$locations) {
  if ($GLOBALS['forum']) {
    push_parents($locations,$GLOBALS['inforum']['f_parent']);
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
  }
  if ($GLOBALS['action']=="view") $GLOBALS['rss_link']="rss.php?a=last_topics";
  if ($GLOBALS['action']=="view_updated") {
    $days = intval(getvar('time'));
    if(!$days || $days>=30) $days=3;
    $GLOBALS['rss_link']="rss.php?a=allnew&amp;days=".$days;
  }
  if ($GLOBALS['action']=="view") array_push($locations,MSG_f_newposts);
  elseif ($GLOBALS['action']=="view_unanswered") array_push($locations,MSG_t_unanswered);
  elseif ($GLOBALS['action']=="view_updated") array_push($locations,MSG_t_updated);
  return $locations;
}
