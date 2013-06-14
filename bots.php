<?  /*

Bot detection script for Intellect Board 2 Project

(C) 2004-2006, XXXX Pro, United Open Project (http://www.openproj.ru)
Visit us online: http://intboard.ru

*/

if (file_exists($GLOBALS['opt_dir'].'/config/bots.txt')) {
  $botlist=file($GLOBALS['opt_dir'].'/config/bots.txt');
  $buffer;
  if (is_array($botlist)) foreach ($botlist as $curbot) {
    list($botname,$botagent,$bottime)=explode('|',trim($curbot));
    if (strpos($_SERVER['HTTP_USER_AGENT'],$botagent)!==false) {
      $botfound=$botname;
      $buffer.=$botname.'|'.$botagent.'|'.$GLOBALS['curtime']."\n";
    }
    else {
      $buffer.=$curbot;
    }
  }
  if ($botfound) {
    $GLOBALS['inuser']['u__name']=$botfound;
    $fh=fopen($GLOBALS['opt_dir'].'/config/bots.txt','w');
    flock($fh,LOCK_EX);
    fputs($fh,$buffer);
    fclose($fh);
  }
}

?>
