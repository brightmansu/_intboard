<?

function do_out_online($prefix,$starttime,$endtime,$group,$more) {
  global $link;
  if ($group==1) $grpsql="AND sid.uo_lasttime=uo.uo_time";
  $sql = 'SELECT pu_uid AS u_id, pu_uname AS u__name, pu_hidden AS u_hidden, '.
    'pu_action AS uo_action, pu_module AS uo_module, pu_ip AS uo_ip, pu_lasttime AS uo_time, '.
    "f.f_id,f.f_link,f.f_title,f.f_lview,t.t_id,t.t_title,t.t_link ".
    "FROM ".$GLOBALS['DBprefix']."Present pu ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."Forum f ON (pu.pu_fid=f.f_id) ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."Topic t ON (pu.pu_tid=t.t_id) ".
    "WHERE pu_lasttime>=$starttime ".
    "ORDER BY pu_lasttime DESC";
  $res =&db_query($sql,$link);
  while ($udata=&db_fetch_array($res)) {
    $comment=build_online_message($udata);
    if (!$udata['u_hidden'] || $GLOBALS['inuserlevel']>=1000) online_entry($udata,$comment);
  }
}

function build_online_message($udata) {
   if ($udata['uo_module']=="main") $comment = MSG_view_mainpage;
    elseif ($udata['f_id'] && $udata['f_lview']>$GLOBALS['inuserlevel']) $comment = MSG_view_mainpage;
    elseif (strpos($udata['uo_action'],"_view")!==false && $udata['t_id']) {
      $ftmp['f_id']=$udata['f_id'];
      $ftmp['f_link']=$udata['f_link'];
      $comment = MSG_view_topic." \"<a href=\"".build_url($udata)."\">".$udata['t_title']."</a>\" ".MSG_view_inforum." \"<a href=\"".build_url($ftmp)."\">".$udata['f_title']."</a>\"";
    }
    elseif (strpos($udata['uo_action'],"_view")!==false && $udata['f_id']) $comment = MSG_view_forum." \"<a href=\"".build_url($udata)."\">".$udata['f_title']."</a>\"";
    elseif ($udata['uo_action']=="do_post") {
      $ftmp['f_id']=$udata['f_id'];
      $ftmp['f_link']=$udata['f_link'];
      $comment = MSG_view_dopost." \"<a href=\"".build_url($udata)."\">".$udata['t_title']."</a>\" ".MSG_view_inforum." \"<a href=\"".build_url($ftmp).
       "\">".$udata['f_title']."</a>\"";
    }
    elseif ($udata['uo_action']=="do_topic") $comment = MSG_view_dotopic." ".MSG_view_inforum.
    " \"<a href=\"".build_url($udata)."\">".$udata['f_title']."</a>\"";
    elseif ($udata['uo_module']=="stdforum" && $udata['uo_action']=="rules") $comment = MSG_view_rules." ".
    MSG_view_inforum." \"<a href=\"".build_url($udata)."\">".$udata['f_title']."</a>\"";
    elseif ($udata['uo_module']=="profile") {
     if ($udata['uo_action']=="rules") $comment = MSG_view_rules." ".MSG_view_before_register;
     elseif ($udata['uo_action']=="register") $comment = MSG_view_registering;
     elseif ($udata['uo_action']=="do_register") $comment = MSG_view_registered;
     elseif ($udata['uo_action']=="login") $comment = MSG_view_logging_in;
     elseif ($udata['uo_action']=="do_login") $comment = MSG_view_logged_in;
     elseif ($udata['uo_action']=="do_logout") $comment = MSG_view_logged_out;
     elseif ($udata['uo_action']=="view") $comment = MSG_view_profile;
     elseif ($udata['uo_action']=="listusers") $comment = MSG_view_userlist;
     elseif ($udata['uo_action']=="online") $comment = MSG_view_online;
     else $comment = MSG_view_undescribed;
    }
    elseif ($udata['uo_module']=="search") {
     if ($udata['uo_action']=="view") $comment = MSG_view_start_search;
     elseif ($udata['uo_action']=="result") $comment = MSG_view_search_topic;
     else $comment = MSG_view_undescribed;
    }
    elseif ($udata['uo_module']=="newpost") {
     if ($udata['uo_action']=="view_updated") $comment= MSG_view_updated;
    }
    else $comment = MSG_view_undescribed;
    return $comment;
}
