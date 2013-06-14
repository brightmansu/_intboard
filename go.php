<? $IBOARD=1;
require("config/iboard.php");
$_SERVER['QUERY_STRING'] = isset($_SERVER['QUERY_STRING'])?trim($_SERVER['QUERY_STRING']):"";
if(preg_match("/^(http|https|ftp):\/\/[a-z0-9\/:_\-_\.\?\$,;~=#&%()\+]+$/i", $_SERVER['QUERY_STRING'])) {
    $location = $_SERVER['QUERY_STRING'];    
} else {
    $location = $opt_url;
}
header('Location: '.$location, TRUE, 302);	
die() or exit();
?>