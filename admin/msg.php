<? /*

Messages administration script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function msg_select() {
  $sql = "SELECT ln_file,ln_name FROM ".$GLOBALS['DBprefix']."Language";
  $langselect = build_select($sql);
  lang_select_form($langselect);
}

function msg_list() {
  $lang = getvar("lang");
  $mode = getvar("mode");
  if (strpos($lang,"/")!==false || strpos($lang,"\\")!==false || strpos($lang,".")!==false) global_error("Hack attempt! Lang: ".$lang);  
  if ($mode=="main") $fh=fopen($GLOBALS['opt_dir']."/langs/$lang/main.php","r");
  elseif ($mode=="admin") $fh=fopen($GLOBALS['opt_dir']."/langs/$lang/admin.php","r");
  if (!$fh) error(MSG_e_msg_nofile);
  list_msg_start();
  while (!feof($fh)) {
    $buffer=fgets($fh);
    if (preg_match("|define\(\"(\S+?)\",\"(.*)\"\);|is",$buffer,$matches)) {
      list_msg_entry($matches[1],$matches[2]);
    }
  }
  list_msg_end();
  msg_add_form();
}

function msg_save() {
  check_post();
  $lang = getvar("lang");
  $mode = getvar("mode");
  if (strpos($lang,"/")!==false || strpos($lang,"\\")!==false || strpos($lang,".")!==false) global_error("Hack attempt! Lang: ".$lang);  
  if ($mode=="main") $fh=fopen($GLOBALS['opt_dir']."/langs/$lang/main.php","w");
  elseif ($mode=="admin") $fh=fopen($GLOBALS['opt_dir']."/langs/$lang/admin.php","w");
  fputs($fh,"<?\r\n");
  $msgs=$_POST['msg'];
  foreach ($msgs as $curmsg=>$curvalue) {
    if ($curvalue) {
      if (substr($curmsg,0,6)!=$prevmsg) {
        $prevmsg=substr($curmsg,0,6);
        fputs($fh,"\r\n");
      }
      $curvalue=str_replace("'","\\'",$curvalue);
      fputs($fh,"define('$curmsg','$curvalue');\r\n");
    }
  }
  fputs($fh,"?>");
  fclose($fh);
  ad_message(MSG_msg_saved,MSG_msg_list,"admin/index.php?m=msg&a=msg_list&lang=$lang&mode=$mode");
}

function msg_add() {
  check_post();
  $lang = getvar("lang");
  $mode = getvar("mode");
  if (strpos($lang,"/")!==false || strpos($lang,"\\")!==false || strpos($lang,".")!==false) global_error("Hack attempt! Lang: ".$lang);  
  if ($mode=="main") $msgs=file($GLOBALS['opt_dir']."/langs/$lang/main.php","r");
  elseif ($mode=="admin") $msgs=file($GLOBALS['opt_dir']."/langs/$lang/admin.php","r");
  $count=count($msgs);
  $curmsg=getvar("msg");
  if (substr($curmsg,0,4)!="MSG_") $curmsg="MSG_".$curmsg;
  $curvalue=getvar("value");
  $msgs[$count-1]="define(\"$curmsg\",\"$curvalue\");\r\n";
  if ($mode=="main") $fh=fopen($GLOBALS['opt_dir']."/langs/$lang/main.php","w");
  elseif ($mode=="admin") $fh=fopen($GLOBALS['opt_dir']."/langs/$lang/admin.php","w");
  foreach ($msgs as $curstr) fputs($fh,$curstr);
  fputs($fh,"?>");
  fclose($fh);
  ad_message(MSG_msg_add,MSG_msg_list,"admin/index.php?m=msg&a=msg_list&lang=$lang&mode=$mode");
}
