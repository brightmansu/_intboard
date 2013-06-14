<? /*

Common library script for Intellect Board 2

(c) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru
*/

if (!$IBOARD) die ("Hack attempt!");

$GLOBALS['common']=1;

return;

function check_post() {
  if (strtolower($_SERVER['REQUEST_METHOD'])!="post") global_error('HACK ATTEMPT! Only POST method allowed here!');
}

function load_style($file) {
    global $link;;
    if (file_exists($GLOBALS['opt_dir']."/styles/".$GLOBALS['inuser']['st_file']."/".$file)) {
      require_once($GLOBALS['opt_dir']."/styles/".$GLOBALS['inuser']['st_file']."/".$file);
    }
    else {
      $dir=$GLOBALS['inuser']['st_parent'];
      $curdir=$GLOBALS['opt_dir']."/styles/".$dir."/";
      while ($dir && !file_exists($curdir.$file)) {
        $sql = "SELECT st_parent FROM ".$GLOBALS['DBprefix']."StyleSet WHERE st_file=\"$dir\"";
        $res=&db_query($sql,$link);
        list($dir)=db_fetch_row($res);
        $curdir=$GLOBALS['opt_dir']."/styles/".$dir."/";
      }
      if (file_exists($curdir.$file)) require_once($curdir.$file);
      else global_error("File not found in styles: $file");
    }
}

function load_lang($file) {
    require_once($GLOBALS['opt_dir']."/langs/".$GLOBALS['inuser']['ln_file']."/".$file);
}

function build_msg_select($sql,$value="") {
    $res =&db_query($sql,$GLOBALS['link']);
    $tmp = "";
    while ($tmpdata=db_fetch_row($res)) {
        if ($tmpdata[0]==$value) $tmp.="<option value=\"".$tmpdata[0]."\" selected>".constant($tmpdata[1]);
        else $tmp.="<option value=\"".$tmpdata[0]."\">".constant($tmpdata[1]);
    }
    db_free_result($res);
    return $tmp;
}

function title_output($title=0) {
  $buffer='';
  $locations=$GLOBALS['locations'];
  if ($locations) $locations=array_reverse($locations);
  if (isset($GLOBALS['inforum']['tp_library']) && $GLOBALS['action']==$GLOBALS['inforum']['tp_library']."_view") {
    $count=count($locations);
    if ($count>2) $count=2;
    for ($i=0; $i<$count; $i++) {
      $buffer.=strip_tags($locations[$i]);
      if ($i<$count-1) $buffer.=" / ";
    }
  }
  elseif (!is_array($locations) || count($locations)==0) $buffer.=strip_tags($GLOBALS['opt_title']);
  else {
    $count=count($locations);
    if ($count>2) $count=2;
    for ($i=0; $i<$count;$i++) {
      $buffer.=strip_tags($locations[$i]);
      if ($i<$count-1) $buffer.=" / ";
    }
  }
  if ($title) { $buffer = (isset($locations[0]) && $locations[0]) ? strip_tags($locations[0]) : strip_tags($GLOBALS['opt_title']); }
  return $buffer;
}

function build_forum_select($level,$tpid=0,$condition="",$select="") {
    if ($condition) $condition=" AND ($condition)";
    $link = $GLOBALS['link'];
    $uid = $GLOBALS['inuserid'];
    if ($tpid) $tpidsql = " (f_tpid=".$tpid." OR f_tpid=1 OR tp_container=1) AND ";
    if ($level!="f_lread" || $condition!="" || $tpid!=0) {
        $sql="SELECT f_id,f_title,f_tpid,ct_name,f_sortfield,ct_sortfield,f_parent FROM ".$GLOBALS['DBprefix']."Category ct, ".$GLOBALS['DBprefix']."ForumType tp, ".$GLOBALS['DBprefix']."Forum f ".
        "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=$uid AND ua.fid=f.f_id) ".
        "WHERE $tpidsql COALESCE(ua_level,".$GLOBALS['inuserbasic'].")>=$level AND f_ctid=ct_id AND tp_id=f_tpid $condition".
        " ORDER BY ct_sortfield";
        $res=&db_query($sql,$link);
        while ($tmpdata=db_fetch_array($res)) $forums[]=$tmpdata;
        db_free_result($res);
        $forums=sort_forums_recurse($forums);
        if ($tpid) foreach ($forums as $key=>$value) if ($value['f_tpid']!=$tpid && $value['f_tpid']!=1) unset($forums[$key]);
    }
    else {
      if (file_exists($GLOBALS['opt_dir'].'/config/fselect.txt')) {
        $forums=array();
        $bufarray=@file($GLOBALS['opt_dir'].'/config/fselect.txt');
        if (is_array($bufarray)) foreach ($bufarray as $curbuf) {
          list($tmp['f_id'],$tmp['f_title'],$tmp['ct_name'])=explode('|||',trim($curbuf));
          $forums[]=$tmp;
        }
      }
      else {
        $sql="SELECT f_id,f_title,ct_name,ct_sortfield,f_sortfield,f_parent FROM ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."Category ct ".
        "WHERE f_ctid=ct_id ORDER BY ct_sortfield";
        $res =&db_query($sql,$GLOBALS['link']);
        $forums=array();
        while ($tmpdata=db_fetch_array($res)) $forums[]=$tmpdata;
        db_free_result($res);
        $forums=sort_forums_recurse($forums);
        $fh=fopen($GLOBALS['opt_dir'].'/config/fselect.txt','w');
        foreach ($forums as $curbuf) {
          fputs($fh,$curbuf['f_id'].'|||'.$curbuf['f_title'].'|||'.$curbuf['ct_name']."\n");
        }
        fclose($fh);
      }
    }
    $flist = "";
    $oldcat = "0";
    $buf=','.$GLOBALS['inuser']['forum_noaccess'].',';
    $fs=isset($_GET['fs'])?explode(',',$_GET['fs']):array();
    if (is_array($forums)) foreach ($forums as $tmpdata) {
      if (strpos($buf,','.$tmpdata['f_id'].',')===false) {
        if ($tmpdata['ct_name']!=$oldcat) {
          if ($flist) $flist.="</OPTGROUP>";
          $flist.="<OPTGROUP label=\"".$tmpdata['ct_name']."\">";
          $oldcat=$tmpdata['ct_name'];
        }
        if (isset($GLOBALS['inforum']['f_id']) && $tmpdata['f_id']==$GLOBALS['inforum']['f_id'] || array_search($tmpdata['f_id'],$fs)!==false) $flist.="<option value=\"".$tmpdata['f_id']."\" selected>".$tmpdata['f_title'];
        else $flist.="<option value=\"".$tmpdata['f_id']."\">".$tmpdata['f_title'];
      }
    }
    $flist.="</optgroup>";
    return $flist;
}

function build_level_select() {
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UserLevel ORDER BY l_level";
    return build_select($sql,$udata['u__level']);
}

function build_userlevel_select($level=0) {
    $sql = "SELECT l_level, l_title FROM ".$GLOBALS['DBprefix']."UserLevel WHERE l_level!=0 AND l_level<1024 ORDER BY l_level";
    return build_select($sql,$level);
}

function error($errmsg,$code=0) {
    if (!(isset($GLOBALS['admin']) && $GLOBALS['admin'])) { load_style("message.php"); }
    $tmp_link1 = "<a href=\"".(referer_uri()?referer_uri():$GLOBALS["opt_url"])."\">".MSG_go_back."</a>";
    $tmp_link2 = "<a href=\"/\">".MSG_go_mainpage."</a>";
    ob_end_clean();
    ob_start();
    if (!headers_sent() && $code) {
        if ($code==404) header('HTTP/1.1 404 Not Found');
        elseif($code==403) header('HTTP/1.1 403 Forbidden');
        else header('HTTP/1.1 503 Service Unavailable');
    }
    output_message(MSG_e.$errmsg,$tmp_link1,$tmp_link2,"");
    exit();
}

function message($textmsg,$golink=0) {
    $tmp_link1 = referer_uri()?referer_uri():$GLOBALS["opt_url"];
    $tmp_link2 = "<a href=\"/\">".MSG_go_mainpage."</a>";
    if ($golink) $newlink=$tmp_link1;
    output_message($textmsg,"<a href=\"".$tmp_link1."\">".MSG_go_back."</a>",$tmp_link2,"",$newlink);
}

function topic_message($textmsg,$golink=0) {
    $tmp_message = $textmsg;
    if (!$GLOBALS['intopic']) {
      $GLOBALS['intopic']['t_id']=$GLOBALS['topic'];
      $GLOBALS['intopic']['t_link']=&getvar('t_link');
    }
    $tmp_link1 = "<a href=\"".build_url($GLOBALS['intopic'])."#last\">".MSG_go_topic."</a>";
    $tmp_link2 = "<a href=\"".build_url($GLOBALS['inforum'])."\">".MSG_go_forum."</a>";
    $tmp_link3 = "<a href=\"/\">".MSG_go_mainpage."</a>";
    if ($golink) {
      $newlink=build_url($GLOBALS['intopic']);
      $newlink.="#last";
    }
    output_message($tmp_message,$tmp_link1,$tmp_link2,$tmp_link3,$newlink);
}

function month_replace($date) {
  $date=str_replace("January",MSG_January,$date);
  $date=str_replace("February",MSG_February,$date);
  $date=str_replace("March",MSG_March,$date);
  $date=str_replace("April",MSG_April,$date);
  $date=str_replace("May",MSG_May,$date);
  $date=str_replace("June",MSG_June,$date);
  $date=str_replace("July",MSG_July,$date);
  $date=str_replace("August",MSG_August,$date);
  $date=str_replace("September",MSG_September,$date);
  $date=str_replace("October",MSG_October,$date);
  $date=str_replace("November",MSG_November,$date);
  $date=str_replace("December",MSG_December,$date);
  $date=str_replace("Jan",MSG_Jan,$date);
  $date=str_replace("Feb",MSG_Feb,$date);
  $date=str_replace("Mar",MSG_Mar,$date);
  $date=str_replace("Apr",MSG_Apr,$date);
  $date=str_replace("May",MSG_May,$date);
  $date=str_replace("Jun",MSG_Jun,$date);
  $date=str_replace("Jul",MSG_Jul,$date);
  $date=str_replace("Aug",MSG_Aug,$date);
  $date=str_replace("Sep",MSG_Sep,$date);
  $date=str_replace("Oct",MSG_Oct,$date);
  $date=str_replace("Nov",MSG_Nov,$date);
  $date=str_replace("Dec",MSG_Dec,$date);

  $date=str_replace("Monday",MSG_Monday,$date);
  $date=str_replace("Tuesday",MSG_Tuesday,$date);
  $date=str_replace("Wednesday",MSG_Wednesday,$date);
  $date=str_replace("Thursday",MSG_Thursday,$date);
  $date=str_replace("Friday",MSG_Friday,$date);
  $date=str_replace("Saturday",MSG_Saturday,$date);
  $date=str_replace("Sunday",MSG_Sunday,$date);
  $date=str_replace("Mon",MSG_Mon,$date);
  $date=str_replace("Tue",MSG_Tue,$date);
  $date=str_replace("Wed",MSG_Wed,$date);
  $date=str_replace("Thu",MSG_Thu,$date);
  $date=str_replace("Fri",MSG_Fri,$date);
  $date=str_replace("Sat",MSG_Sat,$date);
  $date=str_replace("Sun",MSG_Sun,$date);

  return $date;
}

function long_date_out($date) {
  $date=$date+$GLOBALS['inuser']['u_timeregion']-$GLOBALS['opt_timeregion'];
  if ($date<0) $date=0;
  if ($date) $date=date($GLOBALS['inuser']['u_lformat'],$date);
  else $date=MSG_none;
  return month_replace($date);
}

function short_date_out($date) {
  $date=$date+$GLOBALS['inuser']['u_timeregion']-$GLOBALS['opt_timeregion'];
  if ($date<0) $date=0;
  if ($date) $date=date($GLOBALS['inuser']['u_sformat'],$date);
  else $date=MSG_none;
  return month_replace($date);
}

function sign_code(&$text) {
    if (strpos($text,"[")!==false) {
      $text = preg_replace("/\[br\]/","<br>",$text);

      $text = str_replace("[b]","<b>",$text);
      $text = str_replace("[/b]","</b>",$text);
      $text = str_replace("[i]","<i>",$text);
      $text = str_replace("[/i]","</i>",$text);
      $text = str_replace("[u]","<u>",$text);
      $text = str_replace("[/u]","</u>",$text);
      $text = str_replace("[s]","<s>",$text);
      $text = str_replace("[/s]","</s>",$text);

      $text = preg_replace("/\[font=([^<>]+?)\]/is","<font face=\"$1\">",$text);
      $text = preg_replace("/\[color=([^<>]+?)\]/is","<font color=\"$1\">",$text);
      $text = preg_replace("/\[size=([^<>]+?)\]/is","<font size=\"$1\">",$text);
      $text = str_replace("[/font]","</font>",$text);
      $text = str_replace("[/color]","</font>",$text);
      $text = str_replace("[/size]","</font>",$text);

      $text = preg_replace("/\[url\](\w+?:\/\/[^\"]+?)\[\/url\]/is","<a href=\"/go.php?$1\" rel=\"nofollow\" target=_blank>$1</a>",$text);
      $text = preg_replace("/\[url\]([^\"]+?)\[\/url\]/is","<a href=\"/go.php?http://$1\" rel=\"nofollow\" target=_blank>$1</a>",$text);
      $text = preg_replace("/\[url=(\w+?:\/\/[^\"]+?)\](.+?)\[\/url\]/is","<a href=\"/go.php?$1\" rel=\"nofollow\" target=_blank>$2</a>",$text);
      $text = preg_replace("/\[url=([^\"]+?)\]([^\"]+?)\[\/url\]/is","<a href=\"/go.php?http://$1\" rel=\"nofollow\" target=_blank>$2</a>",$text);
      $text = preg_replace("/\[url=(\S+?)\](.+?)\[\/url\]/is","<a href=\"/go.php?http://$1\" rel=\"nofollow\" target=_blank>$2</a>",$text);
      
      $text = preg_replace("|\/go\.php\?(".$GLOBALS['opt_url']."[^\"]+?\") rel=\"nofollow\" target=_blank|is","$1",$text);

      $text = preg_replace("/\[email\]([\w\d.-]+?\@[\w\d.-]+?)\[\/email\]/is","<a href=\"mailto:$1\">$1</a>",$text);
      $text = preg_replace("/\[email=([\w\d.-]+?\@[\w\d.-]+?)\](.+?)\[\/email\]/is","<a href=\"mailto:$1\">$2</a>",$text);
  }
  $text = str_replace("(c)","&copy;",$text);
  $text = str_replace("(C)","&copy;",$text);
  $text = str_replace("(r)","&reg;",$text);
  $text = str_replace("(R)","&reg;",$text);
  $text = str_replace("(tm)","&trade;",$text);
  $text = str_replace("(TM)","&trade;",$text);
  $text = str_replace("--","&mdash;",$text);

  if ($GLOBALS['opt_sigpics']) {
    $text = preg_replace("/\[img\](\S+?)\[\/img\]/is","<img alt=\"\" src=\"$1\">",$text);
    $text = preg_replace("/\[img=(\S+?)\]/is","<img alt=\"\" src=\"$1\">",$text);
    $text = preg_replace("/(<img .*?>)/ise","check_img(\"$1\")",$text);
    $text = preg_replace("/(<a .*?>.*?<\/a>)/ise","check_link(\"$1\")",$text);
  }
  $text=nl2br($text);

  return $text;
}

function textout($text,$html,$bcode,$smiles,$tid=0,$pid=0) {
  require_once('parser.php');
  if (!$html) $text=htmlspecialchars($text);
  if ($hlight=&getvar('hl')) {
    $hlight=preg_replace("/[.,:;\-\?()\\!+\-*]+/"," ",$hlight);
    $hls=explode(" ",$hlight);
    foreach ($hls as $curhl) {
      if ($curhl && strlen($curlh)>3) $text=preg_replace("|([ >.,:;\-?()\\!\"\]]+)($curhl\S*?)([ <.,:;\-?()\\!\"\[]+)|is","$1<span class=\"hligh\">$2</span>$3",$text);
      elseif ($curhl) $text=str_replace($curhl,"<span class=\"hligh\">$curhl</span>",$text);
    }
  }
  $text=nl2br($text);
  $text=str_replace("&quot;","\"",$text);
  addlinks(&$text);
  if ($smiles) smiles($text);
  if ($bcode) boardcode($text,$html,$tid,$pid);
  if ($text) {
    $count=preg_match_all("/<a [^>]+?>(\S*?)<\/a>/is",$text,$matches);
    if ($count) foreach ($matches[1] as $curmatch) {
      if (strlen($curmatch)>60) $text=str_replace(">".$curmatch."<",">".substr($curmatch,0,30)."...".substr($curmatch,-10,10)."<",$text);
    }
  }
  if ($GLOBALS['action']=="do_print") {
    $text = preg_replace("/<a(.*?)href=\"\/go.php\?([^\"]*?)\"(.*?)>(.*?)<\/a>/is","<a$1href=\"/go.php?$2\"$3>$4</a> ($2)",$text);
  }

  $text = preg_replace("/(<br \/>)?\[table(.*?)\](.*?)\[\/table\](<br \/>)?/ise","table_parse(\"$3\",\"$2\");",$text);
  $text = str_replace('</td>','',str_replace('</tr>','',$text));
  $text = preg_replace("/<tr([^>]*?)>(\s*<br \/>\s*)+</is","<tr$1><",$text);
  $text = preg_replace("/(\s*<br \/>\s*)<li([^>]*?)>/is","<li$2>",$text);
  $text = preg_replace("/<table([^>]*?)>(\s*<br \/>\s*)+</is","<table$1><",$text);
  $text=preg_replace("/&amp;#(\d{3,4};)/is","&#$1",$text);

  $text = str_replace("(c)","&copy;",$text);
  $text = str_replace("(C)","&copy;",$text);
  $text = str_replace("(r)","&reg;",$text);
  $text = str_replace("(R)","&reg;",$text);
  $text = str_replace("(tm)","&trade;",$text);
  $text = str_replace("(TM)","&trade;",$text);
  $text = str_replace("--","&mdash;",$text);

  return $text;
}

function build_avatar_select() {
    $odir=$GLOBALS['opt_dir']."/avatars";
    $avatarselect="";
    if(is_dir($odir) && $dir=opendir($odir)) {
        while ($curfile=readdir($dir)) if (!is_dir($GLOBALS['opt_dir']."/avatars/".$curfile)) $avatarselect.="<option value=\"$curfile\">$curfile";
    closedir($dir);
    }
    return $avatarselect;
}

function load_smiles() {
  if (!(isset($GLOBALS['smiles']) && is_array($GLOBALS['smiles']))) {
    if (is_file($GLOBALS['opt_dir'].'/config/smiles.php')) require_once($GLOBALS['opt_dir'].'/config/smiles.php');
    else {
      global $link;
      $GLOBALS['smiles']=array();
      $sql = "SELECT sm_code,sm_file,sm_show FROM ".$GLOBALS['DBprefix']."Smile";
      $res =&db_query($sql,$link);
      while ($smile=&db_fetch_array($res)) {
        $GLOBALS['smiles'][]=$smile;
      }
      if (db_num_rows($res)==0) $GLOBALS['smiles']=array();
      db_free_result($res);
      $fh=fopen($GLOBALS['opt_dir'].'/config/smiles.php','w');
      fputs($fh,"<?\n\$GLOBALS['smiles']=".var_export($GLOBALS['smiles'],TRUE).";\n?>");
      fclose($fh);
    }
  }
}

function smiles(&$text) {
  if (!(isset($GLOBALS['smiles']) && is_array($GLOBALS['smiles']))) load_smiles();
  foreach ($GLOBALS['smiles'] as $cursmile) {
    if (isset($GLOBALS['smiles_fullpath']) && $GLOBALS['smiles_fullpath'])
      $text=str_replace($cursmile['sm_code'],"<img src=\"".$GLOBALS['opt_url']."/smiles/".$cursmile['sm_file']."\" alt=\"".$cursmile['sm_code']."\">",$text);
    else
      $text=str_replace($cursmile['sm_code'],"<img src=\"smiles/".$cursmile['sm_file']."\" alt=\"".$cursmile['sm_code']."\">",$text);
  }
}

function show_email_q($email,$showtype,$uid) {
    if ($showtype==1) {
        $buffer="<a href=\"mailto:$email\">Email</a>";
    }
    elseif ($showtype==2) {
        $curpos=0;
        $email="<a href=\"mailto:$email\">Email</a>";
        $buffer="<script type=\"text/javascript\"><!--\n";
        while ($curpos<strlen($email)) {
            $pos=rand() % 8 +1;
            $buffer.="document.write('".str_replace('/','\/',substr($email,$curpos,$pos))."');\n";
            $curpos+=$pos;
        }
        $buffer.="//--></script>";
    }
    elseif ($showtype==3) {
        $buffer="<a href=\"index.php?m=misc&amp;a=sendmail&amp;u=$uid\">Email</a>";
    }
    return $buffer;
}

function show_email_f($email,$showtype,$uid) {
    if ($showtype==1) {
        echo "<a href=\"mailto:$email\">$email</a>";
    }
    elseif ($showtype==2) {
        $curpos=0;
        $email="<a href=\"mailto:$email\">$email</a>";
        echo "<script type=\"text/javascript\"><!--\n";
        while ($curpos<strlen($email)) {
            $pos=rand() % 5 +1;
            echo "document.write('".str_replace('/','\/',substr($email,$curpos,$pos))."');\n";
            $curpos+=$pos;
        }
        echo "//--></script>";
    }
    elseif ($showtype==3) {
        echo "<img src=\"agent.php?a=email&amp;u=$uid\" alt=\"E-mail\">";
    }
}

function push_parents(&$locations,$fid) {
  if ($fid!=0) {
    global $link;;
    $sql = "SELECT f_title,f_parent,f_link,f_id FROM ".$GLOBALS['DBprefix']."Forum WHERE f_id=$fid";
    $res =&db_query($sql,$link);
    $fname =&db_fetch_array($res);
    push_parents($locations,$fname['f_parent']);
    array_push($locations,"<a href=\"".build_url($fname)."\">".$fname['f_title']."</a>");
  }
}

function user_out($uname,$uid) {
    if ($uid==1 || !$uid) $output = $uname;
    elseif ($GLOBALS['opt_hurl']) $output = "<a class=\"username\" href=\"user/".urlencode($uname)."/\">$uname</a>";
    else $output = "<a class=\"username\" href=\"index.php?m=profile&amp;u=$uid\">$uname</a>";
    if ($uid==1 || !$uid) $output = str_replace("Yandex", "<font color=MediumOrchid>Yandex</font>", $output);
    if ($uid==1 || !$uid) $output = str_replace("Rambler", "<font color=orange>Rambler</font>", $output);
    if ($uid==1 || !$uid) $output = str_replace("MSN", "<font color=blue>MSN</font>", $output);
    if ($uid==1 || !$uid) $output = str_replace("Google", "<font color=DeepSkyBlue>Google</font>", $output);
    if ($uid==1 || !$uid) $output = str_replace("Yahoo", "<font color=green>Yahoo</font>", $output);
    if ($uid==1 || !$uid) $output = str_replace("Aport", "<font color=black>Aport</font>", $output);
    if ($uid==1 || !$uid) $output = str_replace("WebAlta", "<font color=silver>WebAlta</font>", $output);
    if ($uid==1 || !$uid) $output = str_replace("Twiceler", "<font color=Maroon>Twiceler</font>", $output);
    if ($uid==1 || !$uid) $output = str_replace("Majestic-12", "<font color=DeepSkyBlue>Majestic-12</font>", $output);
    return $output;
}

function load_mail($filename) {
    if (!file_exists($GLOBALS['opt_dir']."/langs/".$GLOBALS['inuser']['ln_file']."/$filename")) error(MSG_e_nomail,404);
    $size = filesize($GLOBALS['opt_dir']."/langs/".$GLOBALS['inuser']['ln_file']."/$filename");
    $fh = fopen($GLOBALS['opt_dir']."/langs/".$GLOBALS['inuser']['ln_file']."/$filename","r");
    $buffer = fread($fh,$size);
    fclose($fh);
  return $buffer;
}

function process_mail($filename,$email,$subject) {
    $buffer=load_mail($filename);
    return replace_mail($buffer,$email,$subject);
}

function mime_encode($text,$charset) {
         return "=?".$charset."?B?".base64_encode($text)."?=";
}

function replace_mail($buffer,$email,$subject) {
  if ($GLOBALS['opt_nomailsend']!=1) {
    if (eregi("^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$", $email)) {
      preg_match_all('/\$(\w+)/s',$buffer,$matches);
      foreach ($matches[1] as $curmatch) {
          $buffer = str_replace("\$".$curmatch,$GLOBALS[$curmatch],$buffer);
      }
      if ($GLOBALS['opt_noname_mail']==0 && $GLOBALS['username']) $email=mime_encode($GLOBALS['username'],$GLOBALS['inuser']['ln_charset'])." <".$email.">";
      $from_email = $GLOBALS['opt_mailout'];
      if ($GLOBALS['opt_noname_mail']==0 && $GLOBALS['opt_title']) $from_email=mime_encode($GLOBALS['opt_title'],$GLOBALS['inuser']['ln_charset'])." <".$from_email.">";
      $buffer=str_replace("\r","",$buffer);
      $headers="From: ".$from_email."\r\n";
      $headers.="Reply-To: ".$from_email."\r\n";
      $headers.="Return-Path: ".$from_email."\r\n";
      $headers.="X-Mailer: IntB Mailer\r\n";
      $headers.="Content-Type: text/plain; charset=\"".$GLOBALS['inuser']['ln_charset']."\"\r\n";
      $headers.="Content-Transfer-Encoding: 8bit\r\n";
      $headers.="X-Priority: 3\r\n";
      return mail($email,mime_encode($subject,$GLOBALS['inuser']['ln_charset']),$buffer,$headers);
    }
  }
}

function show_avatar(&$udata,$noavatar=0) {
    $size="";
    if ($GLOBALS['opt_avatarx']) $size.=" width=".$GLOBALS['opt_avatarx'];
    if ($GLOBALS['opt_avatary']) $size.=" height=".$GLOBALS['opt_avatary'];
    if($noavatar) $tmp = "<img src=\"images/no_avatar.gif\" border=0 alt=\"".MSG_user_avatar."\"$size>";
    else $tmp = "";
    if ($udata['u_avatartype']==1 && isset($udata['u__avatar']) && trim($udata['u__avatar'])) {
        $tmp="<img src=\"avatars/".$udata['u__avatar']."\" border=0 alt=\"".$udata['u__name']."\"$size>";
    }
    elseif ($udata['u_avatartype']==2 && trim($udata['u__avatar'])) {
        $tmp="<img src=\"".$udata['u__avatar']."\" border=0 alt=\"".$udata['u__name']."\"$size>";
    }
    elseif ($udata['u_avatartype']==3 && intval($udata['u__pavatar_id'])) {
        $tmp="<img src=\"file.php?fid=".$udata['u__pavatar_id']."&amp;key=0\" border=0 alt=\"".$udata['u__name']."\"$size>";
    }
    return $tmp;
}

function check_image($name,$maxsize,$maxx,$maxy,$errsize,$errtype,$errxy) {
    if (!is_uploaded_file($_FILES[$name]['tmp_name']) || $_FILES[$name]['size']==0 ||
    $_FILES[$name]['size']>$maxsize || strpos($_FILES[$name]['type'],"image")===false) error($errsize);
    if ($GLOBALS['opt_graphics']) {
        $imdata=getimagesize($_FILES[$name]['tmp_name']);
        if (!$imdata) error($errtype);
        if (($maxx && $imdata[0]>$maxx) || ($maxy && $imdata[1]>$maxy)) error($errxy);
    }
}

function is_new($tviews,$maxdata,$markall=0) {
    if (!$markall) $markall=isset($GLOBALS['inuser']['lv_markall'])?$GLOBALS['inuser']['lv_markall']:0;
    if ($GLOBALS['opt_fixviews'] && intval($maxdata)>$markall) $tmp = $tviews;
    elseif ($GLOBALS['opt_fixviews'] && $markall>=$maxdata) $tmp=1;
    else $tmp = (intval($maxdata)<$GLOBALS['userlast2']);
    if ($GLOBALS['inuserid']<=3) $tmp=1;
    return !$tmp;
}

function check_moderate(&$udata,$level) {
    $tmp=0;
    if ($udata['ua_level'] && $udata['ua_level']>$level) $tmp=1;
    elseif ($udata['u__level']>$level) $tmp=1;
    return $tmp;
}

function user_substr(&$udata,$forum) {
    global $link;;
    if (is_array($udata)) foreach ($udata as $uid=>$count) {
      $sql="UPDATE ".$GLOBALS['DBprefix']."UserStat SET us_count=us_count-$count WHERE fid=\"$forum\" AND uid=\"$uid\"";
      $res =&db_query($sql,$link);
    }
}

function user_summ(&$udata,$forum) {
    global $link;;
    if (is_array($udata)) foreach ($udata as $uid=>$count) {
      $sql="UPDATE ".$GLOBALS['DBprefix']."UserStat SET us_count=us_count+$count WHERE fid=\"$forum\" AND uid=\"$uid\"";
      $res =&db_query($sql,$link);
    }
}

function send_pm($urecv,$usend,$text,$subj,$sqldata) {
    $link = $GLOBALS['link'];
    if ($sqldata!='') $sqldata=", ".$sqldata;
    $curtime = $GLOBALS['curtime'];
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."PersonalMessage SET pm__box=0, pm__owner=$urecv, pm__senddate=$curtime, pm__readdate=0, pm__correspondent=$usend, pm_subj=\"$subj\", pm_text=\"$text\" $sqldata";
    $res =&db_query($sql,$link);
    $pmid = db_insert_id($res);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__pmcount=u__pmcount+1, u__pmtime=".$GLOBALS['curtime']." WHERE u_id=$urecv";
    $res =&db_query($sql,$link);
    $sql = "SELECT u_pmnotify, u__name, u__email FROM ".$GLOBALS['DBprefix']."User WHERE u_id=$urecv";
    $res=&db_query($sql,$link);
    list($notify,$name,$email)=db_fetch_row($res);
    remove_cached_user($urecv);
    if ($notify) {
      $GLOBALS['username']=$name;
      $GLOBALS['subj']=$subj;
      if ($_POST['pm_text']) $GLOBALS['text']=$_POST['pm_text'];
      else $GLOBALS['text']=$text;
      $GLOBALS['sender']=$GLOBALS['inuser']['u__name'];
      process_mail("newpm.txt",$email,MSG_pm_recived." (".$GLOBALS['sender'].")");
    }
    return $pmid;
}

function build_diff_list($topic) {
    global $link;;
    $sql = "SELECT p_uid,COUNT(p_id) AS ucount FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=\"$topic\" GROUP BY p_uid";
    $res =&db_query($sql,$link);
    while ($udata=db_fetch_row($res)) {
      $userdif[$udata[0]]=$udata[1];
    }
    return $userdif;
}

function delete_topic($topic,$maxtime=0) {
  require_once('delete.php');
  do_delete_topic($topic,$maxtime);
}

function forum_resync($fid) {
    $link = $GLOBALS['link'];
    $sql = "SELECT MAX(p_id),COUNT(p_id) FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Topic t ".
    "WHERE p_tid=t_id AND t_fid=$fid AND p__premoderate=0";
    $res =&db_query($sql,$link);
    list($pmax,$pcount)=db_fetch_row($res);
    db_free_result($res);

    $sql= "SELECT MAX(t__startpostid), COUNT(t_id) FROM ".$GLOBALS['DBprefix']."Topic t WHERE t_fid=$fid AND t__pcount>0";
    $res =&db_query($sql,$link);
    list($pstart,$tcount)=db_fetch_row($res);

//    if ($GLOBALS['forum']!=$fid || $GLOBALS['inforum']['f_premoderate']) {
      $sql = "SELECT COUNT(p_id) FROM ".$GLOBALS['DBprefix']."Post, ".$GLOBALS['DBprefix']."Topic ".
      " WHERE p_tid=t_id AND t_fid=$fid AND p__premoderate=1";
      $res =&db_query($sql,$link);
      list($premod)=db_fetch_row($res);
      db_free_result($res);
//    }
//    else $premod=0;

    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f__pcount=".intval($pcount).", f__lastpostid=".intval($pmax).", f__premodcount=".intval($premod).", f__startpostid=".intval($pstart).", f__tcount=".intval($tcount)." WHERE f_id=$fid";
    $res =&db_query($sql,$link);
}

function topic_resync($topic) {
  global $link;
  $sql = "SELECT MAX(p_id),COUNT(p_id),MIN(p_id),MAX(p__time) FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Topic t ".
  "WHERE p_tid=t_id AND t_id=$topic AND p__premoderate=0";
  $res =&db_query($sql,$link);
  list($pmax,$pcount,$pmin,$plast)=db_fetch_row($res);
  db_free_result($res);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET t__pcount=".intval($pcount).", t__lastpostid=".intval($pmax).", t__startpostid=".intval($pmin).", t__lasttime=".intval($plast)." WHERE t_id=$topic";
  $res =&db_query($sql,$link);
}

function check_selfmod() {
  return ($GLOBALS['inforum']['f_selfmod'] && $GLOBALS['intopic']['t_author']==$GLOBALS['inuserid']);
}

function out_online($prefix,$starttime,$endtime,$group,$more="") {
  require('online.php');
  do_out_online($prefix,$starttime,$endtime,$group,$more);
}

function delete_user($uid) {
  require_once('delete.php');
  do_delete_user($uid);
}

function delete_post($pid) {
  require_once('delete.php');
  return do_delete_post($pid);
}

function common_topic_view($topic) {
  global $link;
  if ($GLOBALS['opt_fixviews'] && !$GLOBALS['intopic']['visited'] && $GLOBALS['inuserid']>3 && $GLOBALS['intopic']['lasttime']>$GLOBALS['inforum']['lv_markall']) {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."TopicView VALUES (\"$topic\",".$GLOBALS['inuserid'].")";
    $res =&db_query($sql,$link);
  }

  // защита от превышения нагрузки
  if (($GLOBALS['DBheavyload'] & 4)!=4 && !getvar('preview')) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."TopicVC SET t__views=t__views+1 WHERE tid=\"$topic\"";
    $res =&db_query($sql,$link);
    
    if (db_affected_rows($res)==0) {
      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."TopicVC (tid,t__views) VALUES (\"".$topic."\",1)";
      $res=&db_query($sql,$link);
    }

    $sql = "UPDATE ".$GLOBALS['DBprefix']."ForumVC SET f__views=f__views+1 WHERE fid=\"".$GLOBALS['forum']."\"";
    $res =&db_query($sql,$link);

    if (db_affected_rows($res)==0) {
      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."ForumVC (fid,f__views) VALUES (\"".$GLOBALS['forum']."\",1)";
      $res=&db_query($sql,$link);
    }
  }

  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."TopicRate WHERE tid=\"$topic\" AND uid=\"".$GLOBALS['inuserid']."\"";
  $res =&db_query($sql,$link);
  list($rate) = db_fetch_row($res);
  db_free_result($res);

  return $rate;
}

function handle_upload($file,$id=0,$delete=false,$zerokey=false) {
  global $link;
  if (is_uploaded_file($file['tmp_name']) && !$id) {
    if (!$zerokey) $key=mt_rand();
    else $key=0;
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."File (file_type,file_name,file_size,file_key) VALUES (\"".db_slashes($file['type'])."\",\"".urlencode($file['name'])."\",\"".$file['size']."\",\"".intval($key)."\")";
    $res=&db_query($sql,$link);
    $id=db_insert_id($res);
    move_uploaded_file($file['tmp_name'],$GLOBALS['opt_dir']."/files/".$id.".htm");
    eval('chmod($GLOBALS[\'opt_dir\']."/files/".$id.".htm",0644);');
  }
  elseif (is_uploaded_file($file['tmp_name']) && $id) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."File SET file_type=\"".db_slashes($file['type'])."\", file_name=\"".db_slashes($file['name'])."\", file_size=\"".$file['size']."\" WHERE file_id=\"$id\"";
    $res=&db_query($sql,$link);
    if (file_exists($GLOBALS['opt_dir']."/files/".$id.".htm")) unlink($GLOBALS['opt_dir']."/files/".$id.".htm");
    if (file_exists($GLOBALS['opt_dir']."/files/".$id."_p.htm")) unlink($GLOBALS['opt_dir']."/files/".$id."_p.htm");
    move_uploaded_file($file['tmp_name'],$GLOBALS['opt_dir']."/files/".$id.".htm");
    eval('chmod($GLOBALS[\'opt_dir\']."/files/".$id.".htm",0644);');
  }
  elseif ($delete) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE file_id=\"$id\"";
    $res=&db_query($sql,$link);
    if (file_exists($GLOBALS['opt_dir']."/files/".$id.".htm")) unlink($GLOBALS['opt_dir']."/files/".$id.".htm");
    if (file_exists($GLOBALS['opt_dir']."/files/".$id."_p.htm")) unlink($GLOBALS['opt_dir']."/files/".$id."_p.htm");
    $id=0;
  }
  return $id;
}

function process_post($topic,$newtopic=0) {
  require('process.php');
  if ($newtopic) new_topic_mail($topic,is_premod_need(1));
  return do_process_post($topic,$newtopic);
}

function is_premod_need($newtopic=0) {
  if ($newtopic) $premoderate=$GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ltopicpremod'];
  else $premoderate=$GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lpremod'];
  if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) $premoderate=0;
  return $premoderate;
}

function topic_increment($forum,$topic,$pid) {
  global $link;
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f__tcount=f__tcount+1, f__startpostid=$pid, f__lastpostid=$pid WHERE f_id=$forum";
  $res=&db_query($sql,$link);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET t__startpostid=$pid, t__lastpostid=$pid, t__lasttime=".$GLOBALS['curtime']." WHERE t_id=$topic";
  $res=&db_query($sql,$link);
}

function build_mod_list($forum,$level) {
  global $link;
  global $modlist;
  $modlist="";
  if (is_file($GLOBALS['opt_dir'].'/config/moders'.$forum.'.php')) {
    require($GLOBALS['opt_dir'].'/config/moders'.$forum.'.php');
  }
  elseif ($level>0) {
    $sql = "SELECT u_id, u__name ".
    "FROM ".$GLOBALS['DBprefix']."User u ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (uid=u_id AND fid=$forum) ".
    "WHERE COALESCE(ua_level,u__level)>=$level AND u__level<1000 ORDER BY u__name";
    $res =&db_query($sql,$link);
    while ($udata=&db_fetch_array($res)) {
      if ($udata['u_id']>3) {
        if ($modlist) $modlist.=", ";
        $modlist.= user_out($udata['u__name'],$udata['u_id']);
      }
    }
    $fh=fopen($GLOBALS['opt_dir'].'/config/moders'.$forum.'.php','w');
    flock($fh,LOCK_EX);
    fputs($fh,"<?\n\$modlist = \"".db_slashes($modlist)."\"\n?>");
    fclose($fh);
  }

  $counter=substr_count($modlist,'</a>');
  if ($counter==1) $modlist=MSG_moderator.": ".$modlist;
  elseif ($counter>1) $modlist=MSG_moderators.": ".$modlist;
}

function last_topics($topics=10,$forumtype=0,$msg='') {
    global $link;

    if ($forumtype) $sqldata=" AND f_tpid=".$forumtype;
    $sql = "SELECT t_id,t_title,t_link,f_id,f_link FROM ".$GLOBALS['DBprefix']."Topic,  ".$GLOBALS['DBprefix']."Forum f ".
    "WHERE t_fid=f_id AND t__pcount>0 $sqldata AND ".check_access_read('t_fid',true).
    "ORDER BY t_id DESC LIMIT $topics";
    $res =&db_query($sql,$link);
    if (!$msg) $msg=MSG_t_lasts;
    tlist_start($msg,"rss.php?a=last_topics&amp;count=$topics&amp;type=$forumtype");
    while ($tdata=&db_fetch_array($res)) {
      $list.=tlist_entry($tdata);
    }
    tlist_end("rss.php?a=last_topics&amp;count=$topics&amp;type=$forumtype");
}

function active_topics($topics=10,$forumtype=0,$period=0) {
    global $link;
    if ($forumtype) $sqldata=" AND f_tpid=".$forumtype;
    if ($period) $sqldata.=" AND t__lasttime>".($GLOBALS['curtime']-$period*60*60*24);
    $sql = "SELECT t_id,t_title,t_link,f_id,f_link ".
    "FROM ".$GLOBALS['DBprefix']."Topic, ".$GLOBALS['DBprefix']."Forum f ".
    "WHERE t_fid=f_id AND ".check_access_read('t_fid',true)." AND t__pcount>0 $sqldata ".
    "ORDER BY t__pcount DESC LIMIT $topics";
    $res =&db_query($sql,$link);
    tlist_start(MSG_t_actives." ".format_word($topics,MSG_t1,MSG_t2,MSG_t3),"rss.php?a=active&amp;count=$topics&amp;period=$period&amp;type=$forumtype");
    while ($tdata=&db_fetch_array($res)) {
        $list.=tlist_entry($tdata);
    }
    tlist_end("rss.php?a=active&amp;count=$topics&amp;period=".$period."&amp;type=".$forumtype);
}

function check_system_pass($password) {
    global $link;;
    $sql = "SELECT u__password FROM ".$GLOBALS['DBprefix']."User WHERE u_id=2";
    $res =&db_query($sql,$link);
    list($rightpass)=db_fetch_row($res);
    db_free_result($res);

    if (md5($password)==$rightpass) $result=1;
    else $result=0;
    return $result;
}

function check_ddos($name) {
    $code =&getvar($name);
    global $link;
    $sid=&getvar("sid_ddos");
    $sql = "SELECT code FROM ".$GLOBALS['DBprefix']."Code WHERE sid=\"$sid\"";
    $res =&db_query($sql,$link);
    list($rightcode)=db_fetch_row($res);
    $res =&db_query($sql,$link);
    if ($code!=$rightcode || db_num_rows($res)==0) error(MSG_e_badcode,403);
    db_free_result($res);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Code WHERE sid=\"$sid\"";
    $res =&db_query($sql,$link);
}

function show_ddos_code() {
    $sid = md5(rand());
    return '<input type=text name=code size=8 maxlength=8> <input type=hidden name="sid_ddos" value="'.$sid.'"><img src="agent.php?a=code&amp;sid='.$sid.'" alt="'.MSG_user_nocode.'">';
}

function do_rate() {
    global $link;
    if ($GLOBALS['inuserid']<=3) error(MSG_e_t_rnoguest,403);
    if ($GLOBALS['inforum']['f_lvote']>$GLOBALS['inuserlevel']) error(MSG_e_v_norightsvote,403);
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."TopicRate WHERE tid=\"".$GLOBALS['topic']."\" AND uid=\"".$GLOBALS['inuserid']."\"";
    $res =&db_query($sql,$link);
    $rate = db_fetch_row($res);
    db_free_result($res);

    if ($rate[0]>0) error(MSG_e_t_rated);
    $trvalue=&getvar("tr_value");
    if ($trvalue<1 || $trvalue>7) error(MSG_e_t_badvalue);
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."TopicRate VALUES(\"".$GLOBALS['topic']."\", \"".$GLOBALS['inuserid']."\",\"$trvalue\")";
    $res =&db_query($sql,$link);

    $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET t__ratingsum=t__ratingsum+".intval($trvalue).", t__ratingcount=t__ratingcount+1 WHERE t_id=".$GLOBALS['topic'];
    $res =&db_query($sql,$link);
    topic_message(MSG_t_rated,1);
}

function format_calendar($vardate,$mindate,$reflink,$calend) {
    $day = date("j",$vardate);
    $month = date("n",$GLOBALS['curtime']);
    $year = date("Y",$GLOBALS['curtime']);
    $curdate=mktime(0,0,0,$month,$day,$year);
    $testdate=mktime(0,0,0,date("n",$mindate),1,date("Y",$mindate));
    $first_day=date("w",mktime(0,0,0,date("n",$vardate),1,date("Y",$vardate)));
    if ($first_day==0) $first_day=7;
    while ($curdate>=$testdate) {
      if (date('n',$curdate)==date('n',$vardate) && date('Y',$curdate)==date('Y',$vardate)) $checked='selected';
      else $checked='';
      $monthselect.="<option value=\"".date('d.n.Y',$curdate)."/\" ".$checked.">1-".date("t",$curdate)." ".date("F",$curdate)." ".date("Y",$curdate);
      $month--;
//      if ($month<1) { $month=12; $year--; }
      $curdate=mktime(0,0,0,$month,$day,$year);
    }
    $days_in_month=date("t",$vardate);
    $monthselect=month_replace($monthselect);
    $reflen=strlen($reflink);
    if (!$GLOBALS['opt_hurl']) {
      if (strpos($reflink,'?')==false) $reflink.='?vdate=';
      else $reflink.='&amp;vdate=';
    }
    calendar_out($vardate,$monthselect,$days_in_month,$mindate,$first_day,$reflink,$calend);
}

function f_rules() {
    $ref=$_ENV['HTTP_REFERER'];
    if (!$ref) $ref="index.php?f=".$GLOBALS['forum'];
    forum_rules($ref,$GLOBALS['inforum']['f_rules']);
}

function fast_switch($button=true) {
    $flist = build_forum_select("f_lread");
    fast_switch_form($flist,$button);
}

function present_list() {
  global $link;
  $mode=$GLOBALS['opt_showpresent'];
  $forum=$GLOBALS['forum'];
  $topic=$GLOBALS['topic'];

  if (isset($action) && $action && $action!="view" && $action!="contnr_view" && ($module=="main" || $module==$inforum['tp_library'])) $mode=0;
  if ($mode==1 && $forum) $mode=0;
  if ($mode==2 && $topic) $mode=0;
  if ($mode) {

    $curtime=$GLOBALS['curtime'];
    $todaytime=mktime(0,0,0,date('m',$curtime),date('d',$curtime),date('Y',$curtime))-$GLOBALS['inuser']['u_timeregion']+$GLOBALS['opt_timeregion'];
    $lasttime = $GLOBALS['curtime']-$GLOBALS['opt_heretime']*60;

    $sql = "SELECT pu_uid, pu_uname, pu_hidden, pu_lasttime FROM ".$GLOBALS['DBprefix']."Present WHERE ";
    if (0) $sql.=' pu_lasttime>='.$lasttime.' AND pu_fid='.$forum;
    elseif ($topic) $sql.=' pu_lasttime>='.$lasttime.' AND pu_tid='.$topic;
    else $sql.='pu_lasttime>='.$todaytime;
    $sql.= ' ORDER BY pu_lasttime DESC';

    $res=&db_query($sql,$link);
    $ulist=array();
    $tlist=array();
    $users =0; $guests = 0; $hidden = 0; $today_users = 0; $today_guests = 0; $today_hidden = 0;
    while ($udata=&db_fetch_array($res)) {
      if ($udata['pu_lasttime']>=$lasttime && !(isset($listed) && $listed[$udata['pu_uname']])) {
        if ($udata['pu_hidden']==0 && ($udata['pu_uid']>3 || $udata['pu_uname']!='Guest')) {
          $ulist[]=user_out($udata['pu_uname'],$udata['pu_uid']);
          if ($udata['pu_uid']>3)$users++;
        }
        elseif ($udata['pu_hidden'] && $udata['pu_uid']>3) $hidden++;
        elseif ($udata['pu_uname']=='Guest') $guests++;
      }
      if ($udata['pu_hidden']==0 && ($udata['pu_uid']>3 || ($udata['pu_uname']!='Guest' && !$listed[$udata['pu_uname']]))) {
        $listed[$udata['pu_uname']]=1;
        $tlist[]=user_out($udata['pu_uname'],$udata['pu_uid']);
        if ($udata['pu_uid']>3) $today_users++;
      }
      elseif ($udata['pu_hidden'] && $udata['pu_uid']>3) $today_hidden++;
      elseif ($udata['pu_uname']=='Guest') $today_guests++;
    }
    db_free_result($res);
    if (is_array($ulist)) $userlist=join(' &raquo; ',$ulist);
    if (is_array($tlist)) $today_userlist=join(' &raquo; ',$tlist);
    $today_usercount=format_word($today_guests,MSG_ug1,MSG_ug2,MSG_ug3).", ".format_word($today_hidden,MSG_uh1,MSG_uh2,MSG_uh3);
    $usercount=format_word($guests,MSG_ug1,MSG_ug2,MSG_ug3).", ".format_word($hidden,MSG_uh1,MSG_uh2,MSG_uh3);

    if ($users+$guests+$hidden>$GLOBALS['opt_record_count']) {
      $GLOBALS['opt_record_count']=$users+$hidden+$guests;
      $GLOBALS['opt_record_guests']=$guests;
      $GLOBALS['opt_record_date']=$GLOBALS['curtime'];
      options_save();
    }
    if (($GLOBALS['action']=="view" || !$GLOBALS['action']) && $GLOBALS['module']=="main") {
      $GLOBALS['recordmsg']=format_word($GLOBALS['opt_record_count'],MSG_u1,MSG_u2,MSG_u3,MSG_uv1,MSG_uv2,MSG_uv3);
      $GLOBALS['guestmsg']=format_word($GLOBALS['opt_record_guests'],MSG_ug1,MSG_ug2,MSG_ug3);
    }

    $GLOBALS['totalmsg']=format_word($users+$hidden+$guests,MSG_v1." ".MSG_uv1,MSG_v2." ".MSG_uv2,MSG_v3." ".MSG_uv3);
    $GLOBALS['timemsg']=format_word($GLOBALS['opt_heretime'],MSG_m1,MSG_m2,MSG_m3,MSG_lm1,MSG_lm2,MSG_lm3);

    if ($forum && !$topic) main_present_users(MSG_f_in,$userlist,$users,$usercount,$today_userlist,$today_users,$today_usercount);
    if ($forum && $topic) main_present_users(MSG_topic_in,$userlist,$users,$usercount,$today_userlist,$today_users,$today_usercount);
    if (!$forum && !$topic) main_present_users(MSG_forum_in,$userlist,$users,$usercount,$today_userlist,$today_users,$today_usercount);
  }
}

function check_post_params() {
  check_post();
  if ($GLOBALS['inforum']['f_smiles']) $sqldata.=", p__smiles=\"".intval(getvar("p__smiles"))."\"";
  if ($GLOBALS['inforum']['f_bcode']) $sqldata.=", p__bcode=\"".intval(getvar("p__bcode"))."\"";
  if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lhtml']) $sqldata.=", p__html=\"".intval(getvar("p__html"))."\"";
  else { $sqldata.=", p__html=0 "; }
  if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) $sqldata.=", p__modcomment=\"".getvar('p__modcomment')."\" ";
  if (!isset($_POST['p_signature'])) $sqldata.=", p_signature=0 ";
  return $sqldata;
}

function check_hurl() {
  global $link;
  if ($GLOBALS['opt_hurl']) {
    $tlink=&getvar('t_link');
    if (!preg_match('/^[a-z][a-z\d\-_]*$/i',$tlink)) unset($_POST['t_link']);
    else {
      $sql = "SELECT t_id FROM ".$GLOBALS['DBprefix']."Topic WHERE t_link=\"".getvar('t_link')."\"";
      $res=&db_query($sql,$link);
      if (db_num_rows($res)>0) unset($_POST['t_link']);
      db_free_result($res);
    }
  }
}

function check_topic_params() {
    check_post();

    $inforum=$GLOBALS['inforum'];
    if ($GLOBALS['inuserlevel']<$inforum['f_lpost']) error(MSG_e_p_norights,403);
    if ($inforum['f_status']!=0) error(MSG_e_f_closed);
    if (!$text=db_slashes($_POST['p_text'])) error(MSG_e_p_empty);

    if (strlen($text)<$GLOBALS['opt_minpost']) error(MSG_e_p_toosmall);
    if ($GLOBALS['opt_maxpost'] && strlen($text)>$GLOBALS['opt_maxpost']) error(MSG_e_p_toolarge);

    if (getvar("t__rate") && $GLOBALS['inforum']['f_rate']) $sqldata.=", t__rate=1";
    if ($GLOBALS['inforum']['f_lsticky']<=$GLOBALS['inuserlevel']) $sqldata.=", t__sticky=\"".intval(getvar('t__sticky'))."\"";
    if ($GLOBALS['inforum']['f_lsticky']<=$GLOBALS['inuserlevel']) $sqldata.=", t__stickypost=\"".intval(getvar('t__stickypost'))."\"";
    return $sqldata;
}

function list_smiles($func,$row=5,$col=5) {
  global $link;
  load_smiles();
  $buffer="<table class=\"table-smile\"><tr><th colspan=\"".$col."\">Смайлики<tr>";
  $counter = 0;
  if (is_array($GLOBALS['smiles'])) foreach ($GLOBALS['smiles'] as $cursmile)
   if ($cursmile['sm_show']) {
    if ($counter && $col && ($counter % $col)==0) $buffer.="<tr>";
    $buffer.="<td><a href=\"".request_uri()."#\" onClick=\"$func(' ".$cursmile['sm_code']." '); return false;\"><img border=0 title=\"".$cursmile['sm_code']."\" src=\"smiles/".$cursmile['sm_file']."\" alt=\"".$cursmile['sm_code']."\"></a>";
    $counter++;
  }
  $buffer.="<tr><td class=\"link\" colspan=\"".$col."\"><a target=\"_blank\" href=\"?m=misc&amp;a=show_smiles\">Все смайлики</a></table>";
  return $buffer;
}

function show_vote($votetopic) {
  global $link;;
  $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Vote WHERE uid=".$GLOBALS['inuserid']."  AND tid=$votetopic";
  $res =&db_query($sql,$link);
  list($voted)=db_fetch_row($res);
  db_free_result($res);
  $sql = "SELECT pl.* FROM ".$GLOBALS['DBprefix']."Poll pl WHERE pl_tid=$votetopic";
  $res =&db_query($sql,$link);

  if ($pldata=&db_fetch_array($res)) {
      $pldata['voted']=$voted;
      if ($pldata['voted'] || $GLOBALS['inuserid']<=3 ||
           ($pldata['pl_enddate'] && $pldata['pl_enddate']<$GLOBALS['curtime'])) show_vote_resbegin($pldata);
      else std_vote_begin($pldata);
      $sql = "SELECT pv_id,pv_text, pv_count FROM ".$GLOBALS['DBprefix']."PollVariant pv WHERE pv_plid=".$pldata['pl_id']." ORDER BY pv_id ";
      $res =&db_query($sql,$link);
      $pv_text = array();
      while ($pv_data=&db_fetch_array($res)) {
        $pv_count[$pv_data['pv_id']]=$pv_data['pv_count'];
        $pv_total+=$pv_data['pv_count'];
        $pv_text[$pv_data['pv_id']]=$pv_data['pv_text'];
      }
      db_free_result($res);
      foreach ($pv_text as $curid=>$curtext) {
        if ($pldata['voted'] || $GLOBALS['inuserid']<=3 ||
           ($pldata['pl_enddate'] && $pldata['pl_enddate']<$GLOBALS['curtime'])) show_vote_resentry($pv_text[$curid],$pv_count[$curid],$pv_total);
        else show_vote_entry($pv_text[$curid],$curid);
      }
      if ($pldata['voted'] || $GLOBALS['inuserid']<=3 ||
           ($pldata['pl_enddate'] && $pldata['pl_enddate']<$GLOBALS['curtime'])) show_vote_resend($pv_total);
      else show_vote_end();
  }
}

function options_save() {
  $fh = fopen($GLOBALS['opt_dir']."/config/iboard.php","w") or global_error("Unable to create iboard.php");
  fputs($fh,"<? if (!\$IBOARD) die(\"Hack attempt!\");\n");
  $keys = preg_grep("/opt_/",array_keys($GLOBALS));
  while (!flock($fh,LOCK_EX)) sleep(1);
  foreach ($keys as $curkey) {
    $keydata=str_replace('\\','\\\\',$GLOBALS[$curkey]);
    $keydata=str_replace('"','\"',$keydata);
    fputs($fh,"\$$curkey=\"".$keydata."\";\n");
  }
  fclose($fh);
}

function check_warnings(&$udata) {
  global $link;
  $uw_value=$udata['uw_count'];
  if ($udata['u__warntime']>0 && $udata['u__warntime']<$GLOBALS['curtime']) {
    $sql = "SELECT SUM(uw_value) FROM ".$GLOBALS['DBprefix']."UserWarning WHERE uw_uid=".$udata['u_id']." AND uw_validtill=0";
    $res =&db_query($sql,$link);
    list($uw_const_value)=db_fetch_row($res);
    db_free_result($res);

    $sql = "SELECT SUM(uw_value),MIN(uw_validtill) FROM ".$GLOBALS['DBprefix']."UserWarning WHERE uw_uid=".$udata['u_id']." AND uw_validtill>".$GLOBALS['curtime'];
    $res =&db_query($sql,$link);
    list($uw_value,$uw_validtill)=db_fetch_row($res);
    db_free_result($res);

    if ($uw_validtill<$GLOBALS['curtime']) $uw_validtill="0";
    $uw_value=$uw_value+$uw_const_value;
    $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__warnings=$uw_value, u__warntime=$uw_validtill WHERE u_id=".$udata['u_id'];
    $res =&db_query($sql,$link);
  }
  return $uw_value;
}

function get_premod() {
  return $GLOBALS['inforum']['f__premodcount'];
}

function debug($text) {
  if ($GLOBALS['inuserbasic']>=1000) echo $text."<br>";
}

function edit_post($func,$msg) {
  global $link;
  $pid=&getvar("p");
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."User u ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u.u_id AND ua.fid=".$GLOBALS['forum'].") ".
  "WHERE p_id=\"$pid\" AND u.u_id=p.p_uid AND p_tid=".$GLOBALS['topic'];
  $res =&db_query($sql,$link);
  if (db_num_rows($res)!=1) error(MSG_e_p_notfound,404);
  $pdata =&db_fetch_array($res);
  db_free_result($res);
  if (($pdata['p_uid']!=$GLOBALS['inuserid'] || $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_p_noeditrights);
  if ($pdata['p_uid']!=$GLOBALS['inuserid'] && check_moderate($pdata,$GLOBALS['inuserlevel'])) error(MSG_e_mod_subordinate);
  load_style('display.php');
  display_post_form($msg,$pdata,1);
}

function do_edit_post() {
  if (isset($_POST['continue'])) {
    put_to_draft();
    return ;
  }
  if (getvar("delete")) {
    do_delete_comment();
    return;
  }
  $pid=&getvar("p");
  global $link;;
  $sql = "SELECT p_uid,p_uname,p_id,p_attach FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."User u ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u.u_id AND ua.fid=".$GLOBALS['forum'].") ".
  "WHERE p_id=\"$pid\" AND u.u_id=p.p_uid";
  $res =&db_query($sql,$link);
  if (db_num_rows($res)!=1) error(MSG_e_p_notfound,404);
  $pdata =&db_fetch_array($res);
  db_free_result($res);
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) error(MSG_e_p_noeditrights);
  if ($pdata['p_uid']!=$GLOBALS['inuserid'] && check_moderate($pdata,$GLOBALS['inuserlevel'])) error(MSG_e_mod_subordinate);
  unset($_POST['p_uid']);
  unset($_POST['p_tid']);
  
  if (getvar('preview')) {
    load_style('display.php');
    if ($pdata['p_uid']!=$GLOBALS['inuserid']) {
      $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."User WHERE u_id=".$pdata['p_uid'];
      $res = db_query($sql,$link);
      $udata=db_fetch_array($res);
      db_free_result($res);
    }
    else $udata=&$GLOBALS['inuser'];
    get_preview_data($pdata['p_uid'],$udata);
      
    $pdata=array_merge($pdata,$_POST);
    $pdata['p_tid']=$GLOBALS['topic'];
    display_topic_start('',$pdata,true,false,false,false);  
    display_topic_entry($pdata,$udata,'postentry',array(),array(),'',0,0);
    display_topic_end('',$pdata,true,false,false,false);
    
    display_post_form(MSG_p_edit,$pdata,1);
    return;
  }

  $inforum=$GLOBALS['inforum'];
  $inuserlevel=$GLOBALS['inuserlevel'];
  $sqldata = build_sql("p_");
  $sqldata.= check_post_params();

  if ($GLOBALS['inuserid']==$pdata['p_uid']) $sqldata .= ", p__edittime=".$GLOBALS['curtime'];
  if (is_uploaded_file($_FILES['attach']['tmp_name'])) {
    if ($inuserlevel<$inforum['f_lattach']) error(MSG_e_p_norightsattach);
    if ($inforum['f_attachpics']) {
      check_image("attach",$GLOBALS['opt_maxfileattach'],0,0,MSG_e_p_toobig,MSG_e_p_onlypics,"");
    }
    elseif ($_FILES['attach']['size']>$GLOBALS['opt_maxfileattach']) error(MSG_e_p_toobig);
  }
  $pattach = handle_upload($_FILES['attach'],$pdata['p_attach'],getvar("delattach"));
  $sqldata.=", p_attach=$pattach";

  $sql = "UPDATE ".$GLOBALS['DBprefix']."Post SET $sqldata WHERE p_id=\"$pid\"";
  $res =&db_query($sql,$link);
  redirect(build_url($GLOBALS['intopic']));
}

function get_preview_data($uid,&$udata) {
  global $link;
  
  $sql = "SELECT COALESCE(lv2.l_title,lv1.l_title) AS l_title ".
    "FROM ".$GLOBALS['DBprefix']."User u ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u_id AND fid=".$GLOBALS['forum'].") ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserLevel lv1 ON (lv1.l_level=u__level) ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserLevel lv2 ON (lv2.l_level=ua_level) ".
    "WHERE u_id=".$uid;
  $res = db_query($sql,$link);
  $ldata=db_fetch_row($res);
  $udata['l_title']=$ldata[0];
  db_free_result($res);
  
  $sql = "SELECT SUM(ur_value), uid FROM ".$GLOBALS['DBprefix']."UserRating ".
  "WHERE uid=".$uid." ".
  "GROUP BY uid";
  $res = db_query($sql,$link);
  $rdata=db_fetch_row($res);
  $udata['rating']=$rdata[0];
  db_free_result($res);
  
  $sql = "SELECT SUM(uw_value), uw_uid FROM ".$GLOBALS['DBprefix']."UserWarning ".
  "WHERE uw_uid=".$uid." ".
  "GROUP BY uw_uid";
  $res = db_query($sql,$link);
  $uwdata=db_fetch_row($res);
  $udata['uw_count']=$uwdata[0];
  db_free_result($res);
  
  if ($GLOBALS['inuser']['forum_noaccess']) $skip_forums = explode(',',$GLOBALS['inuser']['forum_noaccess']);
  $sql = "SELECT f_id FROM ".$GLOBALS['DBprefix']."Forum WHERE f_nostats=1";
  $res = db_query($sql,$link);
  while ($fdata=db_fetch_row($res)) $skip_forums[]=$fdata[0];
  db_free_result($res);
  $sql = "SELECT SUM(us_count), uid FROM ".$GLOBALS['DBprefix']."UserStat ".
  "WHERE uid=".$uid." ";
  if (count($skip_forums)) $sql.="AND fid NOT IN (".join(',',$skip_forums).") ";
  $sql.="GROUP BY uid";
  $res = db_query($sql,$link);
  $usdata=db_fetch_row($res);
  $udata['posts']=$usdata[0];
  db_free_result($res);
  
  $udata['rated']=1;
}

function do_delete_comment() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  $pid=&getvar("p");
  $result=delete_post($pid);
  topic_resync($GLOBALS['topic']);
  forum_resync($GLOBALS['forum']);
  if (!$result) {
    $GLOBALS['refpage']=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['intopic']);
    message(MSG_p_deleted,1);
  }
  else {
    $GLOBALS['refpage']=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['inforum']);
    message(MSG_p_deleted,1);
  }
}

function check_access($field,$ignore=false) {
  if ($ignore && $GLOBALS['inuser']['forum_ignored']) $noforums = ($GLOBALS['inuser']['forum_noaccess']?($GLOBALS['inuser']['forum_noaccess'].','.$GLOBALS['inuser']['forum_ignored']):$GLOBALS['inuser']['forum_ignored']);
  else $noforums = $GLOBALS['inuser']['forum_noaccess'];
  if ($noforums) return " $field NOT IN (".$noforums.") ";
  else return " 1=1 ";
}

function check_access_read($field,$ignore=false) {
  if ($ignore && $GLOBALS['inuser']['forum_ignored']) $noforums = ($GLOBALS['inuser']['forum_noread']?($GLOBALS['inuser']['forum_noread'].','.$GLOBALS['inuser']['forum_ignored']):$GLOBALS['inuser']['forum_ignored']);
  else $noforums = $GLOBALS['inuser']['forum_noread'];
  if ($noforums) return " $field NOT IN (".$noforums.") ";
  else return " 1=1 ";
}

function build_url(&$data,$params='',$lastparam='',$lastparamname='') {
  if ($GLOBALS['opt_hurl']) {
    if ($GLOBALS['opt_hurl']==2) $prefix='index.php/';
    else $prefix='';
    if ($params=='a=do_print') {
      $prefix='print/';
      $params='';
    }
    
    if (isset($data['t_id']) && $data['t_id']) {
      if (!(isset($data['f_id'])&&$data['f_id'])) $url=build_url($GLOBALS['inforum']);
      elseif ($data['f_link']) $url=$prefix.$data['f_link']."/";
      else $url=$prefix.$data['f_id']."/";
      if ($data['t_link']) $result=$url.$data['t_link']."/";
      else $result=$url.$data['t_id']."/";
    }
    else {
      if ($data['f_link']) $result=$prefix.$data['f_link']."/";
      else $result=$prefix.$data['f_id']."/";
    }
    if ($lastparam!="") $result.=$lastparam.'.htm';
    if ($params) $result.='?'.$params;
    if ($GLOBALS['opt_hurl']==2) $result=$result;
  }
  else {
    if ($data['t_id']) $result="index.php?t=".$data['t_id'];
    elseif (!$data['f_id'] && $data['ct_id']) $result='index.php?ct='.$data['ct_id'];
    else $result="index.php?f=".$data['f_id'];
    if ($params) $result.='&amp;'.$params;
    if ($lastparam!="") $result.='&amp;'.$lastparamname.'='.$lastparam;
  }
  if (strpos($result,'blog_')===0) $result=str_replace('blog_','blogs/',$result);
  if (strpos($result,'gallery_')===0) $result=str_replace('gallery_','gallerys/',$result);
  return $result;
}

function build_pages_hurl($count,$start,$perpage,$tdata,$ref) {
    $pages = "";
    if ($GLOBALS['opt_hurl']) {
    $t_url = build_url($tdata);
    if (!$perpage) $perpage=10;
    if ($ref) $ref="?".$ref;
    $numpages = ceil($count/$perpage);
    $asterisk = floor($start/$perpage)*$perpage;
    $stpos=floor($start/$perpage);
    if ($numpages>1 || $start>0) {
      $pages = MSG_pages;
      $number=0;
      for ($i=1; $i<=$numpages; $i++) {
        if ($numpages<=10 || ($i<=5 || $i>=$numpages-5 || ($i>$stpos-4 && $i<=$stpos+5))) {
          if ($number!=$start || $start=="all") {
              $pages.="<a href=\"$t_url"."$number.htm\">$i</a> ";
              if ($asterisk==$number && $start!="all") $pages.=" * ";
          }
          else $pages.="$i ";
        }
        elseif ($i==6) $pages.="... ";
        elseif ($i==$numpages-6) $pages.="... ";
        $number+=$perpage;
      }
      if ($GLOBALS['inuserid']>3) {
        if ($start==='all') $pages.="#"; 
        else $pages.="<a href=\"$t_url"."all.htm\">#</a> ";
      }      
    }
  }
  elseif ($tdata['t_id']) $pages =&build_pages($count,$start,$perpage,"index.php?t=".$tdata['t_id'].'&amp;'.$ref);
  else $pages =&build_pages($count,$start,$perpage,"index.php?f=".$tdata['f_id'].'&amp;'.$ref);
  return trim($pages);
}

function remove_cached_user($uid) {
  global $link;
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res=&db_query($sql,$link);
}

function put_to_draft() {
  global $link;
  if ($GLOBALS['topic']) $draft['dr_tid']=$GLOBALS['topic'];
  else $draft['dr_tid']="0";
  if ($GLOBALS['inuserid']<3) error(MSG_e_dr_noguests,403);
  $draft['dr_fid']=$GLOBALS['forum'];
  $draft['dr_uid']=$GLOBALS['inuserid'];
  $_POST['time']=$GLOBALS['curtime'];
  $draft['dr_text']=db_slashes(serialize($_POST));
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Draft SET dr_text=\"".$draft['dr_text']."\" ".
  " WHERE dr_uid=\"".$draft['dr_uid']."\" AND dr_tid=".$draft['dr_tid']." AND dr_fid=".$draft['dr_fid'];
  $res=&db_query($sql,$link);
  if (db_affected_rows($res)==0) {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Draft SET ".
    "dr_uid=".$GLOBALS['inuserid'].', dr_fid='.intval($GLOBALS['forum']).', '.
    'dr_tid='.intval($draft['dr_tid']).', dr_text="'.$draft['dr_text'].'"';
    $res=&db_query($sql,$link);
  }
}

function get_from_draft() {
  global $link;
  $sql = "SELECT dr_text FROM ".$GLOBALS['DBprefix']."Draft WHERE dr_uid=".$GLOBALS['inuserid']." AND ";
  if ($GLOBALS['topic']) $sql.="dr_tid=".$GLOBALS['topic'];
  else $sql.="dr_fid=".$GLOBALS['forum']." AND dr_tid=0";
  $res=&db_query($sql,$link);
  list($data)=db_fetch_row($res);
  $data=unserialize($data);
  $data['p_tid']=$data['t'];
  return $data;
}

function build_link_tag() {
  $start =&getvar('st');
  $globurl = $GLOBALS['opt_url'];
  if (substr($globurl,-1)!='/') $globurl.='/';
  #$globurl='/';

  if (isset($GLOBALS['intopic']) && $GLOBALS['intopic'] && $GLOBALS['inforum']['f_tpid']==1) {
    $perpage=$GLOBALS['inuser']['u_mperpage'];
    $count=$GLOBALS['intopic']['t__pcount'];
    $url=$globurl.build_url($GLOBALS['intopic']);
    $buffer='<link rel="index" href="'.$globurl.build_url($GLOBALS['inforum']).'">';
    $buffer.='<link rel="up" href="'.$globurl.build_url($GLOBALS['inforum']).'" id="UpLink">';
    $buffer.='<link rev="subsection" href="'.$globurl.build_url($GLOBALS['inforum']).'">';
    $start=calc_start_offset();
    $pagecount = ceil($count/$perpage);
    $curpage = floor($start/$perpage);
  }
  elseif (isset($GLOBALS['inforum']) && $GLOBALS['inforum']) {
    if ($GLOBALS['inforum']['f_tpid']==1) $perpage=$GLOBALS['inuser']['u_tperpage'];
    else $perpage=$GLOBALS['inuser']['u_aperpage'];
    $count=$GLOBALS['inforum']['f__tcount'];
    $url=$globurl.build_url($GLOBALS['inforum']);
    $buffer='<link rel="index" href="'.$globurl.'">';
    if ($fdata['f_id']=$GLOBALS['inforum']['f_parent']) $buffer.='<link rel="up" href="'.build_url($fdata).'" id="UpLink">';
    else $buffer.='<link rel="up" href="'.$globurl.'" id="UpLink">';
    $start=isset($_GET['st'])?$_GET['st']:"";
    $pagecount = ceil($count/$perpage);
    $curpage = floor($start/$perpage);
  } else $buffer = "";

  if (isset($perpage) && $perpage && ($pagecount>1 || $start>0) && $start!="all") {
    if ($start==$curpage*$perpage) { // если начинаем не с середины
      $prev=$start-$perpage;
      $next=$start+$perpage;
    }
    else {
      $prev=$curpage*$count;
      $next=$curpage*$count+$perpage;
    }
    $query=$_SERVER['QUERY_STRING'];
    if ($query) $query='?'.$query;

    if ($GLOBALS['opt_hurl']) {
      if ($next<$count) $buffer.='<link rel="next" href="'.$url.intval($next).'.htm'.$query.'" id="NextLink">';
      if ($prev>=0) $buffer.='<link rel="prev" href="'.$url.intval($prev).'.htm'.$query.'" id="PrevLink">';
      if ($start>0) $buffer.='<link rel="first" href="'.$url.'0.htm'.$query.'">';
      $buffer.='<link rel="last" href="'.$url.($pagecount-1)*$perpage.'.htm'.$query.'">';
    }
    else {
      $query=preg_replace('/st=\d+?/i','',$query);
      $query=str_replace('&&','&',$query);
      if ($next<$GLOBALS['intopic']['t__pcount']) $buffer.='<link rel="next" href="'.$url.$query.'&amp;st='.$next.'" id="NextLink">';
      if ($prev>0) $buffer.='<link rel="prev" href="'.$url.$query.'&amp;st='.$prev.'" id="PrevLink">';
      if ($start>0) $buffer.='<link rel="first" href="'.$url.$query.'&amp;st='.$start.'">';
      $buffer.='<link rel="last" href="'.$url.($pagecount-1)*$perpage.'&amp;st='.$query.'">';
    }
  }
  $buffer.='<link rel="search" href="'.$globurl.'?m=search">';
  $buffer.='<link rel="contents" href="'.$globurl.'">';
  $buffer.='<link rel="help" href="'.$globurl.'?m=misc&a=show_bcode">';
  if ($GLOBALS['action']!='view' && $GLOBALS['action']!=$GLOBALS['inforum']['tp_template']."_view" &&
  $GLOBALS['action']!='listusers' && $GLOBALS['action']!='view_updated')
    $buffer.='<meta name="robots" content="noindex">';
  if ($GLOBALS['module']=='profile' && $GLOBALS['action']=='view') $buffer.='<meta name="robots" content="nofollow">';
  $buffer=str_replace('&','&amp;',$buffer);
  return $buffer;
}

function calc_start_offset() {
  global $link;
  static $offset,$firstcall;
  if (!$firstcall) {
    if (getvar("o")=="1") $sort=1;
    elseif (getvar("o")=="0") $sort=0;
    else $sort=$GLOBALS['inuser']['u_sortposts'];

    $topic=$GLOBALS['topic'];
    $start=&getvar("st");
    if (!$start && isset($_GET['st'])) $start="0";
    if ($post=&getvar('p')) {
      if ($sort==1) $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$topic AND p__premoderate=0 AND p_id>=\"$post\"";
      else $sql="SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$topic AND p__premoderate=0 AND p_id<\"$post\"";
      $res=&db_query($sql,$link);
      list($start)=db_fetch_row($res);
      if (!getvar('cutid')) {
        if ($sort==1) $start=$start+$GLOBALS['inuser']['u_prevmsgs'];
        else $start=$start-$GLOBALS['inuser']['u_prevmsgs'];
      }
      db_free_result($res);
      if ($sort==1) {
        if ($start>$GLOBALS['inuser']['u_mperpage']) $start=$start-$GLOBALS['inuser']['u_mperpage'];
        else $start="0";
      }
    }
    if ((!isset($_GET['st']) && !$post && $GLOBALS['intopic']['t__pcount']>$GLOBALS['inuser']['u_mperpage'] && !$sort) ||
      ($start=="new" && $GLOBALS['inuserid']<=3)) {
      if ($GLOBALS['inuser']['u_firstpost'] || $_GET['last']) $start=$GLOBALS['intopic']['t__pcount']-$GLOBALS['inuser']['u_mperpage'];
      else $start="0";
    }
    elseif ("new"==$start && !$post && $GLOBALS['inuserid']>3) {
       if (!(isset($_SESSION['t'.$GLOBALS['topic']]) && $_SESSION['t'.$GLOBALS['topic']])) $last=intval($GLOBALS['userlast2']);
       else $last=$_SESSION['t'.$GLOBALS['topic']];
       if ($sort==1) $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$topic AND p__premoderate=0 AND p__time>".$last." ";
       else $sql="SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p_tid=$topic AND p__premoderate=0 AND p__time<=".$last." ";
       $res=&db_query($sql,$link);
       list($start)=db_fetch_row($res);
       if ($sort==1) $start=$start+$GLOBALS['inuser']['u_prevmsgs'];
       else $start=$start-$GLOBALS['inuser']['u_prevmsgs'];
       db_free_result($res);
       if ($sort==1) {
         if ($start>$GLOBALS['inuser']['u_mperpage']) $start=$start-$GLOBALS['inuser']['u_mperpage'];
         else $start="0";
       }
    }
    $firstcall=1;
    $offset=$start;
  }
  return $offset;
}

function show_news_feed($fid,$title,$maxnews=10,$maxdays=0) {
  global $link;
  $rsslink="rss.php?a=newtopic&amp;f=46&amp;count=50";
  if ($maxdays) $timelimit=" AND  p__time>".($GLOBALS['curtime']-$maxdays*24*60*60);
  if ($maxnews) $limit=" LIMIT 0,".$maxnews;
  $sql = "SELECT t.*, f_id, f_link, p__time FROM ".$GLOBALS['DBprefix']."Topic t, ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Forum f ".
  " WHERE t_fid=".$fid." AND p_tid=t_id AND t__startpostid=p_id AND p__premoderate=0 AND f_id=t_fid $timelimit ORDER BY p__time DESC $limit";
  $res =&db_query($sql,$link);
  news_feed_start($title,$rsslink);
  while ($ndata=&db_fetch_array($res)) {
    news_feed_entry($ndata);
  }
  if (db_num_rows($res)==0) news_feed_noentries();
  news_feed_end();
}

function log_file_name($time) {
  return $GLOBALS['opt_dir'].'/logs/'.date('y',$time).'_'.date('m',$time).'_'.date('d',$time).'.txt';
}

function max_file_attach($size) {
  $max1=return_bytes(ini_get('upload_max_filesize'));
  $max2=return_bytes(ini_get('post_max_size'));
  $size=min($size,$max1,$max2);
  return (intval($size/1024));
}

function return_bytes($val) {
   $val = trim($val);
   $last = strtolower($val{strlen($val)-1});
   switch($last) {
        case 'g':
           $val *= 1024;
       case 'm':
           $val *= 1024;
       case 'k':
           $val *= 1024;
   }
   return $val;
}

function sort_forums_callback($a,$b) {
  if ($a['ct_sortfield']==$b['ct_sortfield']) return $a['f_sortfield']-$b['f_sortfield'];
  else return $a['ct_sortfield']-$b['ct_sortfield'];
}

function sort_forums_recurse(&$forums,$level=0,$parent=0) {
  $result = array();
  $tmp=array();
  for ($i=0, $count=count($forums); $i<$count; $i++) {
    if ($level==0 || $forums[$i]['f_id']>0)
      if ($parent==$forums[$i]['f_parent']) $tmp[]=$forums[$i];
  }
  usort($tmp,'sort_forums_callback');
  for ($i=0, $count=count($tmp); $i<$count; $i++) {
    $tmp[$i]['f_title']=str_repeat('&nbsp;&nbsp;&nbsp;',$level).$tmp[$i]['f_title'];
    $result[]=$tmp[$i];
    $result=array_merge($result,sort_forums_recurse($forums,$level+1,$tmp[$i]['f_id']));
  }
  return $result;
}

function get_forum_sub() {
  global $link;
  if ($GLOBALS['inuserid']>3) {
    $sql = "SELECT tid FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=".$GLOBALS['inuserid']." AND fid=".$GLOBALS['forum']." AND tid=4294967294 LIMIT 1";
    $res =&db_query($sql,$link);
    $inforum = db_num_rows($res);
    db_free_result($res);

    $sql = "SELECT tid FROM ".$GLOBALS['DBprefix']."Subscription WHERE uid=".$GLOBALS['inuserid']." AND fid=".$GLOBALS['forum']." AND tid=4294967295 LIMIT 1";
    $res =&db_query($sql,$link);
    $autosub = db_num_rows($res);
    db_free_result($res);
  }
  return array($inforum,$autosub);
}

function do_sub() {
  global $link;

  $tid=&getvar("tid");
  $fid=$GLOBALS['forum'];
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE fid=$fid AND tid=\"$tid\" AND uid=".$GLOBALS['inuserid'];
  $res =&db_query($sql,$link);

  if (getvar("sub")) {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription SET uid=".$GLOBALS['inuserid'].", fid=$fid, tid=\"$tid\"";
    $res =&db_query($sql,$link);
  }

//  $GLOBALS['refpage']="index.php?f=".$GLOBALS['forum'];
  message(MSG_sub_saved,1);
}

function redirect($url = '', $code = 302) {
    global $link;
    $site_url=$GLOBALS['opt_url'];
    if (substr($site_url,-1,1)!='/') $site_url.='/';
    if($url=='self') $url=$site_url.request_uri();
    elseif($url=='ref') $url=referer_uri();
    if(strpos(trim($url),"://")) $location = $url;
    else $location = $site_url.$url;
    if($GLOBALS['inuserid']>3 && !isset($_COOKIE[session_name()])) { 
        session_write_close();  
        if(preg_match('/(.*)?(.+)=(.+)/', $location)) { 
            $location .= '&'.session_name().'='.session_id(); 
        } else {         
            $location .= '?'.session_name().'='.session_id(); 
        } 
    }    
    $location = str_replace(array("\n", "\r"), '', $location);
    db_close($link);
    header('Location: '.$location, TRUE, $code);
    die("<a href=\"".htmlspecialchars($location)."\">".MSG_f_go."&nbsp;&rarr;</a>") or exit("<a href=\"".htmlspecialchars($location)."\">".MSG_f_go."&nbsp;&rarr;</a>");;
}

function referer_uri() {
    if (isset($_REQUEST['refpage']) && $_REQUEST['refpage']) $refpage = $_REQUEST['refpage'];
    elseif (isset($GLOBALS['refpage']) && $GLOBALS['refpage']) $refpage = $GLOBALS['refpage'];
    elseif (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) $refpage =$_SERVER['HTTP_REFERER'];
    else $refpage = "";
    return $refpage;
}

function request_uri() {
  if (isset($_SERVER['REQUEST_URI'])) $uri = $_SERVER['REQUEST_URI'];
  else {
    if (isset($_SERVER['argv'])) $uri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['argv'][0];
    elseif (isset($_SERVER['QUERY_STRING'])) $uri = $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
    else $uri = $_SERVER['SCRIPT_NAME'];
  }
  return $uri;
}

function UTF8toCP1251($str) {
         if (is_callable('iconv')) { 
            return iconv('UTF-8', 'Windows-1251', $str); 
         } else if (is_callable('mb_convert_encoding')) { 
            return mb_convert_encoding($str, 'Windows-1251', 'UTF-8'); 
         } else {
            $table = array("\xD0\x81" => "\xA8", "\xD1\x91" => "\xB8", "\xD0\x8E" => "\xA1", "\xD1\x9E" => "\xA2", "\xD0\x84" => "\xAA", "\xD0\x87" => "\xAF", "\xD0\x86" => "\xB2", "\xD1\x96" => "\xB3", "\xD1\x94" => "\xBA", "\xD1\x97" => "\xBF", "\xD3\x90" => "\x8C", "\xD3\x96" => "\x8D", "\xD2\xAA" => "\x8E", "\xD3\xB2" => "\x8F", "\xD3\x91" => "\x9C", "\xD3\x97" => "\x9D", "\xD2\xAB" => "\x9E", "\xD3\xB3" => "\x9F");
            return preg_replace('#([\xD0-\xD1])([\x80-\xBF])#se', 'isset($table["$0"]) ? $table["$0"] :  chr(ord("$2")+("$1" == "\xD0" ? 0x30 : 0x70))', $str);
         }
}

function CP1251toUTF8($str) {
         if (is_callable('iconv')) { 
            return iconv('Windows-1251', 'UTF-8', $str); 
         } else if (is_callable('mb_convert_encoding')) { 
            return mb_convert_encoding($str, 'UTF-8', 'Windows-1251'); 
         } else {
            $table = array("\xA8" => "\xD0\x81", "\xB8" => "\xD1\x91", "\xA1" => "\xD0\x8E", "\xA2" => "\xD1\x9E", "\xAA" => "\xD0\x84", "\xAF" => "\xD0\x87", "\xB2" => "\xD0\x86", "\xB3" => "\xD1\x96", "\xBA" => "\xD1\x94", "\xBF" => "\xD1\x97", "\x8C" => "\xD3\x90", "\x8D" => "\xD3\x96", "\x8E" => "\xD2\xAA", "\x8F" => "\xD3\xB2", "\x9C" => "\xD3\x91", "\x9D" => "\xD3\x97", "\x9E" => "\xD2\xAB", "\x9F" => "\xD3\xB3");
            return preg_replace('#[\x80-\xFF]#se',' "$0" >= "\xF0" ? "\xD1".chr(ord("$0")-0x70) : ("$0" >= "\xC0" ? "\xD0".chr(ord("$0")-0x30) : (isset($table["$0"]) ? $table["$0"] : ""))', $str);
         }
}

function valid_email_address($mail) {
  $user = '[a-zA-Z0-9_\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+';
  $domain = '(?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.?)+';
  $ipv4 = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
  $ipv6 = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';

  return preg_match("/^$user@($domain|(\[($ipv4|$ipv6)\]))$/", $mail);
}

function valid_url($url, $absolute = FALSE) {
  $allowed_characters = '[a-z0-9\/:_\-_\.\?\$,;~=#&%\+]';
  if ($absolute) {
    return preg_match("/^(http|https|ftp):\/\/". $allowed_characters ."+$/i", $url);
  }
  else {
    return preg_match("/^". $allowed_characters ."+$/i", $url);
  }
}

?>
