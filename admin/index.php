<?/*

Main administration script for Intellect Board 2

(c) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru
*/

error_reporting(E_ALL & ~E_NOTICE);
set_error_handler("err_handler");
set_magic_quotes_runtime(0);

$IBOARD=1;
$GLOBALS['admin']=1;
require("../xaphpi.php");
require("../common.php");
require("../addons.php");
require("../auth.php");
require("../config/database.php");
require("../config/iboard.php");
require("../db/$DBdriver.php");
//if (file_exists('../addons.php')) require("../addons.php");

if (get_magic_quotes_gpc()) {
  strips($_GET);
  strips($_POST);
  strips($_COOKIE);
}


if (file_exists("../install.php")) {
  if (!is_writable("../")) global_error("Can't delete install.php! Delete it manually via FTP!");
  unlink("../install.php");
  if (file_exists("../install.php")) global_error("Can't delete install.php! Delete it manually via FTP!");
}

global $link;
if ($DBpersist) $link=db_pconnect($DBhost,$DBusername,$DBpassword,$DBname);
else $link=db_connect($DBhost,$DBusername,$DBpassword,$DBname);

$GLOBALS['inuser']=auth_process("");
if (auth_checkpass($GLOBALS['inuser'])!=1) global_error("Invalid password");
if ($GLOBALS['inuser']['u__level']<1000) global_error("You are not Administrator");

$GLOBALS['inuserlevel']=1000;
$GLOBALS['inuserbasic']=1000;
$GLOBALS['inuserid']=$GLOBALS['inuser']['u_id'];

load_style("admin/message.php");
load_lang("admin.php");
load_lang("main.php");
load_lang("addons.php");
load_style("main.php");
load_style("common.php");
load_style("admin/main.php");
//setlocale(LC_ALL,$inuser['ln_locale']);

$action = getvar("a");
$module = getvar("m");

if (!$module) $module = "stats";
if (!$action) $action = "view";

load_style("admin/".$module.".php");
require ($module.".php");

if ($action=="logout") {
  $_SESSION['uid']=1;
  $_SESSION['password']="";
  $_SESSION['salt']=0;
  setcookie('IB2XP_long'.$GLOBALS['DBprefix'],"",24*60*60,"/");
  setcookie('IB2XP'.$GLOBALS['DBprefix'],"",24*60*60,"/");
  header("Location: $opt_url/agent.php?a=logout");
  exit();
}
elseif ($action=="do_backup") {
  check_post();
  if (!check_system_pass(getvar('sys_pass'))) error(MSG_e_badsyspass);
  if (!$mode=getvar("mode")) $mode="f";
  $curtime=time();
  $urldata=parse_url($GLOBALS['opt_url']);
  $filename=strtolower($urldata['host']);
//  $filename=str_replace("http://","",$filename);
  $filename=str_replace(".","_",$filename);
//  $filename=str_replace("/","_",$filename);
  $filename.="_".date("j",$curtime)."_".date("m",$curtime)."_".date("Y",$curtime).'_'.substr(md5(rand()),0,6).".sql";
  $filepath=$GLOBALS['opt_dir']."/temp/$filename";
  $cmdline=db_backup_cmd();
  eval('exec("'.$cmdline.'>'.$filepath.'");');
  if (!is_file($filepath) || filesize($filepath)==0) db_backup($filepath,'f');
  if ($mode=="gz") {
    if ($fp_out=gzopen($filepath.'.gz','wb9')) {
       if ($fp_in=fopen($filepath,'rb')) {
           while (!feof($fp_in)) gzwrite($fp_out,fread($fp_in,1024*512));
           fclose($fp_in);
           unlink($filepath);
       }
       gzclose($fp_out);
    }
  }
  if ($mode=="bz") {
    if ($fp_out=bzopen($filepath.'.bz2','w')) {
       if ($fp_in=fopen($filepath,'rb')) {
           while (!feof($fp_in)) bzwrite($fp_out,fread($fp_in,1024*512));
           fclose($fp_in);
           unlink($filepath);
       }
       bzclose($fp_out);
    }
  }
  header("Location: ".$GLOBALS['opt_url']."/admin/index.php?m=basic&a=backup_files");

  $GLOBALS['opt_last_backup']=$curtime;
  options_save();
}
elseif (isset($_POST['continue'])) {
  call_user_func($action);
  header("HTTP/1.1 204 No Content");
  exit();
}
else {
header("Content-type: text/html; charset=".$inuser['ln_charset']);
header("Cache-Control: private, must-revalidate");
header("Last-Modified: ".date("r"));

ad_main_start();

$sql="SELECT * FROM ".$GLOBALS['DBprefix']."AdminEntry ORDER BY ad_sortfield,ad_category";
$res=db_query($sql,$link);

$oldcat = "";
while ($menuitem=db_fetch_array($res)) {
  if ($menuitem['ad_category']!=$oldcat) {
    ad_category($menuitem);
    $oldcat = $menuitem['ad_category'];
  }
  ad_menuitem($menuitem);
}

ad_main_middle();

call_user_func($action);

ad_main_end();
}

function ad_message($msgtext,$text1,$link1) {
  if (strpos($link1,"IB2XPnew".$GLOBALS['DBprefix'])===false &&
    !isset($_COOKIE['IB2XPnew'.$GLOBALS['DBprefix']]) && $GLOBALS['action']!='do_logout') {
    if (strpos($link1,"?")===false) $link1.="?";
    else $link1.="&amp;";
    $link1.="IB2XPnew".$GLOBALS['DBprefix']."=".session_id();
  }

  output_message($msgtext,"<a href=\"$link1\">$text1</a>","<a href=\"admin/index.php\">".MSG_go_stats."</a>","");
}

function err_handler($errno, $errstr, $errfile, $errline) {
  if ($errno & (E_ALL ^ E_NOTICE)) {
    $errfile = substr($errstr,0,strrpos($errfile,"/")-1);
    global_error($errno." ($errfile line $errline)"." ".$errstr);
  }
}

function global_error($errtext) {
  $fh=fopen("../config/error.log","a");
  $errtext=str_replace("\n","<br>",$errtext);
  $errtext=str_replace("\r","",$errtext);
  $str=time()."|".$_SERVER['REMOTE_ADDR']."|".$_SERVER['HTTP_X_FORWARDED_FOR']."|".$GLOBALS['inuserid']."|".htmlspecialchars($GLOBALS['inuser']['u__name'])."|".htmlspecialchars($errtext)."\n";
  fputs($fh,$str);
  fclose($fh);?>
<html><head><title><?=$opt_title;?></title>
<link rel="stylesheet" href="../styles/abstract/abstract.css" type="text/css">
</head>
<body bgcolor="#DCDCDC">
<table border="0" cellspacing="1" class="innertable"><tr>
<td class="tablehead">GLOBAL ADMIN ERROR: <?=htmlspecialchars($errtext);?>
<tr>
<td><ul><li><a href="<?=$_SERVER['HTTP_REFERER'];?>">Goto previous page</a>
<li><a href="../index.php">Goto main page</a></li>
</table></body></html>
<? exit(); }

function gettimedif($start,$stop) {
    list($startusec,$startsec) = explode(" ",$start);
    list($stopusec,$stopsec) = explode(" ",$stop);
    return ($stopsec-$startsec)+($stopusec-$startusec);
}
