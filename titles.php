<? /*

Forum titles script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function stdforum_title(&$fdata) {
  std_title_form($fdata,$GLOBALS['moderators'][$fdata['f_id']]);
}

function recurse_max($forum,$parent,$sublinks=0) {
  global $link;
  global $oldcat;
  if ($GLOBALS['inuser']['u_multilang']==0) {
    $lang="(f_lnid=0 OR f_lnid=".$GLOBALS['inuser']['ln_id'].") AND ";
  }
  if (!$forum) $forum="0";

  foreach ($GLOBALS['forumlist'][$oldcat] as $fdata) if ($fdata['f_parent']==$forum) {
    if ($fdata['f_parent']==$forum && $sublinks==0) {
     if ($resdata['sublinks']) $resdata['sublinks'].=" ";
     $resdata['sublinks'].=(($GLOBALS['inuserid']>3 && is_subforum_new($fdata,$fdata) && $resdata['tf_pcount'])?"<a title=\"".MSG_shownewposts."\" href=\"index.php?m=newpost&amp;fs=".$fdata['f_id']."\" class=\"sb_new\">&#8226;</a>":"&#8226;")." <a href=\"".build_url($fdata)."\">".$fdata['f_title']."</a><br>";
    }
    if ($fdata['tp_container']) $fdata=recurse_max($fdata['f_id'],$forum,$sublinks);
    $resdata['tf_pcount']+=$fdata['tf_pcount'];
    $resdata['tf_tcount']+=$fdata['tf_tcount'];
    $resdata['tf_visited']+=$fdata['tf_visited']; 
    if (!$resdata['tf_lasttime'] || $resdata['tf_lasttime']<$fdata['tf_lasttime']) {
     $resdata['tf_lasttime']=$fdata['tf_lasttime'];
     $resdata['p_uid']=$fdata['p_uid'];
     $resdata['p_uname']=$fdata['p_uname'];
     $resdata['lp_id']=$fdata['lp_id'];
     $resdata['lp_fid']=$fdata['f_id'];
     $resdata['lp_flink']=$fdata['f_link'];
     $resdata['lp_title']=$fdata['lp_title'];
     $resdata['lp_link']=$fdata['lp_link'];
     if ($resdata['lv_time2']<$fdata['lv_time2']) $resdata['lv_time2']=$fdata['lv_time2'];
    }
  }
  return $resdata;
}

function contnr_title($fdata) {
  $resdata=recurse_max($fdata['f_id'],$fdata['f_id'],$fdata['f_nosubs']);
  contnr_title_form($fdata,$resdata);
}

function irc_title($fdata) {
  global $link;
  $sql = "SELECT pu_uid, pu_uname FROM ".$GLOBALS['DBprefix']."Present ".
  "WHERE pu_fid=".$fdata['f_id']." AND pu_lasttime>".($GLOBALS['curtime']-$GLOBALS['opt_heretime']*60);
  $res =&db_query($sql,$link);
  while ($udata=&db_fetch_array($res)) {
    if ($udata['pu_uid']==1) { if ($udata['pu_uname']=="Guest") $guests++; }
    else {
      if ($userlist) $userlist.=", ";
      $userlist.=user_out($udata['pu_uname'],$udata['pu_uid']);
    }
  }
  irc_title_form($fdata,$userlist,$guests);
}

function article_title(&$fdata) {
  article_title_form($fdata,$GLOBALS['moderators'][$fdata['f_id']]);
}

function epedia_title(&$fdata) {
  epedia_title_form($fdata,$GLOBALS['moderators'][$fdata['f_id']]);
}


function download_title(&$fdata) {
  download_title_form($fdata,$GLOBALS['moderators'][$fdata['f_id']]);
}

function photos_title(&$fdata) {
  photos_title_form($fdata,$GLOBALS['moderators'][$fdata['f_id']]);
}

function news_title(&$fdata) {
  global $link;
  if ($GLOBALS['opt_news_main_mode']==1 || $GLOBALS['opt_news_main_mode']==3) $timelimit=" AND  p__time>".($GLOBALS['curtime']-$GLOBALS['opt_news_main_days']*24*60*60);
    if ($GLOBALS['opt_news_main_mode']==2 || $GLOBALS['opt_news_main_mode']==3) $limit=" LIMIT 0,".intval($GLOBALS['opt_news_main_count']);
  $sql = "SELECT t.*, p__time AS time FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post p ".
  " WHERE t_fid=".$fdata['f_id']." AND p_tid=t_id AND t__startpostid=p_id AND p__premoderate=0 $timelimit ORDER BY time DESC $limit";
  $res =&db_query($sql,$link);
  news_start($fdata);
  while ($ndata=&db_fetch_array($res)) {
    $ndata['f_id']=$fdata['f_id'];
    $ndata['f_link']=$fdata['f_link'];
    news_entry($ndata);
  }
  if (db_num_rows($res)==0) news_noentries();
  news_end($fdata, $GLOBALS['moderators'][$fdata['f_id']]);
}

function present_title(&$fdata) {
  contnr_title($fdata);
}

function dynpage_title(&$fdata) {
  contnr_title($fdata);
}


function blog_title(&$fdata) {
  $fdata['f_link']=str_replace('blog_','blogs/',urlencode($fdata['f_link']));
  blog_title_form($fdata);
}

function gallery_title(&$fdata) {
  $fdata['f_link']=str_replace('gallery_','gallerys/',urlencode($fdata['f_link']));
  gallery_title_form($fdata);
}

function is_forum_new($fdata) {
  if ($GLOBALS['opt_fixviews'] && $GLOBALS['inuserid']>3) $result=$fdata['tf_tcount']-$fdata['tf_visited'];
  else $result = ($fdata['lv_time2']<$fdata['tf_lasttime']);
  if ($GLOBALS['inuserid']<=3) $result=0;
  return $result;
}

function is_subforum_new($fdata,$resdata) {
  return is_forum_new($resdata);
}