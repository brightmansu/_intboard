<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head><?=build_link_tag();?>
<title><?=title_output();?></title><?
$opt_url=$GLOBALS['opt_url'];
if (substr($opt_url,-1,1)!='/') $opt_url.='/'; ?>
<base href="<?=$opt_url;?>">
<meta http-equiv="Content-Type" content="text/html; charset=<?=$GLOBALS['inuser']['ln_charset'];?>">
<link rel="SHORTCUT ICON" href="<?=$opt_url;?>favicon.ico">
<link rel="stylesheet" href="styles/gradblue/gradblue.css" type="text/css">
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

<? if ($GLOBALS['inuser']['pmcount']) $blink="class=\"newpm\"";
else $blink="class=\"menuitem\""; ?>
<ul class="mainmenu">
<? if ($GLOBALS['inuserid']==1) { ?>
<li><a href="index.php?a=rules&amp;m=profile"><?=MSG_a_register;?></a>
<li><a href="index.php?a=login&amp;m=profile"><?=MSG_a_login;?></a>
<li><a href="index.php?a=listusers&amp;m=profile"><?=MSG_a_users;?></a>
<li><a href="index.php?a=online&amp;m=profile"><?=MSG_a_online;?></a>
<li><a href="index.php?m=search"><?=MSG_a_search;?></a>
<li><a href="index.php?m=newpost&amp;a=view_updated"><?=MSG_a_updated;?></a>
<li><a href="index.php?m=filelist"><?=MSG_fl_files;?></a>
<li><a href="feedback.php"><?=MSG_a_feedback;?></a>
<? }
else { ?>
<li><a href="index.php?a=edit&amp;m=profile"><?=MSG_a_profile;?></a>
<li><a href="index.php?a=do_logout&amp;m=profile"><?=MSG_a_logout;?> [<?=$GLOBALS['inuser']['u__name'];?>]</a>
<li><a href="index.php?a=listusers&amp;m=profile"><?=MSG_a_users;?></a>
<li><a href="index.php?a=online&amp;m=profile"><?=MSG_a_online;?></a>
<li><a href="index.php?m=search"><?=MSG_a_search;?></a>
<li><a href="index.php?m=newpost&amp;a=view_updated"><?=MSG_a_updated;?></a>
<li><a href="index.php?m=bookmark"><?=MSG_a_bookmarks;?></a>
<li><a href="index.php?m=subscr"><?=MSG_a_subscribe;?></a>
<li><a href="index.php?m=messages"><?=MSG_a_pm;?>
<span <?=$blink;?>>[<?=str_replace(' ','&nbsp;',format_word($GLOBALS['inuser']['pmcount'],MSG_pmnew1,MSG_pmnew2,MSG_pmnew3));?>]</span></a>
<li><a href="index.php?m=group"><?=MSG_a_groups;?></a>
<li><a href="feedback.php"><?=MSG_a_feedback;?></a>
<li><a href="index.php?m=addrbook"><?=MSG_a_addrbook;?></a>
<li><a href="index.php?m=filelist"><?=MSG_fl_files;?></a>
<li><a href="index.php?m=drafts"><?=MSG_dr_drafts;?></a>
<li><a href="index.php?m=newpost"><?=MSG_a_newposts;?></a>
<? } ?>
</ul><a name="top"></a>
<div class="outertable">
<?announce();?>
<?main_location($GLOBALS['locations']);?>
<?main_action();?>
<?present_list();?>
<?last_topics(5,0);?>
<?active_topics(5,0,7);?>
<?time_diff();?>
</div>
<?require ("config/bottom.txt");?>
<? main_copyright(); ?>
</body></html>