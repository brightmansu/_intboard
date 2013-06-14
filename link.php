<?
header('HTTP/1.1 301 Moved Permanently');
header('Location: '.$GLOBALS['inforum']['f_url']);
db_close($link);
exit();

