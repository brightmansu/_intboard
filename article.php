<? /*

Article script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function article_view() {
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
  if (!$perpage) $perpage=$GLOBALS['inuser']['u_aperpage'];
  $tcount=$GLOBALS['inforum']['f__tcount'];
  $pages =&build_pages($tcount,$start,$perpage,"index.php?f=$forum&o=$order&desc=$desc");
  if ($start!="all") $limit = " LIMIT $start,$perpage";
  if (!$order) $order=" t__lasttime DESC";

  $sql = "SELECT t.*, t__views, a.*, p1.p_uname AS u__name, p1.p__time AS posttime, t__ratingsum/NULLIF(t__ratingcount,0) AS trating, tv.tid AS visited, t__pcount AS pcount, p2.p__time AS lastpost ".
     "FROM ".$GLOBALS['DBprefix']."Post p2, ".
     $GLOBALS['DBprefix']."Article a, ".$GLOBALS['DBprefix']."Post p1, ". $GLOBALS['DBprefix']."Topic t ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."TopicView tv ON (tv.tid=t.t_id AND tv.uid=".$GLOBALS['inuserid'].") ".
     "LEFT JOIN ".$GLOBALS['DBprefix']."TopicVC tvc ON (tvc.tid=t.t_id) ".
     "WHERE t.t_fid=$forum AND t.t_id=a.a_tid AND t.t__lastpostid=p2.p_id AND t.t__startpostid=p1.p_id ".
     "ORDER BY t__sticky DESC, $order $direct $limit";
  $res =&db_query($sql,$link);

  article_list_start($pages);
  while ($adata=&db_fetch_array($res)) {
    article_list_entry($adata);
  }
  list($inforum,$autosub)=get_forum_sub();  
  article_list_end($pages,$inforum,$autosub);
}

function view_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  $forum=$GLOBALS['forum'];
  $topic=$GLOBALS['topic'];
  global $link;
  
  $rated=common_topic_view($topic);

  $sql = "SELECT a.* FROM ".$GLOBALS['DBprefix']."Article a WHERE a_tid=$topic";
  $res =&db_query($sql,$link);
  $adata=&db_fetch_array($res);
  db_free_result($res);
  $tdata=&$GLOBALS['intopic'];
  $sql = "SELECT p.*, u.u__name, u.u_id FROM ".$GLOBALS['DBprefix']."Post p ".
      "LEFT JOIN ".$GLOBALS['DBprefix']."User u ON (u_id=p_uid) ".
      "WHERE p.p_tid=$topic AND p.p__premoderate=0 AND p_id=".$GLOBALS['intopic']['t__startpostid'];
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  db_free_result($res);

  $start=&getvar("pg");
  $list=split_article($pdata['p_text'],$pdata['p__html']);
  $pages = build_article_pages($start,$list,$tdata['t_id']);
  $pdata['p_text']=article_subtext($start,$list,$pdata['p_text']);

  article_display($tdata,$adata,$pdata,$rated,$pages);
/*  $numrows=db_num_rows($res);
  if ($adata['a_disc_tid']) {
    $sql = "SELECT p.*, u.u__name, u.u_id FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."User u ".
        "WHERE p.p_tid=".$adata['a_disc_tid']." AND u.u_id=p.p_uid AND p_uid<>2 AND p__premoderate=0";
    $res =&db_query($sql,$link);
    $numrows=db_num_rows($res)+1;
  }*/
  
  load_style('display.php');
  require('display.php');
  display_comment_link($adata['a_disc_tid']);
  display_form($adata['a_disc_tid']);
  
/*  if ($numrows>1 || $adata['a_disc_tid']) {
    article_discuss_start();
    while ($aentry=&db_fetch_array($res)) {
      article_discuss_entry($aentry);
    }
    article_discuss_end($adata['a_disc_tid']);
  }
  $trash['p__html']=$GLOBALS['inforum']['f_html'];
  $trash['p__bcode']=$GLOBALS['inforum']['f_bcode'];
  $trash['p__smiles']=$GLOBALS['inforum']['f_smiles'];
  if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpost'] && !$adata['a_disc_tid'] && !$tdata['t__status'] && $GLOBALS['action']!='edit_from_draft') article_discuss_form($trash,MSG_a_addcoment,"do_post");*/
  
}

function view_comments() {
  global $link;
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  
  require('display.php');
  load_style('display.php');
  
  $sql = "SELECT a.* FROM ".$GLOBALS['DBprefix']."Article a WHERE a_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);
  $adata=&db_fetch_array($res);
  db_free_result($res);
  
  if ($adata['a_disc_tid']) {
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Topic WHERE t_id=".$adata['a_disc_tid'];
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
  $sql = "SELECT a_disc_tid FROM ".$GLOBALS['DBprefix']."Article WHERE a_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);
  list ($tid)=db_fetch_row($res);
  if (!$tid) $tid=$GLOBALS['topic'];
  process_post($tid);
  if (!getvar('preview')) {
    if (!is_premod_need(0)) {
      topic_message(MSG_a_commentadded,1);
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
    display_post_form(MSG_dl_editcomment,$data,0);
  }
  else {
    $forumlist=build_forum_select("f_ltopic",1);
    article_edit_form($data,$data,$data,$data['a'],MSG_a_edit,$forumlist);
  }
}

function add_article() {
  $forumlist=build_forum_select("f_ltopic",1);
  $pdata['p__bcode']=$GLOBALS['inforum']['f_bcode'];
  $pdata['p__smiles']=$GLOBALS['inforum']['f_smiles'];
  article_edit_form($tdata,$adata,$pdata,"do_topic",MSG_a_adding,$forumlist);
}

function do_topic() {
  if ($GLOBALS['inuserlevel']<$inforum['f_ltopic']) error(MSG_e_t_norights);
  if (isset($_POST['continue'])) {
    put_to_draft();
    return ;
  }
  if (!getvar("a_author")) { //error(MSG_e_a_noauthor);
    $_POST['a_author']=$GLOBALS['inuser']['u__name'];
  }
  if (getvar('nobr')) clear_br($_POST['p_text']);
  if (isset($_POST['preview'])) {
    article_preview();
    return;
  }
  if (!getvar("t_title")) { // error(MSG_e_a_emptytitle);
    $_POST['t_title']=substr($_POST['p_text'],0,20)."...";
  }
  if ($GLOBALS['inforum']['f_status']!=0) error(MSG_e_f_closed);
  if (!getvar("p_text")) error(MSG_e_a_emptytext);
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

  $premod=is_premod_need();
  $pid=process_post($tid,1);

  $sqldata = build_sql("a_");
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Article SET a_tid=$tid, $sqldata";
  $res =&db_query($sql,$link);

  if (!$premod) topic_increment($forum,$tid,$pid);

  $newfid =&getvar("fid");
  if ($newfid) {
    $title =&getvar("t_title");
    $descr = MSG_a_discussion;
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Topic SET t_fid=\"$newfid\", t_title=\"$title\", t_descr=\"$descr\"";
    $res =&db_query($sql,$link);
    $newtid = db_insert_id($res);

    $text = MSG_a_disclink." \\\"<a href=\\\"index.php?t=$tid\\\">$title</a>\\\"";
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Post SET p_tid=$newtid, p_text=\"$text\", p__time=".$GLOBALS['curtime'].", p__html=1, p_uid=2, p_uname=\"System\"";
    $res =&db_query($sql,$link);
    $newpid = db_insert_id($res);

    $sql = "UPDATE ".$GLOBALS['DBprefix']."Article SET a_disc_tid=$newtid WHERE a_tid=$tid";
    $res =&db_query($sql,$link);

    topic_increment($newfid,$newtid,$newpid);
  }

  if (!$premod) {
    $GLOBALS['intopic']['t_id']=$tid;
    $GLOBALS['intopic']['t_link']=&getvar('t_link');
    topic_message(MSG_a_added,1);
  }
  else {
    $GLOBALS['refpage']=build_url($GLOBALS['inforum']);
    message(MSG_t_premoderated,1);
  }
}

function edit_article() {
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

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Article WHERE a_tid=$tid";
  $res =&db_query($sql,$link);
  $adata =&db_fetch_array($res);

//  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicView WHERE tid=$tid";
//  $res =&db_query($sql,$link);
  article_edit_form($tdata,$adata,$pdata,"do_edit",MSG_a_edit);
}

function do_edit() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['intopic']['t_author']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) error(MSG_e_t_norights);
  if (isset($_POST['continue'])) {
    put_to_draft();
    return ;
  }
  if (isset($_POST['preview'])) {
    article_preview();
    return;
  }

  if (!getvar("t_title")) error(MSG_e_a_emptytitle);
  if (!getvar("p_text")) error(MSG_e_a_emptytext);
  if (!getvar("a_author")) error(MSG_e_a_noauthor);
  if (!$_POST['a_origin']) { $_POST['a_origin']=$GLOBALS['opt_title']; $_POST['a_originurl']=$GLOBALS['opt_url']; }
  $forum=$GLOBALS['forum'];
  global $link;
  if (getvar('nobr')) clear_br($_POST['p_text']);

  check_hurl();
  if (!$_POST['t_link']) {
  $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
  $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
  }
  $sqldata = build_sql("t_");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET $sqldata WHERE t_id=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  $sqldata = build_sql("a_");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Article SET $sqldata WHERE a_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  $sql = "SELECT MIN(p_id) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);
  list($pid)=db_fetch_row($res);

  $sqldata = build_sql("p_");
  $sqldata.= check_post_params();
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET $sqldata WHERE p_id=\"$pid\" AND p_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  topic_message(MSG_a_saved,1);
}

function do_delete_article() {
  if ($inuserlevel<$inforum['f_lmoderate']) error(MSG_e_t_norights);
  $tid=$GLOBALS['topic'];
  global $link;

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Article WHERE a_tid=$tid";
  $res =&db_query($sql,$link);

  delete_topic($tid);
  forum_resync($GLOBALS['forum']);
  $GLOBALS['refpage']="index.php?f=".$GLOBALS['forum'];
  message(MSG_t_deleted);
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

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Article WHERE a_tid=$tid";
  $res =&db_query($sql,$link);
  $adata =&db_fetch_array($res);
  article_print_form($tdata,$adata,$pdata);
}

function delete_confirm() {
  $params['t']=$GLOBALS['topic'];
  confirm("article","do_delete_article",$params,MSG_a_deleteconfirm." ".$GLOBALS['intopic']['t_title'],"index.php?t=".$GLOBALS['topic']);
}

function edit() {
  edit_comment();
}

function edit_comment() {
  edit_post('article_discuss_form',MSG_a_editcoment,"do_edit_post");
}

function split_article($text,$html=0) {
  $result=array();
  $buffer="";
  array_push($result,0);
  $counter=0;
  $len=strlen($text);
  if ($GLOBALS['opt_article_split']) {
    if (!$html) $tmp=explode("\n",trim($text));
    else $tmp=explode("\n",trim($text)); //else $tmp=explode('<br />',trim($text));
    foreach ($tmp as $curstr) {
      $buffer.=$curstr;
      $counter+=strlen($curstr)+1;
      if (strlen($buffer)>=$GLOBALS['opt_article_split'] && $counter<$len) {
        array_push($result,$counter);
        $buffer="";
      }
    }
  }
  array_push($result,$len);
  return $result;
}

function build_article_pages($start,&$list,$topic) {
    $numpages = count($list)-1;
    if ($numpages>1 || $start>0) {
        $pages = MSG_pages;
        $number=0;
        for ($i=1; $i<=$numpages; $i++) {
            if ($number!=$start || $start=="all") $pages.="<a href=\"".build_url($GLOBALS['intopic'],"pg=".$number).'">'.$i.'</a> ';
            else $pages.="$i ";
            $number++;
        }
      if ($start=="all") $pages.="#";
      else $pages.="<a href=\"".build_url($GLOBALS['intopic'],"pg=all").'">#</a> ';
     }
    return $pages;
}

function article_subtext($start,&$list,$text) {
  if ($start=="all") return $text;
  else return substr($text,$list[$start],$list[$start+1]-$list[$start]);
}

function clear_br(&$text) {
  $text = str_replace('<IntB:','<IntB::',$text);
  $text = str_replace("\r",'',$text);
  $count=preg_match_all('|<pre>(.*?)</pre>|is',$text,$matches);
  for ($i=0; $i<$count; $i++) {
    $matches[1][$i]='<pre>'.str_replace("\n",'<IntB:BR />',$matches[1][$i]).'</pre>';
    $text=str_replace($matches[0][$i],$matches[1][$i],$text);
  }
  $text = str_replace("\n",' ',$text);
  $text = str_replace('<IntB:BR />',"\n",$text);
  $text = str_replace('<IntB::','<IntB:',$text);
}

function article_preview() {
  article_display($_POST,$_POST,$_POST,false,'');
  $forumlist=build_forum_select("f_ltopic",1);
  article_edit_form($_POST,$_POST,$_POST,$_POST['a'],MSG_a_edit,$forumlist);
}

function article_locations($locations) {
  if ($GLOBALS['topic'] && calc_start_offset()>0 && getvar('st')=='new') $GLOBALS['action']='view_comments';  
  push_parents($locations,$GLOBALS['inforum']['f_parent']);
  if ($GLOBALS['topic']) {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    if ($GLOBALS['action']=='article_view') array_push($locations,$GLOBALS['intopic']['t_title']);
    else array_push($locations,"<a href=\"".build_url($GLOBALS['intopic'])."\">".$GLOBALS['intopic']['t_title']."</a>");
    $GLOBALS['rss_link']="rss.php?t=".$GLOBALS['topic']."&amp;count=".$GLOBALS['inuser']['u_mperpage'];
  }
  else {
    array_push($locations,$GLOBALS['inforum']['f_title']);
    $GLOBALS['rss_link']="rss.php?a=newtopic&amp;f=".$GLOBALS['forum']."&amp;count=".$GLOBALS['inuser']['u_aperpage'];
  }
  if ($GLOBALS['action']=="edit_article") {
    array_push($locations,MSG_a_edit);
  }
  elseif ($GLOBALS['action']=="edit_comment"  || $GLOBALS['action']=="do_edit_post") {
    array_push($locations,MSG_a_editcomment);
  }
  elseif ($GLOBALS['action']=="add_article") {
    array_push($locations,MSG_a_adding);
  }
  elseif ($GLOBALS['action']=="view_comments") {
    array_push($locations,MSG_a_viewcomments);
  }
  if (getvar("preview")) array_push($locations,MSG_preview);  
  return $locations;
}