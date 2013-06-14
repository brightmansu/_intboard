<? /*

Parse URL library for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die ("Hack attempt HURL!");

$tmp = parse_url($GLOBALS['opt_url']);
$urldir = $tmp['path'];
unset($tmp);
$query = urldecode($_SERVER['REQUEST_URI']);
if ($GLOBALS['opt_hurl']==2) $query=str_replace('/index.php/','/',$query);
if (strpos($query,'?')!==false) $query=substr($query,0,strpos($query,'?'));
/*if (substr($query,-1,1)=='/') {
  $dirlength=strlen($urldir)-1;
  $query=substr($query, $dirlength);
  unset($dirlength);
}*/

if ($urldir && $urldir!='/') {
  $query = str_replace($urldir,'/',$query);
  $GLOBALS['urldir']=$urldir;
}

$query=preg_replace('|/+|','/',$query);

if (substr($query,0,7)=='/print/') {
  $_GET['a']='do_print';
  $query=str_replace('/print','',$query);
}

if (substr($query,0,6)=='/post/') {
  $query=str_replace('/post','',$query);
  $postlink=true;
}

 if (substr($query,0,10) != '/index.php' && $query != '/' && substr($query,0,2) != '/?' && $query) {
  if (preg_match('|^/*blogs/(.+?)/?$|i',$query,$matches)) {
    $query=str_replace('/blogs/','/blog_',$query);
  }
  elseif (preg_match('|^/*gallerys/(.+?)/?$|i',$query,$matches)) {
    $query=str_replace('/gallerys/','/gallery_',$query);
  }

  if (preg_match('|^/*user/(.*?)/?$|i',$query,$matches)) {
    $_GET['m']='profile';
    $_GET['a']='view';
    $_GET['u_name']=urldecode($matches[1]);
  }
  elseif (preg_match('|^/*([\w\d-]+)/(\d{1,2}\.\d{1,2}\.\d{4})/$|',$query,$matches)) {
    if (preg_match('|\D|',$matches[1])) $forumurl=$matches[1];
    else $_GET['f']=$matches[1];
    $_GET['vdate']=$matches[2];
  }
  elseif (preg_match('|^/*([^/]+)/?([\w\d]+\.htm)?$|',$query,$matches)) {
    $matches[2]=str_replace('.htm','',$matches[2]);
    if (preg_match('|\D|',$matches[1])) {
      if (substr($matches[1],0,3)=='cat') $_GET['ct']=str_replace('cat','',$matches[1]);
      else $forumurl=$matches[1];
    }
    else $_GET['f']=$matches[1];
    if ($matches[2]) {
      if (strtolower($matches[2])=='f_rules') $_GET['a']='f_rules';
      else $_GET['st']=str_replace('.htm','',str_replace('/','',$matches[2]));
    }
  }
  elseif (preg_match('|^/*([^/]+)/([\w\d\-]+)/?([\w\d]+?\.htm)?$|',$query,$matches)) {
    if (preg_match('|\D|',$matches[1])) $forumurl=$matches[1];
    else $_GET['f']=$matches[1];
    if (preg_match('|\D|',$matches[2]))  $topicurl=$matches[2];
    else $_GET['t']=$matches[2];
    if ($matches[3]) {
      $matches[3]=str_replace('.htm','',str_replace('/','',$matches[3]));
      if (substr($matches[3],0,1)=='p') {
        if ($postlink) $_GET['p']=str_replace('p','',$matches[3]);
        else {
          header('HTTP/1.1 301 Moved permanently');
          header('Location: http://'.$_SERVER['HTTP_HOST'].'/post'.$_SERVER['REQUEST_URI']);
          exit();
        }
      }
      elseif ($matches[3]=='next' || $matches[3]=='last') $step=$matches[3];
      else $_GET['st']=$matches[3];
    }
  }

  else {
    header('HTTP/1.1 404 Not Found');
    echo '<h1>404 The requested URL was not found on this server!</h1>';
    exit();
  }
}
unset($matches);
unset($query);

