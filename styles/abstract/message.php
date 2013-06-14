<? function output_message($tmp_message,$tmp_link1,$tmp_link2,$tmp_link3,$newlink="") { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title><?=$GLOBALS['opt_title'];?></title>
<? $opt_url=$GLOBALS['opt_url'];
if (substr($opt_url,-1,1)!='/') $opt_url.='/'; ?>
<base href="<?=$opt_url;?>">
<meta http-equiv="Content-Type" content="text/html; charset=<?=$GLOBALS['inuser']['ln_charset'];?>">
<link rel="stylesheet" href="<?=$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/".$GLOBALS['inuser']['st_file'];?>.css" type="text/css">
<? if ($newlink) { ?><meta http-equiv="refresh" content="1; url=<?=$newlink;?>"><? } ?>
</head>
<body style="padding: 0; margin: 0; height:auto !important;  height:100%;  min-height:100%;">
<table class="innertable" style="margin-top: 21%; margin-left: 20%; height: 20%; width: 60%; "><tr>
<td class="tablehead"><?=$tmp_message;?>
<tr>
<td><?=$msg_go_choose;?><ul>
<li><?=$tmp_link1;?></li><li><?=$tmp_link2;?><? if ($tmp_link3) echo "</li><li>$tmp_link3";?></li></ul>
</table></body></html>
<? } ?>
