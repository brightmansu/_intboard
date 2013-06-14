<? /*

Basic administration script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function opt_edit() {
  $levels = build_level_select();
  $forums = "<option value=\"\">".MSG_opt_forumlist;
  $GLOBALS['inuserbasic']="0";
  $forums .= build_forum_select("f_lview");
  ad_opt_edit($levels,$forums);
}

function opt_edit2() {
  $levels = build_level_select();
  $forums = "<option value=\"\">".MSG_opt_forumlist;
  $GLOBALS['inuserbasic']="0";
  $forums .= build_forum_select("f_lview",0);
  ad_opt_edit2($levels,$forums);
}

function opt_edit3() {
  $levels = build_level_select();
  $forums = "<option value=\"\">".MSG_opt_forumlist;
  $GLOBALS['inuserbasic']="0";
  $forums .= build_forum_select("f_lview",0);
  ad_opt_edit3($levels,$forums);
}

function opt_edit4() {
  $levels = build_level_select();
  $GLOBALS['inuserbasic']="0";
  $sql = "SELECT f_id,f_title FROM ".$GLOBALS['DBprefix']."Forum, ".$GLOBALS['DBprefix']."ForumType WHERE f_tpid=tp_id AND tp_container=1";
  $forums = "<option value=\"\">".MSG_opt_notshow.build_select($sql);
  $catselect = build_select("SELECT ct_id,ct_name FROM ".$GLOBALS['DBprefix']."Category");
  ad_opt_edit4($levels,$forums,$catselect);
}

function opt_save() {
 check_post();
 $keys = preg_grep("/opt_/",array_keys(array_merge($GLOBALS,$_POST)));
 foreach ($keys as $curkey) {
   if (isset($_POST[$curkey])) {
     $GLOBALS[$curkey]=$_POST[$curkey];
   }
 }
 options_save();
 if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'http://')!==false) $url=$_SERVER['HTTP_REFERER'];
 else $url="admin/index.php?m=basic&a=opt_edit";
 ad_message(MSG_opt_saved,MSG_opt_go,$url);
}

function list_badword() {
  $link = $GLOBALS['link'];
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."BadWord";
  $res = db_query($sql,$link);
  ad_bw_list_start();
  while ($badword=db_fetch_array($res)) {
    ad_bw_list_entry($badword);
  }
  ad_bw_list_end();
  ad_bw_add_start();
  for ($i=0; $i<10; $i++) {
    ad_bw_add_entry($i);
  }
  ad_bw_add_end();
}

function save_badword() {
  check_post();
  $link = $GLOBALS['link'];
  $badword = $_POST['badword'];
  foreach ($badword as $curid=>$curword) {
    if ($_POST['delete'][$curid]) {
      if ($sqldata) $sqldata.=" OR ";
      $sqldata.="w_id=\"".addslashes($curid)."\"";
    }
    else {
      $sql = "UPDATE ".$GLOBALS['DBprefix']."BadWord SET w_bad=\"".addslashes($curword)."\", ".
      "w_good=\"".addslashes($_POST['goodword'][$curid])."\", w_onlyname=".intval($_POST['onlyname'][$curid])." WHERE w_id=\"".addslashes($curid)."\"";
      $res = db_query($sql,$link);
    }
  }
  if ($sqldata) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."BadWord WHERE ($sqldata)";
    $res = db_query($sql,$link);
  }
  ad_message(MSG_bw_updated,MSG_bw_go,"admin/index.php?m=basic&a=list_badword");
}

function add_badword() {
  check_post();
  $link = $GLOBALS['link'];
  $badword = $_POST['badword'];
  foreach ($badword as $curid=>$curword) {
    if ($curword && ($_POST['goodword'][$curid] || $_POST['onlyname'][$curid])) {
      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."BadWord SET w_bad=\"".addslashes($curword)."\", w_good=\"".addslashes($_POST['goodword'][$curid])."\", w_onlyname=".intval($_POST['onlyname'][$curid]);
      $res = db_query($sql,$link);
    }
  }
  ad_message(MSG_bw_updated,MSG_bw_go,"admin/index.php?m=basic&a=list_badword");
}

function rules() {
  $sql = "SELECT ln_file,ln_name FROM ".$GLOBALS['DBprefix']."Language";
  $langselect=build_select($sql);
  $newaction="edit_rules";
  $newmodule="basic";
  lang_select($newaction,$newmodule,$langselect,MSG_rules_foredit);
}

function edit_rules() {
  $lang = getvar("lang");
  if (strpos($lang,"/")!==false || strpos($lang,"\\")!==false || strpos($lang,".")!==false) global_error("Hack attempt! Lang: ".$lang);
  $fh = fopen("../langs/".$lang."/rules.txt","r");
  while (!feof($fh)) $buffer.=fgets($fh);
  fclose($fh);
  edit_rules_form($buffer);
}

function save_rules() {
  check_post();
  $lang = getvar("lang");
  if (strpos($lang,"/")!==false || strpos($lang,"\\")!==false || strpos($lang,".")!==false) global_error("Hack attempt! Lang: ".$lang);
  $rules = $_POST["rules_text"];
  $fh = fopen("../langs/".$lang."/rules.txt","w");
  fwrite($fh,$rules,strlen($rules));
  fclose($fh);
  ad_message(MSG_rules_saved,MSG_rules_go,"admin/index.php?m=basic&a=rules");
}

function template() {
  $file = getvar("file");
  if ($file!="tmplate1.php" && $file!="tmplate2.php" && $file!="top.txt" && $file!="bottom.txt" && $file!="head.txt") error(MSG_e_badfile);
  $fh = fopen("../config/".$file,"r");
  if (!$fh) error(MSG_e_notemplate." ".$file);
  while (!feof($fh)) $buffer.=fgets($fh);
  fclose($fh);
  if ($file=="tmplate1.php") $msg=MSG_main_template;
  elseif ($file=="tmplate2.php") $msg=MSG_quick_template;
  elseif ($file=="top.txt") $msg=MSG_top_counters;
  elseif ($file=="bottom.txt") $msg=MSG_bottom_counters;
  elseif ($file=="head.txt") $msg=MSG_headers;
  edit_template_form($buffer,$msg);
}

function save_template() {
  if (!check_system_pass(getvar("sys_pass"))) error(MSG_e_badsyspass);
  $file=getvar("file");
  if ($file!="tmplate1.php" && $file!="tmplate2.php" && $file!="top.txt" && $file!="bottom.txt" && $file!="head.txt") error(MSG_e_badfile);
  $fh = fopen("../config/".$file,"w");
  fwrite($fh,$_POST['template'],strlen($_POST['template']));
  fclose($fh);
  ad_message(MSG_template_saved,MSG_template_go,"admin/index.php?m=basic&a=template&file=$file");
}

/*function mailsend() {
  mailsend_form();
}*/

function build_mail_select() {
  global $link;
  $sql="SELECT f_id,f_title,ct_name, f_sortfield, ct_sortfield, f_parent FROM ".$GLOBALS['DBprefix']."Category ct, ".$GLOBALS['DBprefix']."Forum f ".
      "WHERE ct_id=f_ctid AND f_tpid IN (3,4,7,9) AND f_lview<1000".
      " ORDER BY ct_sortfield";
      $res=&db_query($sql,$link);
      while ($tmpdata=db_fetch_array($res)) $forums[]=$tmpdata;
      db_free_result($res);
  $flist = "";
  $oldcat = "0";
  $buf=','.$GLOBALS['inuser']['forum_noaccess'].',';
  $fs=explode(',',$_GET['fs']);
//  $forums=sort_forums_recurse($forums);
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

function mailsend() {
  global $link;
  if ($_GET['days']) $days=getvar('days');
  else $days=$GLOBALS['opt_sendmail_days'];
  if (!$days) $days=7;
  $time=time()-($days*24*60*60);
  if (is_array($_GET['fs'])) {
    $sql = "SELECT t_id,t_title,t_descr, t_link,f_id,f_link,p__time  FROM ".$GLOBALS['DBprefix']."Topic,".
    "  ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."Post ".
    "WHERE t_fid=f_id AND t__pcount>0 AND t_fid IN (".join(',',$_GET['fs']).") AND t__lasttime>$time AND t__startpostid=p_id ".
    "ORDER BY f_sortfield, p__time DESC";
    $res = db_query($sql,$link);
    while ($tdata=db_fetch_array($res)) {
      $data.=short_date_out($tdata['p__time']).' -- "'.$tdata['t_title'].'" ( '.$GLOBALS['opt_url'].'/'.build_url($tdata).' )'."\n".
        $tdata['t_descr']."\n\n";
    }
  }

  $forums = build_mail_select();
  mailsend_form($data,$days,$forums);
}


function mailsend_process() {
  global $link;
  $start = intval($_GET['st']);
  $err=$_GET['err'];
  $sent=$_GET['sent'];
  if ($start==0) {
    check_post();
    if (!$text=$_POST['m_text']) error(MSG_e_empty_mail);
    $fh=fopen($GLOBALS['opt_dir'].'/config/mailtext.txt','w');
    fputs($fh,$text);
    fclose($fh);
    if (is_file($GLOBALS['opt_dir'].'/config/badmail.txt')) unlink($GLOBALS['opt_dir'].'/config/badmail.txt');
  }
  else {
    if (!is_file($GLOBALS['opt_dir'].'/config/mailtext.txt')) error(MSG_e_nomailfile);
    $text=join('',file($GLOBALS['opt_dir'].'/config/mailtext.txt'));
  }
  $sql = "SELECT u__name,u__email FROM ".$GLOBALS['DBprefix']."User WHERE u_id>3 AND u_nomails=0 LIMIT $start,50";
  $res = db_query($sql,$link);
  $buffer=load_mail("adm_mail.txt");
  $counter=0;
  while ($udata=db_fetch_row($res)) {
    $GLOBALS['username']=$udata[0];
    $GLOBALS['text']=str_replace("\$username",$udata[0],$text);
    if (replace_mail($buffer,$udata[1],MSG_admin_mail." ".$GLOBALS['opt_title'])) $sent++;
    else {
      if (!$ferr) $ferr=fopen($GLOBALS['opt_dir'].'/config/badmail.txt','a');
      fputs($ferr,$udata[1].' - '.$udata[0]."\n");
      $err++;
    }
  }
  if ($ferr) fclose($ferr);
  $start=$start+db_num_rows($res);
  if (db_num_rows($res)==0) {
    unlink($GLOBALS['opt_dir'].'/config/mailtext.txt');
    $msg=MSG_mail_send.": ".$sent;
    if ($err>0) $msg.=", ".MSG_mail_error.": ".$err.". ".MSG_mail_errors." /config/badmail.txt";
    ad_message($msg,MSG_mail_send_go,"admin/index.php?m=basic&a=mailsend");
  }
  else {
    echo MSG_mail_partsend."<br>";
    $url=$GLOBALS['opt_url'].'/admin/index.php?m=basic&amp;a=mailsend_process&amp;st='.$start.'&amp;sent='.$sent.'&amp;err='.$err;
    echo '<meta http-equiv="refresh" content="1;'.$url.'">';
    echo '<a href="'.$url.'">'.MSG_mail_click.'</a>';
  }
  db_free_result($res);
}

function upload() {
  avatar_form();
  smile_form();
  global $link;
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Smile";
  $res = db_query($sql,$link);
  smile_list_start();
  while ($smdata=db_fetch_array($res)) {
    smile_entry($smdata);
  }
  smile_list_end();
}

function do_edit_sm_code() {
  global $link;
  $code = getvar('sm_code');
  $show = getvar('sm_show');
  $file = getvar('sm_file');
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Smile SET sm_code=\"$code\", sm_show=\"$show\"".
  "WHERE sm_file=\"$file\"";
  $res=db_query($sql,$link);
  if (is_file($GLOBALS['opt_dir'].'/config/smiles.php')) unlink($GLOBALS['opt_dir'].'/config/smiles.php');
  ad_message(MSG_smile_code,MSG_smile_go,"admin/index.php?m=basic&a=upload");
}

function do_edit_sm_file() {
  global $link;
  if (is_uploaded_file($_FILES['smile']['tmp_name'])) {
    if (quick_check_image("smile",$GLOBALS['opt_maxavatarsize'],0,0)) {
      if (is_file($GLOBALS['opt_dir']."/smiles/".$_FILES['smile']['name'])) unlink($GLOBALS['opt_dir']."/smiles/".$_FILES['smile']['name']);
      move_uploaded_file($_FILES['smile'.$i]['tmp_name'],$GLOBALS['opt_dir']."/smiles/".$_FILES['smile'.$i]['name']);
      $sql = "UPDATE ".$GLOBALS['DBprefix']."Smile ".
      "SET sm_file=\"".$_FILES['smile']['name']."\" WHERE sm_code=\"".getvar("sm_code")."\"";
      $res = db_query($sql,$link);
      eval('chmod($GLOBALS[\'opt_dir\']."/smiles/".$_FILES[\'smile\'][\'name\'],0644);');
      $counter++;
    }
    if (is_file($GLOBALS['opt_dir'].'/config/smiles.php')) unlink($GLOBALS['opt_dir'].'/config/smiles.php');
  }
  ad_message(MSG_smile_uploaded.": ".$counter,MSG_upload_go,"admin/index.php?m=basic&a=upload");
}

function delete_smile() {
  global $link;
  $smcode=getvar('sm_code');
  $sql = "SELECT sm_file FROM ".$GLOBALS['DBprefix']."Smile WHERE sm_code=\"$smcode\"";
  $res=db_query($sql,$link);
  list($name)=db_fetch_row($res);
  db_free_result($res);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Smile WHERE sm_code=\"$smcode\"";
  $res = db_query($sql,$link);
  @unlink($GLOBALS['opt_dir']."/smiles/".$name);
  if (is_file($GLOBALS['opt_dir'].'/config/smiles.php')) unlink($GLOBALS['opt_dir'].'/config/smiles.php');
  ad_message(MSG_smile_deleted,MSG_upload_go,"admin/index.php?m=basic&a=upload");
}

function avatar_upload() {
  for ($i=0; $i<10; $i++) {
    if (!file_exists($GLOBALS['opt_dir']."/avatars/".$_FILES['avatar'.$i]['name'])) {
      if (quick_check_image("avatar".$i,$GLOBALS['opt_maxavatarsize'],$GLOBALS['opt_maxavatarx'],$GLOBALS['opt_maxavatary'])) {
        move_uploaded_file($_FILES['avatar'.$i]['tmp_name'],$GLOBALS['opt_dir']."/avatars/".$_FILES['avatar'.$i]['name']);
        $counter++;
        eval('chmod($GLOBALS[\'opt_dir\']."/avatars/".$_FILES[\'avatar\'.$i][\'name\'],0644);');
      }
    }
  }
  ad_message(MSG_avatar_uploaded.": ".$counter,MSG_upload_go,"admin/index.php?m=basic&a=upload");
}

function smile_upload() {
  global $link;
  for ($i=0; $i<10; $i++) {
    if (!file_exists($GLOBALS['opt_dir']."/smiles/".$_FILES['smile'.$i]['name'])) {
      if (quick_check_image("smile".$i,$GLOBALS['opt_maxavatarsize'],0,0) && getvar("sm_code$i")) {
        move_uploaded_file($_FILES['smile'.$i]['tmp_name'],$GLOBALS['opt_dir']."/smiles/".$_FILES['smile'.$i]['name']);
        $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Smile ".
        "SET sm_code=\"".getvar("sm_code$i")."\", sm_file=\"".$_FILES['smile'.$i]['name']."\", sm_show=\"".getvar("sm_show$i")."\"";
        $res = db_query($sql,$link);
        eval('chmod($GLOBALS[\'opt_dir\']."/smiles/".$_FILES[\'smile\'.$i][\'name\'],0644);');
        $counter++;
      }
    }
  }
  if (is_file($GLOBALS['opt_dir'].'/config/smiles.php')) unlink($GLOBALS['opt_dir'].'/config/smiles.php');
  ad_message(MSG_smile_uploaded.": ".$counter,MSG_upload_go,"admin/index.php?m=basic&a=upload");
}

function mail_select() {
  $sql = "SELECT ln_file,ln_name FROM ".$GLOBALS['DBprefix']."Language";
  $langselect=build_select($sql);
  $newaction="mail_tmpl";
  $newmodule="basic";
  lang_select($newaction,$newmodule,$langselect,MSG_mail_foredit);
}

function mail_tmpl() {
  $lang=getvar("lang");
  if (strpos($lang,"/")!==false || strpos($lang,"\\")!==false || strpos($lang,".")!==false) global_error("Hack attempt! Lang: ".$lang);
  $list=file($GLOBALS['opt_dir']."/langs/$lang/mail.lst");
  foreach ($list as $curstr) {
    list($mailname,$maildescr)=explode("|",$curstr);
    $buffer.="<option value=\"$mailname\">$maildescr";
  }
  mail_form($buffer);
}

function edit_mail() {
  $lang = getvar("lang");
  $mail = getvar("mail");
  if (strpos($lang,"/")!==false || strpos($lang,"\\")!==false || strpos($lang,".")!==false) global_error("Hack attempt! Lang: ".$lang);
  if (strpos($mail,"/")!==false || strpos($mail,"\\")!==false || strpos($mail,".")!==false) global_error("Hack attempt! Mail: ".$mail);
  $fh = fopen("../langs/".$lang."/$mail.txt","r");
  while (!feof($fh)) $buffer.=fgets($fh);
  fclose($fh);
  edit_mail_form($buffer);
}

function save_mail() {
  check_post();
  $lang = getvar("lang");
  $mail = getvar("mail");
  if (strpos($lang,"/")!==false || strpos($lang,"\\")!==false || strpos($lang,".")!==false) global_error("Hack attempt! Lang: ".$lang);
  if (strpos($mail,"/")!==false || strpos($mail,"\\")!==false || strpos($mail,".")!==false) global_error("Hack attempt! Mail: ".$mail);
  $text = $_POST['text'];
  $fh = fopen("../langs/".$lang."/$mail.txt","w");
  fwrite($fh,$text);
  fclose($fh);
  ad_message(MSG_mail_saved,MSG_mail_go,"admin/index.php?m=basic&a=mail_tmpl&lang=$lang");
}

function sql_query() {
  sql_query_form();
}

function sql_process() {
  if (!check_system_pass(getvar("sys_pass"))) error(MSG_e_badsyspass);
  $sqltext=$_POST["sqltext"];
  $sqltext=str_replace("prefix_",$GLOBALS['DBprefix'],$sqltext);
  global $link;
  $res=db_query($sqltext,$link);
  $sqlparts=explode(" ",$sqltext);
  if (strtoupper($sqlparts[0]=="SELECT") || strtoupper($sqlparts[0]=="SHOW")) {
    $count = db_num_fields($res);
    sql_field_start($count);
    for ($i=0; $i<$count; $i++) {
      $fieldname = db_field_name($res,$i);
      sql_field_entry($fieldname);
    }
    sql_field_end();
    while ($row=db_fetch_row($res)) {
      sql_row_start();
      for ($i=0; $i<$count; $i++) sql_row_entry($row[$i]);
      sql_row_end();
    }
    sql_query_end();
  }
  else ad_message(MSG_query_done.": ".db_affected_rows($res),MSG_query_go,"admin/index.php?m=basic&a=sql_query");
  sql_query_form();
}

function sys_pass_change() {
  sys_pass_form();
}

function sys_pass_process() {
  global $link;
  if (!check_system_pass(getvar("old_pass"))) error(MSG_e_badsyspass);
  $newpass1=getvar("new_pass1");
  $newpass2=getvar("new_pass2");
  if ($newpass1!=$newpass2) error(MSG_e_pass_notmatch);
  $hash=md5($newpass1);
  $trash=rand();
  $newkey=addslashes(substr(crypt($trash),0,12));
  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__password=\"$hash\", u_encrypted=1, u__key=\"$newkey\" WHERE u_id=2";
  $res = db_query($sql,$link);
  $salt=rand();

  if ($GLOBALS['inuserid']==2) {
    $_SESSION['password']=crypt($newkey.$hash,$salt);
    $_SESSION['salt']=$salt;
  }

  ad_message(MSG_sys_passchanged,MSG_sys_logout,"admin/index.php?a=logout");
}

function do_optimize() {
  db_optimize();
  ad_message(MSG_optimized,MSG_go_stats,"admin/index.php?m=stats&a=view");
}

function backup() {
  backup_form();
}

function backup_files() {
  $urldata=parse_url($GLOBALS['opt_url']);
  $filename=strtolower($urldata['host']);
  $filename=str_replace(".","_",$filename);
  $dh=opendir($GLOBALS['opt_dir']."/temp");
  backup_start();
  while ($file=readdir($dh)) {
    if (is_file($GLOBALS['opt_dir']."/temp/".$file) && strpos($file,$filename)!==false) {
      $fsize=filesize($GLOBALS['opt_dir']."/temp/".$file);
      backup_entry($file,$fsize);
    }
  }
  closedir($dh);
  backup_end();
}

function backup_confirm() {
  $params['bfile']=getvar('bfile');
  confirm("basic","backup_delete",$params,MSG_backup_delete." ".$params['bfile']."?","admin/index.php?m=basic&a=backup_files");
}

function backup_delete() {
  $bfile = getvar('bfile');
  if (strpos($bfile,".")!==false && strpos($bfile,"/")!==false) error(MSG_e_backup_file);
  unlink($GLOBALS['opt_dir']."/temp/".$bfile);
  ad_message(MSG_backup_deleted,MSG_go_stats,"admin/index.php?m=basic&a=backup_files");
}

function edit_bcode() {
  if (file_exists($GLOBALS['opt_dir'].'/config/bcodes.txt')) {
    $codes=file($GLOBALS['opt_dir'].'/config/bcodes.txt');
  }
  bcode_edit_start();
  if (is_array($codes)) foreach ($codes as $curcode) {
    list($str1,$str2)=explode(' ::: ',trim($curcode));
    bcode_edit_entry($str1,$str2);
  }
  for ($i=0; $i<5; $i++) bcode_edit_entry('','');
  bcode_edit_end();
}

function do_edit_bcode() {
  $fh=fopen($GLOBALS['opt_dir'].'/config/bcodes.txt','w');
  if (is_array($_POST['codes'])) {
    $count=count($_POST['codes']);
    for ($i=0; $i<$count; $i++) {
      if ($_POST['codes'][$i] && $_POST['replace'][$i]) {
        fputs($fh,$_POST['codes'][$i].' ::: '.$_POST['replace'][$i]."\n");
      }
    }
  }
  fclose($fh);
  ad_message(MSG_bcodes_saved,MSG_go_bcodes,"admin/index.php?m=basic&a=edit_bcode");
}

function convert_files() {
  global $link;
  $start=intval(getvar('st'));
  $sql = "SELECT file_id,file_data FROM ".$GLOBALS['DBprefix']."File LIMIT $start,50";
  $res=db_query($sql,$link);
  while ($data=db_fetch_array($res)) {
    $fh=fopen($GLOBALS['opt_dir']."/files/".$data['file_id'].".htm","w");
    fwrite($fh,$data['file_data'],strlen($data['file_data']));
    fclose($fh);
  }
  if (($done=db_num_rows($res))>0) convert_file_next($start+$done);
  else {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."File SET file_size=OCTET_LENGTH(file_data)";
    $res=db_query($sql,$link);
    $sql = "ALTER TABLE ".$GLOBALS['DBprefix']."File DROP COLUMN file_data";
    $res=db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."AdminEntry WHERE ad_name=\"MSG_ad_fileconvert\"";
    $res=db_query($sql,$link);
    ad_message(MSG_go_stats,MSG_go_stats,"admin/index.php");
  }
}

function convert_photos() {
  global $link;
  $start=intval(getvar('st'));
  $sql = "SELECT ph_tid, ph_thumb, ph_image FROM ".$GLOBALS['DBprefix']."Photo LIMIT $start,25";
  $res=db_query($sql,$link);
  while ($data=db_fetch_array($res)) {
    $fh=fopen($GLOBALS['opt_dir']."/photos/previews/".$data['ph_tid'].".jpg","w");
    fwrite($fh,$data['ph_thumb'],strlen($data['ph_thumb']));
    fclose($fh);
    $fh=fopen($GLOBALS['opt_dir']."/photos/".$data['ph_tid'].".jpg","w");
    fwrite($fh,$data['ph_image'],strlen($data['ph_image']));
    fclose($fh);
  }
  if (($done=db_num_rows($res))>0) convert_photo_next($start+$done);
  else {
    $sql = "ALTER TABLE ".$GLOBALS['DBprefix']."Photo DROP COLUMN ph_thumb, DROP COLUMN ph_image";
    $res=db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."AdminEntry WHERE ad_name=\"MSG_ad_photoconvert\"";
    $res=db_query($sql,$link);
    ad_message(MSG_go_stats,MSG_go_stats,"admin/index.php");
  }
}

function quick_check_image($name,$maxsize,$maxx,$maxy) {
  if (!is_uploaded_file($_FILES[$name]['tmp_name']) || $_FILES[$name]['size']==0 ||
  $_FILES[$name]['size']>$maxsize || strpos($_FILES[$name]['type'],"image")===false) return 0;
  if ($GLOBALS['opt_graphics']) {
      $imdata=getimagesize($_FILES[$name]['tmp_name']);
      if (!$imdata) return 0;
      if (($maxx && $imdata[0]>$maxx) || ($maxy && $imdata[1]>$maxy)) return 0;
  }
  return 1;
}

function edit_ip() {
  if (file_exists($GLOBALS['opt_dir'].'/config/ban_ip.txt')) $ips=file($GLOBALS['opt_dir'].'/config/ban_ip.txt');
  else $ips=array();
  edit_ip_start();
  for ($i=0; $i<count($ips); $i++) edit_ip_entry(explode(':',trim($ips[$i])));
  for ($i=0; $i<5; $i++) edit_ip_entry(array());
  edit_ip_end();
}

function do_edit_ip() {
  $fh=fopen($GLOBALS['opt_dir'].'/config/ban_ip.txt','w');
  if (is_array($_POST['ips1'])) for ($i=0; $i<count($_POST['ips1']); $i++) {
    if ($_POST['ips1'][$i]!='') fwrite($fh,$_POST['ips1'][$i].':'.$_POST['ips2'][$i]."\n");
  }
  fclose($fh);
  ad_message(MSG_go_ipeditor,MSG_go_stats,"admin/index.php?m=basic&a=edit_ip");
}
