<? /*

Agent script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

$IBOARD=1;
error_reporting(E_ERROR | E_WARNING | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

require("xaphpi.php");
require("auth.php");
require("config/database.php");
require("config/iboard.php");
require("db/$DBdriver.php");

if ($opt_gzip) ob_start("ob_gzhandler");

if ($DBpersist) $link=db_pconnect($DBhost,$DBusername,$DBpassword,$DBname);
else $link=db_connect($DBhost,$DBusername,$DBpassword,$DBname);

$action =&getvar("a");

$sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."Language ln, ".$GLOBALS['DBprefix']."StyleSet st ".
       "WHERE ln.ln_id=u.u_lnid AND st.st_id=u.u_stid AND u_id=1";
$res =&db_query($sql,$link);
$inuser =&db_fetch_array($res);
db_free_result($res);
require_once ($GLOBALS['opt_dir']."/langs/".$inuser['ln_file']."/main.php");
//setlocale(LC_ALL,$inuser['ln_locale']);
require_once($GLOBALS['opt_dir']."/styles/abstract/message.php");

$uid = intval(getvar("u"));
$key =&getvar("key");
if ($action!="email" && $action!="code") header("Content-type: text/html; charset=".$inuser['ln_charset']);

if ($action=="pass") {
  $result=auth_actpass();
  if ($result==-1) error(MSG_e_u_notfound);
  elseif ($result==-2) error(MSG_e_u_badkey);
  $_POST['refpage']="index.php?a=login&m=profile";
  message(MSG_u_pass_activated);
}
elseif ($action=="subscr") {
  $sql = "SELECT u__key FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  $tmp=db_fetch_row($res);
  db_free_result($res);
  $topic=&getvar("t");
  $forum=&getvar("f");

  if ($key!=md5($topic.$tmp[0]) || !$topic || !$tmp[0]) error(MSG_e_sub_badkey);
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription VALUES (\"$uid\",\"$topic\",\"$forum\")";
  $res =&db_query($sql,$link);
  message(MSG_sub_subscribed);
}
elseif ($action=="unsub") {
  $sql = "SELECT u__key FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  $tmp=db_fetch_row($res);
  db_free_result($res);
  $topic=&getvar("t");
  $forum=&getvar("f");

  if ($key!=md5($topic.$tmp[0])) error(MSG_e_sub_badkey);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=\"$uid\" AND tid=\"$topic\"";
  $res =&db_query($sql,$link);
  message(MSG_sub_unsubscribed);
}
elseif ($action=="email") {
  $sql = "SELECT u__email FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  $email = db_fetch_row($res);
  db_free_result($res);
  graph_out($email[0]);
}
elseif ($action=="activate") {
  $result=auth_activate();
  if ($result==-1) error(MSG_e_u_noactivation);
  elseif ($result==-2) error(MSG_e_u_notfound);
  elseif ($result==-3) error(MSG_e_u_badkey);
  message(MSG_user_profile_activated);
}
elseif ($action=="chat") {
  $sql = "SELECT u__key,u__password FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)!=1) error(MSG_e_u_notfound);
  $udata=&db_fetch_array($res);
  db_free_result($res);
  if ($key!=md5($udata['u__password'].$udata['u__key'])) error(MSG_e_u_badkey);
  $time=&getvar("time");
  $fid=&getvar("f");
  $curtime=time();
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Present SET pu_lasttime=$curtime WHERE pu_uid=\"$uid\" AND pu_fid=\"$fid\"";
  $res =&db_query($sql,$link);
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">';
  echo "<html><head><title>Chat refresh</title>";
  echo "<meta http-equiv=\"Refresh\" content=\"".($GLOBALS['opt_heretime']*60/3).";".$GLOBALS['opt_url']."/agent.php?u=$uid&amp;a=chat&amp;f=$fid&amp;key=$key&amp;time=$curtime\"></head><body><p>Refresh</p></body></html>";
}
elseif ($action=="code") {
  $pos = rand() % 8;
  $code = substr(md5(rand()),$pos,8);
  $sid=&getvar("sid");
  $time=time();
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Code WHERE time<".($time-$GLOBALS['opt_visittime']*60)." OR sid=\"".$sid."\"";
  $res =&db_query($sql,$link);
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Code SET sid=\"$sid\", code=\"$code\", time=".$time;
  $res =&db_query($sql,$link);

  graph_out($code);
}
elseif ($action=="logout") {
  auth_logout();
  header("Location: ".$GLOBALS['opt_url']);
}
db_close($link);

function graph_out($text) {
  $maxx=12*strlen($text);
  $maxy=30;
  $im = @imagecreate($maxx, $maxy) or error("Error using GD module!");
  header ("Content-type: image/png");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");                          // HTTP/1.0

  $base=4+rand() % 6;
  $background_color = imagecolorallocate ($im, 255, 255, 255);
  $text_color = imagecolorallocate ($im, 64+(rand() % 64), 0, 128+(rand() % 92));
  for ($i=0;$i<strlen($text); $i++) {
    imagestring ($im, 5, $base+11*$i+(rand() % 2), 8+(rand() % 3),  $text[$i], $text_color);
    $maxpix = rand() % 16;
    for ($j=0; $j<$maxpix; $j++) imagesetpixel($im, rand() % $maxx, rand() % $maxy, $text_color);
  }
  imagepng ($im);
}

function message($textmsg,$golink=0) {
    if ($_POST['refpage'])    $tmp_link1 = $_POST['refpage'];
    elseif ($GLOBALS['refpage']) $tmp_link1 = $GLOBALS['refpage'];
    elseif ($_SERVER['HTTP_REFERER']) $tmp_link1 =$_SERVER['HTTP_REFERER'];
    else $tmp_link1 = "<a href=\"javascript:document.history(-1)\">".MSG_go_back."</a>";
    $tmp_link2 = "<a href=\"index.php\">".MSG_go_mainpage."</a>";
    if ($golink) $newlink=$tmp_link1;
    output_message($textmsg,"<a href=\"$tmp_link1\">".MSG_go_back."</a>",$tmp_link2,"",$newlink);
}

function global_error($errmsg) {
  error($errmsg);
}

function error($errmsg) {
    $tmp_link1 = "<a href=\"".$_SERVER['HTTP_REFERER']."\">".MSG_go_back."</a>";
    $tmp_link2 = "<a href=\"index.php\">".MSG_go_mainpage."</a>";
    output_message(MSG_e.$errmsg,$tmp_link1,$tmp_link2,"");
    exit();
}