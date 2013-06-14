<? function output_message($tmp_message,$tmp_link1,$tmp_link2,$tmp_link3) { ?>
<html><head><title><?=$GLOBALS['opt_title'];?></title>
<link rel="stylesheet" href="<?=$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/".$GLOBALS['inuser']['st_file'];?>.css" type="text/css">
</head>
<body bgcolor="#808080">
<table cellspacing=1 class="innertable" width="100%" align=center><tr>
<td class="tablehead"><?=$tmp_message;?>
<tr>
<td><?=$msg_go_choose;?><ul>
<li><?=$tmp_link1;?><? if ($tmp_link1!=$tmp_link2) { ?><li><?=$tmp_link2;?><? }  if ($tmp_link3) echo "<li>$tmp_link3";?></ul>
</table></body></html>
<? } ?>
