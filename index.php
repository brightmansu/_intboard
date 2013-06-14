<? /*

Main script for Intellect Board 2 Project

(C) 2004-2006, XXXX Pro, United Open Project (http://www.openproj.ru)
Visit us online: http://intboard.ru

*/

$IBOARD=1;

error_reporting(E_ALL & ~E_NOTICE);
set_error_handler("err_handler");
set_magic_quotes_runtime(0);
$start_time = microtime();

require("config/iboard.php");
require("xaphpi.php");

if ($GLOBALS['opt_gzip']) ob_start("ob_gzhandler");
else ob_start();

if (file_exists($GLOBALS['opt_dir'].'/config/ban_ip.txt')) {
  $ipdata=file($GLOBALS['opt_dir'].'/config/ban_ip.txt');
  $ip1=iptonum($_SERVER['REMOTE_ADDR']);
  $ip2=iptonum($_SERVER['HTTP_X_FORWARDED_FOR']);
  for ($i=0; $i<count($ipdata); $i++) {
    list($ip_start,$ip_end)=explode(':',trim($ipdata[$i]));
    if (!$ip_end) $ip_end=$ip_start;
    $ip_start=iptonum($ip_start);
    $ip_end=iptonum($ip_end);
    if ($ip_start<=$ip1 && $ip1<=$ip_end) global_error('Your IP is banned!'.$_SERVER['REMOTE_ADDR']);
    if ($ip2) if ($ip_start<=$ip2 && $ip2<=$ip_end) global_error('Your IP is banned!');
  }
  unset($ipdata,$ip1,$ip2,$ip_start,$ip_end);
}

require("common.php");
require("auth.php");
require("config/database.php");
require("db/".$DBdriver.".php");
if (file_exists('addons.php')) require("addons.php");

// Очистка массива параметров от MagicQuotes, если они присутствуют
if (get_magic_quotes_gpc()) {
  strips($_GET);
  strips($_POST);
  strips($_COOKIE); 
  strips($_REQUEST);
  if (isset($_SERVER['PHP_AUTH_USER'])) strips($_SERVER['PHP_AUTH_USER']); 
  if (isset($_SERVER['PHP_AUTH_PW']))   strips($_SERVER['PHP_AUTH_PW']);
}

// Установка соединения с БД
if ($DBpersist) $link=db_pconnect($DBhost,$DBusername,$DBpassword,$DBname);
else $link=db_connect($DBhost,$DBusername,$DBpassword,$DBname);

// Обработка HURL, если они включены в настройках
if ($GLOBALS['opt_hurl']) require('hurl.php');

// Получение базовых переменных и их проверка
$forum =&getvar("f");
$topic =&getvar("t");
$step =&getvar("step");
$module =&getvar("m");
$action =&getvar("a");
$start =&getvar("st");

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') { $ajax = true; } else { $ajax = false; }

if (!$forum) $forum=0;
if (!$topic) $topic=0;

if (strpos($module,"/")!==false || strpos($module,"\\")!==false || strpos($module,".")!==false) global_error("Hack attempt! Module: ".$module,1);
if (!is_numeric($forum) || $forum<0) global_error("Hack attempt! Forum: ".$forum,1);
if (!is_numeric($topic) || $topic<0) global_error("Hack attempt! Topic: ".$topic,1);
if ($start && !is_numeric($start) && $start!="all" && $start!="new") global_error("Hack attempt! Start: ".$start);

$order =&getvar('o');
if (preg_match("/\W+/",$order)) global_error("Hack attempt: order=$order");

// Обработка ситуаций "шаг к следующей/предыдущей теме
if ($step=="next" || $step=="prev") {
  if ($step=="next") {
    $sql = "SELECT t_id FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=$forum AND t_id>$topic ORDER BY t_id LIMIT 1";
  }
  else {
    $sql = "SELECT t_id FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=$forum AND t_id<$topic ORDER BY t_id DESC LIMIT 1";
  }
  $res=&db_query($sql,$link);
  if (db_num_rows($res)==0) redirect("index.php?t=".$topic);
  list($topic)=db_fetch_row($res);
  db_free_result($res);
  redirect("index.php?t=".$topic);
}

$curtime=time();

// Анализ HTTP_ACCEPT_LANGUAEGE и выбор языка, который будет использоваться
$lang =&getvar("lang");
if ($lang) {
  if (file_exists($GLOBALS['opt_dir']."/langs/$lang/main.php")) $_SESSION['lang']=$lang;
  else $lang="";
}
if (!$lang && isset($_SESSION['lang']) && $_SESSION['lang']) {
  if (file_exists($GLOBALS['opt_dir']."/langs/".$_SESSION['lang']."/main.php")) $lang=$_SESSION['lang'];
  else $lang="";
}

if (substr($action,0,3)!="do_" || getvar("preview") || getvar("more")) $do_mode=1;
else $do_mode=0;

// снижение приоритета UPDATE-операторов, если мы покказываем страницу, а не выполняем действие по обновлению
if(!isset($GLOBALS['DBheavyload'])) $GLOBALS['DBheavyload'] = 0;
if ($GLOBALS['DBdriver']=='mysql' && (($GLOBALS['DBheavyload'] & 1)==1) && $do_mode==1) {
  $sql='SET LOW_PRIORITY_UPDATES=1';
  $res=&db_query($sql,$link);
}

$GLOBALS['inuser'] =& auth_process($lang);
$GLOBALS['inuserid'] =& $inuser['u_id'];

// Проверка на поискового бота
if ($GLOBALS['inuserid']<=3) {
  require('bots.php');
}

// Подгрузка языковых и стилевых модулей
load_lang("main.php");
if (!$do_mode || getvar('preview')) load_style("message.php");
load_lang("format.php");
load_lang("addons.php");
if ($do_mode) {
  load_style("main.php");
  load_style("common.php");
}
//setlocale(LC_ALL,$inuser['ln_locale']);

$passresult =& auth_checkpass($GLOBALS['inuser']);
if ($passresult==-1) error(MSG_e_u_ipchanged,403);
elseif ($passresult==-2) error(MSG_e_u_inactive,403);
elseif ($passresult==-3) error(MSG_e_badpassword,403);

$inuserbasic =& $inuser['u__level'];
if ($GLOBALS['inuserbasic']==1024 && $action!="do_logout") { // если пользователь -- System, отправляем его сразу в АЦ
  redirect("admin/",301);
  ob_end_clean();
  db_close($link);
  exit();
}

if ($GLOBALS['opt_status'] && $module!="profile" && $action!="do_login" && $action!="login" && $inuserlevel<1000) {
    if (trim($GLOBALS['opt_closetext'])) error($GLOBALS['opt_closetext'],503);
    else error(MSG_e_closed_status,503);
}

if (!$action && !$module && !$forum && !(isset($forumurl) && $forumurl) && !(isset($topicurl) && $topicurl) && !$topic && !getvar('ct') && $GLOBALS['opt_mainpage']) $forum=$GLOBALS['opt_mainpage'];

if ($action=='complain' && $module=='moderate' && $GLOBALS['opt_complain']>0 && $GLOBALS['inuserid']<=3) {
   error(MSG_e_p_nocomplain,404);
}

// Подгрузка данных о теме, если она передана в параметрах
if (isset($topic) && $topic || isset($topicurl) && $topicurl) {
  $sql = 'SELECT t.*, t__lasttime AS lasttime, t.t__ratingsum/NULLIF(t__ratingcount,0) AS trating FROM  '.$GLOBALS['DBprefix'].'Topic t WHERE ';
  if ($topic) $sql.=' t_id="'.db_slashes($topic).'"';
  else $sql.=' t_link="'.db_slashes($topicurl).'"';
  $sql.=' LIMIT 1';
  $res =& db_query($sql,$link);
  if (db_num_rows($res)==0) {
    error(MSG_e_t_notexists,404);
  }
  $intopic =& db_fetch_array($res);
  db_free_result($res,$link);
  $GLOBALS['topic'] = $intopic['t_id'];
  $GLOBALS['meta_descr'] = $intopic['t_descr'];
  if (isset($intopic['t_ratingcount']) && $intopic['t_ratingcount']) $intopic['trating']=$intopic['t_ratingsim']/$intopic['t_ratingcount'];
  $intopic['pcount']=$intopic['t__pcount'];

  if (($GLOBALS['DBheavyload'] & 8)!=8  && $GLOBALS['inuserid']>3) {
    // извлечение вспомогательных параметров темы -- наличия закладки, голосования и т.д.
    // таблица prefix_AdminEntry используется исключительно потому, что все остальные таблицы
    // должны присоединяться через LEFT JOIN, так как в них требуемой строки может и не быть
    $sql = 'SELECT bm.tid AS bmk, sb.tid AS subscr, v.pvid AS voted, tv.tid AS visited, pl.* '.
    ' FROM '.$GLOBALS['DBprefix']."AdminEntry ".
    'LEFT JOIN '.$GLOBALS['DBprefix'].'TopicView tv ON (tv.tid='.$topic.' AND tv.uid='.$GLOBALS['inuserid'].')'.
    'LEFT JOIN '.$GLOBALS['DBprefix'].'Bookmark bm ON (bm.tid='.$topic.' AND bm.uid='.$GLOBALS['inuserid'].') '.
    'LEFT JOIN '.$GLOBALS['DBprefix'].'Subscription sb ON (sb.tid='.$topic.' AND sb.uid='.$GLOBALS['inuserid'].') '.
    'LEFT JOIN '.$GLOBALS['DBprefix'].'Poll pl ON (pl_tid='.$topic.') '.
    'LEFT JOIN '.$GLOBALS['DBprefix'].'Vote v ON (v.tid='.$topic.' AND v.uid='.$GLOBALS['inuserid'].') '.
    'WHERE ad_sortfield=100 LIMIT 1';
    $res=&db_query($sql,$link);
    $tmp=&db_fetch_array($res);
    db_free_result($res);
    $intopic=array_merge($intopic,$tmp);
    unset($tmp);
  }
  elseif ($GLOBALS['inuserid']>3) {
    $sql = 'SELECT tid FROM '.$GLOBALS['DBprefix'].'TopicView WHERE tid='.$topic.' AND uid='.$GLOBALS['inuserid'];
    $res=&db_query($sql,$link);
    list($tid)=db_fetch_row($res);
    db_free_result($res);
    $intopic['visited']=$tid;
    unset($tid);
  } else {
    $intopic['visited'] = $topic; 
  }

  $sql='SELECT p2.p_uid AS t_author FROM '.$GLOBALS['DBprefix'].'Post p2 WHERE p2.p_id='.$intopic['t__startpostid'];
  $res=&db_query($sql,$link);
  list($author)=db_fetch_row($res);
  db_free_result($res);
  $intopic['t_author']=&$author;
  unset($author);

  $GLOBALS['forum'] =& $intopic['t_fid'];
}

// Подгрузка данных о разделе, если он передан в параметрах
if (isset($forum) && $forum || isset($forumurl) && $forumurl) {
  $sql = "SELECT f.*, ct.*, tp.*  ".
  "FROM ".$GLOBALS['DBprefix']."ForumType tp, ".$GLOBALS['DBprefix']."Category ct, ".$GLOBALS['DBprefix']."Forum f ".
  "WHERE f.f_tpid=tp.tp_id AND f.f_ctid=ct.ct_id ".
  "AND ".check_access('f_id');
  if ($forum) $sql.=' AND f.f_id="'.db_slashes($forum).'"';
  else $sql.=' AND f.f_link="'.db_slashes($forumurl).'"';
  $sql.=' LIMIT 1';
  $res =& db_query($sql,$link);
  if (db_num_rows($res)==0) {
    error(MSG_e_t_notexists,404);
  }
  $inforum =& db_fetch_array($res);
  db_free_result($res,$link);
  if ($GLOBALS['inuser']['u_id']>3) {
    $sql = "SELECT lv_markall, lv_markcount, lv_time1, lv_time2 FROM ".$GLOBALS['DBprefix']."LastVisit lv WHERE (lv.fid=".$inforum['f_id']." AND lv.uid=".$GLOBALS['inuserid'].") ";
    $res =& db_query($sql,$link);
    if (db_num_rows($res)==1) {
      $lastdata =& db_fetch_array($res);
      $inforum=array_merge($inforum,$lastdata);
      unset($lastdata);
    }
    db_free_result($res);
  }

  $GLOBALS['forum']=&$inforum['f_id'];
  if(!isset($GLOBALS['meta_descr']) && $GLOBALS['meta_descr']) $GLOBALS['meta_descr']=strip_tags(trim(textout($inforum['f_descr'],1,1,0)));
  //if(!isset($GLOBALS['meta_descr']) || !$GLOBALS['meta_descr']) $GLOBALS['meta_descr']=strip_tags(trim(textout($inforum['f_descr'],1,1,0)));

  if (!(isset($intopic) && $intopic) && $inforum['f__lastpostid']) {
    $sql = 'SELECT p.p__time FROM '.$GLOBALS['DBprefix'].'Post p WHERE p.p_id='.$inforum['f__lastpostid'];
    $res=&db_query($sql,$link);
    list($lasttime)=db_fetch_row($res);
    db_free_result($res);
    $inforum['lasttime']=$lasttime;
    unset($lasttime);
  } else $inforum['lasttime']=$curtime;

  $GLOBALS['inuser']['lv_markall']=&$inforum['lv_markall'];
  $GLOBALS['inuser']['lv_markcount']=&$inforum['lv_markcount'];
  $GLOBALS['inuser']['lv_time1']=&$inforum['lv_time1'];
  $GLOBALS['inuser']['lv_time2']=&$inforum['lv_time2'];

  if ($inuser['forum_levels'][$inforum['f_id']] && $inuserbasic!=-1) $inuserlevel=$inuser['forum_levels'][$inforum['f_id']];
  else $inuserlevel=$inuserbasic;
  if (!$module) {
    $module = $inforum['tp_library'];
  }
  $flevel=$inforum['f_lmoderate'];

// принудительный редирект на новый вариант написания URL (со ссылок вида ?t=номер\
// или ссылок вида /раздел/номер/ на /раздел/название, если оно есть
  $newurl = "";
  if ($GLOBALS['opt_hurl'] && strtoupper($_SERVER['REQUEST_METHOD'])=="GET") {
    if ((strpos(request_uri(),'/index.php')!==false && $intopic) ||
      (isset($intopic) && $intopic && (($intopic['t_link'] != $topicurl) || ($inforum['f_link'] != $forumurl)))) {
      $newurl=$GLOBALS['opt_url'];
      if (substr($newurl,-1,1)!='/') $newurl.='/';
      $newurl.=build_url($intopic);
      if (isset($_GET['p'])) $newurl.='p'.$_GET['p'].'.htm';
      elseif (isset($_GET['st'])) $newurl.=$_GET['st'].'.htm';
      $qstr = $_SERVER['QUERY_STRING'];
      $qstr = str_replace('t='.$intopic['t_id'],'',$qstr);
      $qstr = str_replace('f='.$intopic['t_fid'],'',$qstr);
      $qstr = str_replace('st='.$_GET['st'],'',$qstr);
      $qstr = str_replace('p='.$_GET['p'],'',$qstr);
    }
    elseif ((strpos(request_uri(),'/index.php')!==false && $inforum) ||
      ($inforum && $inforum['f_link'] && !$forumurl)) {
      $newurl=$GLOBALS['opt_url'];
      if (substr($newurl,-1,1)!='/') $newurl.='/';
      $newurl.=build_url($inforum);
      if ($GLOBALS['action']=='f_rules') $newurl.='f_rules.htm';
      elseif (isset($_GET['st'])) $newurl.=$_GET['st'].'.htm';
      $qstr = $_SERVER['QUERY_STRING'];
      $qstr = str_replace('f='.$inforum['f_id'],'',$qstr);
      $qstr = str_replace('st='.$_GET['st'],'',$qstr);
    }
    if ($newurl) {
      if ($qstr) $newurl.='?'.$qstr;
      $newurl=str_replace('?&','?',$newurl);
      $newurl=str_replace('&&','&',$newurl);
      redirect($newurl,301);
    }
  }

  build_mod_list($forum,$flevel);
}
else $inuserlevel=$inuserbasic;

if(!isset($GLOBALS['meta_descr']) || !$GLOBALS['meta_descr']) $GLOBALS['meta_descr']=$GLOBALS["opt_descr"];

if ($GLOBALS['opt_hurl'] && strpos($_SERVER['QUERY_STRING'],'a=do_print')!==false) {
  $query_str = str_replace('a=do_print','',request_uri());
  $query_str = str_replace('&&','&',$query_str);
  $query_str = str_replace('?&','?',$query_str);
  $query_str=str_replace($GLOBALS['urldir'],'',$query_str);
  $newurl=$GLOBALS['opt_url'].'print'.$query_str;
  if (substr($newurl,-1,1)=='?') $newurl=substr($newurl,0,strlen($newurl)-1);
  redirect($newurl,301);
}

// Фиксация даты последнего визита в раздел
if ($inuserid>3 && $action!="do_mark_read") {
  $userlast1=isset($GLOBALS['inuser']['lv_time1'])?$GLOBALS['inuser']['lv_time1']:0;
  $userlast2=isset($GLOBALS['inuser']['lv_time2'])?$GLOBALS['inuser']['lv_time2']:0;
  if (!$GLOBALS['opt_visittime']) $GLOBALS['opt_visittime']=120;
  if ($userlast1<$curtime-$GLOBALS['opt_visittime']*60) $userlast2=$userlast1;
  $userlast1=$curtime;
  if (!$userlast2) $userlast2=0;

  $sql = "UPDATE ".$GLOBALS['DBprefix']."LastVisit SET  lv_time1=$userlast1, lv_time2=$userlast2 WHERE uid=$inuserid AND fid=$forum";
  $res=&db_query($sql,$link);
  if (db_affected_rows($res)==0) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."LastVisit WHERE uid=$inuserid AND fid=$forum";
    $res =& db_query($sql,$link);
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."LastVisit (uid,fid,lv_time1,lv_time2,lv_markall,lv_markcount) ".
    "VALUES ($inuserid,$forum,$userlast1,$userlast2,".intval($GLOBALS['inuser']['lv_markall']).",".intval($GLOBALS['inuser']['lv_markcount']).")";
    $res=&db_query($sql,$link);
  }
}

// Проверка модуля и действия
if (!$module) $module = "main";
if (!$action && !$forum) $action ="view";
elseif (!$action && $forum) $action = $inforum['tp_template']."_view";

if ($module!="main" || getvar('ct')!=0 || $forum!=0) $locations=array("<a href=\"./\">$opt_title</a>");
else $locations=array();

// Подгрузка модуля и стиля к нему
if (!is_file($opt_dir."/".$module.".php")) global_error(MSG_e_nomodule." ".$module.".php",1);
load_style("$module.php");
require ($opt_dir."/".$module.".php");

if (isset($inforum) && $module==$inforum['tp_template']) $locations=call_user_func($module."_locations",$locations);
else $locations=locations($locations);

if ($topic) $lasttime=$intopic['lasttime'];
elseif ($forum && !$topic) $lasttime=$inforum['lasttime'];
else $lasttime=$curtime;
if(isset($_SESSION['login_time'])) $lasttime=max($lasttime,$_SESSION['login_time']);

if (isset($_POST['continue'])) {
  main_action();
  header("HTTP/1.1 204 No Content");
  db_close($link);
  ob_end_clean();
  exit();
}

$last_modified = gmdate('D, d M Y H:i:s', $lasttime) .' GMT';
$etag = '"'. md5($last_modified.$GLOBALS['inuserid']) .'"';
$if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) : FALSE;
$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : FALSE;
if ($if_modified_since && strtotime($if_modified_since) >= $lasttime && $if_none_match && $if_none_match == $etag && $GLOBALS['inuserid']<3) {
    header("HTTP/1.1 304 Not Modified");
    header("Last-Modified: $last_modified");
    header("Etag: $etag");
    ob_end_clean();
    db_close($link);
    exit();
}
header("Last-Modified: $last_modified");
header("ETag: $etag");

#if ($GLOBALS['inuserid']<3) header("Cache-Control: public, must-revalidate");
#else header("Cache-Control: private, must-revalidate");

header("Content-Type: text/html; charset=".$GLOBALS['inuser']['ln_charset']);

if (strtoupper($_SERVER['REQUEST_METHOD'])=='HEAD') {
  db_close($link);
  ob_end_clean();
  exit();
}

// Выполнение операций с логами и списками присутствующих
$ip = iptonum(getip());
if ($opt_log==2 || ($opt_log==1 && $inuserid>3)) {
  $sid = session_id();
  $fname=log_file_name($curtime);
  $fh=fopen($fname,'a');
  while (!flock($fh,LOCK_EX)) sleep(1);
  fputs($fh,$GLOBALS['inuserid'].'|'.$GLOBALS['inuser']['u__name'].'|'.getip().'|'.md5($sid.$_SERVER['HTTP_USER_AGENT']).'|'.$curtime.'|'.$GLOBALS['action'].'|'.$GLOBALS['module'].'|'.$GLOBALS['forum'].'|'.$GLOBALS['topic'].'|'.referer_uri().'|'.$_SERVER['HTTP_USER_AGENT']."\n");
  fclose($fh);
}

// Фиксация пользователя в таблице присутствующих
// Удаляем старые записи об этом пользователе, если он только что вошел, чтобы не было дублей
if (isset($_POST['inusername']) && $GLOBALS['inuserid']>3) {
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Present WHERE pu_uid=".$GLOBALS['inuserid'];
  $res=&db_query($sql,$link);
}

$sql = "UPDATE ".$GLOBALS['DBprefix']."Present SET pu_ip=".$ip.', '.
'pu_uname="'.$GLOBALS['inuser']['u__name'].'", pu_lasttime='.$curtime.', '.
'pu_module="'.$GLOBALS['module'].'", pu_action="'.$GLOBALS['action'].'", pu_tid="'.$topic.'", pu_fid="'.$forum.'", '.
'pu_hits=pu_hits+1, pu_uid='.$GLOBALS['inuserid'].', pu_hidden='.$GLOBALS['inuser']['u_hidden'].' ';
if (isset($_POST['inusername']) && $GLOBALS['inuserid']>3) {
  $sql2=$sql.'WHERE pu_uid=1 AND pu_ip='.$ip;
}
elseif ($GLOBALS['inuserid']>3) $sql2=$sql.'WHERE pu_uid='.$GLOBALS['inuserid'];
else $sql2=$sql.'WHERE pu_uid=1 AND pu_ip='.$ip;
$res=&db_query($sql2,$link);

// если подходящей записи не было обнаружено
if (db_affected_rows($res)==0) {
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Present SET pu_ip=".iptonum(getip()).', '.
  'pu_uname="'.$GLOBALS['inuser']['u__name'].'", pu_lasttime='.$curtime.', '.
  'pu_module="'.$GLOBALS['module'].'", pu_action="'.$GLOBALS['action'].'", pu_tid="'.$topic.'", pu_fid="'.$forum.'", '.
  'pu_uid='.$GLOBALS['inuserid'].', pu_hits=1, pu_hidden='.$GLOBALS['inuser']['u_hidden'];
  $res=&db_query($sql,$link);
}

// Очистка таблицы присутствующих и кеша пользователей от устарвеших записей
$todaytime=mktime(0,0,0,date('m',$curtime),date('d',$curtime),date('Y',$curtime));
fclose(fopen($GLOBALS['opt_dir'].'/temp/logclean.txt', 'a+'));
$fh=fopen($GLOBALS['opt_dir'].'/temp/logclean.txt','r+');
while (!flock($fh,LOCK_EX)) sleep(1);
$logclean=intval(fgets($fh));
if ($logclean<$todaytime) {
  $oldtime = mktime(0,0,0,date('m',$curtime),date('d',$curtime)-$GLOBALS['opt_keeplogs'],date('Y',$curtime));
  $oldfname=log_file_name($oldtime);
  if (file_exists($oldfname)) unlink ($oldfname);

  $sql ="DELETE FROM ".$GLOBALS['DBprefix']."Present WHERE pu_lasttime<".($GLOBALS['curtime']-2*24*60*60);
  $res=&db_query($sql,$link);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=&db_query($sql,$link);

  rewind($fh);
  fwrite($fh,$curtime);

//  options_save();
}
fclose($fh);

// Вызов основного шаблона
if($GLOBALS["ajax"]) { main_action(); }
elseif ($do_mode) {
  if ($GLOBALS['inuser']['st_integrated']==1) require_once($opt_dir.'/styles/'.$GLOBALS['inuser']['st_file'].'/template.php');
  else require_once($opt_dir."/config/tmplate1.php");
}
else require_once($opt_dir."/config/tmplate2.php");

ob_end_flush();
db_close($link);
exit();

function main_action() {
  $funcs = get_defined_functions();
  if (array_search($GLOBALS['action'],$funcs['user'])===false) global_error("Undefined action: ".$GLOBALS['action'],1);
  #if (!isset($_POST['continue'])) ob_flush();
  call_user_func($GLOBALS['action']);
  #session_write_close();
}

function time_diff() {
  $curtime = microtime();
  $tdif = gettimedif($GLOBALS['start_time'],$curtime);
  main_time_diff(sprintf("%.4f",$tdif),$GLOBALS['query_count'],sprintf("%.4f",$GLOBALS['query_time']));
}

function menu() {
  $link = $GLOBALS['link'];
  if ($GLOBALS['opt_submenu']) $menusql="AND tp_menu=1";
  else $menusql="AND f_parent=0";
  $sql = "SELECT ct.ct_name,ct.ct_id,f.f_title,f.f_id FROM ".$GLOBALS['DBprefix']."Category ct, ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."ForumType tp ".
  "WHERE f.f_ctid=ct.ct_id AND f_tpid=tp_id $menusql AND ".check_access('f_id').
  " ORDER BY ct_sortfield,f_sortfield";
  $res =& db_query($sql,$link);
  menu_start();
  while ($fdata=&db_fetch_array($res)) {
    if ($fdata['ct_name']!=$oldcat) {
      if ($oldcat) menu_cat_end();
      menu_cat_entry($fdata);
      $oldcat=$fdata['ct_name'];
    }
    menu_entry($fdata);
  }
  menu_cat_end();
  menu_end();
}

function announce($id='') {
  if ($GLOBALS['opt_announce']==2 || ($GLOBALS['opt_announce']==1 && $GLOBALS['module']=="main" && $GLOBALS['action']=="view")) {
    announce_form($id);
  }
}

function err_handler($errno, $errstr, $errfile, $errline, $context) {
  if (error_reporting() == 0) {
    return;
  }
  if ($errno & (E_ALL ^ E_NOTICE)) {
    $types = array(1 => 'error', 2 => 'warning', 4 => 'parse error', 8 => 'notice', 16 => 'core error', 32 => 'core warning', 64 => 'compile error', 128 => 'compile warning', 256 => 'user error', 512 => 'user warning', 1024 => 'user notice', 2048 => 'strict warning', 4096 => 'recoverable fatal error');
    if (isset($context[DB_ERROR])) {
      $backtrace = array_reverse(debug_backtrace());
      $query_functions = array('db_query');
      foreach ($backtrace as $index => $function) {
        if (in_array($function['function'], $query_functions)) {
          $errline = $backtrace[$index]['line'];
          $errfile = $backtrace[$index]['file'];
          break;
        }
      }
    }
    global_error($types[$errno] .': '. $errstr .' in '. $errfile .' on line '. $errline .'.');
  }
}

function global_error($errtext,$notfound=0) {
  global $link;
  ob_end_clean();
  if ($GLOBALS['opt_gzip']) ob_start("ob_gzhandler");
  else ob_start();
  if (!headers_sent()) {
    if (!$notfound) header('HTTP/1.1 503 Service Unavailable');
    else header('HTTP/1.1 404 Not Found');
  }
  $fh=fopen("config/error.log","a");
  $errtext=str_replace("\r","",$errtext);
  $errtext=str_replace(array("<br>","<br />"),"\n",$errtext);
  $str="Date: ".date("r",time()).", IP: ".getip().($_SERVER['HTTP_X_FORWARDED_FOR']?",".$_SERVER['HTTP_X_FORWARDED_FOR']:"");
  $str.=", User: ".$GLOBALS['inuserid']." - ".$GLOBALS['inuser']['u__name'];
  $str.=", Module: ".$GLOBALS['module'].", Action: ".$GLOBALS['action'];
  $str.=", Request string: ".request_uri()."\n".$errtext."\n\n";
  fputs($fh,$str);
  fclose($fh);
  $opt_url=$GLOBALS['opt_url'];
  if (substr($opt_url,-1,1)!='/') $opt_url.='/';
  $err_tmp = nl2br(htmlspecialchars($errtext));
  if($GLOBALS["ajax"]) { echo $err_tmp; }
  else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<base href="<?=$opt_url;?>">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>Ошибка / <?=$GLOBALS['opt_title']?></title>
<style type="text/css">
<!--
html { background: #E5E5E5; }
body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; color: #3a3a3a; background: #fff; border: #bbb 1px solid; margin: 5px; padding: 10px; }
h1 { font-size: 15px; margin: 0; padding: 0; }
.copyright { font-size: 10px; color: gray; margin-top: 20px; }
li { padding: 5px 0; }
ul { margin: 10px 0; }
.global_error { border-left: 3px solid #f30; padding: 5px 10px; margin: 20px 0; background: #eee; }
-->
</style>
</head>
<body>
<h1>Ошибка / <?=$GLOBALS['opt_title']?></h1>
<?if($GLOBALS['inuserlevel']>=1000) {?><div class="global_error"><?=$err_tmp;?></div><?}?>
<p>Если ошибка возникает часто, <a href="feedback.php">сообщите</a> нам об этом с указанием действий, вызвавших ошибку.</p>
<ul>
<li><a href="<?=referer_uri()?referer_uri():$opt_url;?>">Вернуться на предыдущую страницу</a></li>
<li><a href="<?=$opt_url?>">Перейти на главную страницу</a></li>
</ul>
<div class="copyright" style="text-align: left">&copy; <?=$GLOBALS['opt_copyright'];?></div>
</body>
</html>
<? exit();
}
}

function check_point($name) {
  $GLOBALS['checkpoints'][$name]=sprintf("%.4f",gettimedif($GLOBALS['start_time'],microtime()));
}

function list_checks() {
  if (is_array($GLOBALS['checkpoints'])) {
    foreach ($GLOBALS['checkpoints'] as $name=>$value) echo "$name - $value<br>";
  }
}

function gettimedif($start,$stop) {
    list($startusec,$startsec) = explode(" ",$start);
    list($stopusec,$stopsec) = explode(" ",$stop);
    return ($stopsec-$startsec)+($stopusec-$startusec);
}
