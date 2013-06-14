<? /*

Miscellanuos script for Intellect Board 2 Project
 
(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

$action=&getvar("a");

function show_bcode() {
	bcode_example();
}

function show_smiles() {
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Smile";
  $link = $GLOBALS['link'];
  $res =&db_query($sql,$link);
  smiles_start();
  while ($smile=&db_fetch_array($res)) {
	  smiles_entry($smile);
  }
  smiles_end();
}

function friend() {
	if ($GLOBALS['inuserid']<=3) error(MSG_e_u_guestnosend);
	friend_form();
}

function do_friend() {
	check_ddos("code");
	if ($GLOBALS['inuserid']<=3) error(MSG_e_u_guestnosend);
	$GLOBALS['text']=&getvar("text");
	$name=&getvar("name");  
	$GLOBALS['username']=$name;
	$subj=&getvar("subj");
	$email=&getvar("email");
	if (!$GLOBALS['text'] || !$subj || !$email || !$name) error(MSG_e_misc_unfilled);
	$GLOBALS['sendname']=$GLOBALS['inuser']['u__name'];
	$GLOBALS['ttitle']=$GLOBALS['intopic']['t_title'];
	$GLOBALS['ftitle']=$GLOBALS['inforum']['f_title'];
	$GLOBALS['flink']=$GLOBALS['opt_url']."/index.php?t=".$GLOBALS['topic'];
	process_mail("friend.txt",$name." <".$email.">",$subj);
	topic_message(MSG_friend_sent);
}

function sendmail() {
	if ($GLOBALS['inuserid']<=3) error(MSG_e_mail_guests);	
	$link = $GLOBALS['link'];
	$uid =&getvar("u");
	$sql = "SELECT u__email,u_showmail,u__name,u_id FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
	$res =&db_query($sql,$link);
	$udata =&db_fetch_array($res);
	sendmail_form($udata);
}

function do_sendmail() {
	check_ddos("code");
	if ($GLOBALS['inuserid']<=3) error(MSG_e_mail_guests);	
  $text=$_POST["text"];
	$subj=$_POST["subj"];
	if (!$text) error(MSG_e_mail_empty);
	if (!$subj) error(MSG_e_mail_emptysubj);
	$link = $GLOBALS['link'];
	$uid =&getvar("u");
	$sql = "SELECT u__email,u__name FROM ".$GLOBALS['DBprefix']."User WHERE u_id=\"$uid\"";
	$res =&db_query($sql,$link);
	$udata =&db_fetch_array($res);	
	$GLOBALS['username']=$udata['u__name'];
  if ($GLOBALS['opt_noname_mail']==0) {
    $email=$udata['u__name']." <".$udata['u__email'].">";
	  mail($email,$subj,$text,"From: ".$GLOBALS['inuser']['u__name']." <".$GLOBALS['inuser']['u__email'].">\r\nX-Mailer: Intellect Board 2 Forum Script\r\nContent-Type: text/plain; charset=".$GLOBALS['inuser']['ln_charset']."\r\nContent-Transfer-Encoding: 8bit");
  } else {
	  mail($udata['u__email'],$subj,$text,"From: ".$GLOBALS['inuser']['u__email']."\r\nX-Mailer: Intellect Board 2 Forum Script\r\nContent-Type: text/plain; charset=".$GLOBALS['inuser']['ln_charset']."\r\nContent-Transfer-Encoding: 8bit");
  }
	message(MSG_misc_mailsent);
}

function detrans() {
	detrans_form();
}

function view_rules() {
    $ref=$_ENV['HTTP_REFERER'];
    if (!$ref) $ref="index.php";
    $filename=$GLOBALS['opt_dir']."/langs/".$GLOBALS['inuser']['ln_file']."/rules.txt";
    if (($size=filesize($filename))>0) {
      $fh=fopen($filename,"r");
      $rules=fread($fh,$size);
      fclose($fh);
    }
    forum_rules($ref,$rules);
}

function do_select_topic() {
  global $link;
  $title=&getvar('title');
  if ($title) {
    if ($fid=$GLOBALS['forum']) $sqldata=" AND t_fid=\"$fid\"";
    if ($GLOBALS['opt_search_ext']) $modedata=" IN BOOLEAN MODE";    
    $sql = "SELECT t_id,t_title, ".db_match($GLOBALS['DBprefix']."Topic.",$modedata,$title,"t_title,t_descr")." FROM ".$GLOBALS['DBprefix']."Topic WHERE ".db_match2($GLOBALS['DBprefix']."Topic.",$modedata,$title,"t_title,t_descr").">0 $sqldata ORDER BY rel DESC";
    $res=&db_query($sql,$link);
    tsel_start($title);
    while ($tdata=&db_fetch_array($res)) {
      tsel_entry($tdata);
    }
    if (db_num_rows($res)==0) tsel_noentries();
    tsel_end();
  }
  $flist="<option value=0>".MSG_f_all.build_forum_select('f_lview');
  tsel_form($flist,$title);
}

function locations($locations) {
	if ($GLOBALS['action']=="show_bcode") array_push($locations,MSG_misc_bcode);
	elseif ($GLOBALS['action']=="show_smiles") array_push($locations,MSG_misc_smiles);
	elseif ($GLOBALS['action']=="friend") { 
		array_push($locations,"<a href=\"index.php?f=".$GLOBALS['forum']."\">".$GLOBALS['inforum']['f_title']."</a>");
		array_push($locations,"<a href=\"index.php?t=".$GLOBALS['intopic']['t_id']."\">".$GLOBALS['intopic']['t_title']."</a>");		
		array_push($locations,MSG_misc_sendfriend); 
	}
	elseif ($GLOBALS['action']=="sendmail") {
		array_push($locations,MSG_misc_mail); 
	}
	elseif ($GLOBALS['action']=="detrans") {
		array_push($locations,MSG_misc_detrans); 
	}
	elseif ($GLOBALS['action']=="view_rules") {
		array_push($locations,MSG_forum_rules); 
	}
	return $locations;
}
