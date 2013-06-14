<? /*

Addon installer script for Intellect Board 2 Project
 
(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function addon_start() {
  global $link;
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Addon";
  $res = db_query($sql,$link);
  addon_list_start();
  while ($addon=db_fetch_array($res)) {
	  addon_list_entry($addon);
  }
  addon_list_end();
  addon_upload_form();
}

function addon_upload() {
	$dir=opendir($GLOBALS['opt_dir']."/temp");
	while ($curdir=readdir($dir)) if (is_file($curdir)) unlink($GLOBALS['opt_dir']."/temp/".$curdir);
	closedir($dir);
	
	echo $_FILES['addon']['tmp_name'];
  if (!is_uploaded_file($_FILES['addon']['tmp_name'])) error(MSG_e_addon_nofile);
  $zip=zip_open($_FILES['addon']['tmp_name']);
	while ($zip_file=zip_read($zip)) {
	  zip_entry_open($zip,$zip_file);
	  $name = zip_entry_name($zip_file);
		$size=zip_entry_filesize($zip_file);
		$buffer=zip_entry_read($zip_file,$size);
		$file=fopen($GLOBALS['opt_dir']."/temp/".$name,"w") or die("Не удалось создать файл ".$name);
		fwrite($file,$buffer,$size);
		fclose($file);
		zip_entry_close($zip_file);
	}
	zip_close($zip);
	
	header("Location: ".$GLOBALS['opt_url']."/admin/index.php?m=addon&a=addon_install");
}

function addon_install() {
	global $link;
	require ($GLOBALS['opt_dir']."/temp/addon.php");
	if ($minibver>$GLOBALS['opt_ibversion']) error(MSG_e_addon_ibver);
	$sql = "SELECT a_ver FROM ".$GLOBALS['DBprefix']."Addon WHERE a_name=\"".addslashes($addonname)."\"";
	$res = db_query($sql,$link);
	if (db_num_rows($res)>0) {
		$tmp=db_fetch_row($res);
		$version=$tmp[0];
	}
	else $version=0;
	db_free_result($res);
	if (($replacever=="none" || $replacever<$version) && $version!=0) error(MSG_e_addon_versions);
	addon_install_form($addonfullname,$addondescr,$addonver);
}

function addon_process() {
  if (!check_system_pass(getvar("sys_pass"))) error(MSG_e_badsyspass);		
	global $link;
	require ($GLOBALS['opt_dir']."/temp/addon.php");
	if ($minibver<$GLOBALS['ibversion']) error(MSG_e_addon_ibver);
	$sql = "SELECT a_ver FROM ".$GLOBALS['DBprefix']."Addon WHERE a_name=\"".addslashes($addonname)."\"";
	$res = db_query($sql,$link);
	if (db_num_rows($res)>0) {
		$tmp=db_fetch_row($res);
		$version=$tmp[0];
	}
	else $version=0;
	db_free_result($res);
	if (($replacever=="none" || $replacever<$version) && $version!=0) error(MSG_e_addon_versions);
	if (!$addonfile) error(MSG_e_addon_nofile);
	check_install_file($addonfile);
	process_install_file($addonfile);
	if (!$addonversion) $addonversion = "0";

	$sql = "INSERT INTO ".$GLOBALS['DBprefix']."Addon SET a_name=\"".addslashes($addonname)."\", a_ver=\"".
	addslashes($addonversion)."\", a_fullname=\"".addslashes($addonfullname)."\", a_descr=\"".addslashes($addondescr)."\"";
	$res = db_query($sql,$link);
	$dir=opendir($GLOBALS['opt_dir']."/temp");
	while ($curfile=readdir($dir)) if (is_file($dir."/".$curfile)) unlink($GLOBALS['opt_dir']."/temp/".$curfile);
	closedir($dir);
	
	ad_message(MSG_addon_installed,MSG_addon_go,"admin/index.php?m=addon&a=addon_start");
}

function check_install_file($file) {
	$fhmain=fopen($GLOBALS['opt_dir']."/temp/$file","r");
	$filelist=array();
	while (!feof($fhmain)) {
		$cmd = trim(fgets($fhmain));
		$cmd = str_replace("\r","",$cmd);
		$cmddata = explode(" ",$cmd);
		$cmddata[0]=trim($cmddata[0]);
		if ($cmddata[0]=="COPY" || $cmddata[0]=="ERASE") {
			if ($cmddata[0]=="COPY") $number=2;
			else $number=1;
			$filename=substr($cmddata[$number],0,strrpos($cmddata[$number],"/"));
			if (!is_writable($GLOBALS['opt_dir']."/".$filename)) array_push($filelist,$filename."/");
			if (file_exists($GLOBALS['opt_dir']."/".$cmddata[$number]) && !is_writable($GLOBALS['opt_dir']."/".$cmddata[$number])) array_push($filelist,$cmddata[$number]);
		}
		elseif ($cmddata[0]=="OPEN") {
			if (!is_writable($GLOBALS['opt_dir']."/".$cmddata[1])) array_push($filelist,$cmddata[1]);
		}
		elseif ($cmddata[0]=="TEMPLATE" || $cmddata[0]=="ADMIN_TEMPLATE") {
			global $link;
			$sql = "SELECT st_file FROM ".$GLOBALS['DBprefix']."StyleSet";
			$res = db_query($sql,$link);
			if ($cmddata[0]="ADMIN_TEMPLATE") $admin="admin/";
			while ($style=db_fetch_row($res)) {
				if (!is_writable($GLOBALS['opt_dir']."/styles/".$style[0]."/".$admin)) array_push($filelist,"styles/".$style[0]."/".$admin);
			}
			db_free_result($res);
		}
	  elseif ($cmddata[0]=="MERGE") {
		  if (!is_writable($GLOBALS['opt_dir']."/langs/".$cmddata[2]."/".$cmddata[3])) array_push($filelist,"langs/".$cmddata[2]."/".$cmddata[3]);
	  }
	  elseif ($cmddata[0]=="APPEND" || $cmddata[0]=="FIND" || $cmddata[0]=="REPLACE" || $cmddata[0]=="INS_BEFORE" || $cmddata[0]=="INS_AFTER") {
		  while (substr($tmpstr=fgets($fhmain),0,6)!="------") $tmpbuf.=$tmpstr;
	  }
  }
  fclose($fhmain);
  if (count($filelist)) error(MSG_e_filelist.": ".join(", ",$filelist));
}

function process_install_file($file) {
	global $link;	
	$fhmain=fopen($GLOBALS['opt_dir']."/temp/$file","r");
	while (!feof($fhmain)) {
		$cmd = trim(fgets($fhmain));
		$cmddata = explode(" ",$cmd);
		if ($cmddata[0]=="COPY") {
			$result=copy($GLOBALS['opt_dir']."/temp/".$cmddata[1],$GLOBALS['opt_dir']."/".$cmddata[2]);
			if (!$result) error(MSG_e_addon_copy." ".$cmddata[1]." ".$cmddata[2]);
		}
		elseif ($cmddata[0]=="ERASE") {
			$result=unlink($GLOBALS['opt_dir']."/".$cmddata[1]);
			if (!$result) error(MSG_e_addon_delete." ".$cmddata[1]);
		}
		elseif ($cmddata[0]=="EXECSQL") {
			process_sql_file($cmddata[1]);
		}
		elseif ($cmddata[0]=="MERGE") {
			process_lang_file($cmddata[1],$cmddata[2],$cmddata[3]);
		}
		elseif ($cmddata[0]=="OPEN") {
			$curname=$GLOBALS['opt_dir']."/".$cmddata[1];
			$curfile = fopen($curname,"r");
			if (!$curfile) error(MSG_e_addon_file." ".$cmddata[1]);
			$size = filesize($curname);
			$mainbuffer=fread($curfile,$size);
			fclose($curfile);
		}
		elseif ($cmddata[0]=="CLOSE") {
			$curfile = fopen($curname,"w");
			if (!$curfile) error(MSG_e_addon_file." ".$cmddata[1]);
			flock($curfile,LOCK_EX);
			fwrite($curfile,$mainbuffer);
			fclose($curfile);
			$curfile=NULL;
		}
		elseif ($cmddata[0]=="APPEND") {
			while (substr($tmpstr=fgets($fhmain),0,6)!="------") $tmpbuf.=$tmpstr;
			$mainbuffer.=$tmpbuf;
			unset($tmpbuf);
			unset($tmpstr);
		}
		elseif ($cmddata[0]=="FIND") {
			$findbuf="";
			while (substr($tmpstr=fgets($fhmain),0,6)!="------") $findbuf.=$tmpstr;
			$findbuf=trim($findbuf);
			unset($tmpstr);
			$findpos = strpos($mainbuffer,$findbuf);
			$findlen = strlen($findbuf);
			if ($findpos===false) error(MSG_e_addon_notfound." ".$findbuf);
		}
		elseif ($cmddata[0]=="REPLACE") {
			while (substr($tmpstr=fgets($fhmain),0,6)!="------") $replacebuf.=$tmpstr;
			unset($tmpstr);
			$mainbuffer=str_replace($findbuf,trim($replacebuf),$mainbuffer);
			unset($replacebuf);
		}
		elseif ($cmddata[0]=="INS_BEFORE") {
			while (substr($tmpstr=fgets($fhmain),0,6)!="------") $insbuf.=$tmpstr;
			unset($tmpstr);
			$mainbuffer = substr($mainbuffer,0,$findpos).trim($insbuf).substr($mainbuffer,$findpos);
			$findpos = strpos($mainbuffer,$findbuf);			
			unset($insbuf);			
		}
		elseif ($cmddata[0]=="INS_AFTER") {
			while (substr($tmpstr=fgets($fhmain),0,6)!="------") $insbuf.=$tmpstr;
			unset($tmpstr);
			$mainbuffer = substr($mainbuffer,0,$findpos+$findlen).trim($insbuf).substr($mainbuffer,$findpos+$findlen);
			unset($insbuf);			
		}
		elseif ($cmddata[0]=="TEMPLATE") {
			global $link;
			$sql = "SELECT st_file FROM ".$GLOBALS['DBprefix']."StyleSet";
			$res = db_query($sql,$link);
			while ($style=db_fetch_row($res)) {
				copy($GLOBALS['opt_dir']."/temp/".$cmddata[1],$GLOBALS['opt_dir']."/styles/".$style[0]."/".$cmddata[1]);
			}
			db_free_result($res);
		}
		elseif ($cmddata[0]=="ADMIN_TEMPLATE") {
			global $link;
			$sql = "SELECT st_file FROM ".$GLOBALS['DBprefix']."StyleSet";
			$res = db_query($sql,$link);
			while ($style=db_fetch_row($res)) {
				copy($GLOBALS['opt_dir']."/temp/".$cmddata[1],$GLOBALS['opt_dir']."/styles/".$style[0]."/admin/".$cmddata[1]);
			}
			db_free_result($res);
		}		
	}
	fclose($fhmain);
	if ($curfile) fclose($curfile);
}

function process_sql_file($fname) {
	global $link;
	$fh=fopen($GLOBALS['opt_dir']."/temp/".$fname,"r");
	if (!$fh) error(MSG_e_addon_sqlfile." ".$fname);
	$size=filesize($GLOBALS['opt_dir']."/temp/".$fname);
	$buffer=fread($fh,$size);
	$buffer=str_replace("\r","",$buffer);
	$buffer=str_replace("prefix_",$GLOBALS['DBprefix'],$buffer);
	$sqlops=explode("\ngo\n",$buffer);
	foreach ($sqlops as $sql) if (trim($sql)) $res = db_query($sql,$link);
}

function process_lang_file($fname,$lang,$where) {
	if (!file_exists($GLOBALS['opt_dir']."/temp/".$fname)) error(MSG_e_addon_langfile." ".$fname);
	$fdata=file($GLOBALS['opt_dir']."/temp/".$fname);
	if (!file_exists($GLOBALS['opt_dir']."/langs/".$lang."/".$where)) error(MSG_e_addon_nolang." ".$lang." ".$where);
	$msgdata=file($GLOBALS['opt_dir']."/langs/".$lang."/".$where);
	unset($msgdata[count($msgdata)-1]);
	$msgdata=array_merge($msgdata,$fdata);
	array_push($msgdata,"\n?>");
	$fh=fopen($GLOBALS['opt_dir']."/langs/".$lang."/".$where,"w");
	foreach ($msgdata as $curline) fputs($fh,$curline);
	fclose($fh);
}