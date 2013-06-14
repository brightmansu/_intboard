<?

error_reporting(E_ERROR | E_WARNING | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

if (file_exists('config/guest.txt')) {
  $guest = join('',file('config/guest.txt'));
  $GLOBALS['inuser']=unserialize($guest);
  unset($guest);
}
else {
  $GLOBALS['inuser']['ln_file']='ru';
  $GLOBALS['inuser']['ln_charset']='cp1251';
}

require("langs/".$GLOBALS['inuser']['ln_file']."/feedback.php");

if ($_POST['a']=='do_feedback') do_feedback();
else feedback();

function feedback() {
  $emails = file('config/feedback.txt');
  $buffer = '<select name="fb_id"><option value=0>'.MSG_misc_fbselect;
  for ($i=1, $count=count($emails); $i<=$count; $i++) if (trim($emails[$i-1])) {
    list($email,$descr)=explode('|',trim($emails[$i-1]),2);
    $buffer.='<option value="'.$i.'">'.$descr;
  }
  $buffer.='</select>';
  feedback_form($buffer);
}

function do_feedback() {
  $emails = file('config/feedback.txt');
  $number = $_POST['fb_id'];
  if (!$emails[$number-1]) feedback_message(MSG_misc_nomail);
  if (!check_last()) feedback_message(MSG_misc_already_sent);
  list($email,$descr) = explode('|',trim($emails[$number-1]),2);

  $GLOBALS['text']=$_POST['text'];
  $GLOBALS['sendername']=$_POST['sendername'];
  $GLOBALS['email']=$_POST['email'];
  $GLOBALS['ip']=$_SERVER['REMOTE_ADDR'];
  $GLOBALS['forum_url']='http://'.$_SERVER['HTTP_HOST'].str_replace('feedback.php','',$_SERVER['REQUEST_URI']);
  
  process_mail('feedback.txt',$email,$_SERVER['HTTP_HOST'].' -- '.MSG_misc_fbmsg.': '.$_POST['subj'],$_POST['email']);
  feedback_message(MSG_misc_fbsent);
}

function check_last() {
  if (file_exists("config/feedlast.txt")) $regdata=file("config/feedlast.txt");
  if (is_array($regdata)) foreach ($regdata as $curstr) {
    list($time,$ip,$proxy)=explode("|",trim($curstr));
    if ($time>time()-300) {
      $newipdata.=$curstr;
      if ($ip==$_SERVER['REMOTE_ADDR'] || ($_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR']==$proxy)) return false;
    }
  }
  $newipdata.=time()."|".$_SERVER['REMOTE_ADDR'].'|'.$_SERVER['HTTP_X_FORWARDED_FOR']."\n";
  $fh=fopen("config/feedlast.txt","w");
  flock($fh,LOCK_EX);
  fputs($fh,$newipdata);
  fclose($fh);
  return true;
}

function feedback_form($select) { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title><?=MSG_misc_feedback;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$GLOBALS['inuser']['ln_charset'];?>">
<link rel="SHORTCUT ICON" href="favicon.ico">
<base href="<?=$GLOBALS['opt_url'];?>/">
<link rel="stylesheet" href="styles/<?=$GLOBALS['inuser']['st_file'];?>/<?=$GLOBALS['inuser']['st_file'].".css";?>" type="text/css">
<script type="text/javascript"><!--
function check_form(frm) {
  if (frm.elements['fb_id'].value==0) {
    alert('<?=MSG_misc_nomail;?>');
    return false;
  }
  else return true;
}
// -->
</script>
</head>
<body>
<form action="feedback.php" method="post" onSubmit="return check_form(this);">
<table class="innertable"><!--style="margin: 16% auto 0 auto; width: 60%;">-->
<!--<col width="50%"><col>-->
<tr><td class="tablehead" colspan=2><?=MSG_misc_feedback;?>
<tr><td><?=MSG_misc_fbto;?>:<td><?=$select;?>
<tr><td><?=MSG_misc_fbfrom_name;?>:<td><input type="text" name="sendername" size=40 maxlength=80>
<tr><td><?=MSG_misc_fbfrom;?>:<td><input type="text" name="email" size=40 maxlength=60>
<tr><td><?=MSG_misc_fbsubj;?>:<td><input type="text" name="subj" size=40 maxlength=255>
<tr><td colspan=2 style="text-align: center"><?=MSG_misc_fbtext;?>:<br />
<textarea name="text" cols=60 rows=8></textarea>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="do_feedback">
<input type=submit value="<?=MSG_send;?>">
</table></form>
<? }

function feedback_message($msg) { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title><?=MSG_misc_feedback;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$GLOBALS['inuser']['ln_charset'];?>">
<link rel="SHORTCUT ICON" href="favicon.ico">
<base href="<?=$GLOBALS['opt_url'];?>/">
<link rel="stylesheet" href="styles/<?=$GLOBALS['inuser']['st_file'];?>/<?=$GLOBALS['inuser']['st_file'].".css";?>" type="text/css">
</head>
<body>
<table class="innertable"><!--style="margin: 21% auto 0 auto; height: 20%; width: 60%; ">--><tr>
<td class="tablehead"><?=$msg;?>
<tr>
<td><?=MSG_misc_fbgo;?><ul>
<li><a href="feedback.php"><?=MSG_misc_fbgo_back;?></a></ul>
</table></body></html>
<? exit();
}

function load_mail($filename) {
    if (!file_exists("langs/".$GLOBALS['inuser']['ln_file']."/$filename")) error(MSG_e_nomail);
    $size = filesize("langs/".$GLOBALS['inuser']['ln_file']."/$filename");
    $fh = fopen("langs/".$GLOBALS['inuser']['ln_file']."/$filename","r");
    $buffer = fread($fh,$size);
    fclose($fh);
    return $buffer;
}

function process_mail($filename,$email,$subject,$from_email=false) {
    $buffer=load_mail($filename);
    if (!$from_email) $from_email=$GLOBALS['opt_mailout'];
    return replace_mail($buffer,$email,$subject,$from_email);
}

function mime_encode($text,$charset) {
         return "=?".$charset."?B?".base64_encode($text)."?=";
}

function replace_mail($buffer,$email,$subject,$from_email) {
  if ($GLOBALS['opt_nomailsend']!=1) {
    if (ereg("^[a-zA-Z0-9_.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$", $email)) {
      preg_match_all('/\$(\w+)/s',$buffer,$matches);
      foreach ($matches[1] as $curmatch) {
          $buffer = str_replace("\$".$curmatch,$GLOBALS[$curmatch],$buffer);
      }
      if ($GLOBALS['opt_noname_mail']==0 && $GLOBALS['username']) $email=mime_encode($GLOBALS['username'],$GLOBALS['inuser']['ln_charset'])." <".$email.">";
      if ($GLOBALS['opt_noname_mail']==0 && $GLOBALS['opt_title']) $from_email=mime_encode($GLOBALS['opt_title'],$GLOBALS['inuser']['ln_charset'])." <".$from_email.">";
      $buffer=str_replace("\r","",$buffer);
      $headers="From: ".$from_email."\r\n";
      $headers.="X-Mailer: Intellect Board Mailer\r\n";
      $headers.="Content-Type: text/plain; charset=".$GLOBALS['inuser']['ln_charset']."\r\n";
      $headers.="Content-Transfer-Encoding: 8bit\r\n";
      $headers.="X-Priority: 3\r\n";
      return mail($email,mime_encode($subject,$GLOBALS['inuser']['ln_charset']),$buffer,$headers);
    }
  }
}

?>