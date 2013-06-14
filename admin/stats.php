<? /*

Statistics script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function view() {
  $link = $GLOBALS['link'];
  $sql = "SELECT SUM(f__pcount) AS p_count, SUM(f__tcount) as t_count, MAX(f__lastpostid) AS p_max, ".
  "MAX(f__startpostid) AS t_max, SUM(f__pcount*(1-f_nostats)) AS p_nostats, SUM(f__tcount*(1-f_nostats)) AS t_nostats ".
  "FROM ".$GLOBALS['DBprefix']."Forum";
  $res = db_query($sql,$link);
  $stats = db_fetch_array($res);
  if ($stats['p_count']>0) {
    $sql = "SELECT t.*,p_uid,p_uname,p__time FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Topic t WHERE p.p_tid=t.t_id AND p__premoderate=0 ORDER BY p_id DESC LIMIT 1";
    $res = db_query($sql,$link);
    $lastpost=db_fetch_array($res);
    db_free_result($res);
  }

  $sql = "SELECT COUNT(p_id) FROM ".$GLOBALS['DBprefix']."Post WHERE p__premoderate=1";
  $res = db_query($sql,$link);
  list($premodcount)=db_fetch_row($res);
  $stats['premodcount']=$premodcount;

  $sql = "SELECT COUNT(*) AS u_count FROM ".$GLOBALS['DBprefix']."User WHERE u_id>3";
  $res = db_query($sql,$link);
  list($ucount)=db_fetch_row($res);
  $stats['u_count']=$ucount;
  $sql = "SELECT u_id,u__name,u__regdate FROM ".$GLOBALS['DBprefix']."User WHERE u_id>3 ORDER BY u_id DESC LIMIT 1";
  $res = db_query($sql,$link);
  $lastuser=db_fetch_array($res);
  $stats['u__name']=$lastuser['u__name'];
  $stats['u__regdate']=$lastuser['u__regdate'];

  $sql = "SELECT COUNT(file_id) AS files,SUM(file_size) AS size FROM ".$GLOBALS['DBprefix']."File";
  $res=db_query($sql,$link);
  $fstats=db_fetch_array($res);
  db_free_result($res);
  $stats['files']=$fstats['files'];
  $stats['size']=$fstats['size'];

  $curtime=time();

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p__time>".($curtime-24*60*60);
  $res=db_query($sql,$link);
  list($dayly) = db_fetch_row($res);
  db_free_result($res);

  $sql = "SELECT u_id, u__name FROM ".$GLOBALS['DBprefix']."User WHERE u__level>=1000";
  $res=db_query($sql,$link);
  $users='';
  while ($curuser=db_fetch_array($res)) {
    if ($users) $users.=', ';
    $users.=user_out($curuser['u__name'],$curuser['u_id']);
  }

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p__time>".($curtime-7*24*60*60);
  $res=db_query($sql,$link);
  list($weekly) = db_fetch_row($res);
  db_free_result($res);

  $stats['dayly']=$dayly;
  $stats['weekly']=$weekly;

  $sql=db_table_status();
  $res=db_query($sql,$link);
  while ($row=db_fetch_array($res)) {
    if ($row['name']) $row['Name']=$row['name'];
    if ($row['data_length']) $row['Data_length']=$row['data_length'];
    if (!$GLOBALS['DBprefix'] || strpos($row["Name"],$GLOBALS['DBprefix'])!==false) $dbsize+=$row["Data_length"];
  }

  if ($stats['t_count']>0) $stats['p_per_t'] = $stats['p_count']/$stats['t_count'];
  else $stats['p_per_t'] = MSG_na;
  if ($stats['u_count']>0) {
    $stats['p_per_u'] = $stats['p_count']/($stats['u_count']);
    $stats['t_per_u'] = $stats['t_count']/($stats['u_count']);
  }
  else {
    $stats['p_per_u'] = MSG_na;
    $stats['t_per_u'] = MSG_na;
  }

  $bots=array();
  if (file_exists($GLOBALS['opt_dir'].'/config/bots.txt')) {
    $botdata=file($GLOBALS['opt_dir'].'/config/bots.txt');
    foreach ($botdata as $curbot) {
      $bots[]=explode('|',$curbot);
    }
  }
  ad_statistics($stats,$lastpost,$dbsize,$users,$bots);
}

function stats() {
  $_GET['hits']=1;
  $_GET['truehits']=1;
  $_GET['sessions']=1;
  $_GET['hosts']=1;
  $_GET['users']=1;
  $_GET['period']='all';
  stats_form(mktime(0,0,0,1,1,2004),time());
}

function show_stats() {
  global $link;
  $starttime = get_date_field("start");
  $endtime = get_date_field("end")+23*60*60+59*60+59;
  $period=getvar("period");

  $hits = array();
  $truehits = array();
  $session = array();
  $hosts = array();
  $users = array();
  $posts = array();
  $topics = array();
  $searches = array();
  $mainpage = array();
  $fviews = array();
  $tviews = array();
  $hps = array();
  $spu = array();
  $hpu = array();

  for ($i=$starttime; $i<$endtime-$GLOBALS['inuser']['u_timeregion']; $i+=24*60*60) {
    if (is_file(log_file_name($i))) {
      $fh =fopen(log_file_name($i),'r');
      while ($buffer=fgets($fh)) {
        list($uid,$uname,$ip,$session,$time,$action,$module,$forum,$topic,$referer,$agent)=explode('|',$buffer);

        if (!$period || $period=="all") $group1=0;
        elseif ($period=="days") $group1=floor(($time+date('Z',$i))/(24*60*60));
        elseif ($period=="wdays") $group1=(floor(($time+date('Z',$i))/(24*60*60))) % 7;
        elseif ($period=="hours") $group1=floor((($time+date('Z',$i))/(60*60)) % 24);

        if (getvar("hits") || getvar("hps") || getvar("hpu")) {
          $hits[$group1]++;
          $count++;
        }

        if (getvar("truehits")) {
          if (strpos($action,'do_')===false) $truehits[$group1]++;
          $count++;
        }

        if (getvar("sessions") || getvar("hps") || getvar("spu")) {
          $sessions[$group1][$session]++;
          $count++;
        }

        if (getvar("hosts")) {
          $hosts[$group1][$ip]++;
          $count++;
        }

        if (getvar("users") || getvar("spu") || getvar("hpu")) {
          $users[$group1][$uid]++;
          $count++;
        }

        if (getvar("posts")) {
          if ($action=='do_post') {
            $posts[$group1]++;
            $count++;
          }
        }

        if (getvar("topics")) {
          if ($action=='do_topic') {
            $topics[$group1]++;
          }
          $count++;
        }

        if (getvar("searches")) {
          if ($action=='search') {
            $searches[$group1]++;
          }
          $count++;
        }

        if (getvar("mainpage")) {
          if ($action=='view' && $module=='main') {
            $mainpage[$group1]++;
          }
          $count++;
        }

        if (getvar("fviews")) {
          if ($forum!=0 && strpos($action,'view')!==false && !$topic) {
            $fviews[$group1]++;
          }
          $count++;
        }

        if (getvar("tviews")) {
          if ($topic!=0 && strpos($action,'view')!==false) {
            $tviews[$group1]++;
          }
          $count++;
        }
      }
      fclose($fh);
    }
  }

  $output=array();
  $data=array();
  if (!$period || $period=="all") { $output=array(MSG_stats_alltime); $data[]=0; }
  elseif ($period=="days") {
    for ($curtime=$starttime;$curtime<$endtime;$curtime+=24*60*60) {
      $output[floor(($curtime+date('Z',$i))/(24*60*60))]=short_date_out($curtime);
    }
  }
  elseif ($period=="wdays") {
    for ($i=0; $i<7; $i++) $output[$i]=strftime("%A",$i*24*60*60+1);
  }
  elseif ($period=="hours") {
    for ($i=0; $i<24; $i++) $output[$i]=$i.":00 - ".($i+1).":00";
  }

  stat_start($count);
  foreach ($output as $curpos=>$curout) {
    if (is_array($sessions[$curpos])) $scount[$curpos]=count($sessions[$curpos]);
    else $scount[$curpos]=0;
    if (is_array($hosts[$curpos])) $hcount[$curpos]=count($hosts[$curpos]);
    else $hcount[$curpos]=0;
    if (is_array($users[$curpos])) $ucount[$curpos]=count($users[$curpos]);
    else $ucount[$curpos]=0;
    if ($scount[$curpos]>0) $hps[$curpos]=$hits[$curpos]/$scount[$curpos];
    else $hps[$curpos]=0;
    if ($ucount[$curpos]>0) $spu[$curpos]=$scount[$curpos]/$ucount[$curpos];
    else $spu[$curpos]=0;
    if ($ucount[$curpos]>0) $hps[$curpos]=$hits[$curpos]/$ucount[$curpos];
    else $hps[$curpos]=0;

    if ($hits[$curpos]>0 || $truehits[$curpos]>0 || $scount[$curpos]>0 || $hcount[$curpos]>0 ||
       $users[$curpos]>0 || $topics[$curpos]>0 || $posts[$curpos]>0 || $searches[$curpos]>0 ||
       $mainpage[$curpos]>0 || $fviews[$curpos]>0 || $tviews[$curpos]>0 || $hps[$curpos]>0 || $spu[$curpos]>0 || $hpu[$curpos]>0) {
      stat_entry($curout,$curpos,$hits,$truehits,$scount,$hcount,$ucount,$topics,$posts,$searches,$mainpage,$fviews,$tviews,$hps,$spu,$hpu);
    }
  }
  stat_end();
  stats_form($starttime,$endtime);
}

function stat_select() {
  $end=time();
  $start=time()-7*24*60*60;
  $_GET['perpage']=50;
  stat_select_form($start,$end,"forum_only");
}

function get_topic_info(&$topics) {
  global $link;
  if (!is_array($topics) || count($topics)==0) return array();
  else {
    $sql = "SELECT t_id, t_title, t_link, f_id, f_link, f_title ".
    "FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum ".
    "WHERE t_fid=f_id AND t_id IN (".join(',',array_keys($topics)).')';
    $res=db_query($sql,$link);
    $data=array();
    while ($tmp=db_fetch_array($res)) $data[$tmp['t_id']]=$tmp;
    db_free_result($res);
    return $data;
  }
}

function get_forum_info(&$forums) {
  global $link;
  if (!is_array($forums) || count($forums)==0) return array();
  else {
    $sql = "SELECT f_id, f_link, f_title ".
    "FROM  ".$GLOBALS['DBprefix']."Forum ".
    "WHERE f_id IN (".join(',',array_keys($forums)).')';
    $res=db_query($sql,$link);
    $data=array();
    while ($tmp=db_fetch_array($res)) $data[$tmp['f_id']]=$tmp;
    db_free_result($res);
    return $data;
  }
}

function stat_process() {
  global $link;
  $mode = getvar("mode");
  if (!$starttime=getvar('starttime')) $starttime = get_date_field("start");
  if ($starttime==-1) $starttime=getvar("starttime");
  if (!$endtime=getvar("endtime")) $endtime = get_date_field("end")+23*60*60+59*60+59;

  for ($i=$starttime; $i<$endtime; $i=$i+24*60*60) {
    if (is_file(log_file_name($i))) {
      $fh =fopen(log_file_name($i),'r');
      while ($buffer=fgets($fh)) {
        list($uid,$uname,$ip,$session,$time,$action,$module,$forum,$topic,$referer,$agent)=explode('|',$buffer);

        if ($mode=="topic" && $topic) $topics[$topic]++;
        if ($mode=="forum_only" && !$topic && $forum) $forums[$forum]++;
        if ($mode=="forum" && $forum) {
          if ($topic) $topics[$topic]++;
          $forums[$forum]++;
        }
      }
      fclose($fh);
    }
  }

  if ($mode=='topic') {
    asort($topics,SORT_NUMERIC);
    $pagecount=count($topics);
    $data=get_topic_info($topics);
    $result=$topics;
  }
  else {
    asort($forums,SORT_NUMERIC);
    $pagecount=count($forums);
    $data=get_forum_info($forums);
    $result=$forums;
  }
  $perpage=getvar("perpage");
  $start = getvar('st');
  $pages=build_pages($pagecount,$start,$perpage,
     "admin/index.php?m=stats&a=stat_process&mode=$mode&perpage=$perpage&starttime=$starttime&endtime=$endtime");
  $result=array_reverse($result,TRUE);
  if ($start!='all') $result=array_slice_preserve_keys($result,$start,$perpage);
  stat_process_start($pages);
  foreach ($result as $id=>$count) {
    $lnk = build_url($data[$id]);
    if ($mode=="forum" || $mode=="forum_only") $msg=$data[$id]['f_title'];
    elseif ($mode=="topic") $msg=$data[$id]['t_title'];
    stat_process_entry($lnk,$msg,$count);
  }
  stat_process_end($pages);
  stat_select_form($starttime,$endtime,$mode);
}

function stat_detail() {
  $_GET['perpage']=25;
  stat_detail_form(mktime(0,0,0,date('n'),date('d'),date('Y'))-$GLOBALS['inuser']['u__timeregion'],time());
}

function stat_show_detail() {
  if (!$starttime=getvar('starttime')) $starttime = get_time_field("start");
  if (!$endtime=getvar('endtime')) $endtime = get_time_field("end");

  $data=array();
  $forums=array();
  $topics=array();
  if ($ipstart=getvar('ipstart')) $ipstart=iptonum($ipstart);
  if ($ipend=getvar('ipend')) $ipend=iptonum($ipend);
  if ($ipstart && !$ipend) $ipend=$ipstart;

  $names=str_replace(', ',',',str_replace(';',',',getvar('unames')));
  if ($names) $unames=explode(',',$names);
  else $unames=array();
  if (count($unames)==0 && $names) $unames=array($names);
  $umode = getvar('umode');
  if ($flist=getvar('flist')) $flist=','.str_replace(' ','',str_replace(';',',',$flist));
  if ($tlist=getvar('tlist')) $tlist=','.str_replace(' ','',str_replace(';',',',$tlist));
  for ($i=$starttime; $i<$endtime; $i=$i+24*60*60) {
    if (is_file(log_file_name($i))) {
      $fh =fopen(log_file_name($i),'r');
      while ($buffer=fgets($fh)) {
        list($uid,$uname,$ip,$session,$time,$action,$module,$forum,$topic,$referer,$agent)=explode('|',$buffer);
        $ipcheck=TRUE;
        $ipnum=iptonum($ip);
        if ($ipstart && $ipend) $ipcheck=($ipnum>=$ipstart && $ipnum<=$ipend);
        $usercheck=false;
        if (count($unames)==0) $usercheck=true;
        foreach ($unames as $curname) {
          if ($umode==0) $usercheck=$usercheck || ($uname==$curname);
          elseif ($umode==1) $usercheck=$usercheck || (strpos($uname,$curname)===0);
          elseif ($umode==2) $usercheck=$usercheck || (strpos($uname,$curname)!==false);
        }
        if ((!$flist || strpos($flist,','.$forum)!==false) && (!$tlist || strpos($tlist,','.$topic)!==false) && $usercheck && $ipcheck) {
          $data[]=$buffer;
          if ($forum) $forums[$forum]++;
          if ($topic) $topics[$topic]++;
        }
      }
      fclose($fh);
    }
  }
  require('../online.php');
  $fdata=get_forum_info($forums);
  $tdata=get_topic_info($topics);
  $perpage=getvar("perpage");
  $start = getvar('st');
  $pages=build_pages(count($data),$start,$perpage,
     "admin/index.php?m=stats&a=stat_show_detail&flist=".getvar('flist').
     "&tlist=".getvar('tlist').'&unames='.getvar('unames').'&umode='.getvar("umode")."&ipstart=".getvar('ipstart')."&ipend=".getvar('ipend')."&perpage=$perpage&starttime=$starttime&endtime=$endtime");
  if ($start!='all') $data=array_slice_preserve_keys($data,$start,$perpage);

  stat_detail_start($starttime,$endtime,$pages);
  foreach ($data as $buffer) {
    unset($udata);
    list($uid,$uname,$ip,$session,$time,$action,$module,$forum,$topic,$referer,$agent)=explode('|',$buffer);
    $udata['uo_action']=$action;
    $udata['uo_module']=$module;
    if ($topic) $udata=array_merge($udata,$tdata[$topic]);
    if ($forum && !$topic) $udata=array_merge($udata,$fdata[$forum]);
    $msg=build_online_message($udata);
    stat_detail_entry($uid,$uname,$ip,$time,$msg,$referer,$agent);
  }
  stat_detail_end($pages);
  stat_detail_form($starttime,$endtime);
}

function stat_resync() {
  global $link;

  $sql = "LOCK TABLES ".$GLOBALS['DBprefix']."Forum WRITE, ".$GLOBALS['DBprefix']."Topic WRITE, ".
    $GLOBALS['DBprefix']."Post READ, ".$GLOBALS['DBprefix']."ForumVC WRITE, ".$GLOBALS['DBprefix']."TopicVC READ";
  $res=db_query($sql,$link);

  $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f__tcount=0, f__pcount=0, f__lastpostid=0, f__startpostid=0, f__premodcount=0";
  $res=db_query($sql,$link);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET t__pcount=0, t__startpostid=0, t__lastpostid=0, t__ratingsum=0, t__ratingcount=0";
  $res=db_query($sql,$link);

  // количество сообщений
  $sql = "SELECT p_tid,COUNT(p_id) AS pcount, MIN(p_id) AS pmin, MAX(p_id) AS pmax, MAX(p__time) AS lasttime FROM ".$GLOBALS['DBprefix']."Post GROUP BY p_tid";
  $res=db_query($sql,$link);
  while ($tdata=db_fetch_array($res)) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET t__pcount=".$tdata['pcount'].", t__startpostid=".$tdata['pmin'].", t__lastpostid=".$tdata['pmax'].", t__lasttime=".$tdata['lasttime']." WHERE t_id=".$tdata['p_tid'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  // количество сообщений в форуме
  $sql = "SELECT t_fid, COUNT(t_id) AS tcount, SUM(t__pcount) AS pcount, MAX(t__startpostid) AS pstart, MAX(t__lastpostid) AS plast FROM ".$GLOBALS['DBprefix']."Topic GROUP BY t_fid";
  $res=db_query($sql,$link);
  while ($fdata=db_fetch_array($res)) {
    $sql2 = "SELECT COUNT(p_id) FROM  ".$GLOBALS['DBprefix']."Topic,  ".$GLOBALS['DBprefix']."Post WHERE t_fid=".$fdata['t_fid']." AND p_tid=t_id AND p__premoderate=1";
    $res2 = db_query($sql2,$link);
    list($premodcount) = db_fetch_row($res2);
    db_free_result($res2);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f__tcount=".$fdata['tcount'].", f__pcount=".$fdata['pcount'].", f__startpostid=".$fdata['pstart'].", f__lastpostid=".$fdata['plast'].", f__premodcount=".intval($premodcount)." WHERE f_id=".$fdata['t_fid'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  // количество просмотров форумов
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."ForumVC";
  $res=db_query($sql,$link);
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."ForumVC (fid,f__views) SELECT tid, SUM(t__views) FROM ".$GLOBALS['DBprefix']."TopicVC GROUP BY tid";
  $res=db_query($sql,$link);

  // количество сообщений на премодерации
  $sql = "SELECT t_fid, COUNT(p_id) AS premodcount FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Post WHERE p__premoderate=1 AND p_tid=t_id GROUP BY t_fid";
  $res=db_query($sql,$link);
  while ($fdata=db_fetch_array($res)) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f__premodcount=".$fdata['premodcount']."  WHERE f_id=".$fdata['t_fid'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  $sql = "UNLOCK TABLES";
  $res=db_query($sql,$link);

  $sql = "LOCK TABLES ".$GLOBALS['DBprefix']."Topic WRITE, ".$GLOBALS['DBprefix']."TopicRate READ";
  $res=db_query($sql,$link);

  // рейтинги тем
  $sql = "SELECT tid,SUM(tr_value) AS tr_sum, COUNT(tr_value) AS tr_count FROM ".$GLOBALS['DBprefix']."TopicRate GROUP BY tid";
  $res=db_query($sql,$link);
  while ($tdata=db_fetch_array($res)) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET t__ratingsum=".$tdata['tr_sum'].", t__ratingcount=".$tdata['tr_count']." WHERE t_id=".$tdata['tid'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  $sql = "UNLOCK TABLES";
  $res=db_query($sql,$link);

  $sql = "LOCK TABLES ".$GLOBALS['DBprefix']."UserStat WRITE, ".$GLOBALS['DBprefix']."Topic READ, ".$GLOBALS['DBprefix']."Post READ";
  $res=db_query($sql,$link);

  // статистика пользователей
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserStat";
  $res=db_query($sql,$link);
  $sql = "SELECT p_uid,t_fid, COUNT(p_id) AS ucount FROM ".$GLOBALS['DBprefix']."Post, ".$GLOBALS['DBprefix']."Topic WHERE p__premoderate=0 AND t_id=p_tid AND p_uid>3 GROUP BY p_uid,t_fid";
  $res=db_query($sql,$link);
  while ($udata=db_fetch_array($res)) {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UserStat SET uid=".$udata['p_uid'].", fid=".$udata['t_fid'].", us_count=".$udata['ucount'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  $sql = "UNLOCK TABLES";
  $res=db_query($sql,$link);

  $sql = "LOCK TABLES ".$GLOBALS['DBprefix']."Vote READ, ".$GLOBALS['DBprefix']."PollVariant WRITE";
  $res=db_query($sql,$link);

  // статистика голосований
  $sql = "SELECT pvid, COUNT(pvid) AS vcount FROM ".$GLOBALS['DBprefix']."Vote GROUP BY pvid";
  $res=db_query($sql,$link);
  while ($pvdata=db_fetch_array($res)) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."PollVariant SET pv_count=".$pvdata['vcount']."  WHERE pv_id=".$pvdata['pvid'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  $sql = "UNLOCK TABLES";
  $res=db_query($sql,$link);

  $sql = "LOCK TABLES ".$GLOBALS['DBprefix']."UserRating READ, ".$GLOBALS['DBprefix']."User WRITE, ".$GLOBALS['DBprefix']."UserWarning READ, ".$GLOBALS['DBprefix']."PersonalMessage READ";
  $res=db_query($sql,$link);

  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__rating=0, u__pmcount=0, u__warnings=0, u__warntime=0";
  $res=db_query($sql,$link);

  // рейтинг пользователя
  $sql = "SELECT uid,SUM(ur_value) AS urating FROM ".$GLOBALS['DBprefix']."UserRating GROUP BY uid";
  $res=db_query($sql,$link);
  while ($udata=db_fetch_array($res)) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__rating=".$udata['urating']." WHERE u_id=".$udata['uid'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  // подсчет предупреждений пользователя
  $sql = "SELECT uw_uid, SUM(uw_value) AS value, MAX(uw_validtill) AS date FROM ".$GLOBALS['DBprefix']."UserWarning ".
  "WHERE uw_validtill=0 OR uw_validtill>".time()." GROUP BY uw_uid";
  $res = db_query($sql,$link);
  while ($udata=db_fetch_array($res)) {
    if ($udata['date']<time()) $uw_validtill="0";
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__warnings=".intval($udata['value']).", u__warntime=".intval($udata['date'])." WHERE u_id=".$udata['uw_uid'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  // подсчет количества непрочитанных сообщений пользователя
  $sql = "SELECT pm__owner, COUNT(pm_id) AS pmcount FROM ".$GLOBALS['DBprefix']."PersonalMessage ".
  " WHERE pm__box=0 AND pm__readdate=0 GROUP BY pm__owner";
  $res=db_query($sql,$link);
  while ($udata=db_fetch_array($res)) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__pmcount=".intval($udata['pmcount'])."  WHERE u_id=".intval($udata['pm__owner']);
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);

  $sql = "UNLOCK TABLES";
  $res=db_query($sql,$link);

  ad_message(MSG_stats_syncdone,MSG_go_stats,"admin/index.php");
}

function array_slice_preserve_keys($array, $offset, $length = null)
{
   // PHP >= 5.0.2 is able to do this itself
   if (version_compare(phpversion(),'5.0.2','>='))
       return (array_slice($array, $offset, $length, true));

   // prepare input variables
   $result = array();
   $i = 0;
   if($offset < 0)
       $offset = count($array) + $offset;
   if($length > 0)
       $endOffset = $offset + $length;
   else if($length < 0)
       $endOffset = count($array) + $length;
   else
       $endOffset = count($array);

   // collect elements
   foreach($array as $key=>$value)
   {
       if($i >= $offset && $i < $endOffset)
           $result[$key] = $value;
       $i++;
   }

   // return
   return($result);
}
?>