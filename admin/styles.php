<? /*

Styles editor script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function st_select() {
  $link = $GLOBALS['link'];
  $sql="SELECT * FROM ".$GLOBALS['DBprefix']."StyleSet";
  $res=db_query($sql,$link);
  st_select_form_start();
  while ($sdata=db_fetch_array($res)) {
    st_select_form_entry($sdata);
  }
  st_select_form_end();
  $sql = "SELECT st_file,st_name FROM ".$GLOBALS['DBprefix']."StyleSet";
  $styleselect = build_select($sql);
  st_create_form($styleselect);
  st_delete_form($styleselect);
  $sql = "SELECT st_id,st_name FROM ".$GLOBALS['DBprefix']."StyleSet";
  $styleselect2 = build_select($sql);
  st_change_form($styleselect2);
}

function st_list() {
  $style=getvar("style");
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $fsize=filesize($GLOBALS['opt_dir']."/styles/$style/$style.css");
  $fh=fopen($GLOBALS['opt_dir']."/styles/$style/$style.css","r");
  if (!$fh) error(MSG_e_style_badfile);
  $buffer=fread($fh,$fsize);
  fclose($fh);
  $stylelist=explode("}",$buffer);

  if (file_exists($GLOBALS['opt_dir']."/langs/".$GLOBALS['inuser']['ln_file']."/$style.dat")) {
    $fh=fopen($GLOBALS['opt_dir']."/langs/".$GLOBALS['inuser']['ln_file']."/$style.dat","r");
    if (!$fh) error(MSG_e_style_badfile);
    while (!feof($fh)) {
      $buffer=fgets($fh);
      list($tmp1,$tmp2)=explode("|",$buffer);
      $tmp1=trim($tmp1);
      $tmp2=trim($tmp2);
      $styledescr[$tmp1]=$tmp2;
    }
    fclose($fh);
  }

  st_list_start();
  foreach ($stylelist as $curstyle) {
    list($stylename,$srtyledata)=explode("{",$curstyle);
    $stylename=trim($stylename);
    $styledata=trim($styledata);
    if ($stylename) st_list_entry($stylename,$styledata,$styledescr[$stylename],$style);
  }
  st_list_end();
  st_replace_form();
}

function st_edit() {
  global $link;
  $style=getvar("style");
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $fsize=filesize($GLOBALS['opt_dir']."/styles/$style/$style.css");
  $fh=fopen($GLOBALS['opt_dir']."/styles/$style/$style.css","r");
  if (!$fh) error(MSG_e_style_badfile);
  $buffer=fread($fh,$fsize);
  fclose($fh);
  $stylelist=explode("}",$buffer);

  foreach ($stylelist as $curstyle) {
    list($stylename,$styledata)=explode("{",$curstyle);
    $styleset[$stylename]=$styledata;
    $stylename=trim($stylename);
    $styledata=trim($styledata);
    if ($stylename==getvar("name")) {
      st_edit_start($style,$stylename);
      $styles = explode(";",$styledata);
      $defstyles=array("font-family","font-style","font-variant","font-weight","font-size","color","background-color","text-decoration","text-align","border","text-transform","margin","padding");
      foreach ($styles as $curstyle) {
        list($stname,$stdata)=explode(":",$curstyle);
        $st[strtolower(trim($stname))]=trim($stdata);
      }
      foreach ($defstyles as $curstyle) {
        st_edit_entry($curstyle,$st[$curstyle],constant("MSG_css_".str_replace("-","_",$curstyle)));
        unset($st[$curstyle]);
      }
      foreach ($st as $curstyle=>$curdata) {
        if ($curstyle) st_edit_entry($curstyle,$curdata);
      }
      st_edit_end();
    }
  }
}

function st_save() {
  check_post();
  $style=getvar("style");
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $name=getvar("name");
  $fsize=filesize($GLOBALS['opt_dir']."/styles/$style/$style.css");
  $fh=fopen($GLOBALS['opt_dir']."/styles/$style/$style.css","r");
  if (!$fh) error(MSG_e_style_badfile);
  $buffer=fread($fh,$fsize);
  fclose($fh);
  $stylelist=explode("}",$buffer);

  $buffer="";
  foreach ($stylelist as $curstyle) {
    list($stylename,$styledata)=explode("{",$curstyle);
    $stylename=trim($stylename);
    if ($stylename) {
      if ($stylename!=getvar("name")) $buffer.="$curstyle}";
      else {
        $buffer.="\n$name { ";
        $st=$_POST['st'];
        foreach ($st as $stname=>$stdata) if ($stname && $stdata) $buffer.="$stname: $stdata; ";
        $buffer.=getvar("more")."} ";
      }
    }
  }

  $fh=fopen($GLOBALS['opt_dir']."/styles/$style/$style.css","w");
  fwrite($fh,$buffer,strlen($buffer));
  fclose($fh);
//  ad_message(MSG_st_saved,MSG_st_return,"admin/index.php?m=styles&a=st_list&style=$style");
  st_list();
}

function st_delete() {
  check_post();
  $style=getvar("style");
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $name=getvar("name");
  $fsize=filesize($GLOBALS['opt_dir']."/styles/$style/$style.css");
  $fh=fopen($GLOBALS['opt_dir']."/styles/$style/$style.css","r");
  if (!$fh) error(MSG_e_style_badfile);
  $buffer=fread($fh,$fsize);
  fclose($fh);
  $stylelist=explode("}",$buffer);

  $buffer="";
  foreach ($stylelist as $curstyle) {
    list($stylename,$srtyledata)=explode("{",$curstyle);
    $stylename=trim($stylename);
    if ($stylename && $stylename!=getvar("name")) $buffer.="$curstyle}";
  }

  $fh=fopen($GLOBALS['opt_dir']."/styles/$style/$style.css","w");
  fwrite($fh,$buffer,strlen($buffer));
  fclose($fh);
  ad_message(MSG_st_deleted,MSG_st_return,"admin/index.php?m=styles&a=st_list&style=$style");
}

function st_change() {
  global $link;
  $style=getvar('style');

  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u_stid=\"$style\"";
  $res=db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);
  if (file_exists($GLOBALS['opt_dir'].'/config/guest.txt')) unlink($GLOBALS['opt_dir'].'/config/guest.txt');
  ad_message(MSG_st_changed,MSG_st_return,"admin/index.php?m=styles&a=st_list&style=$style");
}

function st_create_set() {
  check_post();
  $style=getvar("st_parent");
  $newstyle=getvar("st_file");
  $newname=getvar("st_name");
  if (strpos($newstyle,".")!==false || strpos($newstyle,"/")!==false) error(MSG_e_st_invalidname);
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  global $link;

  if (is_dir($GLOBALS['opt_dir']."/styles/$newstyle")) error(MSG_e_st_alreadyexists);
  if (!is_writeable($GLOBALS['opt_dir']."/styles/")) error(MSG_e_nowrite." /styles/");

  $sql = "SELECT ln_file FROM ".$GLOBALS['DBprefix']."Language";
  $res = db_query($sql,$link);
  while ($lnfile=db_fetch_row($res)) {
    if (!is_writable($GLOBALS['opt_dir']."/langs/".$lnfile[0]."/")) error(MSG_e_nowrite." /styles/".$lnfile[0]."/");
  }
  
  
  mkdir($GLOBALS['opt_dir']."/styles/$newstyle");
  mkdir($GLOBALS['opt_dir']."/styles/$newstyle/admin");
  if (!is_dir($GLOBALS['opt_dir']."/styles/$newstyle")) error(MSG_e_st_createdir);
  copy($GLOBALS['opt_dir']."/styles/$style/".$style.".css",$GLOBALS['opt_dir']."/styles/$newstyle/".$newstyle.".css");
  if (is_file($GLOBALS['opt_dir']."/styles/".$style."/template.php"))
    copy($GLOBALS['opt_dir']."/styles/".$style."/template.php",$GLOBALS['opt_dir']."/styles/".$newstyle."/template.php");

  $dir=opendir($GLOBALS['opt_dir']."/styles/$style");
  while ($curfile=readdir($dir)) {
    if (is_file($GLOBALS['opt_dir']."/styles/$style/".$curfile) && strpos($curfile,".php")===false && $curfile!=$style.".css") {
      copy($GLOBALS['opt_dir']."/styles/$style/".$curfile,$GLOBALS['opt_dir']."/styles/$newstyle/".$curfile);
    }
  }
  closedir($dir);

  $sql = "SELECT ln_file FROM ".$GLOBALS['DBprefix']."Language";
  $res = db_query($sql,$link);
  while ($lnfile=db_fetch_row($res)) {
    if (file_exists($GLOBALS['opt_dir']."/langs/".$lnfile[0]."/$style.dat")) {
      copy($GLOBALS['opt_dir']."/langs/".$lnfile[0]."/$style.dat",$GLOBALS['opt_dir']."/langs/".$lnfile[0]."/$newstyle.dat");
    }
  }
  db_free_result($res);

  $sql = "SELECT st_integrated FROM ".$GLOBALS['DBprefix']."StyleSet WHERE st_file=\"".$style."\"";
  $res=db_query($sql,$link);
  list($intg)=db_fetch_row($res);
  db_free_result($res);

  $sqldata = build_sql("st_");
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."StyleSet SET st_show=1, st_integrated=$intg, $sqldata";
  $res = db_query($sql,$link);
  ad_message(MSG_st_created,MSG_st_return,"admin/index.php?m=styles&a=st_select");
}

function st_replace() {
  check_post();
  $style=getvar("style");
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $oldcolor = getvar("oldcolor");
  $newcolor = getvar("newcolor");
  $fsize=filesize($GLOBALS['opt_dir']."/styles/$style/$style.css");
  $fh=fopen($GLOBALS['opt_dir']."/styles/$style/$style.css","r");
  if (!$fh) error(MSG_e_style_badfile);
  $buffer=fread($fh,$fsize);
  fclose($fh);

  $buffer=str_replace("#".$oldcolor,"#".$newcolor,$buffer);

  $fh=fopen($GLOBALS['opt_dir']."/styles/$style/$style.css","w");
  fwrite($fh,$buffer,strlen($buffer));
  fclose($fh);
  ad_message(MSG_st_replaced,MSG_st_return,"admin/index.php?m=styles&a=st_select");
}

function st_confirm_set() {
  $params['style']=getvar("style");
  confirm("styles","st_delete_set",$params,MSG_st_confirm_set,"admin/index.php?m=styles&a=st_select");
}

function st_confirm() {
  $params['style']=getvar("style");
  $params['name']=getvar("name");
  confirm("styles","st_delete",$params,MSG_st_confirm,"admin/index.php?m=styles&a=st_edit&style=$style");
}

function st_delete_set() {
  check_post();
  $style=getvar("style");
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  global $link;

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."StyleSet";
  $res = db_query($sql,$link);
  $count=db_fetch_row($res);
  db_free_result($res);
  if ($count[0]==1) error(MSG_e_st_last);

  $dir=opendir($GLOBALS['opt_dir']."/styles/$style/admin");
  while ($curfile=readdir($dir)) {
    if (is_file($GLOBALS['opt_dir']."/styles/$style/admin/".$curfile)) unlink($GLOBALS['opt_dir']."/styles/$style/admin/".$curfile);
  }
  closedir($dir);
  rmdir($GLOBALS['opt_dir']."/styles/$style/admin");

  $dir=opendir($GLOBALS['opt_dir']."/styles/$style");
  while ($curfile=readdir($dir)) {
    if (is_file($GLOBALS['opt_dir']."/styles/$style/".$curfile)) unlink($GLOBALS['opt_dir']."/styles/$style/".$curfile);
  }
  closedir($dir);
  rmdir($GLOBALS['opt_dir']."/styles/$style");

  $sql = "SELECT st_id FROM ".$GLOBALS['DBprefix']."StyleSet WHERE st_file=\"$style\"";
  $res=db_query($sql,$link);
  list($stid)=db_fetch_row($res);
  db_free_result($res);

  $sql = "SELECT MAX(st_id) FROM ".$GLOBALS['DBprefix']."StyleSet WHERE st_show=1";
  $res=db_query($sql,$link);
  list($newstid)=db_fetch_row($res);
  db_free_result($res);

  if (!$newstid) $newstid=1;

  $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u_stid=$newstid WHERE u_stid=$stid";
  $res=db_query($sql,$link);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."StyleSet WHERE st_file=\"$style\"";
  $res = db_query($sql,$link);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=db_query($sql,$link);
  if (file_exists($GLOBALS['opt_dir'].'/config/guest.txt')) unlink($GLOBALS['opt_dir'].'/config/guest.txt');
  ad_message(MSG_st_deleted,MSG_st_return,"admin/index.php?m=styles&a=st_select");
}

function st_hide() {
  $style=getvar("style");
  global $link;
  $show = getvar("st_show");

  $sql = "UPDATE ".$GLOBALS['DBprefix']."StyleSet SET st_show=\"$show\" WHERE st_file=\"$style\"";
  $res = db_query($sql,$link);
  ad_message(MSG_st_changed,MSG_st_return,"admin/index.php?m=styles&a=st_select");
}

function st_edit_css() {
  $style=getvar('style');
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $filename=$GLOBALS['opt_dir']."/styles/$style/$style.css";
  if (!is_writeable($filename)) error(MSG_e_nowrite." /styles/$style/$style.css");
  $stdata=join('',file($filename));
  st_edit_form($style,$stdata,'st_save_css');
}

function st_save_css() {
  check_post();
  $style=getvar('style');
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $filename=$GLOBALS['opt_dir']."/styles/$style/$style.css";
  if (!is_writeable($filename)) error(MSG_e_nowrite." /styles/$style/$style.css");
  $stdata=$_POST['stdata_text'];
  $fh=fopen($filename,"w");
  fputs($fh,$stdata);
  fclose($fh);
  if (isset($_POST['continue'])) st_edit_css();
  else ad_message(MSG_st_css_saved,MSG_st_return,"admin/index.php?m=styles&a=st_select");
}

function st_edit_template() {
  $style=getvar('style');
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $filename=$GLOBALS['opt_dir']."/styles/$style/template.php";
  if (!is_writeable($filename)) error(MSG_e_nowrite." /styles/$style/template.php");
  $stdata=join('',file($filename));
  tp_edit_form($style,$stdata,'st_save_template');
}

function st_save_template() {
  check_post();
  if (!check_system_pass(getvar("sys_pass"))) error(MSG_e_badsyspass);  
  $style=getvar('style');
  if (strpos($style,".")!==false || strpos($style,"/")!==false) error(MSG_e_st_invalidname);
  $filename=$GLOBALS['opt_dir']."/styles/$style/template.php";
  if (!is_writeable($filename)) error(MSG_e_nowrite." /styles/$style/template.php");
  $stdata=$_POST['stdata_text'];
  // Внимание! Удаление этой проверки приведет к нарушению лицензионного соглашения!
  $stdata2=preg_replace("/<!--(.*?)-->/is","",$stdata);
  $stdata3=preg_replace("/<\?(.*?)\?>/is","",$stdata2);
  if ((strpos($stdata3,'http://www.openproj.ru')===false || strpos($stdata3,'http://intboard.ru')==false) &&
    preg_match('/<?\s*main_copyright\(\);/is',$stdata2)==0) error(MSG_st_dontdelete);
  $fh=fopen($filename,"w");
  fputs($fh,$stdata);
  fclose($fh);
  if (!isset($_POST['continue'])) ad_message(MSG_st_css_saved,MSG_st_return,"admin/index.php?m=styles&a=st_select");
}
