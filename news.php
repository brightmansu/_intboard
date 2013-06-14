<? /*

News script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function news_view() {
  if ($GLOBALS['topic']) view_topic();
  else view_list();
}

function view_list() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  $forum=$GLOBALS['forum'];
  global $link;
  
  require('rss_lib.php');
  import_rss('System',2,$GLOBALS['forum']);
  
  $order =&getvar("o");
  $direct=&getvar("desc");
  $sql = "SELECT MIN(p__time) FROM ".$GLOBALS['DBprefix']."Post, ".$GLOBALS['DBprefix']."Topic WHERE p_id=t__startpostid AND t_fid=$forum ";
  $res =&db_query($sql,$link);
  list($mindate)=db_fetch_row($res);

  if ($vdate=&getvar("vdate")) {
    list($day,$month,$year)=explode('.',$vdate);
    $vardate=mktime(0,0,0,$month,$day,$year);
  }
  else $vardate=mktime(0,0,0,date("n",$GLOBALS['curtime']),date("j",$GLOBALS['curtime']),date("Y",$GLOBALS['curtime']));
  $startdate = mktime(0,0,0,date("n",$vardate),1,date("Y",$vardate));
  $enddate = mktime(0,0,0,date("n",$vardate)+1,1,date("Y",$vardate))-1;

  $sql = "SELECT COUNT(*), DAYOFMONTH(FROM_UNIXTIME(p__time)) AS curday FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Post ".
  "WHERE t_fid=".$forum." AND t__pcount>0 AND t__startpostid=p_id AND ".
  " p__time>=".$startdate." AND p__time<=".$enddate.
  " GROUP BY 2 ";
  $res=&db_query($sql,$link);
  while ($tmp=db_fetch_row($res)) {
    $calnd[$tmp[1]]=$tmp[0];
  }
  db_free_result($res);

  if ($mindate) format_calendar($vardate,$mindate,build_url($GLOBALS['inforum']),$calnd);
  if (getvar('vdate')) $timelimit=" AND p__time>=".$vardate." AND p__time<=".($vardate+24*60*60);
  else $timelimit=" AND p__time<=".($vardate+24*60*60);

  $start=&getvar("st");
  if (!$start) $start="0";
  if (!$perpage) $perpage=$GLOBALS['inuser']['u_aperpage'];
  $tcount=$GLOBALS['inforum']['f__tcount'];
  $pages =&build_pages($tcount,$start,$perpage,"index.php?f=$forum&o=$order&desc=$desc");
  if ($start!="all") $limit = " LIMIT $start,$perpage";
  if (!$order) $order=" p__time DESC";

  $sql = "SELECT t.*, t__views, p.*, file.*, t__pcount-1 AS pcount, t__ratingsum/NULLIF(t__ratingcount,0) AS trating FROM  ".
     $GLOBALS['DBprefix']."Topic t ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."TopicVC tvc ON (tvc.tid=t.t_id) ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."Post p ON (t.t__startpostid=p.p_id) ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."File file ON (file_id=p_attach) ".
     "WHERE t.t_fid=$forum AND t__pcount>0 $timelimit ".
     "ORDER BY $order".$direct." $limit";
  $res =&db_query($sql,$link);

  news_list_start($pages);
  while ($ndata=&db_fetch_array($res)) {
    news_list_entry($ndata);
  }
  if (db_num_rows($res)==0) news_list_noentries();
  list($inforum,$autosub)=get_forum_sub();
  news_list_end($inforum,$autosub);
}

function view_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
//  $forum=$GLOBALS['forum'];
//  $topic=$GLOBALS['topic'];
//  global $link;

//  $rated=common_topic_view($topic);
  
  require('display.php');
  load_style('display.php');
  
  $GLOBALS['intopic']['t__stickypost']=1;  
  
  display_topic_data($GLOBALS['intopic'],false);
  
/*  $tdata=$GLOBALS['intopic'];
  $sql = "SELECT p.*, file_type, u.u__name, u.u_id FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."Post p ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."File ON (file_id=p_attach) ".
  "WHERE p.p_tid=$topic AND u.u_id=p.p_uid AND p.p__premoderate=0 ORDER BY p_id";
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  news_display($tdata,$pdata,$rated);
  $numrows=db_num_rows($res);

  if ($numrows>1) {
    news_discuss_start();
    while ($nentry=&db_fetch_array($res)) {
      news_discuss_entry($nentry);
    }
    news_discuss_end();
  }

  $trash['p__bcode']=$GLOBALS['inforum']['f_bcode'];
  $trash['p__smiles']=$GLOBALS['inforum']['f_smiles'];
  if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpost'] && !$tdata['t__status'] && $GLOBALS['action']!='edit_from_draft') news_discuss_form($trash,MSG_n_addcoment,"do_post");*/
}

function do_post() {
  if (isset($_POST['continue'])) {
      put_to_draft();
      return ;
  }
  process_post($GLOBALS['topic']);
  if (!getvar('preview')) {
    if (!is_premod_need(0)) {
      topic_message(MSG_n_commentadded,1);
    }
    else {
      message(MSG_t_premoderated,1);
    }
  }
}

function edit_from_draft() {
  $data = get_from_draft();
  if ($data['t']) {
    view_topic();
    display_post_form(MSG_n_editcoment,$data,0);
  }
  else  news_edit_form($data,$data,$data['a'],MSG_n_edit);
}

function add_news() {
  $pdata['p__bcode']=$GLOBALS['inforum']['f_bcode'];
  $pdata['p__smiles']=$GLOBALS['inforum']['f_smiles'];
  news_edit_form($tdata,$pdata,"do_topic",MSG_n_adding);
}

function do_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ltopic']) error(MSG_e_t_norights);
  if ($GLOBALS['inforum']['f_status']!=0) error(MSG_e_f_closed);
  if (isset($_POST['continue'])) {
      put_to_draft();
      return ;
  }
  if (isset($_POST['preview'])) {
    news_preview();
    return;
  }
  if (!getvar("t_title")) error(MSG_e_n_emptytitle);
  $forum=$GLOBALS['forum'];
  global $link;

  check_hurl();
  if (!$_POST['t_link']) {
  $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
  $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
  }
  $sqldata = build_sql("t_");
  $sqldata.= check_topic_params();

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Topic SET t_fid=$forum, $sqldata";
  $res =&db_query($sql,$link);
  $tid = db_insert_id($res);

  $pid=process_post($tid,1);
  $premod=is_premod_need(1);
  if (!$premod) topic_increment($forum,$tid,$pid);

  if (!$premod) {
    $GLOBALS['intopic']['t_id']=$tid;
    $GLOBALS['intopic']['t_link']=&getvar('t_link');
    topic_message(MSG_n_added,1);
  }
  else {
    $GLOBALS['refpage']=build_url($GLOBALS['inforum']);
    message(MSG_t_premoderated,1);
  }
}

function edit_news() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['intopic']['t_author']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) error(MSG_e_t_norights);
  global $link;
  $tid=$GLOBALS['topic'];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Topic WHERE t_id=$tid";
  $res =&db_query($sql,$link);
  $tdata =&db_fetch_array($res);
  db_free_result($res);

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$tid AND p_id=".$tdata['t__startpostid'];
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  db_free_result($res);

  news_edit_form($tdata,$pdata,"do_edit",MSG_n_edit);
}

function do_edit() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['intopic']['t_author']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) error(MSG_e_t_norights);
  if (isset($_POST['continue'])) {
      put_to_draft();
      return ;
  }
  if (isset($_POST['preview'])) {
    news_preview();
    return;
  }
  if (!getvar("t_title")) error(MSG_e_n_emptytitle);
  if (!getvar("p_text")) error(MSG_e_n_emptytext);
  $forum=$GLOBALS['forum'];
  global $link;

  check_hurl();
  if (!$_POST['t_link']) {
  $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
  $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
  }
  $sqldata = build_sql("t_");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET $sqldata WHERE t_id=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  $sql = "SELECT p_id,p_attach FROM ".$GLOBALS['DBprefix']."Post WHERE p_id=".$GLOBALS['intopic']['t__startpostid'];
  $res =&db_query($sql,$link);
  list($pid,$pattach)=db_fetch_row($res);

  if (is_uploaded_file($_FILES['attach']['tmp_name'])) {
    if ($inuserlevel<$inforum['f_lattach']) error(MSG_e_p_norightsattach);
    if ($inforum['f_attachpics']) {
      check_image("attach",$GLOBALS['opt_maxfileattach'],0,0,MSG_e_p_toobig,MSG_e_p_onlypics,"");
    }
    elseif ($_FILES['attach']['size']>$GLOBALS['opt_maxfileattach']) error(MSG_e_p_toobig);
  }
  $pattach = handle_upload($_FILES['attach'],$pattach,getvar("delattach"));

  $sqldata = build_sql("p_");
  $sqldata.= check_post_params();
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET $sqldata, p_attach=$pattach WHERE p_id=\"$pid\" AND p_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  topic_message(MSG_n_saved,1);
}

function do_delete_news() {
  if ($inuserlevel<$inforum['f_lmoderate']) error(MSG_e_t_norights);
  $tid=$GLOBALS['topic'];
  delete_topic($tid);
  $GLOBALS['refpage']="index.php?f=".$GLOBALS['forum'];
  forum_resync($GLOBALS['forum']);
  message(MSG_t_deleted,1);
}

function do_print() {
  global $link;
  $tid=$GLOBALS['topic'];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Topic WHERE t_id=$tid";
  $res =&db_query($sql,$link);
  $tdata =&db_fetch_array($res);
  db_free_result($res);

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$tid";
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  db_free_result($res);

  news_print_form($tdata,$pdata);
}

function delete_confirm() {
  $params['t']=$GLOBALS['topic'];
  confirm("news","do_delete_news",$params,MSG_n_deleteconfirm." ".$GLOBALS['intopic']['t_title'],"index.php?t=".$GLOBALS['topic']);
}

function edit() {
  edit_comment();
}

function edit_comment() {
  edit_post("news_discuss_form",MSG_n_editcoment,"do_edit_post");
}

function news_preview() {
  news_list_start('');
  $_POST['p__time']=$GLOBALS['curtime'];
  news_list_entry($_POST);
  list($inforum,$autosub)=get_forum_sub();
  news_list_end($GLOBALS['inforum'],$autosub);
  news_edit_form($_POST,$_POST,"do_topic",MSG_n_adding);  
}

function news_locations($locations) {
  push_parents($locations,$GLOBALS['inforum']['f_parent']);
  if ($GLOBALS['topic']) {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    if ($GLOBALS['action']=='news_view') array_push($locations,$GLOBALS['intopic']['t_title']);
    else array_push($locations,"<a href=\"".build_url($GLOBALS['intopic'])."\">".$GLOBALS['intopic']['t_title']."</a>");
    if ($GLOBALS['action']=='news_view') $GLOBALS['rss_link']="rss.php?t=".$GLOBALS['topic']."&amp;count=".$GLOBALS['inuser']['u_mperpage'];
  }
  else {
    if ($GLOBALS['action']!='news_view') {
      array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    }
    else array_push($locations,$GLOBALS['inforum']['f_title']);
    $GLOBALS['rss_link']="rss.php?f=".$GLOBALS['forum']."&amp;count=".$GLOBALS['inuser']['u_tperpage'];
  }
  if ($GLOBALS['action']=="add_news") {
    array_push($locations,MSG_n_adding);
  }
  if ($GLOBALS['action']=="edit_news") {
    array_push($locations,MSG_n_edit);
  }
  elseif ($GLOBALS['action']=="edit_comment" || $GLOBALS['action']=="do_edit_post") {
    array_push($locations,MSG_n_editcomment);
  }
  if (getvar("preview")) array_push($locations,MSG_preview);  
  return $locations;
}