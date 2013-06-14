<? /*

Photo gallery script for Intellect Board 2 Project

(C) 2004-2005, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function photos_view() {
  if (!$GLOBALS['opt_photos_line']) $GLOBALS['opt_photos_line']=5;
  if (!$GLOBALS['opt_photo_thumb_y']) $GLOBALS['opt_photo_thumb_y']=100;
//  if (!$GLOBALS['opt_photo_size_x']) $GLOBALS['opt_photo_size_x']=720;

  if ($GLOBALS['topic']) view_topic();
  else view_list();
}

function view_list() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  $forum=$GLOBALS['forum'];
  global $link;
  $order=&getvar("o");
  $direct=&getvar("desc");
  $start=&getvar("st");
  if (!$start) $start="0";
  if (!$perpage) $perpage=$GLOBALS['inuser']['u_aperpage'];
  $tcount=$GLOBALS['inforum']['f__tcount'];
  $pages =&build_pages($tcount,$start,$perpage,"index.php?f=$forum&o=$order&desc=$desc");
  if (!$order && !$GLOBALS['opt_photo_order']) $order="t__startpostid DESC";
  elseif (!$order && $GLOBALS['opt_photo_order']) $order="t__startpostid";
  if ($start!="all") $limit = " LIMIT $start,$perpage";

  $sql = "SELECT t.*, tvc.t__views, ph.*, p2.p_uname AS u__name, p2.p_uid AS u_id, ".
  "t__lasttime AS posttime, t__ratingsum/NULLIF(t__ratingcount,0) AS trating, ".
  "tv.tid AS visited, t__pcount AS pcount, t__lasttime AS lastpost ".
  "FROM ".$GLOBALS['DBprefix']."Photo ph, ".$GLOBALS['DBprefix']."Topic t ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."TopicView tv ON (tv.tid=t.t_id AND tv.uid=".$GLOBALS['inuserid'].") ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."TopicVC tvc ON (tvc.tid=t.t_id)".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Post p2 ON (t.t__startpostid=p2.p_id) ".
  "WHERE t.t_fid=$forum AND t.t_id=ph.ph_tid  ".
  "ORDER BY t__sticky DESC, $order $direct $limit";
  $res =&db_query($sql,$link);

  $counter=0;
  $rows = ceil(db_num_rows($res)/$GLOBALS['opt_photos_line']);
  photo_list_start($pages,$rows);
  while ($phdata=&db_fetch_array($res)) {
    $phdata['f_id']=$GLOBALS['forum'];
    $phdata['f_link']=$GLOBALS['inforum']['f_link'];
    photo_list_entry($phdata);
  }
  list($inforum,$autosub)=get_forum_sub();
  photo_list_end($pages,$inforum,$autosub);
}

function view_topic() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
  $forum=$GLOBALS['forum'];
  $topic=$GLOBALS['topic'];
  global $link;

  $rated=common_topic_view($topic);

  $sql = "SELECT ph.* FROM ".$GLOBALS['DBprefix']."Photo ph WHERE ph_tid=$topic";
  $res =&db_query($sql,$link);
  $phdata=&db_fetch_array($res);
  db_free_result($res);
  $tdata=&$GLOBALS['intopic'];
  $sql = "SELECT p.*, u.u__name, u.u_id FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."User u ".
      "WHERE p.p_tid=$topic AND u.u_id=p.p_uid AND p.p__premoderate=0 AND p_id=".$GLOBALS['intopic']['t__startpostid'];
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  db_free_result($res);
  
  $thumbs = array();
  if (!$GLOBALS['opt_photo_order']) $desc=' DESC';
  $sql = "SELECT t_id, t_link, t_title, ph_id, ph_key FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Photo ph ".
  " WHERE t_fid=".$forum." AND t__pcount>0 AND t_id=ph_tid ORDER BY ph_id ".$desc;
  $res=&db_query($sql,$link);
  $counter=0;
  while ($tmp=&db_fetch_array($res)) {
    $tmp['f_id']=$forum;
    $tmp['f_link']=$GLOBALS['inforum']['f_link'];
    $thumbs[]=$tmp;
    if ($tmp['t_id']==$topic) $curpos=$counter;
    $counter++;
  }
  db_free_result($res);
  
  photo_display($tdata,$phdata,$pdata,$rated,$thumbs,$curpos);
  
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
  
  $sql = "SELECT ph.* FROM ".$GLOBALS['DBprefix']."Photo ph WHERE ph_tid=".$GLOBALS['topic'];
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
  $tid=$GLOBALS['topic'];
  process_post($tid);
  if (!getvar('preview')) {
    if (!is_premod_need(0)) {
      topic_message(MSG_ph_commentadded,1);
    }
    else message(MSG_p_premoderated,1);
  }
}

function edit_from_draft() {
  $data = get_from_draft();
  if ($data['t']) {
    view_topic();
    display_post_form(MSG_ph_editcoment,$data,0);
  }
  else photo_edit_form($data,$data,$data['a'],MSG_ph_edit);
}

function add_photo() {
  $pdata['p__bcode']=$GLOBALS['inforum']['f_bcode'];
  $pdata['p__smiles']=$GLOBALS['inforum']['f_smiles'];
  photo_edit_form($tdata,$pdata,"do_topic",MSG_ph_adding);
}

function do_topic() {
  if ($GLOBALS['inuserlevel']<$inforum['f_ltopic']) error(MSG_e_t_norights);
  if ($GLOBALS['inforum']['f_status']!=0) error(MSG_e_f_closed);
  if (isset($_POST['continue'])) {
      put_to_draft();
      return ;
  }
  $forum=$GLOBALS['forum'];
  global $link;
  if (!getvar("p_text")) $_POST['p_text']=str_repeat(" ",$GLOBALS['opt_minpost']+1);

  if (!getvar("t_title")) {
    $sql = "SELECT COUNT(*)+1 FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=".$GLOBALS['forum'];
    $res =&db_query($sql,$link);
    list($count)=db_fetch_row($res);
    db_free_result($res);
    $_POST['t_title']=MSG_photo." ".$count;
  }

  if (!$GLOBALS['opt_photos_line']) $GLOBALS['opt_photos_line']=5;
  if (!$GLOBALS['opt_photo_thumb_y']) $GLOBALS['opt_photo_thumb_y']=100;
  if (!$GLOBALS['opt_thumb_qlty']) $GLOBALS['opt_thumb_qlty']=70;
  if (!$GLOBALS['opt_photo_qlty']) $GLOBALS['opt_photo_qlty']=70;

  if (!is_uploaded_file($_FILES['photo']['tmp_name'])) error(MSG_e_ph_nophoto);
  if (!$GLOBALS['opt_photo_maxsize']) $GLOBALS['opt_photo_maxsize']=$GLOBALS['opt_maxfileattach'];
  if ($_FILES['photo']['size']>$GLOBALS['opt_photo_maxsize']) error(MSG_e_ph_toolarge);
  $fh=fopen($_FILES['photo']['tmp_name'],"rb");
  $buffer=fread($fh,$_FILES['photo']['size']);
  fclose($fh);

  $fullimg=imagecreatefromstring($buffer);
  unset($buffer);
  if (!$fullimg) error(MSG_e_ph_badfile);
  $sizey=imagesy($fullimg);
  $sizex=imagesx($fullimg);
  $coeff=$sizex/$sizey;

  $trash=rand();
  $key=substr(md5($trash),0,8);

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

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Photo SET ph_tid=".$tid.", ph_key=\"$key\"";
  $res =&db_query($sql,$link);

  $thumb=imagecreatetruecolor(floor($GLOBALS['opt_photo_thumb_y']*$coeff),$GLOBALS['opt_photo_thumb_y']);
  if ($GLOBALS['opt_GD2']) imagecopyresampled($thumb,$fullimg,0,0,0,0,$GLOBALS['opt_photo_thumb_y']*$coeff,$GLOBALS['opt_photo_thumb_y'],$sizex,$sizey);
  else imagecopyresized($thumb,$fullimg,0,0,0,0,floor($GLOBALS['opt_photo_thumb_y']*$coeff),$GLOBALS['opt_photo_thumb_y'],$sizex,$sizey);
  $tmpname=$GLOBALS['opt_dir']."/photos/previews/$tid.jpg";
  imagejpeg($thumb,$tmpname,$GLOBALS['opt_thumb_qlty']);
  imagedestroy($thumb);

  if ($GLOBALS['opt_photo_size_x'] && $sizex >= $sizey && $sizex>$GLOBALS['opt_photo_size_x']) {
    $photo=imagecreatetruecolor($GLOBALS['opt_photo_size_x'],floor($GLOBALS['opt_photo_size_x']/$coeff));
    if ($GLOBALS['opt_GD2']) imagecopyresampled($photo,$fullimg,0,0,0,0,$GLOBALS['opt_photo_size_x'],floor($GLOBALS['opt_photo_size_x']/$coeff),$sizex,$sizey);
    else imagecopyresized($photo,$fullimg,0,0,0,0,$GLOBALS['opt_photo_size_x'],$GLOBALS['opt_photo_size_x']/$coeff,$sizex,$sizey);
    imagejpeg($photo,$GLOBALS['opt_dir']."/photos/".$tid.".jpg",$GLOBALS['opt_photo_qlty']);
    imagedestroy($photo);
  } elseif ($GLOBALS['opt_photo_size_x'] && $sizey >= $sizex && $sizey>$GLOBALS['opt_photo_size_x']) {
    $photo=imagecreatetruecolor(floor($GLOBALS['opt_photo_size_x']*$coeff),$GLOBALS['opt_photo_size_x']);
    if ($GLOBALS['opt_GD2']) imagecopyresampled($photo,$fullimg,0,0,0,0,floor($GLOBALS['opt_photo_size_x']*$coeff),$GLOBALS['opt_photo_size_x'],$sizex,$sizey);
    else imagecopyresized($photo,$fullimg,0,0,0,0,$GLOBALS['opt_photo_size_x']*$coeff,$GLOBALS['opt_photo_size_x'],$sizex,$sizey);
    imagejpeg($photo,$GLOBALS['opt_dir']."/photos/".$tid.".jpg",$GLOBALS['opt_photo_qlty']);
    imagedestroy($photo);
  }
  else imagejpeg($fullimg,$GLOBALS['opt_dir']."/photos/".$tid.".jpg",$GLOBALS['opt_photo_qlty']);
  imagedestroy($fullimg);
  
  $pid=process_post($tid,1);
  $premod=is_premod_need(1);
  if (!$premod) {
    topic_increment($forum,$tid,$pid);
  }

  if (!$premod) {
    $GLOBALS['intopic']['t_id']=$tid;
    topic_message(MSG_ph_added,1);
  }
  else {
    $GLOBALS['refpage']=build_url($GLOBALS['forum']);
    message(MSG_t_premoderated,1);
  }
}

function edit_photo() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['intopic']['t_author']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) error(MSG_e_t_norights);

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

//  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicView WHERE tid=$tid";
//  $res =&db_query($sql,$link);
  photo_edit_form($tdata,$pdata,"do_edit",MSG_ph_edit);
}

function do_edit() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['intopic']['t_author']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) error(MSG_e_t_norights);

  $forum=$GLOBALS['forum'];
  $tid=$GLOBALS['topic'];
  global $link;

  check_hurl();
  if (!$_POST['t_link']) {
  $_POST['t_link']=str_replace(' ','_',transliterate($_POST['t_title']));
  $_POST['t_link']=preg_replace('/[^\w\d]/','',$_POST['t_link']);
  }
  $sqldata = build_sql("t_");
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET $sqldata WHERE t_id=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  if (!$GLOBALS['opt_photos_line']) $GLOBALS['opt_photos_line']=5;
  if (!$GLOBALS['opt_photo_thumb_y']) $GLOBALS['opt_photo_thumb_y']=100;
  if (!$GLOBALS['opt_thumb_qlty']) $GLOBALS['opt_thumb_qlty']=70;
  if (!$GLOBALS['opt_photo_qlty']) $GLOBALS['opt_photo_qlty']=70;

  if (is_uploaded_file($_FILES['photo']['tmp_name'])) {
    if (!$GLOBALS['opt_photo_maxsize']) $GLOBALS['opt_photo_maxsize']=$GLOBALS['opt_maxfileattach'];
    if ($_FILES['photo']['size']>$GLOBALS['opt_photo_maxsize']) error(MSG_e_ph_toolarge);
    $fh=fopen($_FILES['photo']['tmp_name'],"rb");
    $buffer=fread($fh,$_FILES['photo']['size']);
    fclose($fh);

    $fullimg=imagecreatefromstring($buffer);
    unset($buffer);
    if (!$fullimg) error(MSG_e_ph_badfile);
    $sizey=imagesy($fullimg);
    $sizex=imagesx($fullimg);
    $coeff=$sizex/$sizey;

    if (!$GLOBALS['opt_photo_maxsize']) $GLOBALS['opt_photo_maxsize']=$GLOBALS['opt_maxfileattach'];
    if ($_FILES['photo']['size']>$GLOBALS['opt_photo_maxsize']) error(MSG_e_ph_toolarge);
    $thumb=imagecreatetruecolor(floor($GLOBALS['opt_photo_thumb_y']*$coeff),$GLOBALS['opt_photo_thumb_y']);
    if ($GLOBALS['opt_GD2']) imagecopyresampled($thumb,$fullimg,0,0,0,0,$GLOBALS['opt_photo_thumb_y']*$coeff,$GLOBALS['opt_photo_thumb_y'],$sizex,$sizey);
    else imagecopyresized($thumb,$fullimg,0,0,0,0,floor($GLOBALS['opt_photo_thumb_y']*$coeff),$GLOBALS['opt_photo_thumb_y'],$sizex,$sizey);
    $tmpname=$GLOBALS['opt_dir']."/photos/previews/$tid.jpg";
    if (file_exists($tmpname)) unlink($tmpname);
    imagejpeg($thumb,$tmpname,$GLOBALS['opt_thumb_qlty']);
    imagedestroy($thumb);

    if ($GLOBALS['opt_photo_size_x'] && $sizex >= $sizey && $sizex>$GLOBALS['opt_photo_size_x']) {
      $photo=imagecreatetruecolor($GLOBALS['opt_photo_size_x'],floor($GLOBALS['opt_photo_size_x']/$coeff));
      if ($GLOBALS['opt_GD2']) imagecopyresampled($photo,$fullimg,0,0,0,0,$GLOBALS['opt_photo_size_x'],floor($GLOBALS['opt_photo_size_x']/$coeff),$sizex,$sizey);
      else imagecopyresized($photo,$fullimg,0,0,0,0,$GLOBALS['opt_photo_size_x'],$GLOBALS['opt_photo_size_x']/$coeff,$sizex,$sizey);
      $tmpname=$GLOBALS['opt_dir']."/photos/".$tid.".jpg";
      if (file_exists($tmpname)) unlink($tmpname);
      imagejpeg($photo,$tmpname,$GLOBALS['opt_photo_qlty']);
      imagedestroy($photo);
    } elseif ($GLOBALS['opt_photo_size_x'] && $sizey >= $sizex && $sizey>$GLOBALS['opt_photo_size_x']) {
      $photo=imagecreatetruecolor(floor($GLOBALS['opt_photo_size_x']*$coeff),$GLOBALS['opt_photo_size_x']);
      if ($GLOBALS['opt_GD2']) imagecopyresampled($photo,$fullimg,0,0,0,0,floor($GLOBALS['opt_photo_size_x']*$coeff),$GLOBALS['opt_photo_size_x'],$sizex,$sizey);
      else imagecopyresized($photo,$fullimg,0,0,0,0,$GLOBALS['opt_photo_size_x']*$coeff,$GLOBALS['opt_photo_size_x'],$sizex,$sizey);
      $tmpname=$GLOBALS['opt_dir']."/photos/".$tid.".jpg";
      if (file_exists($tmpname)) unlink($tmpname);
      imagejpeg($photo,$tmpname,$GLOBALS['opt_photo_qlty']);
      imagedestroy($photo);
    }
    else imagejpeg($fullimg,$GLOBALS['opt_dir']."/photos/".$tid.".jpg",$GLOBALS['opt_photo_qlty']);
    imagedestroy($fullimg);    
  }

  $sql = "SELECT MIN(p_id) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);
  list($pid)=db_fetch_row($res);

  $sqldata = build_sql("p_");
  $sqldata.= check_post_params();
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET $sqldata WHERE p_id=\"$pid\" AND p_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);

  topic_message(MSG_ph_saved,1);
}

function do_delete_photo() {
  if ($inuserlevel<$inforum['f_lmoderate']) error(MSG_e_t_norights);
  $tid=$GLOBALS['topic'];
  global $link;

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Photo WHERE ph_tid=$tid";
  $res =&db_query($sql,$link);

  delete_topic($tid);
  unlink($GLOBALS['opt_dir']."/photos/$tid.jpg");
  unlink($GLOBALS['opt_dir']."/photos/previews/$tid.jpg");
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

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$tid LIMIT 1";
  $res =&db_query($sql,$link);
  $pdata=&db_fetch_array($res);
  db_free_result($res);

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Photo WHERE ph_tid=$tid";
  $res =&db_query($sql,$link);
  $phdata =&db_fetch_array($res);
  photo_print_form($tdata,$phdata,$pdata);
}

function delete_confirm() {
  $params['t']=$GLOBALS['topic'];
  confirm("photos","do_delete_photo",$params,MSG_ph_deleteconfirm." ".$GLOBALS['intopic']['t_title'],"index.php?t=".$GLOBALS['topic']);
}

function edit() {
  edit_comment();
}

function edit_comment() {
  edit_post("photo_discuss_form",MSG_ph_editcoment);
}

function photos_locations($locations) {
  if ($GLOBALS['topic'] && calc_start_offset()>0 && getvar('st')=='new') $GLOBALS['action']='view_comments'; 
  push_parents($locations,$GLOBALS['inforum']['f_parent']);
  if ($GLOBALS['topic']) {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    if ($GLOBALS['action']=='photos_view') array_push($locations,$GLOBALS['intopic']['t_title']);
    else array_push($locations,"<a href=\"".build_url($GLOBALS['intopic'])."\">".$GLOBALS['intopic']['t_title']."</a>");
    $GLOBALS['rss_link']="rss.php?t=".$GLOBALS['topic']."&amp;count=".$GLOBALS['inuser']['u_mperpage'];
  }
  elseif (!$GLOBALS['topic'] && $GLOBALS['action']=='photos_view') {
    array_push($locations,$GLOBALS['inforum']['f_title']);
    $GLOBALS['rss_link']="rss.php?a=newtopic&amp;f=".$GLOBALS['forum']."&amp;count=".$GLOBALS['inuser']['u_aperpage'];
  }
  else {
    array_push($locations,"<a href=\"".build_url($GLOBALS['inforum'])."\">".$GLOBALS['inforum']['f_title']."</a>");
    if ($GLOBALS['action']=='photos_view') array_push($locations,$GLOBALS['intopic']['t_title']);
  }
  if ($GLOBALS['action']=="edit_photo") {
    array_push($locations,MSG_ph_edit);
  }
  elseif ($GLOBALS['action']=="edit_comment" || $GLOBALS['action']=="do_edit_post") {
    array_push($locations,MSG_ph_editcomment);
  }
  elseif ($GLOBALS['action']=="add_photo") {
    array_push($locations,MSG_ph_adding);
  }
  elseif ($GLOBALS['action']=="view_comments") {
    array_push($locations,MSG_a_viewcomments);
  }
  if (getvar("preview")) array_push($locations,MSG_preview);  
  return $locations;
}