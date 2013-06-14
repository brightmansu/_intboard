<? /*

Message parsing library for Intellect Board 2

(c) 2007, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

function addlinks(&$text) {
 $text=preg_replace("/\[nocode\](.*?)\[\/nocode\]/ise","str_replace('[','&#091;',str_replace(']','&#093;','$1'))",$text);
 $text=preg_replace("/\[nohtml\](.*?)\[\/nohtml\]/ise","'[nohtml]'.nl2br(str_replace('<','&#060;',str_replace('>','&#062;',str_replace('<br />',\"\n\",'$1')))).'[/nohtml]'",$text);
 $text = "  ".$text."  ";
 $text = str_replace("<"," <",$text);
 $text = str_replace(">","> ",$text);
 $text = preg_replace("/\n/","",$text);
 $text = preg_replace("/\r/","",$text);
// $text = preg_replace("/(\s+)([^:\t \n\[\"']+?\@[^\t \n\[\"']+?)([\s\"']+)/is","$1 <a href=\"mailto:$2\">$2</a> $3",$text);
 $text = preg_replace("/(\s+)(http:\/\/[^\s\"'\[,!]+)([\s\"'\[,!]+)/is","$1 <a href=\"$2\" rel=\"nofollow\" target=_blank>$2</a> $3",$text);
// $text = preg_replace("/(\s+)(www\.[^\s\"']+?)([\s\"']+)/is","$1 <a href=\"http://$2\" rel=\"nofollow\" target=_blank>$2</a> $3",$text);
// $text = preg_replace("/(\s+)([^\s\"'\[]+?\.ru)([\s\"'\[]+)/is","$1 <a href=\"http://$2\" rel=\"nofollow\" target=_blank>$2</a> $3",$text);
// $text = preg_replace("/(\s+)([^\s\"'\[]+?\.com)([\s\"'\[]+)/is","$1 <a href=\"http://$2\" rel=\"nofollow\" target=_blank>$2</a> $3",$text);
// $text = preg_replace("/(\s+)([^\s\"'\[]+?\.net)([\s\"'\[]+)/is","$1 <a href=\"http://$2\" rel=\"nofollow\" target=_blank>$2</a> $3",$text);
// $text = preg_replace("/(\s+)([^\s\"'\[]+?\.org)([\s\"'\[]+)/is","$1 <a href=\"http://$2\" rel=\"nofollow\" target=_blank>$2</a> $3",$text);
 $text = preg_replace("|(<a href=\"".$GLOBALS['opt_url']."[^\"]+?\") rel=\"nofollow\" target=_blank|is","$1",$text);
 $text = str_replace(" <","<",$text);
 $text = str_replace("> ",">",$text);
 $text = preg_replace('/\[nohtml\](.*?)\[\/nohtml\]/ise',"strip_tags('$1')",$text);
}

function check_hidden($posts,$text) {
  if (!$GLOBALS['inuserposts'] && $GLOBALS['inuserid']>3) {
    global $link;
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p_uid=".$GLOBALS['inuserid']." AND p__premoderate=0";
    $res =&db_query($sql,$link);
    list($count)=db_fetch_row($res);
    db_free_result($res);
    $GLOBALS['inuserposts']=$count;
  }
  if ($posts<=$GLOBALS['inuserposts']) return "<hr width=\"80%\" align=left><div class=\"descr\">—крытый текст, требуетс€ ".format_word($posts,MSG_p1,MSG_p2,MSG_p3).", у вас ".intval($GLOBALS['inuserposts']).":</div><br>".$text."<hr width=\"80%\" align=left>";
  else return "<hr width=\"80%\" align=left>—крытый текст, требуетс€ ".format_word($posts,MSG_p1,MSG_p2,MSG_p3).", у вас ".intval($GLOBALS['inuserposts'])."<hr width=\"80%\" align=left>";
}

function check_reg($text) {
  if ($GLOBALS['inuserid']>3) return "<hr width=\"80%\" align=left><div class=\"descr\">—крытый текст, доступен только зарегистрированным пользовател€м:</div><br>".$text."<hr width=\"80%\" align=left>"; 
  else return "<hr width=\"80%\" align=left>—крытый текст, доступен только <a href=\"?a=rules&m=profile\">зарегистрированным</a> пользовател€м.<hr width=\"80%\" align=left>"; 
}

function check_level($level,$text) {
  if ($level<=$GLOBALS['inuserlevel']) return "<hr width=\"80%\" align=left><div class=\"descr\">".MSG_p_levelhide."</div><br>".$text."<hr width=\"80%\" align=left>";
  else return "<hr width=\"80%\" align=left>".MSG_p_levelhide.".<hr width=\"80%\" align=left>";
}

function check_group($group,$text) {
  if (in_array($group,explode(",", $GLOBALS['inuser']['user_groups']))) return "<hr width=\"80%\" align=left><div class=\"descr\">„асть данного сообщени€ доступна только <a href=\"?m=group&amp;a=show&g=".$group."\">определенной группе пользователей</a></div><br>".$text."<hr width=\"80%\" align=left>";
  else return "<hr width=\"80%\" align=left>„асть данного сообщени€ доступна только <a href=\"?m=group&amp;a=show&g=".$group."\">определенной группе пользователей.</a><hr width=\"80%\" align=left>";
}

function check_url($url) {
  $url=strtolower($url);
  $res=1;
  $url=preg_replace('/\t+/','',$url);
  if (strpos($url,"script:")!==false) $res=0;
  if (strpos($url,"/admin")===0) $res=0;
  $forumurl=strtolower($GLOBALS['opt_url']);
  $forumurl=str_replace("www.","",$forumurl);
  $forumurl=preg_replace("|/$|s","",$forumurl);
  $url=str_replace("www.","",$url);
  if (strpos($url,$forumurl."/admin")!==false) $res=0;
  if (strpos($url,"a=do_logout")!==false) $res=0;
  if (strpos($url,"\"")!==false) $res=0;
  return $res;
}

function check_img($imgtext) {
  preg_match("/src=\"([^\"]+?)\"[\s>]/is",$imgtext,$matches);
  if (!$matches[1]) preg_match("/src='([^']+?)'[\s>]/is",$imgtext,$matches);
  if (!$matches[1]) preg_match("/src=(\S+?)/is",$imgtext,$matches);
  $tmptext=str_replace($matches[1],"",$imgtext);
  if (preg_match("/\Won\w+?=/is",$tmptext) || preg_match("/\Wurl\(/is",$tmptext)) {
    $GLOBALS['hackattempt']++;
    return "<br><font color=red><b>HACK ATTEMPT:</b> ".htmlspecialchars($imgtext)."</font><br>";
  }
//  if (strpos($matches[1],$GLOBALS['opt_url']."/smiles")===false) $imgtext=preg_replace("|<(img .*?)>|is","<$1 onLoad=\"ch_img(this)\">",$imgtext);
  if (check_url($matches[1])) return $imgtext;
  else {
    $GLOBALS['hackattempt']++;
    return "<br><font color=red><b>HACK ATTEMPT:</b> ".$matches[1]."</font><br>";
  }
}

function check_link($linktext) {
  preg_match("/href=\"([^\"]+?)\"[\s>]/is",$linktext,$matches);
  if (!$matches[1]) preg_match("/href='([^']+?)'[\s>]/is",$linktext,$matches);
  if (!$matches[1]) preg_match("/href=(\S+?)[\s>]/is",$linktext,$matches);
  $tmptext=str_replace($matches[1],"",$linktext);
  if (preg_match("/\Won\w+?=/is",$tmptext) || preg_match("/\Wurl\(/is",$tmptext)) {
    $GLOBALS['hackattempt']++;
    return "<br><font color=red><b>HACK ATTEMPT:</b> ".htmlspecialchars($linktext)."</font><br>";
  }
  if (check_url($matches[1])) return $linktext;
  else {
    $GLOBALS['hackattempt']++;
    return "<br><font color=red><b>HACK ATTEMPT:</b> ".substr($matches[1],0,50)."</font><br>";
  }
}

function process_code($text,$html) {
  if ($html) $text=str_replace("<br />","\n",$text);
  $text=str_replace("  ","&nbsp;&nbsp;",$text);
  $text=str_replace('\\"','"',$text);
  $text=str_replace("\t","&nbsp;&nbsp;",$text);
  if ($html) {
    $text=str_replace("<","&lt;",$text);
    $text=str_replace(">","&gt;",$text);
  }
  $text="<code>$text</code>";
  $text=str_replace("[","&#091;",$text);
  $text=str_replace("]","&#093;",$text);
  if (is_array($GLOBALS['smiles'])) foreach ($GLOBALS['smiles'] as $cursmiles) if ($cursmiles['sm_show']) $text=str_replace("<img src=\"smiles/".$cursmiles['sm_file']."\" alt=\"".$cursmiles['sm_code']."\">",$cursmiles['sm_code'],$text);
  $text=nl2br($text);
  return $text;
}

function process_php($text,$html) {
  $text=str_replace("<br />","\n",$text);
  $text=str_replace('\\"','"',$text);
  $text=str_replace('&lt;','<',$text);
  $text=str_replace('&gt;','>',$text);
  if (strpos($text,'<?')===false) $text='<? '.$text.' ?>';
  $text = highlight_string($text,true);
  $text=str_replace("[","&#091;",$text);
  $text=str_replace("]","&#093;",$text);
  if (!$html) {
    $text=str_replace('&amp;','&',$text);
  }
  return $text;
}

function table_parse($text,$params) {
  preg_match_all('|(\w+)=(["].*?["])|is',$params,$matches);
  $count=count($matches[1]);
  #$width='"90%"';
  #$align='"center"';
  for ($i=0; $i<$count; $i++) {
    if ($matches[1][$i]=='width') $width=$matches[2][$i];
    if ($matches[1][$i]=='align') $align=$matches[2][$i];
  }
  $text ="<table".($width?" width=".$width:"").($align?" align=".$align:"")." class=\"usertable\">".$text;
  $text = str_replace('[/tr]','',$text);
  $text = str_replace('[/td]','',$text);
  $text = str_replace("[tr]","<tr>",$text);
  $text = str_replace("[td]","<td>",$text);
  $text = preg_replace("/\[td colspan=(\d+)\]/is","<td colspan=\"$1\">",$text);
  $text.="</table>";
  return $text;
}

function boardcode(&$text,$html=0,$tid,$pid) {
 if (strpos($text,"[")!==false) {
  $cutid=&getvar('cutid');
  if ($pid==$cutid || $tid==0 || $GLOBALS['action']=="do_print") {
    $text=preg_replace("/\[cut\](.*?)\[\/cut\]/is","$1",$text);
    $text=preg_replace("/\[cut=\".*?\"\](.*?)\[\/cut\]/is","$1",$text);
  }
  else {
    $tdata['t_id']=$tid;
    $tdata['t_link']=$GLOBALS['intopic']['t_link'];
    $tdata['f_id']=$GLOBALS['forum'];
    $tdata['f_link']=$GLOBALS['inforum']['f_link'];
    $text=preg_replace("/\[cut\](.*?)\[\/cut\]/ise",'\'<a href="\'.build_url($tdata,\'cutid=\'.$pid.\'&p=\'.$pid).\'">'.MSG_p_uncut.'</a>\'',$text);
    $text=preg_replace("/\[cut=\"(.*?)\"\](.*?)\[\/cut\]/ise",'\'<a href="\'.build_url($tdata,\'cutid=\'.$pid.\'&p=\'.$pid).\'">$1</a>\'',$text); // 'return \'<a href="\'.build_url($tdata,\'cutid=$pid\',$pid,\'p\').\'>$1</a>\''
  }
  $text=preg_replace("/\[code\](.*?)\[\/code\]/eis","process_code('$1',\"$html\")",$text);
  $text=preg_replace("/\[php\](.*?)\[\/php\]/eis","process_php('$1',\"$html\")",$text);

  $text = str_replace("[hr]","<hr width=\"100%\" align=\"center\" COLOR=\"#e2a35c\" SIZE=\"2\">",$text);
  $text = str_replace("[br]","<br>",$text);

  $text = str_replace('[quote]','<blockquote><div style="height:1px;width:1px;overflow:hidden">[q]</div>',$text);
  $text = str_replace('[/quote]','<div style="height:1px;width:1px;overflow:hidden">[/q]</div></blockquote>',$text);

  $text = str_replace('[q]','<blockquote><div style="height:1px;width:1px;overflow:hidden">[q]</div>',$text);
  $text = str_replace('[/q]','<div style="height:1px;width:1px;overflow:hidden">[/q]</div></blockquote>',$text);

  $text = preg_replace("/\[quote=(.+?)\]/is","<br>$1 ".MSG_written.":<blockquote><div style=\"height:1px;width:1px;overflow:hidden\">[q]</div>",$text);//.short_date_out($2)."
  $text = preg_replace("/\[q=(.+?)\]/is","<br>$1 ".MSG_written.":<blockquote><div style=\"height:1px;width:1px;overflow:hidden\">[q]</div>",$text);

  $text = preg_replace("/\[url\](\w+?:\/\/[^\"]+?)\[\/url\]/is","<a href=\"$1\" rel=\"nofollow\" target=_blank>$1</a>",$text);
  $text = preg_replace("/\[url\]([^\"]+?)\[\/url\]/is","<a href=\"http://$1\" rel=\"nofollow\" target=_blank>$1</a>",$text);
  $text = preg_replace("/\[url=(\w+?:\/\/[^\"]+?)\](.+?)\[\/url\]/is","<a href=\"$1\" rel=\"nofollow\" target=_blank>$2</a>",$text);
  $text = preg_replace("/\[url=([^\"]+?)\]([^\"]+?)\[\/url\]/is","<a href=\"http://$1\" rel=\"nofollow\" target=_blank>$2</a>",$text);
  $text = preg_replace("/\[url2=([^\"]+?)\](.+?)\[\/url2\]/is","<a href=\"$1\" rel=\"nofollow\">$2</a>",$text);
  $text = preg_replace("|(<a href=\"".$GLOBALS['opt_url']."[^\"]+?\") rel=\"nofollow\" target=_blank|is","$1",$text);

  $text = preg_replace("/\[email\](\S+?\@\S+?)\[\/email\]/is","<a href=\"mailto:$1\">$1</a>",$text);
  $text = preg_replace("/\[email=(\S+?\@\S+?)\](.+?)\[\/email\]/is","<a href=\"mailto:$1\">$2</a>",$text);

  $text = str_replace("[b]","<strong>",$text);
  $text = str_replace("[/b]","</strong>",$text);
  $text = str_replace("[i]","<em>",$text);
  $text = str_replace("[/i]","</em>",$text);
  $text = str_replace("[u]","<u>",$text);
  $text = str_replace("[/u]","</u>",$text);
  $text = str_replace("[s]","<s>",$text);
  $text = str_replace("[/s]","</s>",$text);

  $text = preg_replace("/\[#\](.+?)\[\/#\]/is","",$text);  
  
  $text = preg_replace("/\[youtube\]([\w\d\/\?=&:.\-]+)\[\/youtube\]/is","<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"http://www.youtube.com/v/$1&hl=ru&fs=1\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/$1&hl=ru&fs=1\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"425\" height=\"344\"></embed></object>",$text);
  
  $text = preg_replace("/\[video](\w+?:\/\/[^\"]+?)\[\/video\]/is","<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"$1\"><param name=\"wmode\" value=\"transparent\"><embed src=\"$1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"344\"></object>",$text);
  
  $text = preg_replace("/\[flash=(\d+?),(\d+?),(\w+?:\/\/[^\"]+?)\]/is","<object width=\"$1\" height=\"$2\"><param name=\"movie\" value=\"$3\"><param name=\"wmode\" value=\"transparent\"><embed src=\"$3\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"$1\" height=\"$2\"></object>",$text);
  
  $text = preg_replace("/\[font=([\w ]+?)\]/is","<font face=\"$1\">",$text);
  $text = preg_replace("/\[color=([#\w\d]+?)\]/is","<font color=\"$1\">",$text);
  $text = preg_replace("/\[size=(\d+?)\]/is","<font size=\"$1\">",$text);
  $text = str_replace("[/font]","</font>",$text);
  $text = str_replace("[/color]","</font>",$text);
  $text = str_replace("[/size]","</font>",$text);

  $text = str_replace("[list]","<ul>",$text);
  $text = str_replace("[*]","<li>",$text);
  $text = str_replace("[/list]","</ul>",$text);
  
  $text = str_replace("[list1]","<ol>",$text);
  $text = str_replace("[*]","<li>",$text);
  $text = str_replace("[/list1]","</ol>",$text);
  
  if (!$GLOBALS['opt_imgtag']) {
    $text = preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\"$1\" alt=\"$1\" title=\"$1\" class=\"postImg\">",$text);
    $text = preg_replace("/\[img=(.+?)\](.+?)\[\/img\]/is","<img src=\"$2\" title=\"$1\" alt=\"$1\" class=\"postImg\">",$text);
  }
  else {
    $text = preg_replace("/\[img\](\S+?)\[\/img\]/is","<a href=\"$1\">".MSG_image."</a>",$text);
    $text = preg_replace("/\[img=(.+?)\](.+?)\[\/img\]/is","<a href=\"$2\">$1</a>",$text);
  }
  
  $text = preg_replace("/\[off\](.*?)\[\/off\]/is","<div class=\"offtopic\">".MSG_offtopic.": $1</div>",$text);

  $text = preg_replace("/\[center\](.*?)\[\/center\]/is","<div style=\"text-align : center\">$1</div>",$text);
  $text = preg_replace("/\[c\](.*?)\[\/c\]/is","<div style=\"text-align : center\">$1</div>",$text);
  $text = preg_replace("/\[right\](.*?)\[\/right\]/is","<div style=\"text-align : right\">$1</div>",$text);
  $text = preg_replace("/\[left\](.*?)\[\/left\]/is","<div style=\"text-align : left\">$1</div>",$text);

  $text = preg_replace("/\[translit\](.*?)\[\/translit\]/esi","untransliterate(\"$1\")",$text);

  $text = preg_replace("/\[hide](.*?)\[\/hide\]/esi","check_reg(\"$1\")",$text);
  $text = preg_replace("/\[hide=(\d+?)\](.*?)\[\/hide\]/esi","check_hidden($1,\"$2\")",$text);
  $text = preg_replace("/\[level=(\d+?)\](.*?)\[\/level\]/esi","check_level($1,\"$2\")",$text);
  $text = preg_replace("/\[group=(\d+?)\](.*?)\[\/group\]/esi","check_group($1,\"$2\")",$text);

 if (!(array_key_exists("boardcodes",$GLOBALS)&&$GLOBALS['boardcodes']) && file_exists($GLOBALS['opt_dir'].'/config/bcodes.txt')) {
   $GLOBALS['boardcodes']=file($GLOBALS['opt_dir'].'/config/bcodes.txt');
 } else $GLOBALS['boardcodes']="";
 if (is_array($GLOBALS['boardcodes'])) foreach ($GLOBALS['boardcodes'] as $curcode) {
   list($str1,$str2)=explode(' ::: ',trim($curcode));
   $str1=str_replace('[','\\[',str_replace(']','\\]',str_replace('/','\\/',db_slashes($str1))));
   if ($str1) $text=preg_replace("/$str1/is",$str2,$text);
 }

  $text = preg_replace("/(<img .*?>)/ise","check_img(\"$1\")",$text);
  if (!$html) $text = preg_replace("/(<a .*?>)/ise","check_link(\"$1\")",$text);
  $text = preg_replace("/(<script.*?<\/script>)/ise","\"<br><font color=red><b>HACK ATTEMPT:</b> \".htmlspecialchars(\"$1\").\"</font><br>\"",$text);
 }
}

?>
