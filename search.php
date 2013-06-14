<? /*

Search script for Intellect Board 2 Project

(C) 2004, 2005, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function view() {
  $starttime=24*60*60;
  $stoptime=$GLOBALS['curtime'];
  big_search_form("",$starttime,$stoptime,0,'');
}

function result() {
  global $link;
  $nogrp=&getvar("nogrp");
  $start=&getvar('st');
  $id=&getvar('srid');
  $sort =&getvar('o');
  if (!$sort) $sort='relevancy';
  if (getvar('desc')) $desc='DESC';
  else $desc='';
  if (!$nogrp) $order='ORDER BY f_sortfield, f_id, '.$sort.' '.$desc;
  else $order='ORDER BY '.$sort.' '.$desc;
  $dsc=0;
  if ((!$desc && $sort=='relevancy') || ($desc && $sort=='p__time')) $dsc=1;

  $sql="SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."SearchResult WHERE srid=\"$id\"";
  $res=&db_query($sql,$link);
  list($count)=db_fetch_row($res);
  db_free_result($res);

  $sdata=$GLOBALS['sdata'];
  $mode=$sdata['sr_mode'];
  $_POST['hl']=$sdata['sr_text'];

  $pageref="index.php?m=search&a=result&srid=$id&nogrp=$nogrp&desc=".$desc."&o=".$sort;
  $pages=&build_pages($count,$start,$GLOBALS['inuser']['u_mperpage'],$pageref);
  if ($start!="all") $limit = " LIMIT ".intval($start).", ".intval($GLOBALS['inuser']['u_mperpage']);

  search_result_start($pages);
  if ($mode==0) { //output posts
    $sql = "SELECT p.*,t.*,f.* FROM ".$GLOBALS['DBprefix']."SearchResult sr, ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Forum f ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=".$GLOBALS['inuserid']." AND ua.fid=f_id) ".
    "WHERE srid=\"$id\" AND srpid=p_id AND p_tid=t_id AND f_id=t_fid AND COALESCE(ua_level,".$GLOBALS['inuserlevel'].")>=f_lread ".
    "$order $limit";
    $res=&db_query($sql,$link);
    $oldfid=0;
    if (!$GLOBALS['opt_search_count']) $GLOBALS['opt_search_count']=100;
    if ($count==$GLOBALS['opt_search_count']) search_result_more();
    while ($entry=&db_fetch_array($res)) {
      if ($entry['f_id']!=$oldfid && !$nogrp) {
        search_result_forum($entry);
        $oldfid = $entry['f_id'];
      }
      search_result_post($entry,$sdata['sr_text']);
    }
  }
  else {  // output topics
    $order = str_replace('p__time','t__lasttime',$order);
    $sql = "SELECT t.*,f.* FROM ".$GLOBALS['DBprefix']."SearchResult sr, ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Forum f ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=".$GLOBALS['inuserid']." AND ua.fid=f_id) ".
    "WHERE srid=\"$id\" AND srpid=t_id AND f_id=t_fid AND COALESCE(ua_level,".$GLOBALS['inuserlevel'].")>=f_lread ".
    "$order $limit";
    $res=&db_query($sql,$link);
    $oldfid=0;
    if (!$GLOBALS['opt_search_count']) $GLOBALS['opt_search_count']=100;
    if ($count==$GLOBALS['opt_search_count']) search_result_more();
    while ($entry=&db_fetch_array($res)) {
      if ($entry['f_id']!=$oldfid && $nogrp) {
        if ($nogrp) search_result_forum($entry);
        $oldfid = $entry['f_id'];
      }
      search_result_topic($entry,$sdata['sr_text']);
    }
  }
  if (db_num_rows($res)==0) search_not_found();
  search_result_end($pages);
  big_search_form($sdata['sr_text'],$sdata['sr_starttime'],$sdata['sr_endtime'],
  $sdata['sr_type'],$sdata['sr_mode'],$sdata['sr_uname'],$dsc);
}

function do_post() {
  global $link;

  $username=&getvar('username');
  $order=&getvar('o');
  if (!$order) $order='relevancy';
  if (($order=='relevancy' && !getvar('desc')) || ($order=='p__time' && !getvar('desc'))) $descr="DESC";

  if ($username) {
    $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"$username\"";
    $res=&db_query($sql,$link);
    list($uid)=db_fetch_row($res);
    db_free_result($res);
    if (!$uid) $condition='p_uid=1 AND p_uname="'.getvar('name').'"';
    else $condition='p_uid='.$uid;
  }

  $text = $_POST['text'];
  if (!$text) $text=$_GET['text'];
  if (getvar("from")=="os") $text = UTF8toCP1251($text);
  $text=db_slashes($text);

  if (getvar("res")=='topic') $mode="1";
  else $mode="0";
  $forums=array();
  $forums=$_POST['fs'];
  if (!$forums) $forums=$_GET['fs'];
  if (is_array($forums)) foreach ($forums as $curvalue) {
    if ($sqldata) $sqldata .= " OR ";
    $sqldata .= "t_fid=\"".db_slashes($curvalue)."\"";
  }
  elseif ($forums=="all") { $sqldata="1=1"; $flist="fs=all"; }
  else error(MSG_search_noforums);

  $starttime=get_date_field("start");
  if (($endtime=get_date_field("end"))==-1) $endtime=$GLOBALS['curtime'];
  else $endtime=$endtime+23*60*60+59*60+59;
  if ($starttime>24*60*60) $timelimit="AND p__time>=\"$starttime\" ";
  if ($endtime<$GLOBALS['curtime']) $timelimit.="AND p__time<=\"$endtime\"";

  if ($GLOBALS['opt_search_ext']) $modedata=" IN BOOLEAN MODE";
  if ($GLOBALS['opt_search_limit']) $limit=" LIMIT ".$GLOBALS['opt_search_limit'];
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Search (sr_text,sr_mode,sr_type,sr_starttime,sr_endtime,sr_uname) VALUES (\"$text\",$mode,0,$starttime,$endtime,\"$username\")";
  $res=&db_query($sql,$link);
  $id=db_insert_id($res);

  if ($text) {
    if ($condition) $condition.=' AND ';
    $condition.=db_match("p.",$modedata,$text,'p.p_text','p.p_title').">0";
  }
  if (!$condition) error(MSG_e_search_nodata);

  if ($mode=="0") {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."SearchResult (srid,srpid,relevancy) ".
    "SELECT $id,p_id, ".db_match("p.",$modedata,$text,'p.p_text','p.p_title').
    " AS relevancy FROM ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post p ".
    "WHERE ($condition) AND ($sqldata) AND p_tid=t_id AND t_fid=f_id AND p__premoderate=0 $timelimit ORDER BY $order $descr $limit";
    $res=&db_query($sql,$link);
  }
  else {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."SearchResult (srid,srpid,relevancy) ".
    "SELECT DISTINCT $id,t_id, SUM(".db_match("p.",$modedata,$text,'p.p_text','p.p_title').") ".
    " AS relevancy FROM ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post p ".
    "WHERE ($condition) AND ($sqldata) AND p_tid=t_id AND t_fid=f_id AND p__premoderate=0 $timelimit ".
    "GROUP BY t_id ORDER BY $order $descr $limit";
    $res=&db_query($sql,$link);
  }
  clear_searches($id);
  if($_REQUEST['fs']!="all") $fs='&fs='.join(',',$_REQUEST['fs']);
  else $fs='&fs=all';

  $GLOBALS['refpage']="index.php?m=search&a=result&srid=$id&o=".$order."&nogrp=".getvar('nogrp')."&desc=".$descr.$fs;
  message(MSG_search_processing,1);
}

function do_topic() {
  global $link;

  $text=&getvar('text');
  $username=&getvar('username');
  $order=&getvar('o');
  if (!$order) $order='relevancy';
  if (($order=='relevancy' && !getvar('desc')) || ($order=='p__time' && !getvar('desc'))) $descr="DESC";

  if ($username) {
    $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"$username\"";
    $res=&db_query($sql,$link);
    list($uid)=db_fetch_row($res);
    db_free_result($res);
    if (!$uid) $condition='p_uid=1 AND p_uname="'.getvar('name').'"';
    else $condition='p_uid='.$uid;
  }

  $forums=array();
  $forums=$_POST['fs'];
  if (!$forums) $forums=$_GET['fs'];
  if (is_array($forums)) foreach ($forums as $curvalue) {
    if ($sqldata) $sqldata .= " OR ";
    $sqldata .= "f_id=\"".db_slashes($curvalue)."\"";
  }
  elseif ($forums=="all") { $sqldata="1=1"; $flist="fs=all"; }
  else error(MSG_search_noforums);

  $starttime=get_date_field("start");
  if (($endtime=get_date_field("end"))==-1) $endtime=$GLOBALS['curtime'];
  else $endtime=$endtime+23*60*60+59*60+59;
  if ($starttime>24*60*60) $timelimit="AND t__lasttime>=\"$starttime\" ";
  if ($endtime<$GLOBALS['curtime']) $timelimit.="AND t__lasttime<=\"$endtime\"";

  if ($GLOBALS['opt_search_ext']) $modedata=" IN BOOLEAN MODE";
  if ($GLOBALS['opt_search_limit']) $limit=" LIMIT ".$GLOBALS['opt_search_limit'];
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Search (sr_text,sr_mode,sr_type,sr_starttime,sr_endtime,sr_uname) VALUES (\"$text\",1,1,$starttime,$endtime,\"$username\")";
  $res=&db_query($sql,$link);
  $id=db_insert_id($res);

  if ($text) {
    if ($condition) $condition.=' AND ';
    $condition.=db_match2("t.",$modedata,$text,'t.t_title','t.t_descr').">0";
  }
  if (!$condition) error(MSG_e_search_nodata);
  
  $order=str_replace('p__time','t__lasttime',$order);

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."SearchResult (srid,srpid,relevancy) ".
  "SELECT $id,t_id, SUM(".db_match("t.",$modedata,$text,'t.t_title','t.t_descr').") AS relevancy ".
  "FROM ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post p ".
  "WHERE ($condition) AND ($sqldata) AND t_fid=f_id AND t__pcount>0 AND p_id=t__startpostid $timelimit GROUP BY t_id ORDER BY $order $descr $limit";
  $res=&db_query($sql,$link);
  clear_searches($id);

  $fs='&fs='.join(',',$_POST['fs']);
  $GLOBALS['refpage']="index.php?m=search&a=result&srid=$id&o=".$order."&nogrp=".getvar('nogrp')."&desc=".$descr.$fs;
  message(MSG_search_processing,1);
}


function clear_searches($id) {
  global $link;
  $count=$GLOBALS['opt_search_count'];
  if (!$count) $count=100;
  $minsearch=intval($id-$count-1);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Search WHERE sr_id<$minsearch";
  $res=&db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."SearchResult WHERE srid<$minsearch";
  $res=&db_query($sql,$link);
}

function build_search_select($level,$tpid=0,$condition="",$select="") {
    global $link;
    $sql="SELECT f_id,f_title,ct_name, f_sortfield, ct_sortfield, f_parent FROM ".$GLOBALS['DBprefix']."Category ct, ".$GLOBALS['DBprefix']."Forum f ".
        "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=".$GLOBALS['inuserid']." AND ua.fid=f.f_id) ".
        "WHERE $tpidsql COALESCE(ua_level,".$GLOBALS['inuserbasic'].")>=f_lread AND f_ctid=ct_id".
        " ORDER BY ct_sortfield";
        $res=&db_query($sql,$link);
        while ($tmpdata=db_fetch_array($res)) $forums[]=$tmpdata;
        db_free_result($res);
    $flist = "";
    $oldcat = "0";
    $buf=','.$GLOBALS['inuser']['forum_noaccess'].',';
    $fs=explode(',',$_GET['fs']);
    $forums=sort_forums_recurse($forums);
    if (is_array($forums)) foreach ($forums as $tmpdata) {
      if (strpos($buf,','.$tmpdata['f_id'].',')===false) {
        if ($tmpdata['ct_name']!=$oldcat) {
          if ($flist) $flist.="</OPTGROUP>";
          $flist.="<OPTGROUP label=\"".$tmpdata['ct_name']."\">";
          $oldcat=$tmpdata['ct_name'];
        }
        if (array_search($tmpdata['f_id'],$fs)!==false) $flist.="<option value=\"".$tmpdata['f_id']."\" selected>".$tmpdata['f_title'];
        else $flist.="<option value=\"".$tmpdata['f_id']."\">".$tmpdata['f_title'];
      }
    }
    $flist.="</optgroup>";
    return $flist;
}


function locations($locations) {
  if ($GLOBALS['action']=="view") array_push($locations,MSG_search);
  elseif ($GLOBALS['action']=="result") {
    global $link;
    $id=&getvar('srid');
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Search WHERE sr_id=\"$id\"";
    $res=&db_query($sql,$link);
    $GLOBALS['sdata']=&db_fetch_array($res);
    db_free_result($res);
    $text=htmlspecialchars($GLOBALS['sdata']['sr_text']);
    $user=htmlspecialchars($GLOBALS['sdata']['sr_uname']);

    if ($text) {
      if (!$user) {
        if ($GLOBALS['sdata']['sr_type']==0) array_push($locations,MSG_search_posts." \"$text\"");
        elseif ($GLOBALS['sdata']['sr_type']==1) array_push($locations,MSG_search_topics." \"$text\"");
      }
      else {
        if ($GLOBALS['sdata']['sr_type']==0) array_push($locations,MSG_search_posts." ".$text.", ".MSG_search_authored_by." $user");
        elseif ($GLOBALS['sdata']['sr_type']==1) array_push($locations,MSG_search_topics." ".$text.", ".MSG_search_authored_by." $user");
      }
    }
    else {
      if ($GLOBALS['sdata']['sr_type']==0) array_push($locations,MSG_search_user." $text");
      elseif ($GLOBALS['sdata']['sr_type']==1) array_push($locations,MSG_search_usertopic." $text");
    }
  }

  return $locations;
}