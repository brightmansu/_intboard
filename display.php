<? /*

Topic and comments display library for Intellect Board 2 Project

(C) 2007, XXXX Pro, United Open Project (http://www.openproj.ru)
Visit us online: http://intboard.ru

*/

function display_topic_data($tdata,$comments=true) {
  global $link;

  $start = calc_start_offset();
  $start_cc = $start;
  if ($comments && !isset($_GET['st'])) $start--;
  if ($start<0) $start=0;
  $perpage = $GLOBALS['inuser']['u_mperpage'];
  if (getvar('o')=='1' || $GLOBALS['inuser']['u_sortposts']) $desc=' DESC ';
  else $desc='';

  if (!$perpage) $perpage=10;
  $count=$tdata['t__pcount'];
  if ($comments) {
    $count--;
    if ($start!=='all') $start++;
    $pages=build_pages_hurl($count,$start-1,$perpage,$GLOBALS['intopic'],"a=".$GLOBALS['action']."&amp;o=$sort");
  }
  else $pages=build_pages_hurl($count,$start,$perpage,$GLOBALS['intopic'],"a=".$GLOBALS['action']."&amp;o=$sort");

  $rated=common_topic_view($tdata['t_id']);

  $pdata=array();
  $sqldata='';
  $uids = array();
  if ($tdata['t__stickypost'] && $start!=0) {
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."File ON (p_attach=file_id) ".
    "WHERE p_id=".$tdata['t__startpostid']." AND p__premoderate=0";
    $res = db_query($sql,$link);
    $pdata[0]=db_fetch_array($res);
    if ($pdata[0]['p_uid']) $uids[]=$pdata[0]['p_uid'];
    db_free_result($res);
//    if ($start==0) $sqldata='AND p_id<>'.$pdata[0]['p_id'];
  }

  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."File ON (p_attach=file_id) ".
  "WHERE p_tid=".$tdata['t_id']." AND  p__premoderate=0 ".$sqldata." ".
  "ORDER BY p__time ".$desc;
  if ($start!='all') $sql.='LIMIT '.intval($start).','.$perpage;
  elseif ($start=='all' && $comments) $sql.='LIMIT 1,'.$count;
  $res = db_query($sql,$link);
  while ($post=db_fetch_array($res)) {
    $pdata[]=$post;
    if ($post['p_uid']>0) $uids[]=$post['p_uid'];
  }
  db_free_result($res);
 
  
  if ($GLOBALS['opt_norateperiod']) {
    $sql = "SELECT MAX(p__time) FROM ".$GLOBALS['DBprefix']."Post WHERE p_uid=\"".$GLOBALS['inuserid']."\"";
    $res =&db_query($sql,$link);
    list($lastpost)=db_fetch_row($res);
    db_free_result($res);
    if ($lastpost<$GLOBALS['curtime']-$GLOBALS['opt_norateperiod']*24*60*60) $norate=true; 
  }
  
  if (count($pdata)) {
    $ratetime = $GLOBALS['curtime']-$GLOBALS['opt_ratetime']*24*60*60;
    $present_time = $GLOBALS['curtime']-$GLOBALS['opt_heretime']*60;
    $sql = "SELECT u.*, COALESCE(lv2.l_title,lv1.l_title) AS l_title, COALESCE(lv2.l_pic,lv1.l_pic) AS l_pic, ".
    "IF(pu_lasttime>".$present_time.",1,0) AS present, ".
    "IF(ur.uid IS NOT NULL,1,0) AS rated, u_status, u_partner ".
    "FROM ".$GLOBALS['DBprefix']."User u ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u_id AND fid=".$GLOBALS['forum'].") ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserLevel lv1 ON (lv1.l_level=u__level) ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserLevel lv2 ON (lv2.l_level=ua_level) ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."Present p ON (u_id=pu_uid) ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."AddrBook ab ON (u_owner=".$GLOBALS['inuserid']." AND u_partner=u_id) ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserRating ur ON (ur.uid=u_id AND ur.ur_rated=".$GLOBALS['inuserid']." AND ur_time>".$ratetime.") ".
    "WHERE u_id IN (".join(',',$uids).")";
    $users=array();
    $res = db_query($sql,$link);
    while ($udata=db_fetch_array($res)) {
      if ($rated) $udata['rated']=$norate;
      $users[$udata['u_id']]=$udata;
    }
    db_free_result($res);

    if ($GLOBALS['inuser']['forum_noaccess']) $skip_forums = explode(',',$GLOBALS['inuser']['forum_noaccess']);
    $sql = "SELECT f_id FROM ".$GLOBALS['DBprefix']."Forum WHERE f_nostats=1";
    $res = db_query($sql,$link);
    while ($fdata=db_fetch_row($res)) $skip_forums[]=$fdata[0];
    db_free_result($res);

    $sql = "SELECT SUM(us_count), uid FROM ".$GLOBALS['DBprefix']."UserStat ".
    "WHERE uid IN (".join(',',$uids).") ";
    if (count($skip_forums)) $sql.="AND fid NOT IN (".join(',',$skip_forums).") ";
    $sql.="GROUP BY uid";
    $res = db_query($sql,$link);
    while ($usdata=db_fetch_row($res)) $users[$usdata[1]]['posts']=$usdata[0];
    db_free_result($res);

    $sql = "SELECT SUM(ur_value), uid FROM ".$GLOBALS['DBprefix']."UserRating ".
    "WHERE uid IN (".join(',',$uids).") ".
    "GROUP BY uid";
    $res = db_query($sql,$link);
    while ($rdata=db_fetch_row($res)) $users[$rdata[1]]['rating']=$rdata[0];
    db_free_result($res);
    
    unset($uids);
  }

  $mincount="0";
  if ($comments) $mincount="1";
  $sql = "SELECT t_id, t_title, t_link, f_id, f_link ".
  "FROM ".$GLOBALS['DBprefix']."Forum, ".$GLOBALS['DBprefix']."Topic ".
  "WHERE t__lasttime>".intval($GLOBALS['intopic']['t__lasttime'])." AND t__pcount>$mincount AND t_fid=".$GLOBALS['forum']." AND f_id=t_fid ".
  "ORDER BY t__lasttime LIMIT 1";
  $res = db_query($sql,$link);
  $prev = db_fetch_array($res);
  db_free_result($res);

  $sql = "SELECT t_id, t_title, t_link, f_id, f_link ".
  "FROM ".$GLOBALS['DBprefix']."Forum, ".$GLOBALS['DBprefix']."Topic ".
  "WHERE t__lasttime<".intval($GLOBALS['intopic']['t__lasttime'])." AND t__pcount>$mincount AND t_fid=".$GLOBALS['forum']." AND f_id=t_fid ".
  "ORDER BY t__lasttime DESC LIMIT 1";
  $res = db_query($sql,$link);
  $next = db_fetch_array($res);
  db_free_result($res);
  
  $newcount=0;

  display_topic_start($pages,$tdata,$rated,$prev,$next,$comments);
  for ($i=0, $count=count($pdata); $i<$count; $i++) {
    if ($i % 2 == 1) $class="postentry2";
    else $class="postentry";
    $pdata[$i]["cc"] = intval($start) + $i + 1;
    if ($i==0 && !$comments && $tdata['t__stickypost']) { $class.=" first"; $pdata[0]["cc"] = 1; }
    else { if($tdata['t__stickypost'] && intval($start)) $pdata[$i]["cc"] = intval($start) + $i; }
    if ($users[$pdata[$i]['p_uid']]==2) {
      display_system_entry($pdata[$i],$class);
    }
    else {
      $links = array();
      $links2 = array();
      if ($GLOBALS['inforum']['f_status']==0 && $GLOBALS['intopic']['t__status']==0 && $GLOBALS['inforum']['f_lpost']<=$GLOBALS['inuserlevel']) {
        $links2[]='<a href="'.$_SERVER['REQUEST_URI'].'#answer" onClick="moveForm(\''.$pdata[$i]['p_id'].'\'); return false;">'.MSG_p_answer.'</a>';
        $links2[]='<a onmouseover="copyQN(\''.$pdata[$i]['p_uname']."','p".$pdata[$i]['p_id'].'\');" href="'.$_SERVER['REQUEST_URI'].'#answer" onClick="javascript:pasteQ(); moveForm(\''.$pdata[$i]['p_id'].'\'); return false;" title="'.MSG_p_quotehelp.'">'.MSG_p_quote."</a>";
      }
      if (($pdata[$i]['p_uid']==$GLOBALS['inuserid'] && $pdata[$i]['p_uid']>3 && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ledit']) || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
        $links2[]='<a href="'.build_url($GLOBALS['intopic'],"a=edit&amp;p=".$pdata[$i]['p_id']).'">'.MSG_p_edit."</a>";
      }
      if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate'] || check_selfmod()) {
        $links2[]='<a onClick="return confirm(\''.MSG_p_confirm.'?\')"  href="'.build_url($GLOBALS['intopic'],'a=do_delete_comment&amp;p='.$pdata[$i]['p_id']).'">'.MSG_p_delete."</a>";
      }
      if ($GLOBALS['opt_hurl']==1) {
        $link_to_pcount = "#<a href=\"post/".build_url($GLOBALS['intopic'])."p".$pdata[$i]['p_id'].".htm#pp".$pdata[$i]['p_id']."\">".$pdata[$i]['cc']."</a>";
      }
      elseif ($GLOBALS['opt_hurl']==2) {
        $link_to_pcount = "#<a href=\"index.php/post/".build_url($GLOBALS['intopic'])."p".$pdata[$i]['p_id'].".htm#pp".$pdata[$i]['p_id']."\">".$pdata[$i]['cc']."</a>";
      }
      else {
        $link_to_pcount = "#<a href=\"index.php?t=".$pdata[$i]['p_tid']."&amp;p=".$pdata[$i]['p_id']."#pp".$pdata[$i]['p_id']."\">".$pdata[$i]['cc']."</a>";
      }
      $links2[]=$link_to_pcount;
      $links2[]="<a href='".$_SERVER['REQUEST_URI']."#top'>".MSG_go_top."</a>";
      if ($pdata[$i]['p_uid']>3) {
        if ($GLOBALS['opt_hurl']) array_push($links,"<a href='user/".urlencode($pdata[$i]['p_uname'])."'>".MSG_p_profile."</a>");
        else $links[]='<a href="index.php?m=profile&amp;u='.$pdata[$i]['p_uid'].'">'.MSG_p_profile."</a>";
        if ($pdata[$i]['p_uid']!=$GLOBALS['inuserid'] && $GLOBALS['inuserid']>3) {
          $links[]='<a href="index.php?m=messages&amp;a=newmsg&amp;u='.$pdata[$i]['p_uid'].'">'.MSG_p_pm."</a>";
        }
        if ($pdata['u_showmail']>0) {
          $links[]=show_email_q($pdata[$i]['u__email'],$pdata[$i]['u_showmail'],$pdata[$i]['p_uid']);
        }
        if ($pdata['u_homepage']!="") {
          $links[]='<a href="'.$pdata[$i]['u_homepage'].'" target=_blank>WWW</a>';
        }
        if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['inuserid']!=$pdata[$i]['u_id'] && (($GLOBALS['forum']!=$GLOBALS['inuser']['u__blog_fid'] && $GLOBALS['forum']!=$GLOBALS['inuser']['u__gallery_fid']) || $GLOBALS['inuserbasic']>=1000)) {
          $links[]='<a href="'.build_url($GLOBALS['intopic'],'m=profile&amp;a=warn&amp;u='.$pdata[$i]['p_uid']).'">'.MSG_p_reputation."</a>";
        }
      }
      if (!check_moderate($pdata[$i],$GLOBALS['inforum']['f_lmoderate']) && $pdata[$i]['p_uid']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && ($GLOBALS['opt_complain']==0 || ($GLOBALS['opt_complain']==1 && $GLOBALS['iuserid']>3))) {
        $links[]='<a rel="nofollow" href="'.build_url($GLOBALS['intopic'],'m=moderate&amp;a=complain&amp;p='.$pdata[$i]['p_id']).'">'.MSG_p_tomoder."</a>";
      }
      if ($pdata[$i]['u_status']!=-1) {
        $links[]="<a onClick=\"return confirm('".MSG_p_ignore_warn1.' '.$pdata[$i]['p_uname'].' '.MSG_p_ingore_warn2."?')\" href=\"index.php?m=addrbook&amp;a=do_ignore&amp;uid=".$pdata[$i]['p_uid']."\">".MSG_p_ignore."</a>";
      }

      if ($GLOBALS['opt_hurl']==1) {
        $postlink='post/'.build_url($GLOBALS['intopic']).'p'.$pdata[$i]['p_id'].'.htm';
        $link_to_pcount = "#<a href=\"post/".build_url($GLOBALS['intopic'])."p".$pdata[$i]['p_id'].".htm#pp".$pdata[$i]['p_id']."\">".$pdata[$i]['cc']."</a>";
      }
      elseif ($GLOBALS['opt_hurl']==2) {
        $postlink=str_replace('index.php/','index.php/post/',build_url($GLOBALS['intopic']).'p'.$pdata[$i]['p_id'].'.htm');
        $link_to_pcount = "#<a href=\"index.php/post/".build_url($GLOBALS['intopic'])."p".$pdata[$i]['p_id'].".htm#pp".$pdata[$i]['p_id']."\">".$pdata[$i]['cc']."</a>";
      }
      else {
        $postlink='index.php?t='.$pdata[$i]['p_tid'].'&amp;p='.$pdata[$i]['p_id'];
        $link_to_pcount = "#<a href=\"index.php?t=".$pdata[$i]['p_tid']."&amp;p=".$pdata[$i]['p_id']."#pp".$pdata[$i]['p_id']."\">".$pdata[$i]['cc']."</a>";
      }
      if ($comments) {
        if ($GLOBALS['opt_hurl']) $postlink.='?';
        else $postlink='&amp;';
        $postlink.='a='.$GLOBALS['action'];
      }
      $postlink.='#pp'.$pdata[$i]['p_id'];
      if ($pdata[$i]['p_id']==$GLOBALS['intopic']['t__lastpostid']) $last=true;
      else $last=false;
      
      if ($pdata[$i]['p__time']>max($GLOBALS['inuser']['lv_time2'],$_SESSION['t'.$pdata['p_tid']]) && $GLOBALS['inuserid']!=$pdata[$i]['p_uid']) {
        $newcount++;
      }
      
      if ($users[$pdata[$i]['p_uid']]['u_status']==-1) display_topic_hidden($pdata[$i],$class);
      elseif ($pdata[$i]['p_uid']==2) display_topic_system($pdata[$i],$class);
      else display_topic_entry($pdata[$i],$users[$pdata[$i]['p_uid']],$class,$links,$links2,$postlink,$newcount,$last);
      if (!$comments && $tdata['t__stickypost'] && $i==0) display_topic_separator();
    }
  }
  display_topic_end($pages,$tdata,$rated,$prev,$next,$comments);
  if ($GLOBALS['opt_location_bottom']) main_location($GLOBALS['locations']);
  display_form($tdata['t_id']);
}

function display_comment_link($tid) {
  global $link;
  if ($tid) {
    $sql = "SELECT t_id,t_link, t_title, f_id, f_link, t__pcount+1 AS t__pcount ".
    "FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum ".
    "WHERE t_id=".$tid." AND t_fid=f_id";
    $res =&db_query($sql,$link);
    $tdata=db_fetch_array($res);
    db_free_result($res);
  }
  else {
    $tdata=$GLOBALS['intopic'];
    $params='a=view_comments';
  }
  display_comment_link_form($tdata,$params);
}

function display_form($tid=false) {
  if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpost'] &&
    (!$GLOBALS['intopic']['t__status'] || ($GLOBALS['intopic']['t__status']==2 && ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate'] || check_selfmod())))
    && !$GLOBALS['intopic']['f_status'] &&
    $GLOBALS['action']!='edit_from_draft') {
    $pdata['p__html']=($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lhtml']);
    $pdata['p__bcode']=$GLOBALS['inforum']['f_bcode'];
    $pdata['p__smiles']=$GLOBALS['inforum']['f_smiles'];
    $pdata['p_signature']=$GLOBALS['inuser']['u_usesignature'];
    if (!$tid) $pdata['p_tid']=$GLOBALS['topic'];
    else $pdata['p_tid']=$tid;
    display_post_form(MSG_p_create,$pdata,0);
  }
}

function locations(&$locations) {
}
