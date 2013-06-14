<? /*

Encyclopedia script for Intellect Board 2 Project

(C) 2004, 2005, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function epedia_view() {
  if ($GLOBALS['topic']) view_topic();
  else view_list();
}

function build_letter_list($letter,$forum) {
  global $link;
  $sql = "SELECT DISTINCT SUBSTRING(UPPER(t_title),1,1) AS letter FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=$forum ORDER BY letter";
  $res =&db_query($sql,$link);
  $buffer="";
  while ($curletter=db_fetch_row($res)) {
    if ($curletter[0]!=$letter) $buffer.="<a href=\"".build_url($GLOBALS['inforum'],"l=".urlencode($curletter[0]))."\">".$curletter[0]."</a> &nbsp; ";
    else $buffer.=$curletter[0]." &nbsp; ";
  }
  return $buffer;
}

function view_list() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  global $link;
  $letter=&getvar('l');
  $forum=$GLOBALS['forum'];
  if (!$letter) {
    $sql = "SELECT SUBSTRING(t_title,1,1) FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=$forum ORDER BY t_title LIMIT 1";
    $res =&db_query($sql,$link);
    list($letter)=db_fetch_row($res);
    db_free_result($res);
    $_GET['l']=$letter;
  }
  $letterlist = build_letter_list($letter,$forum);

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=$forum AND t_title LIKE \"$letter%\" ORDER BY t_title";
  $res =&db_query($sql,$link);
  $total = db_num_rows($res);
  encyclo_letters($letterlist);
  encyclo_start();
  $counter=1;
  $columns=2;
  while ($tdata=&db_fetch_array($res)) {
    encyclo_entry($tdata);
    if (floor($total/$columns)==$counter) encyclo_newcol();
    $counter++;
  }
  if ($total<1) encyclo_newcol();
  list($inforum,$autosub)=get_forum_sub();
  encyclo_end($inforum,$autosub);
}

function view_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  global $link;
  $forum=$GLOBALS['forum'];
  $letterlist = build_letter_list($letter,$forum);
  $topic=$GLOBALS['topic'];
  common_topic_view($topic);

  $sql = "SELECT p.*, file.* FROM ".$GLOBALS['DBprefix']."Post p ".
         "LEFT JOIN ".$GLOBALS['DBprefix']."File file ON (file_id=p_attach) ".
         "WHERE p_tid=$topic LIMIT 1";
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  db_free_result($res);
  encyclo_letters($letterlist);
  encyclo_article($pdata,$GLOBALS['intopic']);
}

function edit_from_draft() {
  $data = get_from_draft();
  encyclo_form($data,$data,$data['a'],MSG_en_edit);
}

function add_article() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ltopic']) error(MSG_e_f_norightstopic);
  $pdata['p__html']=($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lhtml']);
  $pdata['p__bcode']=$GLOBALS['inforum']['f_bcode'];
  $pdata['p__smiles']=$GLOBALS['inforum']['f_smiles'];
  $pdata['p_signature']=$GLOBALS['inuser']['u_usesignature'];
  encyclo_form(NULL,$pdata,"do_add",MSG_en_adding);
}

function edit_article() {
    if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_f_norightstopic);
    global $link;
    $topic=$GLOBALS['topic'];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$topic LIMIT 1";
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  encyclo_form($GLOBALS['intopic'],$pdata,"do_edit",MSG_en_edit);
}

function set_links() {
  $text=$_POST['p_text'];
  global $link;
  $forum=$GLOBALS['forum'];
  preg_match_all("/\[word\](.*?)\[\/word\]/is",$text,$matches);
    foreach ($matches[1] as $curmatch) {
    $sql = "SELECT t_id,t_link,f_id,f_link FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum WHERE t_fid=f_id AND f_id=$forum AND t_title=\"".addslashes($curmatch)."\"";
    $res =&db_query($sql,$link);
    if (db_num_rows($res)>0) {
      $tdata=&db_fetch_array($res);
      $text=str_replace("[word]".$curmatch."[/word]","[url2=".$GLOBALS['opt_url'].'/'.build_url($tdata)."]".$curmatch."[/url2]",$text);
    }
    else $text=str_replace("[word]".$curmatch."[/word]",$curmatch,$text);
  }
  preg_match_all("/\[word=([^\]]*?)\](.*?)\[\/word\]/is",$text,$matches);
  $i=0;
    foreach ($matches[2] as $curmatch) {
    $sql = "SELECT t_id, t_link, f_id, f_link FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum WHERE t_fid=f_id AND f_id=$forum AND t_title=\"".addslashes($matches[1][$i])."\"";
    $res =&db_query($sql,$link);
    if (db_num_rows($res)>0) {
      $tdata=db_fetch_array($res);
      $text=str_replace("[word=".$matches[1][$i]."]".$curmatch."[/word]","[url2=".$GLOBALS['opt_url']."/".build_url($tdata)."]".$curmatch."[/url2]",$text);
    }
    else $text=str_replace("[word=".$matches[1][$i]."]".$curmatch."[/word]",$curmatch,$text);
    $i++;
  }
  $_POST['p_text']=$text;
}

function do_add() {
  if ($GLOBALS['inuserlevel']<$inforum['f_ltopic']) error(MSG_e_t_norights);
  if (isset($_POST['continue'])) {
    put_to_draft();
    return ;
  }
  if (!getvar("t_title")) error(MSG_e_en_emptytitle);
  if ($GLOBALS['inforum']['f_status']!=0) error(MSG_e_f_closed);
  if (!getvar("p_text")) error(MSG_e_en_emptytext);
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

  set_links();
  $pid=process_post($tid);
  topic_increment($forum,$tid,$pid);

  $GLOBALS['intopic']['t_title']=&getvar('t_title');
  $GLOBALS['intopic']['t_id']=$tid;
  $GLOBALS['intopic']['t_link']=&getvar('t_link');
  $GLOBALS['refpage']=build_url($GLOBALS['intopic']);
  topic_message(MSG_en_added,1);
}

function do_edit() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['intopic']['t_author']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) error(MSG_e_t_norights);
  if (isset($_POST['continue'])) {
    put_to_draft();
    return ;
  }
  if (!getvar("t_title")) error(MSG_e_en_emptytitle);
  if (!getvar("p_text")) error(MSG_e_en_emptytext);
  $forum=$GLOBALS['forum'];
  global $link;

  check_hurl();
  if (!$_POST['t_link']) {
  $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
  $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
  }
  $sqldata = build_sql("t_");
  $sqldata.= check_topic_params();
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET $sqldata WHERE t_id=".$GLOBALS['topic'];
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

  set_links();
  $pid =&getvar("p");
  $sqldata = build_sql("p_");
  $sqldata.= check_post_params();
  $sqldata.=", p_attach=$pattach";
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET $sqldata WHERE p_id=\"$pid\"";
  $res =&db_query($sql,$link);

  if (!$GLOBALS['inforum']['f_premoderate'] || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
    $GLOBALS['refpage']=build_url($GLOBALS['intopic']);
    topic_message(MSG_en_saved,1);
  }
  else {
    $GLOBALS['refpage']=build_url($GLOBALS['inforum']);
    message(MSG_t_premoderated,1);
  }
}

function do_print() {
  global $link;
  $tid=$GLOBALS['topic'];

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$tid";
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  db_free_result($res);

  article_print_form($GLOBALS['intopic'],$pdata);
}

function delete_confirm() {
  $params['t']=$GLOBALS['topic'];
  confirm("epedia","do_delete_article",$params,MSG_en_deleteconfirm." ".$GLOBALS['intopic']['t_title'],build_url($GLOBALS['intopic']));
}

function do_delete_article() {
  if ($inuserlevel<$inforum['f_lmoderate']) error(MSG_e_t_norights);
  $tid=$GLOBALS['topic'];

  delete_topic($tid);

  forum_resync($GLOBALS['forum']);
  $GLOBALS['refpage']=build_url($GLOBALS['inforum']);
  message(MSG_t_deleted);
}

function epedia_locations($locations) {
  push_parents($locations,$GLOBALS['inforum']['f_parent']);
  if ($GLOBALS['action']=="epedia_view") {
    if ($GLOBALS['topic']) {
      array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
      array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."?l=".substr($GLOBALS['intopic']['t_title'],0,1)."\">".substr($GLOBALS['intopic']['t_title'],0,1)."</a>");
      array_push($locations,$GLOBALS['intopic']['t_title']);
    }
    else {
      array_push($locations,$GLOBALS['inforum']['f_title']);
      array_push($locations,$_GET['l']);
      $GLOBALS['rss_link']="rss.php?a=newtopic&amp;f=".$GLOBALS['forum']."&amp;count=".$GLOBALS['inuser']['u_aperpage'];
    }
  }
  if ($GLOBALS['action']=="edit_article") {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    array_push($locations,"<a href=\"".build_url($GLOBALS['intopic'])."\">".$GLOBALS['intopic']['t_title']."</a>");
    array_push($locations,MSG_en_edit);
  }
  elseif ($GLOBALS['action']=="add_article") {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    array_push($locations,MSG_en_adding);
  }
  return $locations;
}