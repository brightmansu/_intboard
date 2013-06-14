<? /*

File output script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

$IBOARD=1;
error_reporting(E_ERROR | E_WARNING | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

require("config/database.php");
require("config/iboard.php");
require("db/$DBdriver.php");

if ($DBpersist) $link=db_pconnect($DBhost,$DBusername,$DBpassword,$DBname);
else $link=db_connect($DBhost,$DBusername,$DBpassword,$DBname);

$fid = db_slashes($_GET['fid']);
$action = db_slashes($_GET['a']);
$phid = db_slashes($_GET['ph']);
$key = db_slashes($_GET['key']);

if ($action!="thumb" && $action!="photo") {
  $sql = "SELECT file_id,file_type,file_name, file_key FROM ".$GLOBALS['DBprefix']."File WHERE file_id=\"$fid\"";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)==0) echo "File not in database!";
  else {
    $file=db_fetch_row($res);
    db_free_result($res);
    if (intval($_GET['key'])!=$file[3]) {
      header("HTTP/1.1 403 Forbidden");
      echo 'Invalid file key!';
      db_close($link);
      exit();
    }
    if (!$action) {
      $length = filesize($GLOBALS['opt_dir']."/files/".$file[0].".htm");
      $lasttime = filemtime($GLOBALS['opt_dir']."/files/".$file[0].".htm");
      if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $condtime = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        if ($condtime>0 && $condtime>=$lasttime) {
          header("HTTP/1.1 304 Not Modified");
          db_close($link);
          exit();
        }
      }

      if ($_SERVER["HTTP_RANGE"]) {
          $range = $_SERVER["HTTP_RANGE"];
          $range = str_replace("bytes=", "", $range);
          list($range_start,$range_end) =explode("-", $range);
      }
      else {
        $range_start=0;
        $range_end=$length-1;
      }
      if ($range) header("HTTP/1.1 206 Partial Content");
      else header("HTTP/1.1 200 OK");
      if (strpos($file[1],"image")===false) {
        if (!$file[1]) header("Content-Type: application/octet-stream");
        else header("Content-Type: ".$file[1]);
        header("content-disposition: attachment; filename=\"".$file[2]."\"");
      }
      else header("Content-Type: ".$file[1]);
      header("Accept-Ranges: bytes");
      header("Last-Modified: ".date('r',$lasttime));
      header("Content-Range: bytes ".intval($range_start).'-'.intval($range_end)."/".$length);
      $fh=fopen($GLOBALS['opt_dir']."/files/".$file[0].".htm","r");
      if ($range) fseek($fh,$range_start);
      fpassthru($fh);
      fclose($fh);
      $sql = 'UPDATE '.$GLOBALS['DBprefix'].'File SET file_downloads=file_downloads+1 WHERE file_id='.intval($fid);
      db_query($sql,$link);
    }
    elseif ($action=="preview") {
      if (file_exists($GLOBALS['opt_dir']."/files/".$file[0]."_p.htm")) {
        header("Content-Type: image/png");
        header("Content-Length: ".filesize($GLOBALS['opt_dir']."/files/".$file[0]."_p.htm"));
        readfile($GLOBALS['opt_dir']."/files/".$file[0]."_p.htm");
      }
      else {
        $fh=fopen($GLOBALS['opt_dir']."/files/".$file[0].".htm","r");
        $buffer=fread($fh,filesize($GLOBALS['opt_dir']."/files/".$file[0].".htm"));
        fclose($fh);
        $full = imagecreatefromstring($buffer);
        unset($buffer);
        $sizex = imagesx($full);
        $sizey = imagesy($full);
        if (!$opt_previewx) $opt_previewx=256;
        if (!$opt_previewy) $opt_previewy=64;
        $coeff=1;
        while ($sizex/$coeff>$opt_previewx) $coeff++;
        while ($sizey/$coeff>$opt_previewy) $coeff++;
        if ($coeff>1) {
          if ($GLOBALS['opt_GD2']) $thumb = imagecreatetruecolor($sizex/$coeff,$sizey/$coeff);
          else $thumb = imagecreate($sizex/$coeff,$sizey/$coeff);
          if ($GLOBALS['opt_GD2']) imagecopyresampled($thumb,$full,0,0,0,0,$sizex/$coeff,$sizey/$coeff,$sizex,$sizey);
          else imagecopyresized($thumb,$full,0,0,0,0,$sizex/$coeff,$sizey/$coeff,$sizex,$sizey);
          imagedestroy($full);
          imagepng($thumb,$GLOBALS['opt_dir']."/files/".$file[0]."_p.htm");
          header("Content-Type: image/png");
          header("Content-Length: ".filesize($GLOBALS['opt_dir']."/files/".$file[0]."_p.htm"));
          header("Last-Modified: ".date('r'));
          imagepng($thumb);
        }
        else {
          header("Content-Type: ".$file[1]);
          header("Content-Length: ".filesize($GLOBALS['opt_dir']."/files/".$file[0].".htm"));
          $lasttime=filemtime($GLOBALS['opt_dir']."/files/".$file[0].".htm");
          header("Last-Modified: ".date('r',$lasttime));
          readfile($GLOBALS['opt_dir']."/files/".$file[0].".htm");
        }
      }
    }
  }
}
elseif ($action=="thumb") {
  $sql = "SELECT ph_tid,ph_key FROM ".$GLOBALS['DBprefix']."Photo WHERE ph_id=\"$phid\" AND ph_key=\"$key\"";
  $res =&db_query($sql,$link);
  list($thumb,$phkey)=db_fetch_row($res);
  if ($key==$phkey) {
    $len=filesize($GLOBALS['opt_dir']."/photos/previews/$thumb.jpg");
    header("Content-Type: image/jpeg");
    header("Content-Length: ".$len);
    readfile($GLOBALS['opt_dir']."/photos/previews/$thumb.jpg");
  }
  else echo "ERROR: BAD KEY";
}
elseif ($action=="photo") {
  $sql = "SELECT ph_tid,ph_key FROM ".$GLOBALS['DBprefix']."Photo WHERE ph_id=\"$phid\" AND ph_key=\"$key\"";
  $res =&db_query($sql,$link);
  list($photo,$phkey)=db_fetch_row($res);
  if ($key==$phkey) {
    $len=filesize($GLOBALS['opt_dir']."/photos/$photo.jpg");
    header("Content-Type: image/jpeg");
    header("Content-Length: ".$len);
    readfile($GLOBALS['opt_dir']."/photos/$photo.jpg");
  }
  else echo "ERROR: BAD KEY";
}
db_close($link);
