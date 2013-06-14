<? function main_header() {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<title>
<?
$locations=$GLOBALS['locations'];
if ($locations) $locations=array_reverse($locations);
if ($GLOBALS['action']==$GLOBALS['inforum']['tp_library']."_view") {
  for ($i=0; $i<count($locations)-1; $i++) {
    echo strip_tags($locations[$i]);
    if ($i<count($locations)-2) echo " :: ";
  }
}
elseif (!$locations) echo strip_tags($GLOBALS['opt_title']);
else {
  foreach ($locations as $curloc) {
    echo strip_tags($curloc);
    if (next($locations)) echo " :: ";
  }
} ?></title>
<link rel="SHORTCUT ICON" href="favicon.ico">
<base href="<?=$GLOBALS['opt_url'];?>/">
<link rel="stylesheet" href="styles/<?=$GLOBALS['inuser']['st_file'];?>/<?=$GLOBALS['inuser']['st_file'].".css";?>" type="text/css"><?
if ($GLOBALS['rss_link']) { ?>
<link rel="alternate" type="application/rss+xml" title="<?=strip_tags($locations[0]);?>" href="<?=$GLOBALS['rss_link'];?>"><? }
}

function main_body() { ?>
<script type="text/javascript"><!--
function ch_imgs() {
var imgs=document.images;
for (i=0;i<imgs.length;i++) if (imgs[i].name=="itag") {
<? if ($GLOBALS['opt_imglimit_x']) {?>
if (imgs[i].width><?=$GLOBALS['opt_imglimit_x'];?>) { imgs[i].width=<?=$GLOBALS['opt_imglimit_x'];?>; }
<? }
if ($GLOBALS['opt_imglimit_y']) { ?>
if (imgs[i].height><?=$GLOBALS['opt_imglimit_y'];?>) { imgs[i].height=<?=$GLOBALS['opt_imglimit_y'];?>; }
<? } ?>
}
var ilayer=document.getElementById('smiles');
if (ilayer) { ilayer.style.display=''; }
var clayer=document.getElementById('codes');
if (clayer) { clayer.style.display=''; }
}
//--></script></head>
<body onLoad="ch_imgs();">
<? }

function main_top() { ?>
<table align=center class="title"><tr><td><a href="<?=$GLOBALS['opt_url'];?>"><?
if ($GLOBALS['opt_logo']) { ?>
<img src="<?=$GLOBALS['opt_logo'];?>" border=0 alt="<?=htmlspecialchars($GLOBALS['opt_title']." - ".$GLOBALS['opt_descr']);?>">
<? }
else { ?><img src="<?=$GLOBALS['opt_url'];?>/styles/<?=$GLOBALS['inuser']['st_file'];?>/logo.gif" border=0 alt="<?=htmlspecialchars($GLOBALS['opt_title']." - ".$GLOBALS['opt_descr']);?>"><? }
if (!$GLOBALS['opt_logo'] || !$GLOBALS['opt_logo_instead']) { ?>
</a><td width="100%">
<H1><?=$GLOBALS['opt_title'];?></H1>
<H2><?=$GLOBALS['opt_descr'];?></H2>

<? } ?></table>
<? }
function main_menu() {
if ($GLOBALS['inuser']['pmcount']) $blink="class=\"newpm\"";
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
<li><a href="index.php?m=messages"><?=MSG_a_pm;?></a>
<a <?=$blink;?> href="index.php?m=messages&amp;a=viewbox&amp;box=0">[<?=format_word($GLOBALS['inuser']['pmcount'],MSG_pmnew1,MSG_pmnew2,MSG_pmnew3);?>]</a>
<li><a href="index.php?m=group"><?=MSG_a_groups;?></a>
<li><a href="index.php?m=newpost"><?=MSG_a_newposts;?></a>
<li><a href="index.php?m=filelist"><?=MSG_fl_files;?></a>
<? } ?>
</ul>
<? }

function announce_form($id='') {
?><table class="innertable announce" width="100%" cellspacing=1 align=center><tr><td class="tablehead">
<?=MSG_announce;?>
<tr><td><br>
<?=textout($GLOBALS['opt_announcetext'.$id],1,1,1);?><br><br>
</table>
<? }

function main_location($locations) {
  if ($locations) { ?>
<table align=center class="locations" cellspacing=1 cellpadding=6><tr><td><h6>
<? foreach ($locations as $curloc) {
  $curloc=str_replace("<a href=","<a class=inverse href=",$curloc);
  echo $curloc;
  if (next($locations)) echo " &raquo; &nbsp; ";
} ?>
</h6><?
if ($GLOBALS['rss_link']) { ?>
<td width=32><a href="<?=$GLOBALS['rss_link'];?>">
<img border=0 alt="RSS" src="<?=$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/rss.png";?>"></a><? } ?></table>
<? }
}

function main_start() { ?>
<table align=center class="outertable" cellspacing=1 cellpadding=6><tr>
<? }

function action_start() { ?>
<td>
<? }

function main_foreword($text) { ?><br>
<div class="maintext" style="text-align: justify"><?=textout($text,1,1,1);?></div><br>
<? }

function main_statistics($t_total,$p_total,$ucount) { ?>

<table width="100%" border=0><tr class="descr"><td width="50%" valign=bottom>
<?=MSG_main_curtime;?>: <?=long_date_out($GLOBALS['curtime']);?><br>
<? if ($GLOBALS['inuserid']>3) {
  if ($GLOBALS['userlast2']) { ?><?=MSG_main_lastvisit;?>: <?=long_date_out($GLOBALS['userlast2']);?><br><? } ?>
<a href="index.php?m=newpost&amp;a=do_mark_read"><?=MSG_markall;?></a><br>
<a href="index.php?m=newpost&amp;a=view_unanswered"><?=MSG_t_unanswered_show;?></a><br>
<? } ?>
<a class="rules" href="index.php?m=misc&amp;a=view_rules"><?=MSG_forum_rules;?></a>
<td width="50%" style="text-align: right" class="maintext">
<?=$GLOBALS['opt_title'];?> <?=MSG_main_include;?>:<br>
<?=format_word($t_total,MSG_t1,MSG_t2,MSG_t3);?>, <?=format_word($p_total,MSG_p1,MSG_p2,MSG_p3);?>,
<?=format_word($ucount['u_total'],MSG_u1,MSG_u2,MSG_u3);?><br>
<?=MSG_main_lastuser;?>: <?=user_out($ucount['u__name'],$ucount['u_id']);?>
</table>
<br>
<?  }

function main_list_start() { ?>
<table cellspacing=1 class="innertable" width="100%"><tr>
<td colspan=2 width="60%" class="tablehead"><?=MSG_f_title;?>
<td width="10%" class="tablehead"><?=MSG_f_count;?>
<td width="10%" class="tablehead"><?=MSG_t_count;?>
<td width="20%" class="tablehead"><?=MSG_f_lastpost;?>
<? }

function main_category(&$fdata) { ?>
<tr><td colspan=5 class="category">
<a class=inverse href="index.php?ct=<?=$fdata['ct_id'];?>&amp;f=<?=$GLOBALS['forum'];?>"><?=$fdata['ct_name'];?></a>
<? }

function main_list_end() { ?>
</table><br>
<table border=0 width="100%"><tr class="descr"><td width="12%" align=center>
<img src="styles/<?=$GLOBALS['inuser']['st_file'];?>/forum.png" alt=""><td><?=MSG_f_nonew;?>
<tr class="descr"><td align=center><img src="styles/<?=$GLOBALS['inuser']['st_file'];?>/forumnew.png" alt="NEW!">
<td><?=MSG_f_new;?></table>
<? }

function main_present_users($where,$userlist,$users,$usercount,$t_userlist,$t_users,$t_usercount) { ?>
<br>
<table class="innertable" width="100%" cellspacing=1>
<tr><td class="tablehead" style="text-align: left"><?=$GLOBALS['totalmsg'];?> <?=$where;?> <?=$GLOBALS['timemsg'];?>
<tr><td><?=MSG_forum_amongthem;?>: <?=$usercount;?><br>
<?=$userlist;?>
<? if ($GLOBALS['action']=="view" && $GLOBALS['module']=="main" && $GLOBALS['opt_record_date']>0) { ?><br>
<?=MSG_forum_today;?> <?=format_word($t_users,MSG_u1,MSG_u2,MSG_u3,MSG_uv1,MSG_uv2,MSG_uv3);?>,  <?=$t_usercount;?>:<br>
<?=$t_userlist;?><div class="descr">
<?=MSG_forum_record;?> <?=long_date_out($GLOBALS['opt_record_date']);?>.
<?=MSG_forum_record_present;?> <?=$GLOBALS['recordmsg'];?>, <?=MSG_including;?> <?=$GLOBALS['guestmsg'];?>.
</div>
<? } ?>
</table>
<? }

function action_end() {}

function menu_start() { ?>
<td valign=top><table width=160 class="innertable" border=0 cellspacing=1 cellpadding=0>
<? }

function menu_cat_entry(&$ctdata) { ?>
<tr><td class="category"><a class="inverse" href="index.php?ct=<?=$ctdata['ct_id'];?>"><?=$ctdata['ct_name'];?></a>
<? }

function menu_entry(&$fdata) { ?>
<tr><td class="forumentry"><a title="<?=$fdata['f_descr'];?>" href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a>
<? }

function menu_cat_end() {}

function menu_end() { ?>
</table>
<? }

function main_end() { ?>
</table>
<? }

function main_copyright() { ?>
<address class="copyright">
&copy; <?=$GLOBALS['opt_copyright'];?> | <?=MSG_forum_powered;?>
</address>
<? }

function tlist_start($msg,$rsslink="") { ?>
<br>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" width="100%">
<?=$msg;?><? if ($rsslink) { ?><td class="tablehead" width=32><a href="<?=$rsslink;?>">
<img border=0 alt="RSS" src="<?=$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/rss.png";?>"></a><? } ?>
<tr><td colspan=2><? }

function tlist_entry(&$tdata) { ?>
<a href="<?=build_url($tdata);?>"><?=$tdata['t_title'];?></a><br>
<? }

function tlist_end() { ?>
</table>
<? }

function right_menu() {}

function contacts() {}

function small_search_form() { ?>
<form action="index.php" method=POST>
<table border=0><tr><td>&nbsp;<td align=right width="20%" class="descr">
<?=MSG_search;?>: <td width="10%"><input type=text name=text size=20 maxlength=255>
<input type=hidden name=o value="relevancy">
<input type=hidden name=a value="do_post">
<input type=hidden name=res value="post">
<input type=hidden name=m value="search">
<input type=hidden name=fs value="all">
<td width="3%">
<input type=submit value="&gt;&gt;" style="font-size: 10px ">
</table></form>
<? }
