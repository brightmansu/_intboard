<? /*

Sitemap technology script for Intellect Board 2 Project

(C) 2006, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

$IBOARD=1;

error_reporting(E_ERROR | E_WARNING | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
set_error_handler("err_handler");
set_magic_quotes_runtime(0);
$start_time = microtime();

$GLOBALS['inuser']['ln_file']='ru';
$GLOBALS['inuser']['ln_charset']='cp1251';

require("xaphpi.php");
require("common.php");
require("config/database.php");
require("config/iboard.php");
require("db/$DBdriver.php");
require("addons.php");

$forum =&getvar("f");
$topic =&getvar("t");
$action =&getvar("a");
$count =&getvar('count');
if (!$count) $count=10;

if ($opt_gzip) ob_start("ob_gzhandler");

$GLOBALS['inuserid']=1;
$GLOBALS['inuserbasic']="0";

if (!$action && $topic) $action="topic";
elseif (!$action && $forum) $action="forum";
elseif (!$action) $action="stats";

if ($DBpersist) $link=db_pconnect($DBhost,$DBusername,$DBpassword,$DBname);
else $link=db_connect($DBhost,$DBusername,$DBpassword,$DBname);

$sql = "SELECT u_mperpage, u_tperpage, u_aperpage FROM ".$GLOBALS['DBprefix']."User, ".$GLOBALS['DBprefix']."StyleSet st, ".$GLOBALS['DBprefix']."Language ln WHERE u_id=1 AND u_stid=st_id AND u_lnid=ln_id";
$res=&db_query($sql,$link);
$style=&db_fetch_array($res);
db_free_result($res);

$GLOBALS['inuser']=$style;

load_lang("main.php");
if (!$do_mode || getvar('preview')) load_style("messages.php");
load_lang("format.php");

if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
{
  $starttime=strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
}

$GLOBALS['smiles_fullpath']=1;

if ($action=="forum") { // раздел
}
else {
}

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && count($items)==0) {
  header("HTTP/1.1 304 Not Modified");
  exit;
}
if ($url && $url!=$GLOBALS['opt_url']) $url=$GLOBALS['opt_url']."/".$url;
$buffer = '<?xml version="1.0" encoding="'.$style['ln_charset'].'" ?>'."\n";
$buffer.= '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
$buffer.= "<channel>\n";
$buffer.= "<title>".str_replace("&","&amp;",$title)."</title>\n";
$buffer.= "<link>".str_replace("&","&amp;",$url)."</link>\n";
$buffer.= "<description>".str_replace("&","&amp;",$descr)."</description>\n";
$buffer.= "<language>".$style['ln_file']."</language>\n";
if (is_array($items)) foreach ($items as $curitem) {
  $curitem['link']=str_replace('//','/',$curitem['link']);
  $buffer.= "<item>";
  $buffer.= "<guid>".$GLOBALS['opt_url']."/".$curitem['link']."</guid>";
  $buffer.= "<title>".$curitem['title']."</title>\n";
  $buffer.= "<link>".$GLOBALS['opt_url']."/".$curitem['link']."</link>\n";
  $text=str_replace("&","&amp;",$curitem['descr']);
  $text=str_replace(">","&gt;",$text);
  $text=str_replace("<","&lt;",$text);
  $buffer.= "<description>".$text."</description>\n";
  $buffer.= "<dc:creator>".$curitem['author']."</dc:creator>\n";
  $buffer.= "<pubDate>".date("D, d M Y H:i:s O",$curitem['date'])."</pubDate>\n";
  if ($curitem['date']>$lasttime) $lasttime=$curitem['date'];
  if ($curitem['enclosure_url']) {
    $buffer.='<enclosure url="'.$curitem['enclosure_url'].'" length="'.
    $curitem['enclosure_length'].'" type="'.$curitem['enclosure_type'].'" />'."\n";
  }
  $buffer.=  "</item>";
}
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
<link rel="stylesheet" href="styles/abstract/abstract.css" type="text/css">
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
