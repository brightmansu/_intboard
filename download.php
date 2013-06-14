<? /*

Download script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function download_view() {
  if ($GLOBALS['topic']) view_topic();
  else view_list();
}

function view_list() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  $forum=$GLOBALS['forum'];
  global $link;
  $order =&getvar("o");
  $direct=&getvar("desc");
  $start=&getvar("st");
  if (!$start) $start="0";
  $perpage=$GLOBALS['inuser']['u_aperpage'];
  $tcount=$GLOBALS['inforum']['f__tcount'];
  $pages =&build_pages($tcount,$start,$perpage,"index.php?f=$forum&o=$order&desc=$desc");
  if (!$order) $order=" posttime DESC";
  if ($start!="all") $limit = " LIMIT $start,$perpage";

  $sql = "SELECT t.*, t__views, dl.*, p1.p_uname AS u__name, p1.p__time AS posttime, t__ratingsum/NULLIF(t__ratingcount,0) AS trating, tv.tid AS visited, t__pcount AS pcount, p2.p__time AS lastpost ".
     "FROM ".$GLOBALS['DBprefix']."Download dl, ".$GLOBALS['DBprefix']."Post p1, ". $GLOBALS['DBprefix']."Post p2, ".$GLOBALS['DBprefix']."Topic t ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."TopicView tv ON (tv.tid=t.t_id AND tv.uid=".$GLOBALS['inuserid'].") ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."TopicVC tvc ON (tvc.tid=t_id) ".
     "WHERE t.t_fid=$forum AND t.t_id=dl.dl_tid AND t.t__lastpostid=p1.p_id AND t.t__startpostid=p2.p_id ".
     "ORDER BY t__sticky DESC, $order $direct $limit";
  $res =&db_query($sql,$link);

  soft_list_start($pages);
  while ($dldata=&db_fetch_array($res)) {
    soft_list_entry($dldata);
  }
  list($inforum,$autosub)=get_forum_sub();
  soft_list_end($pages,$inforum,$autosub);
}

function view_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  $forum=$GLOBALS['forum'];
  $topic=$GLOBALS['topic'];
  global $link;

  $rated=common_topic_view($topic);

  $sql = "SELECT dl.* FROM ".$GLOBALS['DBprefix']."Download dl WHERE dl_tid=$topic";
  $res =&db_query($sql,$link);
  $dldata=&db_fetch_array($res);
  db_free_result($res);
  $tdata=&$GLOBALS['intopic'];
  $sql = "SELECT p.*, u.u__name, u.u_id, file.* FROM ".$GLOBALS['DBprefix']."Post p ".
      "LEFT JOIN ".$GLOBALS['DBprefix']."User u ON (u_id=p_uid) ".
      "LEFT JOIN ".$GLOBALS['DBprefix']."File file ON (file_id=p_attach) ".
      "WHERE p.p_tid=$topic AND p.p__premoderate=0 AND p_id=".$GLOBALS['intopic']['t__startpostid'];
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  db_free_result($res);

  soft_display($tdata,$dldata,$pdata,$rated);
  
  load_style('display.php');
  require('display.php');
  display_comment_link($adata['a_disc_tid']);
  display_form($adata['a_disc_tid']);
}

function view_comments() {
  global $link;
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  
  require('display.php');
  load_style('display.php');
  
  $sql = "SELECT dl.* FROM ".$GLOBALS['DBprefix']."Download dl WHERE dl_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);
  $dldata=&db_fetch_array($res);
  db_free_result($res);
  
  if ($dldata['dl_disc_tid']) {
    $sql = "SELECT * FROM prefix_Topic WHERE t_id=".$adata['a_disc_tid'];
    $res =&db_query($sql,$link);
    $dtdata=&db_fetch_array($res);
    db_free_result($res);
    display_topic_data($tdata,false);
  }
  else display_topic_data($GLOBALS['intopic'],true);
}

function do_post() {
  global $link;
  if (isset($_POST['continue'])) {
      put_to_draft();
      return ;
  }
  $sql = "SELECT dl_disc_tid FROM ".$GLOBALS['DBprefix']."Download WHERE dl_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);
  list ($tid)=db_fetch_row($res);
  if (!$tid) $tid=$GLOBALS['topic'];
  process_post($tid);

  if (!getvar('preview')) {
    if (!is_premod_need()) {
      topic_message(MSG_dl_commentadded,1);
    }
    else {
      message(MSG_p_premoderated,1);
    }
  }
}

function edit_from_draft() {
  $data = get_from_draft();
  if ($data['t']) {
    view_topic();
    display_post_form(MSG_dl_editcoment,$data,0);
  }
  else {
    $forumlist=build_forum_select("f_ltopic",1);
    soft_edit_form($data,$data,$data,$data['a'],MSG_dl_editing,$forumlist);
  }
}

function add_program() {
  $forumlist=build_forum_select("f_ltopic",1);
  $pdata['p__bcode']=$GLOBALS['inforum']['f_bcode'];
  $pdata['p__smiles']=$GLOBALS['inforum']['f_smiles'];
  soft_edit_form($tdata,$adata,$pdata,"do_topic",MSG_dl_edit,$forumlist);
}

function do_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ltopic']) error(MSG_e_t_norights);
  if ($GLOBALS['inforum']['f_status']!=0) error(MSG_e_f_closed);
  if (isset($_POST['continue'])) {
    put_to_draft();
    return ;
  }
  if ($GLOBALS['inforum']['f_text'] && is_uploaded_file($_FILES['dlfile']['tmp_name']) && ($_FILES['dlfile']['size']>$GLOBALS['opt_maxfileattach'])) error(MSG_e_p_toobig);

  $forum=$GLOBALS['forum'];
  global $link;

  dl_check_params();
  check_hurl();
  if (!$_POST['t_link']) {
  $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
  $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
  }
  $sqldata = ", ".build_sql("t_");
  $sqldata.= check_topic_params();
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Topic SET t_fid=$forum $sqldata";
  $res =&db_query($sql,$link);
  $tid = db_insert_id($res);

  $pid=process_post($tid,1);
  $premod=is_premod_need();
  if (!$premod) topic_increment($forum,$tid,$pid);

  if ($GLOBALS['inforum']['f_text'] && is_uploaded_file($_FILES['dlfile']['tmp_name'])) {
     if ($_FILES['dlfile']['size']>$GLOBALS['opt_maxfileattach']) error(MSG_e_p_toobig);
     $_FILES['dlfile']['name']=str_replace(".php",".php.txt",$_FILES['dlfile']['name']);
     $_FILES['dlfile']['name']=str_replace(".php3",".php3.txt",$_FILES['dlfile']['name']);
     $_FILES['dlfile']['name']=str_replace(".phtml",".phtml.txt",$_FILES['dlfile']['name']);
     $_FILES['dlfile']['name']=str_replace(".phtm",".phtm.txt",$_FILES['dlfile']['name']);
     $_FILES['dlfile']['name']=str_replace(' ','_',transliterate($_FILES['dlfile']['name']));

    move_uploaded_file($_FILES['dlfile']['tmp_name'],$GLOBALS['inforum']['f_text']."/".$_FILES['dlfile']['name']);
    eval('chmod($GLOBALS[\'inforum\'][\'f_text\']."/".$_FILES[\'dlfile\'][\'name\'],0644);');
    $_POST['dl_url']=$GLOBALS['inforum']['f_url']."/".$_FILES['dlfile']['name'];
  }

  $sqldata = build_sql("dl_");
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Download SET dl_tid=$tid, $sqldata";
  $res =&db_query($sql,$link);

  $newfid =&getvar("fid");
  if ($newfid) {
    $title =&getvar("t_title");
    $descr = MSG_dl_discussion;
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Topic SET t_fid=\"$newfid\", t_title=\"$title\", t_descr=\"$descr\"";
    $res =&db_query($sql,$link);
    $newtid = db_insert_id($res);

    $text = MSG_dl_disclink." \\\"<a href=\\\"index.php?t=$newtid\\\">$title</a>\\\"";
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Post SET p_tid=$newtid, p_text=\"$text\", p__time=".$GLOBALS['curtime'].", p__html=1, p_uid=2, p_uname=\"System\"";
    $res =&db_query($sql,$link);

    $sql = "UPDATE ".$GLOBALS['DBprefix']."Download SET dl_disc_tid=$newtid WHERE dl_tid=$tid";
    $res =&db_query($sql,$link);

    topic_increment($newfid,$newtid,$newpid);
  }

  if (!$premod) {
    $GLOBALS['intopic']['t_id']=$tid;
    $GLOBALS['intopic']['t_link']=&getvar('t_link');
    topic_message(MSG_dl_added,1);
  }
  else {
    $GLOBALS['refpage']=build_url($GLOBALS['forum']);
    message(MSG_t_premoderated,1);
  }
}

function edit_program() {
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

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Download WHERE dl_tid=$tid";
  $res =&db_query($sql,$link);
  $dldata =&db_fetch_array($res);

//  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicView WHERE tid=$tid";
//  $res =&db_query($sql,$link);
  soft_edit_form($tdata,$dldata,$pdata,"do_edit",MSG_dl_edit);
}

function do_edit() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['intopic']['t_author']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) error(MSG_e_t_norights);

  if (!getvar("t_title")) error(MSG_e_dl_emptytitle);
  if (!getvar("p_text")) error(MSG_e_dl_emptytext);
  if (!getvar("dl_url")) error(MSG_e_dl_nourl);
//  if (!getvar("dl_homepage")) error(MSG_e_dl_nohomepage);
  $forum=$GLOBALS['forum'];
  global $link;

  dl_check_params();
  if (!$_POST['t_link']) {
  $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
  $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
  }
  $sqldata = build_sql("t_");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET $sqldata WHERE t_id=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  $sqldata = build_sql("dl_");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Download SET $sqldata WHERE dl_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  $sql = "SELECT p_id,p_attach FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=".$GLOBALS['topic']." AND p_id=".$GLOBALS['intopic']['t__startpostid'];
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
  $sqldata.=", p_attach=$pattach";
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET $sqldata WHERE p_id=\"$pid\" AND p_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  topic_message(MSG_a_saved,1);
}

function do_delete_program() {
  if ($inuserlevel<$inforum['f_lmoderate']) error(MSG_e_t_norights);
  $tid=$GLOBALS['topic'];
  global $link;

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Download WHERE dl_tid=$tid";
  $res =&db_query($sql,$link);

  delete_topic($tid);
  forum_resync($GLOBALS['forum']);
  $GLOBALS['refpage']="index.php?f=".$GLOBALS['forum'];
  message(MSG_t_deleted,1);
}

function delete_confirm() {
  $params['t']=$GLOBALS['topic'];
  confirm("download","do_delete_program",$params,MSG_dl_deleteconfirm." ".$GLOBALS['intopic']['t_title'],"index.php?t=".$GLOBALS['topic']);
}

function do_get() {
  global $link;
  $tid=$GLOBALS['topic'];
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Download SET dl__downloads=dl__downloads+1 WHERE dl_tid=$tid";
  $res =&db_query($sql,$link);
  $sql = "SELECT dl_url FROM ".$GLOBALS['DBprefix']."Download WHERE dl_tid=$tid";
  $res =&db_query($sql,$link);
  list($url)=db_fetch_row($res);
  header("Location: $url");
  exit();
}

function dl_check_params() {
  $home=&getvar('dl_homepage');
  $url=&getvar('dl_url');
  if (!getvar('dl_url') && !is_uploaded_file($_FILES['dlfile']['tmp_name'])) error(MSG_e_dl_nourl);
  require('parser.php');
  if (!check_url($home)) error(MSG_e_dl_homepage);
  if (!check_url($url)) error(MSG_e_dl_url);
}

function get_status($url) {
  $urldata=parse_url($url);
  if ($urldata['scheme']=="ftp") {
    if (!$ftpuser=$urldata['user']) $ftpuser="anonymous";
    if (!$ftppass=$urldata['pass']) $ftppass=$GLOBALS['opt_mailout'];
    $connect=ftp_connect($urldata['host'],$urldata['port'],10);
    if (!$connect) $result=-1;
    else {
      $dir=substr($urldata['path'],0,strrpos($urldata['path'],"/"));
      $files = ftp_nlist($connect,$dir);
      if (!is_array($files) || array_search($urldata['file'],$files)===false) $result=404;
      else $result=200;
      ftp_close($connect);
    }
  }
  else {
    if (!$port=$urldata['port']) $port=80;
    $connect=fsockopen($urldata['host'],$port,$errno,$errstr,10);
    if (!$connect) $result=-1;
    else {
      $request=sprintf("HEAD %s HTTP/1.0\r\nHost: %s\r\n\r\n",$urldata['path'],$urldata['host']);
      fputs($connect,$request);
      while (!feof($connect)) $answer.=fgets($connect);
      $result=preg_replace("/HTTP\/1\.\d (\d+?) .*\r\n/s","$1",$answer);
      fclose($connect);
    }
  }
  return $result;
}

function download_check() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;

  $sql = "SELECT t_title,dl_url FROM ".$GLOBALS['DBprefix']."Download dl, ".$GLOBALS['DBprefix']."Topic t ".
  " WHERE dl_tid=t_id AND t_fid=".$GLOBALS['forum'];
  $res =&db_query($sql,$link);
  soft_check_start();
  while ($dldata=&db_fetch_array($res)) {
    $result=get_status($dldata['dl_url']);
    soft_check_entry($dldata,$result);
  }
  soft_check_end();
}

function edit() {
  edit_comment();
}

function edit_comment() {
  edit_post('soft_discuss_form',MSG_dl_editcomment,"do_edit_post");
}

function download_locations($locations) {
  if ($GLOBALS['topic'] && calc_start_offset()>0 && getvar('st')=='new') $GLOBALS['action']='view_comments';
  push_parents($locations,$GLOBALS['inforum']['f_parent']);
  if ($GLOBALS['topic']) {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    if ($GLOBALS['action']=='download_view') array_push($locations,$GLOBALS['intopic']['t_title']);
    else array_push($locations,"<a href=\"".build_url($GLOBALS['intopic'])."\">".$GLOBALS['intopic']['t_title']."</a>");
    $GLOBALS['rss_link']="rss.php?t=".$GLOBALS['topic']."&amp;count=".$GLOBALS['inuser']['u_mperpage'];
  }
  else {
    if ($GLOBALS['action']=="download_view") array_push($locations,$GLOBALS['inforum']['f_title']);
    else array_push($locations,"<a href=\"index.php?f=".$GLOBALS['forum']."\">".$GLOBALS['inforum']['f_title']."</a>");
    $GLOBALS['rss_link']="rss.php?a=newtopic&amp;f=".$GLOBALS['forum']."&amp;count=".$GLOBALS['inuser']['u_aperpage'];
  }
  if ($GLOBALS['action']=="edit_program") {
    array_push($locations,MSG_dl_edit);
  }
  elseif ($GLOBALS['action']=="edit_comment" || $GLOBALS['action']=="do_edit_post") {
    array_push($locations,MSG_dl_editcomment);
  }
  elseif ($GLOBALS['action']=="add_program") {
    array_push($locations,MSG_dl_adding);
  }
  elseif ($GLOBALS['action']=="download_check") {
    array_push($locations,MSG_dl_check);
  }
  elseif ($GLOBALS['action']=="view_comments") {
    array_push($locations,MSG_a_viewcomments);
  }
  if (getvar("preview")) array_push($locations,MSG_preview);  
  return $locations;
}