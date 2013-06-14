<?  /*

Installation script for Intellect Board 2 Project

(C) 2004, 2005 XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

$IBOARD=1;
$GLOBALS['newversion']=222;  // Intellect Board 2.22 Final
error_reporting(E_ALL & ~E_NOTICE);
set_magic_quotes_runtime(0);

function &getvar($name) {
  if (strpos($name,"_text")===false) {
    if (isset($_GET[$name])) $tmp = addslashes($_GET[$name]);
    elseif (isset($_POST[$name])) $tmp= addslashes($_POST[$name]);
  }
  else {
    if (isset($_GET[$name])) $tmp = htmlspecialchars(addslashes($_GET[$name]));
    elseif (isset($_POST[$name])) $tmp= htmlspecialchars(addslashes($_POST[$name]));
  }
  return $tmp;
}

// Общая часть установки:

$action=&getvar("a");
if (!$action) $action="start";

if (!$dir=&getvar("dir")) $dir = realpath('./');
$dir=str_replace("\\","/",$dir);
if (!$url=&getvar("url")) $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$url = str_replace("/install.php","",$url);

$IBOARD=1;
if (file_exists($dir."/config/iboard.php") && filesize($dir."/config/iboard.php")>0) {
  require_once($dir."/config/iboard.php");
  if (!$GLOBALS['opt_ibversion'] && $GLOBALS['opt_dir']) $GLOBALS['opt_ibversion']=200;
}
elseif (file_exists($dir."/iboard.php")) {
  require_once($dir."/iboard.php");
  if (!$GLOBALS['opt_ibversion'] && $GLOBALS['opt_dir']) $GLOBALS['opt_ibversion']=200;
}

if (file_exists($dir."/config/database.php")) require_once($dir."/config/database.php");
elseif (file_exists($dir."/database.php")) require_once($dir."/database.php");

if ($action=="go") go_message();

if ($action=="more") phpinfo();
else {
  common_start();
  server_info();

  if ($action=="start") start_install();
  elseif ($action=="new1") new_install1();
  elseif ($action=="new2") new_install2();
  elseif ($action=="update") update_forum();
  elseif ($action=="clear") delete_forum();
  elseif ($action=="config") config_forum();
  elseif ($action=="restore") restore_forum();
  elseif ($action=="do_restore") do_restore_forum();
  elseif ($action=="do_config") do_config_forum();

  if ($GLOBALS['link'])  db_close($GLOBALS['link']);

  common_end();
}
exit();

function start_install() {
  if ($GLOBALS['opt_ibversion']) install_prev();
  else install_noprev();
}

// Функции выполнения отдельных действий

function zip_expand() {
  $dir=$GLOBALS['dir'];
  echo "Распаковка файла iboard2.zip";
  if (file_exists("iboard2.zip")) {
   $zip=zip_open("iboard2.zip");
   while ($zip_file=zip_read($zip)) {
     zip_entry_open($zip,$zip_file);
     $name = zip_entry_name($zip_file);
     $size=zip_entry_filesize($zip_file);
     $buffer=zip_entry_read($zip_file,$size);
     $file=fopen($dir."/".$name,"w") or global_error("Не удалось создать файл ".$name);
     fwrite($file,$buffer,$size);
     fclose($file);
     zip_entry_close($zip_file);
   }
   zip_close($zip);
   echo "- Ok<br>";
  }
  else echo("- Пропущен<br>");
}

function file_check() {
  $dir=$GLOBALS['dir'];
  echo("<br><b>Проверка файлов:</b><br>");

  $file=fopen($dir."/list.dat","r") or global_error("Не удается найти файл $dir/list.dat. Возможно, разархивация или закачка прошла некорректно.");
  while (!feof($file)) {
    $data = fgets($file);
    if ($data) {
      list($filename,$size)=explode("|",$data);
      if (file_exists($dir."/".$filename)) {
        if (filesize($dir."/".$filename)==$size) echo "Файл $filename - Ok<br>";
        else { echo "<p>Файл $filename - <font color=red>Не совпадает размер!</font><br>"; $result = 2; }
      }
      else { echo "<p>Файл $filename - <font color=red>Не найден!</font><br>"; $result = 1; }
    }
  }
  fclose($file);
  return $result;
}

function check_configs() {
  $dir=$GLOBALS['dir'];

  echo "Проверка доступности на запись каталога config - ";
  if (is_writable($GLOBALS['dir']."/config")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога temp - ";
  if (is_writable($GLOBALS['dir']."/temp")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога photos - ";
  if (is_writable($GLOBALS['dir']."/photos")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога photos/previews - ";
  if (is_writable($GLOBALS['dir']."/photos/previews")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога logs - ";
  if (is_writable($GLOBALS['dir']."/logs")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога smiles - ";
  if (is_writable($GLOBALS['dir']."/smiles")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога files - ";
  if (is_writable($GLOBALS['dir']."/files")) echo "Ok<br>";
  else global_error("не доступен для записи");

  create_file($dir."/config","iboard.php");
  create_file($dir."/config","database.php");
  create_file($dir."/config","error.log");
  create_file($dir."/config","tmplate1.php");
  create_file($dir."/config","tmplate2.php");
  create_file($dir."/config","head.txt");
  create_file($dir."/config","top.txt");
  create_file($dir."/config","bottom.txt");
  create_file($dir,"addons.php");

  echo "Проверка доступности на запись каталога temp - ";
  if (is_writable($dir."/temp")) echo "Ok<br>";
  else global_error("не доступен для записи");
}

function connect_database($checkdb=0) {
  $IBOARD=1;
  if (file_exists("config/database.php")) require("config/database.php");
  elseif (file_exists("database.php")) require("database.php");
  if (getvar("DBdriver")) $DBdriver=&getvar("DBdriver");
  if (getvar("DBhost")) $DBhost=&getvar("DBhost");
  if (getvar("DBusername")) $DBusername=&getvar("DBusername");
  if (getvar("DBpassword")) $DBpassword=&getvar("DBpassword");
  if (getvar("DBname")) $DBname=&getvar("DBname");
  if (getvar("DBcompress")) $DBname=&getvar("DBcompress");

  echo "Подключение драйвера базы данных - ";
  if (!file_exists($GLOBALS['dir']."/db/".$DBdriver.".php")) global_error("не найден файл драйвера db/".$DBdriver.".php");
  require_once($GLOBALS['dir']."/db/".$DBdriver.".php");
  echo "Ok<br>";

  if ($checkdb) {
    echo "Проверка доступности базы данных - ";
    $dbresult = db_exist_check($DBhost,$DBusername,$DBname,$DBpassword);
    if ($dbresult==-1) { echo "базы не существует и недостаточно прав для ее создания!"; exit(); }
    elseif ($dbresult==0) echo "не удалось выполнить проверку. Продолжаем установку, предполагая, что база все же существует.";
    elseif ($dbresult==1) echo "база существует.<br>";
    elseif ($dbresult==2) echo "база создана.<br>";
  }

  echo "Установка соединения с сервером - ";
  $GLOBALS['link']=db_connect($DBhost,$DBusername,$DBpassword,$DBname);
  echo "Ok<br>";
}

function process_sql($file) {
  $fh=fopen($GLOBALS['dir']."/db/".$file,"r") or global_error("Ошибка: не удалось открыть файл со структурой базы данных!");
  $buffer="";
  while (!feof($fh)) $buffer.=fgets($fh);
  fclose($fh);
  $buffer.="\n";
  $buffer=str_replace("\r","",$buffer);
  $prefix=$GLOBALS['DBprefix'];
  if (!$prefix) $prefix=&getvar("DBprefix");
  $buffer=str_replace("prefix_",$prefix,$buffer);
  $sqlarray=split(";\n",$buffer);
  global $link;
  foreach ($sqlarray as $sql) {
    $sql=trim(str_replace("\n","",$sql));
    if ($sql) db_query($sql,$link);
  }
}

function process_sql_increment($file,$mode) {
  $open=$mode."open";
  $read=$mode."read";
  $close=$mode."close";

  $fh=call_user_func($open,$file,"rb") or global_error("Ошибка: не удалось открыть файл с резервной копией!");
  $buflen=16*1024; // обработка файла блоками по 16 Kb
  $buffer="";
  global $link;
  while ($buffer.=call_user_func($read,$fh,$buflen)) {
    $buffer=str_replace("\r","",$buffer);
    $pos=strpos($buffer,";\n");
    while ($pos!==false) {
      $sql = trim(substr($buffer,0,$pos+1));
      $prefix=$GLOBALS['DBprefix'];
      if (!$prefix) $prefix=&getvar("DBprefix");
      $sql=str_replace("prefix_",$prefix,$sql);
      if ($sql) db_query($sql,$link);
      $buffer=substr($buffer,$pos+2);
      $pos=strpos($buffer,";\n");
    }
  }
  $sql = $buffer;
  if ($sql) db_query($sql,$link);
  call_user_func($close,$fh);
}

function extensions_check() {
  echo "<b>Установленные модули</b>:<br>";
  if ($mysql=extension_loaded('mysql')) echo "Клиент MySQL &mdash; установлен<br>";
  else "Клиент MySQL &mdash; <span style=\"color: #F00000\">не установлен!</span><br>";
  if ($mysqli=extension_loaded('mysqli')) echo "Клиент MySQLi &mdash; установлен<br>";
  else "Клиент MySQLi &mdash; <span style=\"color: #F00000\">не установлен!</span><br>";
  if ($pg=extension_loaded('pgsql')) echo "Клиент PostgreSQL &mdash; установлен<br>";
  else "Клиент PostgreSQL &mdash; <span style=\"color: #F00000\">не установлен!</span><br>";
  if (!$mysql && !$pg && !$mysqli) echo "<span style=\"color: #F00000\">У вас не установлен ни один из клиентов БД. Вы не сможете использовать Intellect Board!</span>";
  if ($gd=extension_loaded('gd')) echo "Графическая библиотека GD &mdash; установлена<br>";
  else "Графическая библиотека GD &mdash; <span style=\"color: #F00000\">не установлена!</span><br>";
  if (!$gd) echo "У вас не установлены графические библиотеки GD и GD2. Вы не сможете использовать графические функции форума.<br>";
  else "Графическая библиотека GD2 &mdash; <span style=\"color: #F00000\">не установлена!</span>.<br>";
  if (extension_loaded('zlib')) echo "Библиотека Zlib &mdash; установлена<br>";
  else "Библиотека Zlib &mdash; <span style=\"color: #F00000\">не установлена!</span> Вы не сможете использовать сжатие Web-страниц и GZIP-архивацию резервных копий.<br>";
  if (extension_loaded('bz2')) echo "Библиотека BZ2 &mdash; установлена.<br>";
  else echo "Библиотека BZ2 &mdash; <span style=\"color: #F00000\">не установлена!</span> Вы не сможете использовать BZ2-сжатие резервных копий.<br>";
}

function save_config_file() {
  echo ("Запись файла конфигурации форума iboard.php - ");
  $fh=fopen($GLOBALS['dir']."/config/iboard.php","w");
  if (!$fh) global_error("Не удалось создать файл iboard.php");

  if (!$GLOBALS['opt_title']) $GLOBALS['opt_title']="Форум на основе Intellect Board";
  if (!isset($GLOBALS['opt_descr'])) $GLOBALS['opt_descr']="Благодарим за выбор нашего движка";
  if (!isset($GLOBALS['opt_copyright'])) $GLOBALS['opt_copyright']="2004, Все права на содержимое сайта принадлежат его владельцу и охраняются законодательством";
  $GLOBALS['opt_dir']=$GLOBALS['dir'];
  $GLOBALS['opt_url']=$GLOBALS['url'];
  $url=parse_url($GLOBALS['opt_url']);
  if (!isset($GLOBALS['opt_mailout'])) $GLOBALS['opt_mailout']="admin@".$url['host'];
  if (!isset($GLOBALS['opt_cyrillic'])) $GLOBALS['opt_cyrillic']="1";
  if (!isset($GLOBALS['opt_heretime'])) $GLOBALS['opt_heretime']="15";
  if (!isset($GLOBALS['opt_visittime'])) $GLOBALS['opt_visittime']="120";
  if (!isset($GLOBALS['opt_showpresent'])) $GLOBALS['opt_showpresent']="3";
  if (!isset($GLOBALS['opt_log'])) $GLOBALS['opt_log']="2";
  if (!isset($GLOBALS['opt_announce']) && !$GLOBALS['opt_ibversion']) $GLOBALS['opt_announce']="2";
  if (!isset($GLOBALS['opt_announcetext']) && !$GLOBALS['opt_ibversion']) $GLOBALS['opt_announcetext']="Уважаемые посетители!<br> Наш форум еще не настроен. Пожалуйста, зайдите чуть позже!";
  if (!isset($GLOBALS['opt_status'])) $GLOBALS['opt_status']="0";
  if (!isset($GLOBALS['opt_gzip'])) $GLOBALS['opt_gzip']="0";
  if (!isset($GLOBALS['opt_encrypted'])) $GLOBALS['opt_encrypted']="2";
  if (!isset($GLOBALS['opt_hot'])) $GLOBALS['opt_hot']="20";
  if (!isset($GLOBALS['opt_flood'])) $GLOBALS['opt_flood']="3";
  if (!isset($GLOBALS['opt_activate'])) $GLOBALS['opt_activate']="0";
  if (!isset($GLOBALS['opt_reginfo'])) $GLOBALS['opt_reginfo']="0";
  if (!isset($GLOBALS['opt_posttitles'])) $GLOBALS['opt_posttitles']="1";
  if (!isset($GLOBALS['opt_defvotecount'])) $GLOBALS['opt_defvotecount']="5";
  if (!isset($GLOBALS['opt_fixviews'])) $GLOBALS['opt_fixviews']="0";
  if (!isset($GLOBALS['opt_warnstoban'])) $GLOBALS['opt_warnstoban']="4";
  if (!isset($GLOBALS['opt_ratetime'])) $GLOBALS['opt_ratetime']="7";
  if (!isset($GLOBALS['opt_ltitle'])) $GLOBALS['opt_ltitle']="100";
  if (!isset($GLOBALS['opt_maxavatarsize'])) $GLOBALS['opt_maxavatarsize']="32768";
  if (!isset($GLOBALS['opt_maxphoto'])) $GLOBALS['opt_maxphoto']="128000";
  if (!isset($GLOBALS['opt_maxavatarx'])) $GLOBALS['opt_maxavatarx']="100";
  if (!isset($GLOBALS['opt_maxavatary'])) $GLOBALS['opt_maxavatary']="100";
  if (!isset($GLOBALS['opt_avatarx'])) $GLOBALS['opt_avatarx']="32";
  if (!isset($GLOBALS['opt_avatary'])) $GLOBALS['opt_avatary']="32";
  if (!isset($GLOBALS['opt_maxfileattach'])) $GLOBALS['opt_maxfileattach']="128000";
  if (!isset($GLOBALS['opt_previewx'])) $GLOBALS['opt_previewx']="450";
  if (!isset($GLOBALS['opt_previewy'])) $GLOBALS['opt_previewy']="200";
  if (!isset($GLOBALS['opt_graphics'])) $GLOBALS['opt_graphics']="1";
  if (!isset($GLOBALS['opt_timeregion'])) $GLOBALS['opt_timeregion']="10800";
  if (!isset($GLOBALS['opt_logcount'])) $GLOBALS['opt_logcount']="100000";
  if (!isset($GLOBALS['opt_secbrowser'])) $GLOBALS['opt_secbrowser']="1";
  if (!isset($GLOBALS['opt_ddos'])) $GLOBALS['opt_ddos']="0";
  if (!isset($GLOBALS['opt_directlink'])) $GLOBALS['opt_directlink']="1";
  if (!isset($GLOBALS['opt_news_main_mode'])) $GLOBALS['opt_news_main_mode']="3";
  if (!isset($GLOBALS['opt_news_main_days'])) $GLOBALS['opt_news_main_days']="7";
  if (!isset($GLOBALS['opt_news_main_count'])) $GLOBALS['opt_news_main_count']="5";
  if (!isset($GLOBALS['opt_news_f_mode'])) $GLOBALS['opt_news_f_mode']="3";
  if (!isset($GLOBALS['opt_news_f_days'])) $GLOBALS['opt_news_f_days']="7";
  if (!isset($GLOBALS['opt_news_f_count'])) $GLOBALS['opt_news_f_count']="10";
  if (!isset($GLOBALS['opt_GD2'])) {
    if (extension_loaded('GD2')) $GLOBALS['opt_GD2']="1";
    else $GLOBALS['opt_GD2']="0";
  }
  if (!isset($GLOBALS['opt_submenu'])) $GLOBALS['opt_submenu']="0";
  if (!isset($GLOBALS['opt_noname_mail'])) $GLOBALS['opt_noname_mail']="0";
  if (!isset($GLOBALS['opt_minpost'])) $GLOBALS['opt_minpost']="1";
  if (!isset($GLOBALS['opt_maxpost'])) $GLOBALS['opt_maxpost']="102400";
  if (!isset($GLOBALS['opt_sigpics'])) $GLOBALS['opt_sigpics']="0";
  if (!isset($GLOBALS['opt_photos_line'])) $GLOBALS['opt_photos_line']="5";
  if (!isset($GLOBALS['opt_photo_thumb_y'])) $GLOBALS['opt_photo_thumb_y']="100";
  if (!isset($GLOBALS['opt_photo_size_x'])) $GLOBALS['opt_photo_size_x']="720";
  if (!isset($GLOBALS['opt_thumb_qlty'])) $GLOBALS['opt_thumb_qlty']="70";
  if (!isset($GLOBALS['opt_photo_qlty'])) $GLOBALS['opt_photo_qlty']="80";
  if (!isset($GLOBALS['opt_impersonation'])) $GLOBALS['opt_impersonation']="0";
  if (!isset($GLOBALS['opt_article_split'])) $GLOBALS['opt_article_split']="5000";
  if (!isset($GLOBALS['opt_summary'])) $GLOBALS['opt_summary']="1";
  if (!isset($GLOBALS['opt_exttopic'])) $GLOBALS['opt_exttopic']="1";
  if (!isset($GLOBALS['opt_brutetimeout'])) $GLOBALS['opt_brutetimeout']="1";
  if (!isset($GLOBALS['opt_logo_instead'])) $GLOBALS['opt_logo_instead']="0";
  if (!isset($GLOBALS['opt_exttopic'])) $GLOBALS['opt_exttopic']="1";
  if (!isset($GLOBALS['opt_hinttext'])) $GLOBALS['opt_hinttext']=100;
  if (!isset($GLOBALS['opt_fwelcome'])) $GLOBALS['opt_fwelcome']=1;
  if (!isset($GLOBALS['opt_exttopic'])) $GLOBALS['opt_exttopic']=1;
  if (!isset($GLOBALS['opt_location_bottom'])) $GLOBALS['opt_location_bottom']=1;
  if (!isset($GLOBALS['opt_search_limit'])) $GLOBALS['opt_search_limit']=100;
  if (!isset($GLOBALS['opt_search_count'])) $GLOBALS['opt_search_count']=100;
  if (!isset($GLOBALS['opt_search_ext'])) $GLOBALS['opt_search_ext']=1;
  if (!isset($GLOBALS['opt_imgtag'])) $GLOBALS['opt_imgtag']=0;
  if (!isset($GLOBALS['opt_imglimit_x'])) $GLOBALS['opt_imglimit_x']=700;
  if (!isset($GLOBALS['opt_imglimit_y'])) $GLOBALS['opt_imglimit_y']=1000;
  if (!isset($GLOBALS['opt_maxphoto'])) $GLOBALS['opt_maxphoto']=200000;
  if (!isset($GLOBALS['opt_maxphotox'])) $GLOBALS['opt_maxphotox']=700;
  if (!isset($GLOBALS['opt_maxphotoy'])) $GLOBALS['opt_maxphotoy']=600;
  if (!isset($GLOBALS['opt_blog_level'])) $GLOBALS['opt_blog_level']=1024;
  if (!isset($GLOBALS['opt_gallery_level'])) $GLOBALS['opt_gallery_level']=1024;

  $GLOBALS['opt_ibversion']=$GLOBALS['newversion'];

  fputs($fh,"<? \nif (!\$IBOARD) global_error(\"Hack attempt!\");\n");
  $keys = preg_grep("/opt_/",array_keys($GLOBALS));
  foreach ($keys as $curkey) {
    fputs($fh,"\$$curkey=\"".addslashes($GLOBALS[$curkey])."\";\n");
  }
  fputs($fh,"?>");
  fclose($fh);
  echo "Ok<br>";
}

function save_database_file() {
  echo ("Запись файла базы данных database.php - ");
  $dir=$GLOBALS['dir'];
  $fh = fopen($dir."/config/database.php","w") or global_error("Не удается создать файл database.php");
  fputs($fh,"<? if (!\$IBOARD) global_error(\"Hack attempt!\");\n");
  fputs($fh,"\$DBhost=\"".getvar("DBhost")."\";\n");
  fputs($fh,"\$DBusername=\"".getvar("DBusername")."\";\n");
  fputs($fh,"\$DBpassword=\"".getvar("DBpassword")."\";\n");
  fputs($fh,"\$DBname=\"".getvar("DBname")."\";\n");
  fputs($fh,"\$DBprefix=\"".getvar("DBprefix")."\";\n");
  fputs($fh,"\$DBpersist=\"".getvar("DBpersist")."\";\n");
  fputs($fh,"\$DBdriver=\"".getvar("DBdriver")."\";\n");
  fputs($fh,"\$DBcompress=\"".getvar("DBcompress")."\";\n?>");
  if ($GLOBALS['DBheavyload']) fputs($fh,"\$DBheavyload=\"".$GLOBALS["DBheavyload"]."\";\n?>");
  fclose($fh);
  echo "Ok<br>";
}

function check_pass() {
  if (getvar("syspass1")!=getvar("syspass2")) global_error("Введенный пароль пользователя System не совпадает с его подтверждением");
}

function set_root_pass() {
  echo "Установка пароля пользователя System - ";
  $password = md5(getvar("syspass1"));
  $trash = rand();
  $newkey=substr(md5($trash),0,rand() % 6 + 6);
  global $link;
  $sql="UPDATE ".getvar("DBprefix")."User SET u__password=\"".addslashes($password)."\", u_encrypted=1, u__key=\"$newkey\" WHERE u_id=2";
  db_query($sql,$link);


  $udata['u_id']=2;
  $udata['u__key']=$newkey;
  $udata['u__password']=$password;
  require('auth.php');
  $GLOBALS['key']=generate_key($udata);

  echo("Ok<br>");
}

function create_file($dir,$filename) {
  echo("Проверка файла $filename ");
  if (file_exists($dir."/$filename") && !is_writable($dir."/$filename")) global_error(" - ошибка: файл недоступен для записи!");
  elseif (!file_exists($dir."/$filename") && !is_writable("$dir")) global_error(" - ошибка: файла $filename не существует и его невозможно создать (каталог $dir недоступен для записи)");
  elseif (!file_exists($dir."/$filename") && is_writable("$dir/")) {
    $fh=fopen($dir."/$filename","w");
    fclose($fh);
    eval('chmod($dir."/$filename",0644);');
    echo "- Ok<br>";
  }
  else echo "- Ok<br>";
}

function create_templates() {
  $dir=$GLOBALS['dir'];
  if (filesize($dir."/config/tmplate1.php")==0) {
    echo "Генерация шаблона tmplate1.php - ";
    copy($dir."/config/tmplate1.def",$dir."/config/tmplate1.php");
    echo "Ok.<br>";
  }
  if (filesize($dir."/config/tmplate2.php")==0) {
    echo "Генерация шаблона tmplate2.php - ";
    copy($dir."/config/tmplate2.def",$dir."/config/tmplate2.php");
    echo "Ok.<br>";
  }
}

function copyfile($file) {
  $dir = $GLOBALS['dir'];
  if (!is_file($dir."/config/$file") || filesize($dir."/config/$file")==0) copy($dir."/$file",$dir."/config/$file");
}

function copy_configs() {
  $dir = $GLOBALS['dir'];
  $confmoved = is_file($dir."/config/tmplate1.php") && filesize($dir."/config/tmplate1.php");
  copyfile("iboard.php");
  copyfile("database.php");
  copyfile("error.log");
  copyfile("tmplate1.php");
  copyfile("tmplate2.php");
  copyfile("head.txt");
  copyfile("top.txt");
  copyfile("bottom.txt");

  if (!$confmoved) {
    $fh=fopen($dir."/config/tmplate1.php","r");
    $tmplate=fread($fh,filesize($dir."/config/tmplate1.php"));
    $tmplate=str_replace("head.txt","config/head.txt",$tmplate);
    $tmplate=str_replace("top.txt","config/top.txt",$tmplate);
    $tmplate=str_replace("bottom.txt","config/bottom.txt",$tmplate);
    fclose($fh);
    $fh=fopen($dir."/config/tmplate1.php","w");
    fwrite($fh,$tmplate,strlen($tmplate));
    fclose($fh);
  }
}

// Функции установки

function new_install1() {
  process_start();
  zip_expand();
  #$check=file_check();
  #process_result($check);
  check_configs();
  process_end();
  db_params_form("new2");
}

function new_install2() {
  process_start();
  check_configs();
  check_pass();
  connect_database(1);
  echo "Проверка прав доступа к базе данных - ";
  db_test();
  echo "Ok<br>";
  echo "Создание таблиц в базе данных - ";
  $dbfile1 =getvar("DBdriver").".sql";
  process_sql($dbfile1);
  echo "Ok<br>";
  echo "Настройка начальных данных форума - ";
  $dbfile1 =getvar("DBdriver")."_2.sql";
  process_sql($dbfile1);
  save_config_file();
  save_database_file();
  create_templates();
  set_root_pass();
  process_goto_last();
  process_end();
}

function update_forum() {
  process_start();
  echo "Проверка доступности на запись каталога config - ";
  if (is_writable($GLOBALS['dir']."/config")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога temp - ";
  if (is_writable($GLOBALS['dir']."/temp")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога photos - ";
  if (is_writable($GLOBALS['dir']."/photos")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога photos/previews - ";
  if (is_writable($GLOBALS['dir']."/photos/previews")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога logs - ";
  if (is_writable($GLOBALS['dir']."/logs")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога smiles - ";
  if (is_writable($GLOBALS['dir']."/smiles")) echo "Ok<br>";
  else global_error("не доступен для записи");
  echo "Проверка доступности на запись каталога files - ";
  if (is_writable($GLOBALS['dir']."/files")) echo "Ok<br>";
  else global_error("не доступен для записи");
  
  create_file($GLOBALS['dir'],"addons.php");
  check_configs();
  if (file_exists($GLOBALS['dir']."/iboard.php")) copy_configs();
  connect_database();
  echo "Проверка прав доступа к базе данных - ";
  db_test();
  echo "Ok<br>";
  echo "Обновление структуры базы данных - ";
  $dbfile1 = $GLOBALS['DBdriver'].$GLOBALS['opt_ibversion'].".sql";
  process_sql($dbfile1);
  echo "Ok<br>";
  save_config_file();

  $sql = "SELECT u_id, u__password, u__key FROM ".$GLOBALS['DBprefix']."User WHERE u_id=2";
  $res=&db_query($sql,$GLOBALS['link']);
  $udata=&db_fetch_array($res);
  db_free_result($res);
  require('auth.php');
  $GLOBALS['key']=generate_key($udata);

  process_goto_last();
  process_end();
}

function delete_forum() {
  process_start();
  check_configs();
  connect_database();
  echo "Удаление содержимого баз данных - ";
  process_sql("drop.sql");
  echo "Ok<br>";
  process_end();
}

function correct_names() {
  process_start();
  echo "Корректировка имен таблиц в базе данных - <br>";
  process_sql("rename.sql");
  echo "Ok<br>";
  process_end();
}

function config_forum() {
  db_params_form("do_config");
}

function do_config_forum() {
  process_start();
  check_configs();
  check_pass();
  connect_database();
  save_config_file();
  save_database_file();
  set_root_pass();
  process_goto_last();
  process_end();
}

function restore_forum() {
  $dh=opendir($GLOBALS['dir']."/temp/");
  while ($file=readdir($dh)) {
    if (is_file($GLOBALS['dir']."/temp/".$file)) {
      $fsize=filesize($GLOBALS['dir']."/temp/".$file);
      $buffer.="<a href=\"install.php?a=do_restore&bfile=$file\">$file</a> (".intval($fsize/1024)." Kb)<br>";
    }
  }
  closedir($dh);
  restore_form($buffer);
}

function do_restore_forum() {
  process_start();
  echo "Проверка указанного файла файла с резервной копией - ";
  if ($filename=&getvar('bfile')) {
    if (!file($GLOBALS['dir']."/temp/".$filename)) echo "Указанного файла не существует!";
    $filename=$GLOBALS['dir']."/temp/".$filename;
  }
  else {
    if (is_uploaded_file($_FILES['backup']['tmp_name'])) global_error("Файл не загружен");
    $filename=$_FILES['backup']['tmp_name'];
  }
  $mode=&getvar('mode');
  if (!$mode) $mode="auto";
  if ($mode=="auto") {
    if (substr($filename,".gz")!==false) $mode="gz";
    elseif (substr($filename,".bz")!==false) $mode="bz";
    else $mode="f";
  }
  echo "Ok.<br>";
  echo "Определение режима восстановления - ";
  if ($mode=="gz") echo "GZIP.<br>";
  elseif ($mode=="bz") echo "BZIP2.<br>";
  else echo "без сжатия.<br>";
  connect_database();
  echo "Выполнение запросов из файла резервной копии - ";
  process_sql_increment($filename,$mode);
  echo "Ok.<br>";
  echo "<b>Восстановление форума успешно завершено!</b><br>";
  process_goto_last();
  process_end();
}

function go_message() {
 if ($_POST['key']) {
   session_name('IB2XPnew'.$GLOBALS['DBprefix']);
   session_start();
   if (!$_COOKIE["IB2XP".$GLOBALS['DBprefix'].'uid'] || !$_COOKIE["IB2XP".$GLOBALS['DBprefix'].'key']) {
     setcookie("IB2XP".$GLOBALS['DBprefix'].'uid',2,0,'/');
     setcookie("IB2XP".$GLOBALS['DBprefix'].'key',$_POST['key'],0,'/');
   }
 }
 header("Location: ".$GLOBALS['url']."/admin/index.php?m=basic&a=opt_edit");
 exit();
}

function global_error($msg) {
  echo("<font color=#FF0000><b>Ошибка!</b> $msg</div>");
  common_end();
  exit();
}

// Интерфейс инсталлятора

function common_start() { ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Установка Intellect Board
</title>
<link rel="stylesheet" href="styles/gradblue/gradblue.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<body><table align=center class="title"><tr><td>
<H1>Добро пожаловать в установку Intellect Board</H1>
<H2>Благодарим за ваш выбор!</H2>
</table>
<div class="outertable">
<? }

function server_info() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>Информация о сервере
<tr><td valign="top" width="50%">
Адрес сайта: <?=$_SERVER["SERVER_NAME"];?> (<?=$_SERVER["HTTP_HOST"];?>)<br>
Web-сервер: <?=$_SERVER["SERVER_SOFTWARE"];?><br>
Версия PHP: <? echo phpversion();
 if (version_compare(phpversion(),"4.2.0")==-1) {
   echo "<font color=red> - Рекомендуется обновить до 4.2.0</font>"; }
   else { echo " - Ok"; } ?><br>
Корневой каталог сайта: <?=$_SERVER["DOCUMENT_ROOT"];?><br>
Каталог, в который проводится установка: <?=$GLOBALS['dir'];?><br>
Текущая дата и время: <?=date("D, d F Y H:i");?><br>
Скрипт выполняется от имени пользователя <?=eval('echo get_current_user();');?> (<?=eval('echo getmyuid();');?>)
<br><a href="install.php?a=more">Подробнее</a><br><td valign="top">
<? extensions_check(); ?>
</table><br>
<? }

function install_prev() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead">Начало установки
<tr><td><br><br>В текущем каталоге обнаружена уже установленная версия Intellect Board - <?=sprintf("%.2f",$GLOBALS['opt_ibversion']/100);?>
<br><br><br>
<tr><td class="tablehead"><form action="install.php" method=GET>
<input type=hidden name=a value=config><input type=submit style="width:300px" value="Настроить"></form>
<? if ($GLOBALS['opt_ibversion']<$GLOBALS['newversion']) { ?>
<tr><td class="tablehead"><form action="install.php" method=GET>
<input type=hidden name=a value=update><input type=submit style="width:300px" value="Обновить"></form>
<? } ?>
<tr><td class="tablehead"><form action="install.php" method=GET>
<input type=hidden name=a value=delete><input type=submit style="width:300px" value="Удалить"></form>
<tr><td class="tablehead"><form action="install.php" method=GET>
<input type=hidden name=a value=restore><input type=submit style="width:300px" value="Восстановить из резервной копии"></form>
<tr><td class="tablehead"><form action="install.php" method=GET>
<input type=hidden name=a value=correct><input type=submit style="width:300px" value="Произвести коррекцию регистра букв в именах"></form>
</table><br>
<? }

function install_noprev() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead">Начало установки
<tr><td><br><br>В текущем каталоге предыдущих версий Intellect Board не обнаружено<br><br>
<tr><td class="tablehead"><form action="install.php" method=GET>
<input type=hidden name=a value=new1><input type=submit style="width:300 px" value="Начать новую установку"></form>
</table><br>
<? }


function process_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead">Ход установки
<tr><td><div style="height: 200px; overflow: auto">
<? }

function process_result($result) {
  if ($result==0) echo ("<b>Все файлы загружены корректно!</b><br><br>");
  elseif ($result==1) {
    echo ("<font color=red>Внимание: один или несколько файлов отсутствуют!</font><br>");
    echo ("Проверьте, загружены ли указанные файлы, и при необходимости, загрузите их еще раз.<br>");
    echo ("Вы можете продолжить установку, но в этом случае корректная работа форума не гарантируется.<br><br>");
  }
  elseif ($result==2) {
    echo ("<font color=red>Внимание: размеры одного или нескольких файлов не совпадают с указанными в файле list.dat!</font><br>");
    echo ("Проверьте, были ли указанные файлы корректно загружены, и при необходимости, загрузите их еще раз.<br>");
    echo ("Вы можете продолжить установку, но в этом случае корректная работа форума не гарантируется.<br><br>");
  }
}

function process_goto_last() { ?>
</div><br>
<table width="100%" align=center><tr><td align=center>
<form action="install.php" method=POST>
<input type=hidden name=a value=go><input type=hidden name=salt value="<?=$GLOBALS['salt'];?>">
<input type=hidden name=key value="<?=$GLOBALS['key'];?>">
<input type=submit value="  Перейти в Центр Администрирования  ">
</table></form>
<? }

function process_end() { ?>
<br></table><br>
<? }

function db_params_form($newaction) { ?>
<form action="install.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>Параметры установки форума
<tr><td>Каталог для установки форума:
<td><input type=text name=dir size=40 maxlength=255 value="<?=$GLOBALS['dir'];?>">
<tr><td>URL форума:
<td><input type=text name=url size=40 maxlength=255 value="<?=$GLOBALS['url'];?>">
<tr><td width="50%">Тип SQL-сервера:
<td><select name=DBdriver><option value="mysql">MySQL<option value="mysqli">MySQLi<option value="pssql">PostgreSQL</select>
<tr><td>Адрес SQL-сервера:
<td><input type=text name=DBhost size=40 maxlength=255 value="<?=$GLOBALS['DBhost'];?>">
<tr><td>Имя пользователя для подключения к базе данных:
<td><input type=text name=DBusername size=40 maxlength=255 value="<?=$GLOBALS['DBusername'];?>">
<tr><td>Пароль для подключения к базе данных:
<td><input type=password name=DBpassword size=40 maxlength=255 value="<?=$GLOBALS['DBpassword'];?>">
<tr><td>Название базы данных:
<td><input type=text name=DBname size=40 maxlength=255 value="<?=$GLOBALS['DBname'];?>">
<tr><td>Постоянное (persistent) подключение к базе данных:
<td><input type=radio name=DBpersist value=1>Да &nbsp;
<input type=radio name=DBpersist value="" checked>Нет
<tr><td>Использование сжатия при подключении к БД:<br>
<span class="descr">Не рекомендуется, если в качестве сервера БД указан localhost.</span>
<td><input type=radio name=DBcompress value=1>Да &nbsp;
<input type=radio name=DBcompress value="" checked>Нет
<tr><td>Префикс таблиц в базе данных:
<td><input type=text name=DBprefix size=40 maxlength=255 value="<?=$GLOBALS['DBprefix'];?>">
<tr><td>Пароль пользователя System:
<td><input type=password name=syspass1 size=40 maxlength=255>
<tr><td>Подтверждение пароля:
<td><input type=password name=syspass2 size=40 maxlength=255>
<tr><td class="tablehead" colspan=2><input type=hidden name=a value="<?=$newaction;?>">
<input type=submit value="Сохранить настройки">
</table></form><br>
<? }

function restore_form($buffer) { ?>
<form action="install.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>Восстановление базы данных форума из резервной копии
<tr><td>Обнаруженные в каталоге temp файлы:
<td><?=$buffer;?>
<tr><td>Путь к базе данных на вашем компьютере:
<td><input type=file name=backup size=40 maxlength=255>
<tr><td>Способ сжатия:
<td><input type=radio name=mode value=auto checked>Автоопределение &nbsp;
<input type=radio name=mode value=gz>GZIP &nbsp;
<input type=radio name=mode value=gz>BZIP2 &nbsp;
<input type=radio name=mode value=gz>без сжатия

<tr><td colspan=2>Внимание: для проведения восстановления форума необходимо, чтобы параметры подключения к базе данных были уже настроены корректно. Если это не так, установите сначала форум "с нуля" и только после этого проводите процедуру восстановления.<br>
Внимание: восстановление базы данных - процедура весьма длительная, и для нее может потребоваться увеличение лимита времени выполнения скрипта.
<tr><td class="tablehead" colspan=2><input type=hidden name=a value="do_restore">
<input type=submit value="Приступить к восстановлению">
</table></form><br>
<? }

function common_end() { ?>
</div>
<address class="copyright">
Данный скрипт предназначен для установки форума <a class="inverse" href="http://intboard.ru">Intellect Board</a> <?=sprintf("%.2f",$GLOBALS['newversion']/100);?><br>
&copy; 2004-2007, XXXX Pro, <a class="inverse" href="http://www.openproj.ru" target=_blank>Объединенный Открытый Проект</a><br>
<a class="inverse" href="http://intboard.ru/support/">Перейти на форум поддержки</a>

</address>
</body></html>

<? }

