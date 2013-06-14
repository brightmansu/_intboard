<? /*

Standart forum script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function stdforum_view() {
  if ($GLOBALS['topic']) view_topic();
  else view_forum();
}

function view_forum() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  $forum=$GLOBALS['forum'];
  global $link;
  $start=&getvar("st");
  if (!$start) $start="0";
  $order =&getvar("o");
  $filter =&getvar("filter");

  if ($time && $time!="0") $timelimit=$time*24*60*60;
  else $timelimit=$GLOBALS['curtime'];
  if ($GLOBALS['curtime']-$timelimit>0) $timeexpr="AND t__lasttime>=".intval($GLOBALS['curtime']-$timelimit);
  else $timeexpr="AND t__lasttime>=0";

  if ($filter) $filterexpr=" AND t_title LIKE \"%$filter%\"";
  if (!$filter || !$timelimit) $tcount = $GLOBALS['inforum']['f__tcount'];
  else {
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=$forum AND t__pcount>0 ".$filterexpr." ".$timeexpr;
    $res=&db_query($sql,$link);
    list($tcount)=db_fetch_row($res);
    db_free_result($res);
  }

  $perpage = intval(getvar("perpage"));
  $time=&getvar("time");
  if (!isset($_GET['time'])) $time=$GLOBALS['inuser']['u_timelimit'];

  if (!$perpage) $perpage=$GLOBALS['inuser']['u_tperpage'];
  $pages=build_pages_hurl($tcount,$start,$perpage,$GLOBALS['inforum'],"perpage=$perpage&amp;filter=$filter&amp;o=$order&amp;time=$time&amp;desc=".getvar('desc'));

  list($inforum,$autosub)=get_forum_sub();

  if (!$order) { $order="t__lasttime";  $direct=" DESC"; }
  if (getvar("desc")) $direct=" DESC";
  if ($start!="all") $limit = " LIMIT $start,".($perpage);

  $sql = "SELECT t.*,p1.p_uname, p1.p_uid, p1.p__html, p1.p__bcode, p1.p__smiles, ".
     "p1.p__time AS fp__time, p2.p_uname AS lp_uname, p2.p_uid AS lp_uid, p2.p__time AS lp__time, ".
     "pl_tid, t.t__ratingsum/NULLIF(t__ratingcount,0) AS trating, tv.tid AS visited, t__views, ".
     "t__pcount AS tl_count, SUBSTRING(p1.p_text,1,".(intval($GLOBALS['opt_hinttext'])+20).") AS hint ".
     "FROM ".$GLOBALS['DBprefix']."Topic t  ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."TopicView tv ON (tv.tid=t.t_id AND tv.uid=".$GLOBALS['inuserid'].") ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."Post p1 ON (p1.p_id=t.t__startpostid) ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."Post p2 ON (p2.p_id=t.t__lastpostid) ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."TopicVC tvc ON (tvc.tid=t.t_id) ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."Poll pl ON (pl.pl_tid=t.t_id) ".
     "WHERE t.t_fid=$forum AND t__pcount>0 ".
     "$timeexpr $filterexpr ".
     "ORDER BY t__sticky DESC,$order".$direct.$limit;
  $res =&db_query($sql,$link);
  std_forum_start($pages,$perpage,$filter,$time);
  if ($start=="all") $perpage=$tcount;
  while (($tdata=&db_fetch_array($res))) {
    if ($sticky==1 && $tdata['t__sticky']==0) std_topic_separator();
    $sticky=$tdata['t__sticky'];
    $tpages = build_pages_hurl($tdata['tl_count'],-2,$GLOBALS['inuser']['u_mperpage'],$tdata,"");
    std_topic_entry($tdata,$tpages);
  }
  if (db_num_rows($res)==0) std_forum_noentries();
  std_forum_end($pages,$inforum,$autosub);

  if ($start=='new' || $start=='all' || ($start>$GLOBALS['intopic']['t__pcount']-$GLOBALS['inuser']['u_mperpage']) && $_SESSION['t'.$GLOBALS['topic']]>=$GLOBALS['curtime']-300) $_SESSION['t'.$GLOBALS['topic']]=$GLOBALS['curtime'];
}

function view_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  global $link;
  if (!getvar("preview")) {
    $intopic=&$GLOBALS['intopic'];
    if ($intopic['pl_tid']) {
      if ($intopic['voted'] || $GLOBALS['inuserid']<=3 ||
           ($intopic['pl_enddate'] && $intopic['pl_enddate']<$GLOBALS['curtime'])) std_vote_resbegin($intopic);
      else std_vote_begin($intopic);
      $sql = "SELECT pv_id,pv_text, pv_count FROM ".$GLOBALS['DBprefix']."PollVariant pv ".
      " WHERE pv_plid=".$intopic['pl_id']." ORDER BY pv_id";
      $res =&db_query($sql,$link);
      $pv_text = array();
      while ($pv_data=&db_fetch_array($res)) {
        $pv_count[$pv_data['pv_id']]=$pv_data['pv_count'];
        $pv_total+=$pv_data['pv_count'];
        $pv_text[$pv_data['pv_id']]=$pv_data['pv_text'];
      }
      db_free_result($res);
      foreach ($pv_text as $curid=>$curtext) {
        if ($intopic['voted'] || $GLOBALS['inuserid']<=3 ||
           ($intopic['pl_enddate'] && $intopic['pl_enddate']<$GLOBALS['curtime'])) std_vote_resentry($pv_text[$curid],$pv_count[$curid],$pv_total);
        else std_vote_entry($pv_text[$curid],$curid);
      }
      if ($intopic['voted'] || $GLOBALS['inuserid']<=3 ||
           ($intopic['pl_enddate'] && $intopic['pl_enddate']<$GLOBALS['curtime'])) std_vote_resend($pv_total);
      else std_vote_end();
    }
  }

  require('display.php');
  load_style('display.php');
  display_topic_data($GLOBALS['intopic'],false);
}

function do_post() {
  if (isset($_POST['continue'])) {
    put_to_draft();
    return ;
  }
  process_post($GLOBALS['topic']);
  global $link;

  if (!getvar('preview')) {
    if (!is_premod_need(0)) topic_message(MSG_p_done,1);
    else topic_message(MSG_p_premoderated,1);
  }
}

function do_topic() {
  if (getvar("more")) { preview(1); return; }
  if (getvar("preview")) { preview(); return;}
  $inforum=$GLOBALS['inforum'];
  $forum=$GLOBALS['forum'];
  $inuserlevel=$GLOBALS['inuserlevel'];
  $inuser=$GLOBALS['inuser'];
  $inuserid=$GLOBALS['inuserid'];
  global $link;

  if ($inuserlevel<$inforum['f_ltopic']) error(MSG_e_t_norights);
  if (isset($_POST['continue'])) {
    put_to_draft();
    return ;
  }
  if (!getvar("t_title")) error(MSG_e_t_empty);
  if ($inforum['f_status']!=0) error(MSG_e_f_closed);

  $GLOBALS['ttitle']=&getvar('t_title');
  check_hurl();
  if (!$_POST['t_link']) {
  $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
  $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
  }
  $sqldata = build_sql("t_");
  $sqldata.= check_topic_params();

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Topic SET t_fid=\"$forum\", $sqldata";
  $res =&db_query($sql,$link);
  $GLOBALS['topic'] = db_insert_id($res);
  $topic = $GLOBALS['topic'];
  $premod=is_premod_need(1);
  
  $pid=process_post($GLOBALS['topic'],1);
  if (!$premod) topic_increment($GLOBALS['forum'],$topic,$pid);
  
  $is_vote=is_array($_POST['pl_text']);
  if ($is_vote) foreach ($_POST['pl_text'] as $curline) if ($curline) {
    $votecount++;
  }

  if (getvar("vote") && $is_vote && $votecount) {
    if ($GLOBALS['inuserlevel']<$inforum['f_lpoll']) error(MSG_e_v_norights);
    $pl_title=&getvar("pl_title");
    $pl_tid=$GLOBALS['topic'];
    if (getvar("voteend")) $pl_enddate=time()+getvar("voteend")*24*60*60;
    else $pl_enddate=0;
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Poll (pl_tid,pl_title,pl_enddate) VALUES ($pl_tid,\"$pl_title\",$pl_enddate)";
    $res =&db_query($sql,$link);
    $pl_plid=db_insert_id($res);

    $votevars = $_POST['pl_text'];
    foreach ($votevars as $curnumer=>$curvalue) {
      if ($curvalue) {
        $sqldata="($pl_plid,\"".db_slashes($curvalue)."\",0)";
        $sql = "INSERT INTO ".$GLOBALS['DBprefix']."PollVariant (pv_plid,pv_text,pv_count) VALUES $sqldata";
        $res =&db_query($sql,$link);
      }
    }
  }

  $GLOBALS['refpage']=build_url($GLOBALS['intopic']);
  if (!$premod) topic_message(MSG_t_done,1);
  else {
    $GLOBALS['refpage']=build_url($GLOBALS['forum']);
    message(MSG_t_premoderated,1);
  }
}

function edit_from_draft() {
  $data = get_from_draft();
  if ($data['t']) {
    view_topic();
    display_post_form(MSG_p_edit,$data,0);
  }
  else {
    std_post_form(MSG_p_create,$data['a'],$data,0);
  }
}

function std_newtopic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ltopic']) error(MSG_e_t_norights);
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lpoll'] && getvar("vote")) error(MSG_e_v_norights);
  $pdata['p__html']=($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lhtml']);
  $pdata['p__bcode']=$GLOBALS['inforum']['f_bcode'];
  $pdata['p__smiles']=$GLOBALS['inforum']['f_smiles'] && $GLOBALS['inuser']['u_usesmiles'];
  $pdata['p_signature']=$GLOBALS['inuser']['u_usesignature'];
  $votecount=array();
  if (getvar("vote")) for ($i=0; $i<$GLOBALS['opt_defvotecount']; $i++) array_push($votecount,$i);
  std_post_form(MSG_t_create,"do_topic",$pdata,$votecount);
}

function edit() {
  edit_post('std_post_form',MSG_p_edit);
}

function do_delete() {
  do_delete_comment();
}

function preview($type=0) {
  require('process.php');
  process_preview(false);
  std_post_form(MSG_preview,$GLOBALS['action'],$_POST,$counter);
}

function do_vote() {
  if ($GLOBALS['inuserlevel']<$inforum['f_lvote']) error(MSG_e_v_norightsvote);
  global $link;
  $intopic=$GLOBALS['intopic'];
  if ($intopic['voted']) error(MSG_e_v_already);
  $pvid=&getvar("pv_id");
  if (!$pvid) error(MSG_e_v_novariant);
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Vote (pvid,uid,tid) VALUES(\"".$pvid."\",\"".$GLOBALS['inuserid']."\",".$GLOBALS['topic'].")";
  $res =&db_query($sql,$link);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."PollVariant SET pv_count=pv_count+1 WHERE pv_id=\"$pvid\"";
  $res =&db_query($sql,$link);
  message(MSG_v_voted,1);
}

function do_print() {
  print_start();
  global $link;
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=\"".$GLOBALS['topic']."\" ORDER BY p__time";
  $res =&db_query($sql,$link);
  while ($pdata=&db_fetch_array($res)) {
    print_entry($pdata);
  }
  print_end();
}

function stdforum_locations($locations) {
  push_parents($locations,$GLOBALS['inforum']['f_parent']);
  if ($GLOBALS['action']=="stdforum_view") {
    if ($GLOBALS['topic']) {
      array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
      array_push($locations,$GLOBALS['intopic']['t_title']);
      $GLOBALS['rss_link']="rss.php?t=".$GLOBALS['topic']."&amp;count=".$GLOBALS['inuser']['u_mperpage'];
    }
    else {
      array_push($locations,$GLOBALS['inforum']['f_title']);
      $GLOBALS['rss_link']="rss.php?f=".$GLOBALS['forum']."&amp;count=".$GLOBALS['inuser']['u_tperpage'];
    }
  }
  elseif ($GLOBALS['action']=="std_newtopic") {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    array_push($locations,MSG_t_creating);
  }
  elseif ($GLOBALS['action']=="do_post") {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    array_push($locations,"<a href=\"index.php?t=".$GLOBALS['intopic']['t_id']."\">".$GLOBALS['intopic']['t_title']."</a>");
  }
  elseif ($GLOBALS['action']=="do_topic") {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    array_push($locations,MSG_t_creating);
  }
  elseif ($GLOBALS['action']=="edit" || $GLOBALS['action']=="do_edit_post") {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    array_push($locations,"<a href=\"index.php?t=".$GLOBALS['intopic']['t_id']."\">".$GLOBALS['intopic']['t_title']."</a>");
    array_push($locations,MSG_p_edit);
  }
  if (getvar("preview")) array_push($locations,MSG_preview);  
  return $locations;
}
