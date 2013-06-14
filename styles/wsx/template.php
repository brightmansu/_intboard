<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head><?=build_link_tag();?>
<title><?=title_output(1);?></title>
<link rel="SHORTCUT ICON" href="<?=$opt_url;?>favicon.ico"><?
$opt_url=$GLOBALS['opt_url'];
if (substr($opt_url,-1,1)!='/') $opt_url.='/'; ?>
<base href="<?=$opt_url;?>">
<meta http-equiv="Content-Type" content="text/html; charset=<?=$GLOBALS['inuser']['ln_charset'];?>">
<link rel="stylesheet" href="styles/wsx/wsx.css" type="text/css">
<?require ("config/head.txt");?>
<script type="text/javascript" src="styles/common.js" defer="defer"></script></head>
<body onLoad="ch_imgs();" onKeyDown="nav_keys(event);">
<?require ("config/top.txt");?>

<? if ($GLOBALS['opt_logo']) $logo=$GLOBALS['opt_logo'];
else $logo='styles/'.$GLOBALS['inuser']['st_file'].'/logo.gif';?>
<table align=center class="title" width="100%"><tr><td><a href="./#">
<img src="<?=$logo;?>" border=0 alt="<?=htmlspecialchars(title_output(1));?>"><?
if (!$GLOBALS['opt_logo'] || !$GLOBALS['opt_logo_instead']) { ?>
</a><td width="100%">
<H1><?=$GLOBALS['opt_title'];?></H1>
<H2><?=$GLOBALS['opt_descr'];?></H2>
<? } ?></table>

<TABLE border=0 cellpadding=0 cellspacing=0 width="100%"><tr>
<td class="leftcolumn">
<?small_search_form();?>
<? if ($GLOBALS['inuserid']<=3) right_login_form();
else right_menu();?>
<?present_list();?>
<?last_topics(5,0);?>
<?active_topics(5,0,7);?>
<td valign=top>
<TABLE width="100%" border="0" cellspacing="10"><tr><td>
<?announce();?>
<?main_location($GLOBALS['locations']);?>
<?main_action();?>
</table>
<?time_diff();?>
</table>
<?require ("config/bottom.txt");?>
<?main_copyright();?>
</body></html>