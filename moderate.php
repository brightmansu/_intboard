<? /*

Moderation script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function mod_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && !check_selfmod()) error(MSG_e_mod_norights);
  global $link;
  $topic=$GLOBALS['topic'];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Poll WHERE pl_tid=$topic";
  $res =&db_query($sql,$link);
  if ($vote=db_num_rows($res)) {
    $pldata =&db_fetch_array($res);
    if ($pldata['pl_enddate']) $pldata['enddate']=ceil(($pldata['pl_enddate']-time())/(24*60*60));
    db_free_result($res);
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."PollVariant WHERE pv_plid=".$pldata['pl_id'];
    $res =&db_query($sql,$link);
    $pv_text=array();
    while ($pvdata=&db_fetch_array($res)) $pv_text[$pvdata['pv_id']]=$pvdata['pv_text'];
    for ($i=0; $i<$GLOBALS['opt_defvotecount']; $i++) $pv_text[-($i+1)]="";
    db_free_result($res);
  }
  $uid = $GLOBALS['inuserid'];
  $flist=build_forum_select("f_ltopic",$GLOBALS['inforum']['f_tpid']);
  mod_topic_form($GLOBALS['intopic'],$pldata,$pv_text,$flist);
}

function do_mod_topic() {
  check_post();
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && !check_selfmod()) error(MSG_e_mod_norights);
  if (!getvar("t_title")) error(MSG_e_t_empty);
  $mode=&getvar("mode");
  global $link;
  $forum=$GLOBALS['forum'];
  if ($mode==0 || $mode==1) {
    $topic=$GLOBALS['topic'];
    check_hurl();
    unset($_POST['t_id']);
    if (!$_POST['t_link']) {
    $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
    $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
    }
    $sqldata=build_sql_all("t_");
    if ($mode==1) {
      $newforum=&getvar("newforum");
      $sql = "SELECT f_id FROM ".$GLOBALS['DBprefix']."Forum f LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ".
      "ON (ua.uid=".$GLOBALS['inuserid']." AND ua.fid=f_id) WHERE  f_ltopic<=COALESCE(ua_level,".$GLOBALS['inuserbasic'].") AND f_id=\"$newforum\"";
      $res =&db_query($sql,$link);
      if (db_num_rows($res)==0) error(MSG_e_mod_notopicrights);
      $sqldata.=", t_fid=\"".$newforum."\"";
      $userdif = build_diff_list($topic);
      user_substr($userdif,$forum);
      user_summ($userdif,$newforum);

      $sql = "SELECT MAX(p__time) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=\"$topic\"";
      $res=&db_query($sql,$link);
      list($maxtime)=db_fetch_row($res);
      db_free_result($res);
      if ($maxtime) {
        $sql = "UPDATE ".$GLOBALS['DBprefix']."LastVisit SET lv_markcount=lv_markcount-1 WHERE lv_markall>$maxtime AND fid=".$forum;
        $res=&db_query($sql,$link);
        $sql = "UPDATE ".$GLOBALS['DBprefix']."LastVisit SET lv_markcount=lv_markcount+1 WHERE lv_markall>$maxtime AND fid=".$newforum;
        $res=&db_query($sql,$link);
      }
    }
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET $sqldata WHERE t_id=$topic";
    $res =&db_query($sql,$link);
    if ($plid=&getvar("pl_id")) {
      if (getvar("enddate")) $enddate=time()+getvar("enddate")*24*60*60;
      else $enddate=0;
      $sql = "UPDATE ".$GLOBALS['DBprefix']."Poll SET pl_title=\"".getvar("pl_title")."\", pl_enddate=\"$enddate\" WHERE pl_id=\"$plid\"";
      $res =&db_query($sql,$link);
      $pv = $_POST['pv_text'];
      foreach ($pv as $pv_id=>$pv_text) {
        if ($pv_id>0 && $pv_text=="") {
          $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Vote WHERE pvid=\"".db_slashes($pv_id)."\"";
          $res =&db_query($sql,$link);
          $sql = "DELETE FROM ".$GLOBALS['DBprefix']."PollVariant WHERE pv_id=\"".db_slashes($pv_id)."\"";
          $res =&db_query($sql,$link);
        }
        elseif ($pv_id>0 && $pv_text) {
          $sql = "UPDATE ".$GLOBALS['DBprefix']."PollVariant SET pv_text=\"".db_slashes($pv_text)."\" WHERE pv_id=\"".db_slashes($pv_id)."\"";
          $res =&db_query($sql,$link);
        }
        elseif ($pv_id<0 && $pv_text) {
          $sql = "INSERT INTO ".$GLOBALS['DBprefix']."PollVariant (pv_id,pv_plid,pv_text) VALUES(0,\"$plid\",\"".db_slashes($pv_text)."\")";
          $res =&db_query($sql,$link);
        }
      }
    }
    if ($mode==1) forum_resync($newforum);
    if ($mode!=0) forum_resync($forum);
    topic_message(MSG_mod_saved,1);
  }
  elseif ($mode==2) {
    delete_topic($GLOBALS['topic']);
    $GLOBALS['refpage']=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['inforum']);
    forum_resync($forum);
    message(MSG_mod_topic_deleted,1);
  }
  elseif ($mode==3) {
    $topic=$GLOBALS['topic'];
    if (!getvar("t_title")) error(MSG_e_t_empty);
    $newforum=&getvar("newforum");
    $sql = "SELECT f_id FROM ".$GLOBALS['DBprefix']."Forum f LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ".
    "ON (ua.uid=".$GLOBALS['inuserid']." AND ua.fid=f_id) WHERE  f_ltopic<=COALESCE(ua_level,".$GLOBALS['inuserbasic'].") AND f_id=\"$newforum\"";
    $res =&db_query($sql,$link);
    if (db_num_rows($res)==0) error(MSG_e_mod_notopicrights);
    unset($_POST['t_id']);

    unset($_POST['t_link']);
    if (!$_POST['t_link']) {
    $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
    $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
    }
    $sqldata=build_sql_all("t_");
    $sqldata.=", t_fid=\"".$newforum."\"";
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Topic SET $sqldata";
    $res=&db_query($sql,$link);
    $tid = db_insert_id($res);

    $sql = "SELECT p_uid,COUNT(p_id) AS ucount FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=\"$topic\" GROUP BY p_uid";
    $res =&db_query($sql,$link);
    while ($udata=db_fetch_row($res)) {
      $copydif[$udata[0]]=$udata[1];
    }

    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Post (p_tid,p_text,p__modcomment,p__time,p__edittime,
      p_signature,p__smiles,p__bcode,p__html,p_attach,p_uid,p_uname,p__ip,p_title) ".
      "SELECT \"$tid\",p_text,p__modcomment,p__time,p__edittime,
      p_signature,p__smiles,p__bcode,p__html,p_attach,p_uid,p_uname,p__ip,p_title ".
      "FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=\"$topic\"";
    $res=&db_query($sql,$link);
    user_summ($copydif,$fid);
    topic_resync($tid);
    forum_resync($newforum);
    topic_message(MSG_mod_copied,1);
  }
}

function clear_forum() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  clear_form();
}

function do_clear_forum() {
  check_post();
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  $link =$GLOBALS['link'];

  $forum = $GLOBALS['forum'];
  $sql = "SELECT t_id FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post p1,  ".$GLOBALS['DBprefix']."Post p2 ".
          "WHERE t.t_fid=\"$forum\" AND p1.p_id=t.t__lastpostid AND p2.p_id=t__startpostid";
  if ($days=&getvar("days")) {
    $days = $GLOBALS['curtime']-($days*24*60*60);
    $sql.=" AND p1.p__time<$days";
  }
  if ($count=intval(getvar("count"))) {
    $sql.=" AND t.t__pcount<=\"$count\" AND t.t__pcount>0";
  }
  if ($title=&getvar("title")) {
    $sql.=" AND t.t_title LIKE \"%$title%\"";
  }
  if ($user=&getvar("user")) {
    $sql.=" AND p2.p_uname LIKE \"$user\"";
  }
  if (!$days && !$count && !$title && !$user) error(MSG_e_mod_noparams);
  $res =&db_query($sql,$link);

  while ($tdata=&db_fetch_array($res)) delete_topic($tdata['t_id']);
  forum_resync($forum);
  $msg = format_word($number,MSG_t1,MSG_t2,MSG_t3)." ".MSG_mod_deleted;
  $GLOBALS['refpage']=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['inforum']);
  message($msg,1);
}

function split_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && !check_selfmod()) error(MSG_e_mod_norights);
  global $link;
  $uid=$GLOBALS['inuserid'];
  $flist=build_forum_select("f_ltopic",$GLOBALS['inforum']['f_tpid'],"(1=1)");

  $start=&getvar("st");
  $pcount=$GLOBALS['intopic']['t__pcount'];
  if ((!isset($_GET['st']) && $pcount>$GLOBALS['inuser']['u_mperpage']) || $start=='new') {
    $start=$pcount-$GLOBALS['inuser']['u_mperpage'];
  }
  if (!$start) $start="0";
  if ($start!="all") $limit = " LIMIT $start,".$pcount;

  $pages=&build_pages($pcount,$start,$GLOBALS['inuser']['u_mperpage'],"index.php?m=moderate&a=split_topic&t=".$GLOBALS['topic']."&o=$sort");

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=".$GLOBALS['topic'].' ORDER BY p__time '.$limit;
  $res =&db_query($sql,$link);
  mod_split_start($flist,$pages);
  while ($pdata=&db_fetch_array($res)) {
    mod_split_entry($pdata);
  }

  mod_split_end();
}

function do_split_topic() {
  check_post();
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && !check_selfmod()) error(MSG_e_mod_norights);
  global $link;
  $tid=&getvar('tid');
  if ($tid) {
    if ($tid==$GLOBALS['topic']) error(MSG_e_mod_sametopic);
    $sql = "SELECT f_id FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ON (uid=".$GLOBALS['inuserid']." AND fid=f_id) ".
    "WHERE t_id=\"$tid\" AND t_fid=f_id AND f_ltopic<=COALESCE(ua_level,".$GLOBALS['inuserlevel'].")";
    $res=&db_query($sql,$link);
    if (db_num_rows($res)==0) error(MSG_e_mod_noforumsplit);
    list($fid)=db_fetch_row($res);
    db_free_result($res);
  }
  else $fid=&getvar('newforum');
  $forum=$GLOBALS['forum'];
  if (isset($_POST['move']) || $_POST['copy']) {
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=".$GLOBALS['topic']." AND p_uid!=2";
    $res =&db_query($sql,$link);
    list($pcount)=db_fetch_row($res);
    if ($pcount==1) error(MSG_e_mod_singlepost);

    if (!$tid) {
      if (!getvar("t_title")) error(MSG_e_t_empty);
      check_topic($fid);
      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Topic SET t_fid=\"$fid\", t_title=\"".getvar("t_title")."\", t_descr=\"".getvar("t_descr")."\"";
      foreach ($GLOBALS['intopic'] as $key=>$value) {
        if (substr($key,2,0)=="t_" && $key!="t_title" && $key!="t_descr") $sql.=", $key=\"".db_slashes($value)."\"";
      }
      $res =&db_query($sql,$link);
      $tid = db_insert_id($res);
    }
  }

  $posts=$_POST['pid'];
  foreach ($posts as $pid=>$value) {
    if ($_POST['move'][$pid]) {
      if ($sqldata1) $sqldata1.=" OR ";
      $sqldata1.="p_id=".db_slashes($pid);
    }
    if ($_POST['delete'][$pid]) {
      if ($sqldata2) $sqldata2.=" OR ";
      $sqldata2.="p_id=".db_slashes($pid);
    }
    if ($_POST['copy'][$pid]) {
      if ($sqldata3) $sqldata3.=" OR ";
      $sqldata3.="p_id=".db_slashes($pid);
    }
  }

  if ($sqldata1) {
    $sql = "SELECT p_uid,COUNT(p_id) AS ucount FROM ".$GLOBALS['DBprefix']."Post WHERE $sqldata1 GROUP BY p_uid";
    $res =&db_query($sql,$link);
    while ($udata=db_fetch_row($res)) {
      $movedif[$udata[0]]=$udata[1];
    }

    if (getvar('putlink')) {
      if (getvar('tid')) {
        $sql = "SELECT t_title FROM ".$GLOBALS['DBprefix']."Topic WHERE t_id=\"$tid\"";
        $res=&db_query($sql,$link);
        list($t_title)=db_fetch_row($res);
        db_free_result($res);
      }
      else $t_title=&getvar("t_title");

      $text1 = MSG_t_msgsplitted1." \\\"<a href=\\\"index.php?t=$tid\\\">".$t_title."</a>\\\"";
      $text2 = MSG_t_msgsplitted2." \\\"<a href=\\\"index.php?t=".$GLOBALS['topic']."\\\">".db_slashes($GLOBALS['intopic']['t_title'])."</a>\\\"";

      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Post SET p_uid=2, p_uname=\"System\", p__time=".$GLOBALS['curtime'].", p_tid=".$GLOBALS['topic'].", p_text=\"$text1\", p__html=1";
      $res =&db_query($sql,$link);

      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Post SET p_uid=2, p_uname=\"System\", p__time=".$GLOBALS['curtime'].", p_tid=".$tid.", p_text=\"$text2\", p__html=1";
      $res =&db_query($sql,$link);
    }

    $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET p_tid=\"$tid\" WHERE $sqldata1";
    $res =&db_query($sql,$link);
    $movecount = db_affected_rows($res);
    user_substr($movedif,$forum);
    user_summ($movedif,$fid);
  }
  if ($sqldata2) {
    $sql = "SELECT p_uid,COUNT(p_id) AS ucount FROM ".$GLOBALS['DBprefix']."Post WHERE $sqldata2 GROUP BY p_uid";
    $res =&db_query($sql,$link);
    while ($udata=db_fetch_row($res)) {
      $deldif[$udata[0]]=$udata[1];
    }

    $sql = "SELECT p_attach FROM ".$GLOBALS['DBprefix']."Post WHERE $sqldata2";
    $res =&db_query($sql,$link);
    if (db_num_rows($res)>0) {
      while ($pdata=db_fetch_row($res)) {
        if ($sqldata4) $sqldata4.=" OR ";
        $sqldata4.="file_id=".$pdata[0];
      }
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE $sqldata4";
      $res =&db_query($sql,$link);
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE $sqldata2";
    $res =&db_query($sql,$link);
    $delcount=db_affected_rows($res);
    user_substr($deldif,$forum);
  }
  if ($sqldata3) {
    $sql = "SELECT p_uid,COUNT(p_id) AS ucount FROM ".$GLOBALS['DBprefix']."Post WHERE $sqldata3 GROUP BY p_uid";
    $res =&db_query($sql,$link);
    while ($udata=db_fetch_row($res)) {
      $copydif[$udata[0]]=$udata[1];
    }
    $copycount=count($copydif);

    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Post (p_tid,p_text,p__modcomment,p__time,p__edittime,
      p_signature,p__smiles,p__bcode,p__html,p_attach,p_uid,p_uname,p__ip,p_title) ".
      "SELECT \"$tid\",p_text,p__modcomment,p__time,p__edittime,
      p_signature,p__smiles,p__bcode,p__html,p_attach,p_uid,p_uname,p__ip,p_title ".
      "FROM ".$GLOBALS['DBprefix']."Post WHERE ($sqldata3)";
    $res=&db_query($sql,$link);
    user_summ($copydif,$fid);
  }

  topic_resync($GLOBALS['topic']);
  if ($tid) topic_resync($tid);
  forum_resync($forum);
  if ($sqldata1 && $fid!=$forum) forum_resync($fid);

  $msg=format_word($movecount,MSG_p1,MSG_p2,MSG_p3)." ".MSG_mod_moved.", ".
       format_word($delcount,MSG_p1,MSG_p2,MSG_p3)." ".MSG_mod_deleted.', '.
       format_word($copycount,MSG_p1,MSG_p2,MSG_p3)." ".MSG_mod_pcopied;
  topic_message($msg,1);
}

function mod_forum() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;
  $forum=$GLOBALS['forum'];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=\"$forum\" ORDER BY t__lastpostid DESC";
  $res =&db_query($sql,$link);

  $flist = build_forum_select("f_ltopic",0,"f_id!=$forum");
  mod_forum_start($flist);
  while ($tdata=&db_fetch_array($res)) {
    mod_forum_entry($tdata);
  }
  mod_forum_end();
}

function do_mod_forum() {
  check_post();
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;

  if (count($_POST['move'])>0) check_topic($newforum=getvar("newforum"));
  $topics = $_POST['topics'];
  $forum=$GLOBALS['forum'];
  foreach ($topics as $tid=>$value) {
    if ($_POST['delete'][$tid]) {
      delete_topic($tid);
    }
    else {
      if ($_POST['sticky'][$tid]) {
        if ($sqldata1) $sqldata1.=" OR ";
        $sqldata1.="t_id=".db_slashes($tid);
      }
      else {
        if ($sqldata2) $sqldata2.=" OR ";
        $sqldata2.="t_id=".db_slashes($tid);
      }
      if ($_POST['stickypost'][$tid]) {
        if ($sqldata3) $sqldata3.=" OR ";
        $sqldata3.="t_id=".db_slashes($tid);
      }
      else {
        if ($sqldata4) $sqldata4.=" OR ";
        $sqldata4.="t_id=".db_slashes($tid);
      }
      if ($_POST['status'][$tid]) {
        if ($sqldata5) $sqldata5.=" OR ";
        $sqldata5.="t_id=".db_slashes($tid);
      }
      else {
        if ($sqldata6) $sqldata6.=" OR ";
        $sqldata6.="t_id=".db_slashes($tid);
      }
      if ($_POST['rate'][$tid]) {
        if ($sqldata7) $sqldata7.=" OR ";
        $sqldata7.="t_id=".db_slashes($tid);
      }
      else {
        if ($sqldata8) $sqldata8.=" OR ";
        $sqldata8.="t_id=".db_slashes($tid);
      }
      if ($_POST['move'][$tid]) {
        if ($sqldata9) $sqldata9.=" OR ";
        $sqldata9.="t_id=".db_slashes($tid);
        if ($sqldata10) $sqldata10.=" OR ";
        $sqldata10.="p_tid=".db_slashes($tid);
      }
    }
  }
  if ($sqldata1) {
    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t__sticky=1 WHERE t_fid=\"$forum\" AND ($sqldata1)";
    $res =&db_query($sql,$link);
  }
  if ($sqldata2) {
    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t__sticky=0 WHERE t_fid=\"$forum\" AND ($sqldata2)";
    $res =&db_query($sql,$link);
  }
  if ($sqldata3) {
    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t__stickypost=1 WHERE t_fid=\"$forum\" AND ($sqldata3)";
    $res =&db_query($sql,$link);
  }
  if ($sqldata4) {
    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t__stickypost=0 WHERE t_fid=\"$forum\" AND ($sqldata4)";
    $res =&db_query($sql,$link);
  }
  if ($sqldata5) {
    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t__status=1 WHERE t_fid=\"$forum\" AND ($sqldata5)";
    $res =&db_query($sql,$link);
  }
  if ($sqldata6) {
    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t__status=0 WHERE t_fid=\"$forum\" AND ($sqldata6)";
    $res =&db_query($sql,$link);
  }
  if ($sqldata7) {
    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t__rate=1 WHERE t_fid=\"$forum\" AND ($sqldata7)";
    $res =&db_query($sql,$link);
  }
  if ($sqldata8) {
    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t__rate=0 WHERE t_fid=\"$forum\" AND ($sqldata8)";
    $res =&db_query($sql,$link);
  }
  if ($sqldata9) {
    $sql = "SELECT p_uid,COUNT(p_id) AS ucount FROM ".$GLOBALS['DBprefix']."Post WHERE $sqldata10 GROUP BY p_uid";
    $res =&db_query($sql,$link);
    while ($udata=db_fetch_row($res)) {
      $movedif[$udata[0]]=$udata[1];
    }
    user_substr($movedif,$forum);
    user_summ($movedif,$newforum);

    $sql="UPDATE ".$GLOBALS['DBprefix']."Topic SET t_fid=\"".getvar("newforum")."\" WHERE t_fid=\"$forum\" AND ($sqldata9)";
    $res =&db_query($sql,$link);
  }
  forum_resync($forum);
  if (count($_POST['move'])>0) forum_resync($newforum);
  $GLOBALS['refpage'] = $GLOBALS['opt_url'].'/'.build_url($GLOBALS['inforum']);
  topic_message(MSG_mod_t_saved,1);
}

function check_topic($forum) {
  $link = $GLOBALS['link'];
  $sql = "SELECT f_id FROM ".$GLOBALS['DBprefix']."Forum f LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ".
  "ON (ua.uid=".$GLOBALS['inuserid']." AND ua.fid=f_id) WHERE COALESCE(ua_level,".$GLOBALS['inuserbasic'].")>=f_ltopic AND f_id=\"$forum\"";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_mod_notopicrights);
  db_free_result($res);
}

function join_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;
  $sql = "SELECT t_id,t_title FROM ".$GLOBALS['DBprefix']."Topic ".
  "WHERE t_fid=".$GLOBALS['forum']." AND t_id!=".$GLOBALS['topic'].
  " ORDER BY t__lasttime DESC";
  $topiclist = build_select($sql);
  join_form($topiclist);
}

function do_join_topic() {
  check_post();
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;
  $newtid=intval(getvar("newtid"));
  $text = MSG_t_msgjoin." \\\"".db_slashes($GLOBALS['intopic']['t_title'])."\\\"";
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Post SET p_uid=2, p__time=".$GLOBALS['curtime'].", p_tid=\"$newtid\", p_text=\"$text\", p__html=1";
  $res =&db_query($sql,$link);

  $sql = "SELECT p__time FROM ".$GLOBALS['DBprefix']."Post WHERE p_id=".$GLOBALS['intopic']['t__lastpostid'];
  $res=&db_query($sql,$link);
  list($maxtime)=db_fetch_row($res);
  db_free_result($res);

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=".$GLOBALS['topic']." ORDER BY p_id";
  $res =&db_query($sql,$link);
  while ($pdata=&db_fetch_array($res)) {
    $sqldata="";
    foreach ($pdata as $curkey=>$curvalue) {
      if ($curkey!='p_id' && $curkey!='p_tid') {
        $sqldata.=", $curkey=\"".db_slashes($curvalue)."\"";
      }
    }
    $sql="INSERT INTO ".$GLOBALS['DBprefix']."Post SET p_tid=\"$newtid\" $sqldata";
    $res2 =&db_query($sql,$link);
  }
  $sql="DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);
  delete_topic($GLOBALS['topic'],$maxtime);
  forum_resync($GLOBALS['forum']);
  topic_resync($newtid);
  $GLOBALS['topic']=$newtid;
  topic_message(MSG_mod_joined,1);
}

function edit_rules() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  rules_form($GLOBALS['inforum']['f_rules']);
}

function do_edit_rules() {
  check_post();
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;
  $rules=&getvar("rules_text");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_rules=\"$rules\" WHERE f_id=\"".$GLOBALS['forum']."\"";
  $res =&db_query($sql,$link);
  $GLOBALS['refpage']="index.php?f=".$GLOBALS['forum'];
  message(MSG_f_rules_saved,1);
}

function premod() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;

  $sql = "SELECT p.*,t_id,t_title, ph_tid, ph_key, ph_id, ".
         "file_id, file_name, file_type, file_size ".
         "FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Post p ".
         "LEFT JOIN ".$GLOBALS['DBprefix']."File ON (file_id=p_attach) ".
         "LEFT JOIN ".$GLOBALS['DBprefix']."Photo ON (ph_tid=p_tid) ".
         "WHERE p_tid=t_id AND t_fid=".$GLOBALS['forum']." AND p__premoderate=1 ORDER BY p_tid,p__time";
  $res =&db_query($sql,$link);
  premod_start();
  $oldtopic=0;
  while ($pdata=&db_fetch_array($res)) {
    if ($pdata['t_id']!=$oldtopic) {
      premod_topic($pdata);
      $oldtopic=$pdata['t_id'];
    }
    premod_entry($pdata);
  }
  premod_end();
}

function do_premod() {
  check_post();
  global $link;
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MGE_e_mod_norights);

  $sql = "SELECT p_id FROM ".$GLOBALS['DBprefix']."Post, ".$GLOBALS['DBprefix']."Topic ".
  "WHERE p_tid=t_id AND t_fid=".$GLOBALS['forum']." AND p__premoderate=1";
  $res =&db_query($sql,$link);
  while ($tmp=db_fetch_row($res)) {
    $curkey=$tmp[0];
    $curvalue=$_POST['pid'][$curkey];
    if ($curvalue==2) {
      if ($deldata) $deldata.=" OR ";
      $deldata.="p_id=$curkey";
    }
    elseif ($curvalue==1) {
      if ($pmdata) $pmdata.=" OR ";
      $pmdata.="p_id=$curkey";
    }
  }

  if ($pmdata) {
    $sql = "SELECT p_uid, COUNT(p_id) AS ucount FROM ".$GLOBALS['DBprefix']."Post WHERE $pmdata GROUP BY p_uid";
    $res =&db_query($sql,$link);
    while ($udata=db_fetch_row($res)) {
      $movedif[$udata[0]]=$udata[1];
    }
    user_summ($movedif,$GLOBALS['inforum']);

    $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET p__premoderate=0 WHERE $pmdata";
    $res =&db_query($sql,$link);

    $sql = "SELECT DISTINCT p_tid FROM ".$GLOBALS['DBprefix']."Post WHERE $pmdata";
    $res =&db_query($sql,$link);
    while ($tdata=db_fetch_row($res)) {
      topic_resync($tdata[0]);
    }

    if (!$deldata) forum_resync($GLOBALS['forum']);

    $sql = "SELECT p.*,t_title,t_id,f_title,f_id FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum ".
          "WHERE ($pmdata) AND p_tid=t_id AND t_fid=f_id";
    $res =&db_query($sql,$link);
    $buffer=load_mail("std_post.txt");
    while ($pdata=&db_fetch_array($res)) {
      $sql = "SELECT u_id,u__name,u__email,u__key FROM ".$GLOBALS['DBprefix']."Subscription sb, ".$GLOBALS['DBprefix']."User u ".
      "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u_id AND ua.fid=".$pdata['f_id'].") ".
      "WHERE sb.uid=u_id AND sb.tid=".$pdata['t_id']." AND u_id>3 AND ".
      "COALESCE(ua_level,u__level)<".$GLOBALS['inforum']['f_lmoderate'];
      $res =&db_query($sql,$link);
      while ($email=db_fetch_row($res)) {
        $GLOBALS['username']=$email[1];
        $GLOBALS['postername']=$pdata['p_uname'];
        $GLOBALS['flink']=$GLOBALS['opt_url'].'/'.build_url($pdata);
        $GLOBALS['unsublink']=$GLOBALS['opt_url']."/agent.php?a=unsub&u=".$email[0].
         "&f=".$GLOBALS['forum']."&t=".$GLOBALS['topic']."&key=".md5($GLOBALS['topic'].$email[4]);
        replace_mail($buffer,$email[2],MSG_p_newmessage." ".$pdata['t_title']);
      }
    }
  }

  if ($deldata) {
    $sql = "SELECT p_attach FROM ".$GLOBALS['DBprefix']."Post WHERE $deldata";
    $res =&db_query($sql,$link);
    while ($tmp=db_fetch_row($res)) {
      if ($tmp[0]!=0) {
        if ($attachdata) $attachdata.=", ";
        $attachdata.="file_id=".$tmp[0];
      }
    }

    if ($attachdata) {
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE $attachdata";
      $res =&db_query($sql,$link);
    }

    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE $deldata";
    $res =&db_query($sql,$link);

    $sql = "SELECT p_tid,COUNT(*) AS pcount FROM ".$GLOBALS['DBprefix']."Post, ".$GLOBALS['DBprefix']."Topic ".
    "WHERE p_tid=t_id AND t_fid=".$GLOBALS['forum']." GROUP BY p_tid HAVING pcount=0";
    $res =&db_query($sql,$link);
    while ($tmp=db_fetch_row($res)) {
      delete_topic($tmp[0]);
    }
    forum_resync($GLOBALS['forum']);
  }
  $GLOBALS['refpage']=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['inforum']);
  message(MSG_f_premoderated,1);
}

function mod_banlist() {
  global $link;
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MGE_e_mod_norights);

  $sql = "SELECT u_id,u__name FROM ".$GLOBALS['DBprefix']."User, ".$GLOBALS['DBprefix']."UserAccess ".
  "WHERE u_id=uid AND fid=".$GLOBALS['forum']." AND ua_level=-1 AND u_id>3 ORDER BY u__name";
  $res =&db_query($sql,$link);
  mod_ban_start();
  while ($udata=&db_fetch_array($res)) {
    mod_ban_entry($udata);
  }
  if (db_num_rows($res)==0) mod_ban_noentries();
  mod_ban_end();
}

function do_mod_clearban() {
  $key=&getvar('key');
  if (md5($GLOBALS['inuser']['u__key'].$GLOBALS['forum'])!=$key) error(MSG_e_mod_badkey);
  global $link;
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MGE_e_mod_norights);

  $uid=&getvar("u");
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserAccess WHERE uid=\"$uid\" AND fid=".$GLOBALS['forum'];
  $res =&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res =&db_query($sql,$link);

  message(MSG_mod_bancleared,1);
}

function do_mod_addban() {
  check_post();
  global $link;
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MGE_e_mod_norights);

  $uname=&getvar("uname");
  $sql = "SELECT u_id,u__level,ua_level FROM ".$GLOBALS['DBprefix']."User ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ON (uid=u_id AND fid=".$GLOBALS['forum'].") ".
  "WHERE u__name=\"$uname\"";
  $res =&db_query($sql,$link);
  list($uid,$ulevel,$ualevel)=db_fetch_row($res);

  if ($ulevel>=$GLOBALS['inuserlevel'] || $ualevel>=$GLOBALS['inuserlevel']) error(MSG_e_mod_subordinate);

  if ($uid) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserAccess WHERE uid=$uid AND fid=".$GLOBALS['forum'];
    $res =&db_query($sql,$link);
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UserAccess SET uid=$uid, fid=".$GLOBALS['forum'].", ua_level=-1";
    $res =&db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
    $res =&db_query($sql,$link);
    message(MSG_mod_banned,1);
  }
  else {
    message(MSG_mod_nouser);
  }
}

function complain() {
  complain_form();
}

function do_complain() {
  check_post();
  global $link;
  $text=&getvar("text");
  if (!$text) error(MSG_e_p_emptycomplain);
  if ($GLOBALS['opt_complain']==2 || ($GLOBALS['opt_complain']==1 && $GLOBALS['inuserid']<=3)) error(MSG_e_p_nocomplain);
  $subj=MSG_mod_complain;
  $text.="\n".MSG_topic." ".$GLOBALS['intopic']['t_title']." ".MSG_offorum." ".$GLOBALS['inforum']['f_title']."\n";
  $text.=$GLOBALS['opt_url']."/index.php?t=".$GLOBALS['topic']."&p=".getvar("p");

  $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User u ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u.u_id AND ua.fid=".$GLOBALS['forum'].") ".
  "WHERE COALESCE(ua.ua_level,u.u__level)>=".$GLOBALS['inforum']['f_lmoderate']." AND u_id>3";
  $res =&db_query($sql,$link);

  while ($uid=db_fetch_row($res)) {
    send_pm($uid[0],$GLOBALS['inuserid'],$text,$subj,"");
  }
  $GLOBALS['refpage']=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['intopic']);
  topic_message(MSG_mod_complain_send,1);
}

function view_vote() {
  global $link;
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MGE_e_mod_norights);

  $topic=$GLOBALS['topic'];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Poll WHERE pl_tid=$topic";
  $res =&db_query($sql,$link);
  $pldata =&db_fetch_array($res);
  $plid=$pldata['pl_id'];
  $sql = "SELECT u__name,u_id, pv.* FROM ".$GLOBALS['DBprefix']."PollVariant pv, ".$GLOBALS['DBprefix']."Vote, ".$GLOBALS['DBprefix']."User ".
         "WHERE pv_plid=$plid AND pvid=pv_id AND u_id=uid";
  $res =&db_query($sql,$link);
  vote_view_start($pldata);
  while ($pvdata=&db_fetch_array($res)) {
    vote_view_entry($pvdata);
  }
  vote_view_end();
}

function locations($locations) {
  push_parents($locations,$GLOBALS['inforum']['f_parent']);
  if ($GLOBALS['topic']) {
    array_push($locations,"<a href=\"index.php?f=".$GLOBALS['forum']."\">".$GLOBALS['inforum']['f_title']."</a>");
    array_push($locations,"<a href=\"index.php?t=".$GLOBALS['topic']."\">".$GLOBALS['intopic']['t_title']."</a>");
  }
  else {
    array_push($locations,"<a href=\"index.php?f=".$GLOBALS['forum']."\">".$GLOBALS['inforum']['f_title']."</a>");
  }
  if ($GLOBALS['action']=="mod_topic") array_push($locations,MSG_mod_topic);
  elseif ($GLOBALS['action']=="split_topic") array_push($locations,MSG_mod_split);
  elseif ($GLOBALS['action']=="join_topic") array_push($locations,MSG_mod_join);
  elseif ($GLOBALS['action']=="premod") array_push($locations,MSG_mod_premod);
  elseif ($GLOBALS['action']=="mod_banlist") array_push($locations,MSG_mod_bannedlist);
  elseif ($GLOBALS['action']=="view_vote") array_push($locations,MSG_mod_voteview);
  elseif ($GLOBALS['action']=="mod_forum") array_push($locations,MSG_mod_forum);
  elseif ($GLOBALS['action']=="clear_forum") array_push($locations,MSG_mod_clear);
  return $locations;
}