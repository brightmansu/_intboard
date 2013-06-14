<? /*

XXXX Pro's Application PHP Programming Interface (XAPhPI) for Intellect Board 2

(c) 2005, XXXX Pro, United Open Project
Visit us online: http://openproj.ru
*/

function &getvar($name, $default="") {
    if ((strpos($name,"_text")!==false && $name!="pv_text" && $name!="pm_text") || $name=="f_rules") {
      if (isset($_GET[$name])) $tmp = $_GET[$name];
      elseif (isset($_POST[$name])) $tmp= $_POST[$name];
      else $tmp=$default;
    }
    else {
      if (isset($_GET[$name])) $tmp = htmlspecialchars($_GET[$name]);
      elseif (isset($_POST[$name])) $tmp= htmlspecialchars($_POST[$name]);
      else $tmp=$default;
    }
    $tmp=str_replace('&amp;#','&#',$tmp);
    $tmp=db_slashes($tmp);
    return $tmp;
}

function &build_select($sql,$value="") {
    $res =&db_query($sql,$GLOBALS['link']);
    $tmp = "";
    while ($tmpdata=db_fetch_row($res)) {
        if ($tmpdata[0]==$value) $tmp.="<option value=\"".$tmpdata[0]."\" selected>".$tmpdata[1];
        else $tmp.="<option value=\"".$tmpdata[0]."\">".$tmpdata[1];
    }
    db_free_result($res);
    return $tmp;
}

function &build_sql($prefix,$array=false) {
  $tmp = "";
  $prefixlen = strlen($prefix);
  if (!is_array($array)) $array=&$_POST;
  foreach ($array as $name=>$value) {
      if (substr($name,0,$prefixlen)==$prefix && strpos($name,"__")===false && $name!=$prefix."_id") {
           if (strpos($name,"_text")===false) $value=db_slashes(str_replace('&amp;#','&#',htmlspecialchars($value)));
           else $value=db_slashes($value);
         if ($tmp) $tmp.=", ";
           $tmp .= "$name=\"".$value."\"";
         }
  }
  return $tmp;
}

function &build_sql_all($prefix) {
  $tmp = "";
  $prefixlen = strlen($prefix);
  foreach ($_POST as $name=>$value) {
      if (substr($name,0,$prefixlen)==$prefix) {
         if ($tmp) $tmp.=", ";
         $tmp .= "$name=\"".db_slashes($value)."\"";
      }
  }
  return $tmp;
}

function check($expr) {
    if ($expr>0) echo "checked";
}

function &set_select($select,$value) {
    $select=preg_replace("/selected\s+>/is","",$select);
    $select=preg_replace("/<option\s+value=\"?$value\"?\.*?>/is","<option value=\"$value\" selected>",$select);
    echo $select;
}

function &build_pages($count,$start,$perpage,$ref,$print_msg=1) {
    $reflen = strlen($ref);
    if ($ref[$reflen-1]!="?" && $ref[$reflen-1]!="&") {
        if (!strpos($ref,"?")) $ref.="?";
        else $ref.="&";
    }
    $ref=str_replace("&","&amp;",$ref);
    if (!$perpage) $perpage=10;
    $numpages = ceil($count/$perpage);
    $asterisk = floor($start/$perpage)*$perpage;
    $stpos=floor($start/$perpage);
    if ($numpages>1 || $start>0) {
      $pages = $print_msg ? MSG_pages: "";
      $number=0;
      for ($i=1; $i<=$numpages; $i++) {
        if ($numpages<=10 || ($i<=5 || $i>=$numpages-5 || ($i>$stpos-4 && $i<=$stpos+5))) {
          if ($number!=$start || $start=="all") {
              $pages.="<a href=\"$ref"."st=".$number."\">$i</a> ";
              if ($asterisk==$number && $start!="all") $pages.=" * ";
          }
          else $pages.="$i ";
        }
        elseif ($i==6) $pages.="... ";
        elseif ($i==$numpages-6) $pages.="... ";
        $number+=$perpage;
      }
      if ($GLOBALS['inuserid']>3) {
        if ($start=="all") $pages.="#";
        else $pages.="<a href=\"$ref"."st=all\">#</a> ";
      }
  }
  return $pages;
}

function &getip() {
    $reverse_proxy = false; #true - åñëè èñïîëüçóåòñÿ reverse proxy  
    $reverse_proxy_addresses = array(); #IP àäðåñà reverse proxy
    $ip = $_SERVER['REMOTE_ADDR']; #$_SERVER["HTTP_CLIENT_IP"]
    if ($reverse_proxy && array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($reverse_proxy_addresses) && in_array($ip, $reverse_proxy_addresses, true)) { 
        $ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
    }    
    return $ip;
}

function in_arrayr($needle, $haystack) {
  foreach ($haystack as $v) {
    if ($needle == $v) return true;
    elseif (is_array($v)) {
      if (in_arrayr($needle, $v) === true) return true;
    }
  }
  return false;
}

function &untransliterate($text) {
  $text = str_replace("["," [",$text);
  $text = str_replace("]","] ",$text);
  $text = str_replace("<"," <",$text);
  $text = str_replace(">","> ",$text);
  $words = explode(" ",$text);
  $skip=0;
  $skiptag=0;
  foreach ($words as $curword) {
   if (strpos($curword,"[url")!==false || strpos($curword,"[code]")!==false || strpos($curword,"[email")!==false ||
       strpos($curword,"<a")!==false || strpos($curword,"[_")!==false || $skiptag) { $skiptag=1; }
   else {
    if (strpos($curword,"[")!==false || strpos($curword,"<")!==false || $skip) { $skip = 1; }
    elseif (!in_arrayr($curword,$GLOBALS['smiles'])) {
      $curword = str_replace("ya","ÿ",$curword);
      $curword = str_replace("yo","¸",$curword);
      $curword = str_replace("yu","þ",$curword);
      $curword = str_replace("ay","àé",$curword);
      $curword = str_replace("oy","îé",$curword);
      $curword = str_replace("iy","èé",$curword);
      $curword = str_replace("yy","ûé",$curword);
      $curword = str_replace("uy","óé",$curword);
      $curword = str_replace("ey","åé",$curword);
      $curword = str_replace("sch","ù",$curword);
      $curword = str_replace("sh","ø",$curword);
      $curword = str_replace("ch","÷",$curword);
      $curword = str_replace("zh","æ",$curword);
      $curword = str_replace("\'","ü",$curword);
      $curword = str_replace("\"","ú",$curword);
      $curword = str_replace("`e","ý",$curword);
      $curword = str_replace("`i","é",$curword);

      $curword = str_replace("Ya","ß",$curword);
      $curword = str_replace("Yo","¨",$curword);
      $curword = str_replace("Yu","Þ",$curword);
      $curword = str_replace("Ay","Àé",$curword);
      $curword = str_replace("Oy","Îé",$curword);
      $curword = str_replace("Iy","Èé",$curword);
      $curword = str_replace("Yy","Ûé",$curword);
      $curword = str_replace("Uy","Óé",$curword);
      $curword = str_replace("Ey","Åé",$curword);
      $curword = str_replace("Sch","Ù",$curword);
      $curword = str_replace("Sh","Ø",$curword);
      $curword = str_replace("Ch","×",$curword);
      $curword = str_replace("Zh","Æ",$curword);

      $curword = str_replace("YA","ß",$curword);
      $curword = str_replace("YO","¨",$curword);
      $curword = str_replace("YU","Þ",$curword);
      $curword = str_replace("AY","ÀÉ",$curword);
      $curword = str_replace("OY","ÎÉ",$curword);
      $curword = str_replace("IY","ÈÉ",$curword);
      $curword = str_replace("YY","ÛÉ",$curword);
      $curword = str_replace("UY","ÓÉ",$curword);
      $curword = str_replace("EY","ÅÉ",$curword);
      $curword = str_replace("SCH","Ù",$curword);
      $curword = str_replace("SH","Ø",$curword);
      $curword = str_replace("CH","×",$curword);
      $curword = str_replace("ZH","Æ",$curword);
      $curword = str_replace("`E","Ý",$curword);
      $curword = str_replace("`I","É",$curword);

      $lo_lat=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","r","s","t","u","v","y","z");
      $lo_rus=array("à","á","ö","ä","å","ô","ã","õ","è","æ","ê","ë","ì","í","î","ï","ð","ñ","ò","ó","â","û","ç");

      $up_lat=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","R","S","T","U","V","Y","Z");
      $up_rus=array("À","Á","Ö","Ä","Å","Ô","Ã","Õ","È","Æ","Ê","Ë","Ì","Í","Î","Ï","Ð","Ñ","Ò","Ó","Â","Û","Ç");

      $curword =str_replace($lo_lat,$lo_rus,$curword);
      $curword =str_replace($up_lat,$up_rus,$curword);
    }
    if (strpos($curword,"]")!==false) $skip=0;
   }
   if (strpos($curword,"[/url]")!==false || strpos($curword,"[/code]")!==false || strpos($curword,"[/email]")!==false
       || strpos($curword,"_]")!==false) { $skiptag=0; $skip=0; }
   $untrans .= $curword." ";
  }
  $untrans = str_replace(" [","[",$untrans);
  $untrans = str_replace("] ","]",$untrans);
  $untrans = str_replace(" <","<",$untrans);
  $untrans = str_replace("> ",">",$untrans);
  return $untrans;
}

function transliterate($text) {
  $up_rus=array("À","Á","Â","Ã","Ä","Å","¨","Æ","Ç","È","É","Ê","Ë","Ì","Í","Î","Ï","Ð","Ñ","Ò","Ó","Ô","Õ","Ö","×","Ø","Ù","Ü","Û","Ú","Ý","Þ","ß");
  $up_lat=array("A","B","V","G","D","E","Yo","J","Z","I","I","K","L","M","N","O","P","R","S","T","U","F","H","C","Ch","Sh","Sch","","Y","","E","Yu","Ya");
  $lo_rus=array("à","á","â","ã","ä","å","¸","æ","ç","è","é","ê","ë","ì","í","î","ï","ð","ñ","ò","ó","ô","õ","ö","÷","ø","ù","ü","û","ú","ý","þ","ÿ");
  $lo_lat=array("a","b","v","g","d","e","yo","j","z","i","i","k","l","m","n","o","p","r","s","t","u","f","h","c","ch","sh","sch","","y","","e","yu","ya");
  $text=str_replace($up_rus,$up_lat,$text);
  $text=str_replace($lo_rus,$lo_lat,$text);
  return $text;
}

function &build_date_field($field,$time) {
    $tmp = "<input type=text name=".$field."_day size=2 maxlength=2 value=\"".date("d",$time)."\">.";
    $tmp .= "<input type=text name=".$field."_mon size=2 maxlength=2 value=\"".date("n",$time)."\">.";
    $tmp .= "<input type=text name=".$field."_year size=4 maxlength=4 value=\"".date("Y",$time)."\">";
    return $tmp;
}

function &get_date_field($field) {
  if (getvar($field."_mon")==1 && getvar($field."_day")==1 && getvar($field."_year")==1970) $tmp=23*60*60+1;
  elseif (getvar($field."_mon")=='' || getvar($field."_day")=='' || getvar($field."_year")=='') $tmp=-1;
  else $tmp = mktime(0,0,0,getvar($field."_mon"),getvar($field."_day"),getvar($field."_year"));
  return $tmp;
}

function &build_time_field($field,$time) {
    $tmp = "<input type=text name=".$field."_day size=2 maxlength=2 value=\"".date("d",$time)."\">.";
    $tmp .= "<input type=text name=".$field."_mon size=2 maxlength=2 value=\"".date("n",$time)."\">.";
    $tmp .= "<input type=text name=".$field."_year size=4 maxlength=4 value=\"".date("Y",$time)."\"> ";
    $tmp .= "<input type=text name=".$field."_hour size=2 maxlength=2 value=\"".date("G",$time)."\">:";
    $tmp .= "<input type=text name=".$field."_min size=2 maxlength=2 value=\"".date("i",$time)."\">";
    return $tmp;
}

function &get_time_field($field) {
  if (getvar($field."_mon")==1 && getvar($field."_day")==1 && getvar($field."_year")==1970) $tmp=23*60*60+1;
  elseif (getvar($field."_mon")=='' || getvar($field."_day")=='' || getvar($field."_year")=='') $tmp=-1;
  else $tmp = mktime(getvar($field."_hour"),getvar($field."_min"),0,getvar($field."_mon"),getvar($field."_day"),getvar($field."_year"));
  return $tmp;
}


function &iptonum($ip) {
    $ipdata=explode(".",$ip);
    $num=0;
    for($i=0;$i<4;$i++) $num=$num * 256+floatval($ipdata[$i]);
    return $num;
}

function &numtoip($num) {
    $str=floor($num/(256*256*256)).".";
    $num = ($num/(256*256*256)-floor($num/(256*256*256)))*256*256*256;
    $str.=floor($num/(256*256)).".";
    $num = $num % (256*256);
    $str.=floor($num /256).".";
    $num = $num % (256);
    $str.=$num;
    return $str;
}

function textarea($text) {
    $text=str_replace("<","&lt;",$text);
    $text=str_replace(">","&gt;",$text);
    return $text;
}

function strips(&$el) {
  if (is_array($el)) foreach($el as $k=>$v) strips($el[$k]);
  else $el = stripslashes($el);
}

function clipword($text,$len=512) {
  $text=str_replace("<br />"," ",$text);
  $text=strip_tags($text);
  $text=str_replace("\"","&quot;",$text);
  if (strlen($text)>$len) {
    $text=substr($text,0,$len);
    $pos=strrpos($text,' ');
    $text=substr($text,0,$pos)."...";
  }
  return $text;
}
