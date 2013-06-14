<? /*

RSS script for Intellect Board 2 Project

(C) 2006, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

$IBOARD=1;

error_reporting(E_ALL & ~E_NOTICE);
set_error_handler("err_handler");
set_magic_quotes_runtime(0);
$start_time = microtime();

require("xaphpi.php");
require("common.php");
require("config/database.php");
require("config/iboard.php");
require("db/".$DBdriver.".php");
require("addons.php");

$forum =&getvar("f");
$topic =&getvar("t");
$action =&getvar("a");
$count =&getvar('count');
if (!$count) $count=10;

if ($GLOBALS['opt_gzip']) ob_start("ob_gzhandler");
else ob_start();

$GLOBALS['inuserid']=1;
$GLOBALS['inuserbasic']="0";

if (!$action && $topic) $action="topic";
elseif (!$action && $forum) $action="forum";
elseif (!$action) $action="stats";

if ($DBpersist) $link=db_pconnect($DBhost,$DBusername,$DBpassword,$DBname);
else $link=db_connect($DBhost,$DBusername,$DBpassword,$DBname);

$sql = "SELECT st.*,ln.*, u_lformat FROM ".$GLOBALS['DBprefix']."User, ".$GLOBALS['DBprefix']."StyleSet st, ".$GLOBALS['DBprefix']."Language ln WHERE u_id=1 AND u_stid=st_id AND u_lnid=ln_id";
$res=&db_query($sql,$link);
$style=&db_fetch_array($res);
db_free_result($res);

$GLOBALS['inuser']=$style;

load_lang("main.php");
if (!isset($do_mode) || !$do_mode || getvar('preview')) load_style("message.php");
load_lang("format.php");

if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
  $starttime=intval(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']));
} else $starttime = 0;

$GLOBALS['smiles_fullpath']=1;

if ($action=="topic") { // содержимое темы
  $sql = "SELECT f_title, f_lread, f_link, f_id, t.*, COALESCE(ua_level,0) AS level FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Forum f ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ON (uid=1 AND f_id=fid) ".
  "WHERE t_id=\"".intval($topic)."\" AND t_fid=f_id";
  $res=&db_query($sql,$link);
  $tdata=&db_fetch_array($res);
  db_free_result($res);

  $title=$tdata['t_title'];
  $descr=$tdata['t_descr'];
  $url=build_url($tdata);

  if ($tdata['level']>=$tdata['f_lread']) {
    if ($starttime) $sqldata=' AND p__time>'.$starttime;
    else $sqldata = "";
    $sql = "SELECT p.*, file_size, file_name, file_id, file_type, file_key FROM ".$GLOBALS['DBprefix']."Post p ".
    " LEFT JOIN ".$GLOBALS['DBprefix']."File ON (p_attach=file_id) ".
    " WHERE p_tid=\"".intval($topic)."\" AND p__premoderate=0 ".$sqldata." ORDER BY p__time DESC LIMIT ".intval($count);
    $res=&db_query($sql,$link);
    while ($pdata=&db_fetch_array($res)) {
      $item['title']=$pdata['p_title']?$pdata['p_title']:$tdata['t_title'];
      $item['cat']=$tdata['f_title'];
      $item['descr']=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles']);
      if ($pdata['p_attach']) {
        $item['enclosure_url']=$GLOBALS['opt_url']."/file.php?fid=".$pdata['file_id']."&amp;key=".$pdata['file_key'];
        $item['enclosure_length']=$pdata['file_size'];
        $item['enclosure_type']=$pdata['file_type'];
      }
      else $item['enclosure_url']="";
      $item['date']=$pdata['p__time'];
      $item['author']=$pdata['p_uname'];
      if ($GLOBALS['opt_hurl']) $item['link']=build_url($tdata).'p'.$pdata['p_id'].'.htm#pp'.$pdata['p_id'];
      else $link="index.php?t=".$tdata['t_id']."&amp;p=".$pdata['p_id']."#pp".$pdata['p_id'];
      $items[]=$item;
    }
  }
  else $title="";
}
elseif ($action=="last_topics") { // только новые темы
  $title=$GLOBALS['opt_title'];
  $url=$GLOBALS['opt_url'];
  $descr = "";
  if ($starttime) $sqldata=' AND p__time>'.$starttime;
  else $sqldata = "";
  if ($type=&getvar('type')) $sqldata.=' AND f_tpid="'.$type.'"';
  $sql = "SELECT f_id, f_link, t.*, p_uname, p__time FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Forum f ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=1 AND ua.fid=f.f_id) ".
  "WHERE t_fid=f_id AND t__pcount>0 ".$sqldata." AND f.f_lview<=COALESCE(ua_level,0) AND t_id=p_tid AND t__startpostid=p_id ".
  "ORDER BY t_id DESC LIMIT ".intval($count);
  $res =&db_query($sql,$link);
  while ($tdata=&db_fetch_array($res)) {
    $item['title']=$tdata['t_title'];
    $item['descr']=$tdata['t_descr']."<br />";
    $item['descr'].=format_word($tdata['t__pcount'],MSG_p1,MSG_p2,MSG_p3).", ".
MSG_t_last.": ".$tdata['p_uname'].", ".long_date_out($tdata['p__time']);
    if ($tdata['t__status']) $item['descr'].="<br />".MSG_t_closed;
    $item['date']=$tdata['p__time'];
    $item['cat']=""; $item['enclosure_url']="";
    $item['author']=$tdata['p_uname'];
    $item['link']=build_url($tdata);
    $items[]=$item;
  }
  db_free_result($res);
}
elseif ($action=="forum") { // раздел
  $sql = "SELECT f.*, COALESCE(ua_level,0) AS level FROM ".$GLOBALS['DBprefix']."Forum f ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ON (uid=1 AND f_id=fid) ".
  "WHERE f_id=\"".intval($forum)."\"";
  $res=&db_query($sql,$link);
  $fdata=&db_fetch_array($res);
  db_free_result($res);

  $title=$fdata['f_title'];
  $descr=$fdata['f_descr'];
  $url=build_url($fdata);
  if ($starttime) $sqldata=' AND p__time>'.$starttime;
  else $sqldata = "";
  if ($fdata['level']>=$fdata['f_lread']) {
    $sql = "SELECT t.*, p__time, p_uname ".
    " FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post ".
    " WHERE t_fid=\"".intval($forum)."\" AND p_id=t__lastpostid AND t__pcount>0 ".$sqldata.
    " ORDER BY t__lasttime DESC LIMIT ".intval($count);
    $res=&db_query($sql,$link);
    while ($tdata=&db_fetch_array($res)) {
      $item['title']=$tdata['t_title'];
      $item['descr']=$tdata['t_descr']."<br />";
      $item['descr'].=format_word($tdata['t__pcount'],MSG_p1,MSG_p2,MSG_p3).", ".MSG_t_start.": ".$tdata['p_uname'].", ".long_date_out($tdata['p__time']);
      if ($tdata['t__status']) $item['descr'].="<br />".MSG_t_closed;
      $item['date']=$tdata['p__time'];
      $item['cat']=""; $item['enclosure_url']="";
      $item['author']=$tdata['p_uname'];
      $tdata=array_merge($tdata,$fdata);
      $item['link']=build_url($tdata);
      $items[]=$item;
    }
    db_free_result($res);
  }
  else $title="";
}
elseif ($action=="newtopic") {
  $sql = "SELECT f.*, COALESCE(ua_level,0) AS level FROM ".$GLOBALS['DBprefix']."Forum f ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ON (uid=1 AND f_id=fid) ".
  "WHERE f_id=\"".intval($forum)."\"";
  $res=&db_query($sql,$link);
  $fdata=&db_fetch_array($res);
  db_free_result($res);

  $title=$fdata['f_title']?$fdata['f_title']:$GLOBALS['opt_title'];
  $descr=$fdata['f_descr'];
  $url=build_url($fdata);
  
  if ($fdata['level']>=$fdata['f_lread']) {
    if ($starttime) $sqldata=' AND p__time>'.$starttime;
    else $sqldata = "";
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post ".
    " WHERE t_fid=\"".intval($forum)."\" AND p_id=t__startpostid AND t__pcount>0 ".$sqldata.
    " ORDER BY t__startpostid DESC LIMIT ".intval($count);
    $res=&db_query($sql,$link);
    while ($tdata=&db_fetch_array($res)) {
      $item['title']=$tdata['t_title'];
      $tdata['p_text']=strip_tags(textout($tdata['p_text'],$tdata['p__html'],$tdata['p__bcode'],$tdata['p__smiles']));
      if (strlen($tdata['p_text'])>1024)
      $item['descr']=substr($tdata['p_text'],0,1024)."...";
      else $item['descr']=$tdata['p_text'];
      $item['date']=$tdata['p__time'];
      $item['author']=$tdata['p_uname'];
      $tdata=array_merge($tdata,$fdata);
      $item['link']=build_url($tdata,"st=0");
      $item['cat']=""; $item['enclosure_url']="";
      $items[]=$item;
    }
    db_free_result($res);
  }
}
elseif ($action=="active") { // активные темы
  $title=$GLOBALS['opt_title'];
  $url=$GLOBALS['opt_url'];
  $descr = "";
  $period=&getvar('period');
  if ($starttime) $sqldata=' AND p__time>'.$starttime;
  else $sqldata = "";
  if ($type=&getvar('type')) $sqldata.=' AND f_tpid="'.$type.'"';
  if ($period) $sqldata.=" AND p__time>".(time()-$period*60*60*24);
  $sql = "SELECT t.*, f_id, f_link, p__time, p_uname FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post, ".$GLOBALS['DBprefix']."Forum f ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=1 AND ua.fid=f.f_id) ".
  "WHERE t_fid=f_id AND p_id=t__lastpostid AND f.f_lview<=COALESCE(ua_level,0) AND t__pcount>0 ".$sqldata." ".
  "ORDER BY t__pcount DESC LIMIT ".intval($count);
  $res =&db_query($sql,$link);
  while ($tdata=&db_fetch_array($res)) {
    $item['title']=$tdata['t_title'];
    $item['descr']=$tdata['t_descr']."<br />";
    $item['descr'].=format_word($tdata['t__pcount'],MSG_p1,MSG_p2,MSG_p3).", ".MSG_t_last.": ".$tdata['p_uname'].", ".long_date_out($tdata['p__time']);
    if ($tdata['t__status']) $item['descr'].="<br />".MSG_t_closed;
    $item['date']=$tdata['p__time'];
    $item['author']=$tdata['p_uname'];
    $item['link']=build_url($tdata);
    $item['cat']=""; $item['enclosure_url']="";
    $items[]=$item;
  }
  db_free_result($res);
}
elseif ($action=="allnew") { // все новые сообщения
  $title=$GLOBALS['opt_title'];
  $descr=$GLOBALS['opt_descr'];
  $url=$GLOBALS['opt_url'];
  $period=&getvar('days');

  if (!$period) $period=3;
  if ($period>60) $period=60;
  if (!getvar('all')) $sqldata=" AND f_tpid=1";
  else $sqldata = "";
  if ($period) $sqldata.=" AND p__time>".max($starttime,time()-$period*60*60*24);
  elseif ($starttime) $sqldata.=' AND p__time>'.$starttime;
  $sql = "SELECT f_id, f_link, t.*, p.*, file_id, file_name, file_size, file_type, file_key, f.f_title as ft ".
  "FROM ".$GLOBALS['DBprefix']."Post p ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Topic t ON (p_tid=t_id) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Forum f ON (t_fid=f_id) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=1 AND ua.fid=f.f_id) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."File ON (p_attach=file_id AND p_attach!=0) ".
  "WHERE  f.f_lview<=COALESCE(ua_level,0) AND p.p__premoderate=0 ".$sqldata." ".
  "ORDER BY p__time DESC";
  $res =&db_query($sql,$link);
  while ($tdata=&db_fetch_array($res)) {
    $item['title']=$tdata['t_title'];
    $item['cat']=$tdata['ft'];
    $item['descr']=textout($tdata['p_text'],$tdata['p__html'],$tdata['p__bcode'],$tdata['p__smiles']);
    if ($tdata['p_attach']) {
      $item['enclosure_url']=$GLOBALS['opt_url']."/file.php?fid=".$tdata['file_id']."&amp;key=".$tdata['file_key'];
      $item['enclosure_length']=$tdata['file_size'];
      $item['enclosure_type']=$tdata['file_type'];
    }
    else $item['enclosure_url'] = "";
    $item['date']=$tdata['p__time'];
    $item['author']=$tdata['p_uname'];
    if ($GLOBALS['opt_hurl']) $item['link']=build_url($tdata).'p'.$tdata['p_id'].'.htm#pp'.$tdata['p_id'];
    else $link="index.php?t=".$tdata['t_id']."&amp;p=".$tdata['p_id']."#pp".$tdata['p_id'];
    $items[]=$item;
  }
  db_free_result($res);
}
else { // общая статистика
  $title=$GLOBALS['opt_title'];
  $url=$GLOBALS['opt_url'];
  $descr=MSG_main_include;

  $sql = "SELECT SUM(f__pcount), SUM(f__tcount) FROM ".$GLOBALS['DBprefix']."Forum ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ON (uid=".$GLOBALS['inuserid']." AND fid=f_id) ".
  "WHERE f_nostats=0 AND f_lview<=COALESCE(ua_level,".$GLOBALS['inuserbasic'].")";
  $res =&db_query($sql,$link);
  list($p_total,$t_total) = db_fetch_row($res);
  db_free_result($res);

  $sql = "SELECT p__time FROM ".$GLOBALS['DBprefix']."Post ORDER BY p__time DESC LIMIT 1";
  $res=&db_query($sql,$link);
  list($lastdate)=db_fetch_row($res);
  db_free_result($res);

  $sql = "SELECT COUNT(u_id) AS u_total FROM ".$GLOBALS['DBprefix']."User u WHERE u_id>3";
  $res =&db_query($sql,$link);
  list($u_total) = db_fetch_row($res);
  db_free_result($res);

  $sql = "SELECT u_id, u__name, u__regdate FROM ".$GLOBALS['DBprefix']."User u ORDER BY u_id DESC LIMIT 1";
  $res =&db_query($sql,$link);
  list($uid,$uname,$udate) = db_fetch_row($res);
  $ucount['u_total'] = $u_total;
  db_free_result($res);

  $item['title']=MSG_main_include;
  $item['descr']=format_word($t_total,MSG_t1,MSG_t2,MSG_t3).", ".
    format_word($p_total,MSG_p1,MSG_p2,MSG_p3).", ".format_word($ucount['u_total'],MSG_u1,MSG_u2,MSG_u3).
    ".\n ".MSG_main_lastuser.": ".$uname;
  $item['date']=max($udate,$lastdate);
  $item['author']=$GLOBALS['opt_title'];
  $item['link']="";
  $item['cat']=""; 
  $item['enclosure_url']="";
  $items[]=$item;
}

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && count($items)==0) {
  header("HTTP/1.1 304 Not Modified");
  exit;
}
if ($url && $url!=$GLOBALS['opt_url']) $url=$GLOBALS['opt_url']."/".$url;
$buffer1 = '<?xml version="1.0" encoding="'.$style['ln_charset'].'"?>'."\n";
$buffer1.= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
$buffer1.= "<channel>\n";
$buffer1.= "<title>".htmlspecialchars($title, ENT_NOQUOTES)."</title>\n";
$buffer1.= "<link>".str_replace("&","&amp;",$url)."</link>\n";
$buffer1.= "<description>".htmlspecialchars($descr, ENT_NOQUOTES)."</description>\n";
$buffer1.= "<image><url>".str_replace("&","&amp;",$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/logo.gif")."</url><title>".htmlspecialchars($title, ENT_NOQUOTES)."</title><link>".str_replace("&","&amp;",$url)."</link></image>\n";
$buffer1.= "<language>".$style['ln_file']."</language>\n";
$buffer2 = ""; $lasttime = "";
if (isset($items) && is_array($items)) foreach ($items as $curitem) {
  $curitem['link']=str_replace('//','/',$curitem['link']);
  $buffer2.= "<item>";
  $buffer2.= "<guid>".$GLOBALS['opt_url']."/".$curitem['link']."</guid>";
  $buffer2.= "<title>".htmlspecialchars($curitem['title'], ENT_NOQUOTES)."</title>\n";
  $buffer2.= "<link>".$GLOBALS['opt_url']."/".$curitem['link']."</link>\n";
  $text=str_replace("[q]","",$curitem['descr']);
  $text=str_replace("[/q]","",$text);
  $buffer2.= "<description>".htmlspecialchars($text, ENT_NOQUOTES)."</description>\n";
  $buffer2.= "<author>".htmlspecialchars($GLOBALS['opt_mailout'], ENT_NOQUOTES)." (".htmlspecialchars($curitem['author'], ENT_NOQUOTES).")</author>\n";
  $buffer2.= "<category>".($curitem['cat']?htmlspecialchars($curitem['cat'],ENT_NOQUOTES):htmlspecialchars($title,ENT_NOQUOTES))."</category>\n";
  $buffer2.= "<pubDate>".date("D, d M Y H:i:s O",$curitem['date'])."</pubDate>\n";
  if ($curitem['date']>$lasttime) $lasttime=$curitem['date'];
  if ($curitem['enclosure_url']) {
    $buffer2.='<enclosure url="'.$curitem['enclosure_url'].'" length="'.
    $curitem['enclosure_length'].'" type="'.$curitem['enclosure_type'].'" />'."\n";
  }
  $buffer2.=  "</item>";
}
$lasttime = $lasttime?$lasttime:time();
$buffer = $buffer1."<lastBuildDate>".date("D, d M Y H:i:s O",$lasttime)."</lastBuildDate>\n".$buffer2;
$buffer.= "</channel>\n";
$buffer.= "</rss>";

header("Content-type: text/xml; charset=".$style['ln_charset']);
header('Last-Modified: '.gmdate("D, d M Y H:i:s", $lasttime)." GMT");
echo $buffer;

function err_handler($errno, $errstr, $errfile, $errline) {
  if ($errno & (E_ALL ^ E_NOTICE)) {
    $errfile = substr($errfile,strrpos($errfile,"/")+1);
    global_error("$errfile (line $errline)"." - ".$errstr);
  }
}

function global_error($errtext) {
  $fh=fopen("config/error.log","a");
  $errtext=str_replace("\r","",$errtext);
  $str="Date: ".date("r",time()).", IP: ". $_SERVER['REMOTE_ADDR'].",".$_SERVER['HTTP_X_FORWARDED_FOR'];
  $str.=", User".$GLOBALS['inuserid']." - ".$GLOBALS['inuser']['u__name'];
  $str.=", Module: ".$GLOBALS['module'].", Action: ".$GLOBALS['action']."\n".$errtext."\n\n";
  fputs($fh,$str);
  fclose($fh); ?>
<html><head><title><?=$opt_title;?></title>
<link rel="stylesheet" href="styles/<?=$GLOBALS['inuser']['st_file']?>/<?=$GLOBALS['inuser']['st_file']?>.css" type="text/css">
</head>
<body bgcolor="#DCDCDC">
<table border="0" cellspacing="1" class="innertable" align=center border=1 width="90%"><tr>
<td class="tablehead">GLOBAL FORUM ERROR: <?=htmlspecialchars($errtext);?>
<tr>
<td><ul><li><a href="<?=$_SERVER['HTTP_REFERER'];?>">Goto previous page</a>
<li><a href="index.php">Goto main page</a></li>
</table></body></html>
<? exit();
}


?>
