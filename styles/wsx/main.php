<? function main_header() {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>
<?
$locations=$GLOBALS['locations'];
if ($locations) $locations=array_reverse($locations);
if ($GLOBALS['action']==$GLOBALS['inforum']['tp_library']."_view") {
  for ($i=0; $i<count($locations)-1; $i++) {
    echo strip_tags($locations[$i]);
    if ($i<count($locations)-2) echo " :: ";
  }
}
elseif (!$locations) echo $GLOBALS['opt_title'];
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
<table align=center class="title" width="100%"><tr><td><a href="<?=$GLOBALS['opt_url'];?>"><?
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

function main_menu() { ?>
<? }

function announce_form() {
?><table class="innertable announce" width="100%" cellspacing=1 align=center><tr><td class="tablehead">
<?=MSG_announce;?>
<tr><td>
<?=textout($GLOBALS['opt_announcetext'],1,1,1);?><br>
</table>
<? }

function main_location($locations) {
  if ($locations) { ?>
<table align=center class="locations" cellspacing=1 cellpadding=6><tr><td><h6>
<? foreach ($locations as $curloc) {
//  $curloc=str_replace("<a href=","<a href=",$curloc);
  echo $curloc;
  if (next($locations)) echo " &raquo; &nbsp; ";
} ?>
</h6><?
if ($GLOBALS['rss_link']) { ?>
<td width=32><a href="<?=$GLOBALS['rss_link'];?>"><img border=0 alt="RSS" src="<?=$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/rss.png";?>"></a><? } ?></table>
<? }
}

function main_start() { ?>
<TABLE border=0 cellpadding=0 cellspacing=0 width="100%"><tr>
<td class="leftcolumn">
<? }

function action_start() { ?>
<td valign=top>
<TABLE width="100%" border="0" cellspacing="10"><tr><td>
<? }

function main_foreword($text) { ?><br>
<div class="maintext" style="text-align: justify"><?=textout($text,1,1,1);?></div><br>
<? }

function main_statistics($t_total,$p_total,$ucount) { ?>
<table width="100%" border=0><tr><td width="50%" valign=bottom class="descr">
<?=MSG_main_curtime;?>: <?=long_date_out($GLOBALS['curtime']);?><br>
<? if ($GLOBALS['inuserid']>3) {
  if ($GLOBALS['userlast2']) { ?><?=MSG_main_lastvisit;?>: <?=long_date_out($GLOBALS['userlast2']);?><br><? } ?>
<a href="index.php?m=newpost&amp;a=do_mark_read"><?=MSG_markall;?></a><? }
?><td width="50%" style="text-align: right" class="maintext">
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
<table border=0 width=150 cellspacing=0 cellpadding=0>
<TR><TD class="menucat">
<a style="color: #003366; text-decoration: none" href="index.php?m=profile&amp;a=online"><?=MSG_a_online;?></a>

<TR><TD class="mf">
<TABLE cellspacing=0 cellpadding=0 border=0 width="100%">
<TR><TD width="8">
<TD class="menuentry"><div class="descr">
<?=MSG_forum_now;?> <?=$where;?>:<br>
<?=$userlist?><br>
<?=MSG_forum_also;?>: <?=$usercount;?>.<br>
<? if ($GLOBALS['action']=="view" && $GLOBALS['module']=="main" && $GLOBALS['opt_record_date']>0) { ?><br>
<?=MSG_forum_record;?> <?=long_date_out($GLOBALS['opt_record_date']);?>. <br>
<?=MSG_forum_record_present;?> <?=$GLOBALS['recordmsg'];?>, <?=MSG_including;?> <?=$GLOBALS['guestmsg'];?>.<br><br>
<?=MSG_forum_today;?> <?=format_word($t_users,MSG_u1,MSG_u2,MSG_u3,MSG_uv1,MSG_uv2,MSG_uv3);?>:<br>
<?=$t_userlist;?><br>
<?=MSG_forum_also;?> <?=$t_usercount;?>.
<? } ?></div>
<td width=8> </table>
<tr><td class=mn>
</table>
<? }

function right_login_form() { ?>
<form action="index.php" method=POST>
<table width=150 cellspacing=0 cellpadding=0>
<TR><TD class="menucat"><?=MSG_login;?>

<TR><TD class="mf">
<TABLE cellspacing=0 cellpadding=0 border=0 width=150>
<TR class="maintext"><TD width="12">;
<td>
<?=MSG_userlogin;?>:<td><input type=text name=inusername size=10 maxlength=32>
<td width=8>
<TR class="maintext"><TD width="8">&nbsp;
<td><?=MSG_password;?>:<td><input type=password name=inpassword size=10 maxlength=32>
<td width=8>
<TR><TD width="8"><td colspan=2 style="text-align: center" class="maintext">
<div class="descr" style="text-align: left">
<label><input type=radio name=login_mode value=0 checked><?=MSG_login_normal;?></label><br>
<label><input type=radio name=login_mode value=1><?=MSG_login_secure;?></label><br>
<label><input type=radio name=login_mode value=2><?=MSG_login_keep;?></label><br>
</div><br>
<input type=submit value="<?=MSG_dologin;?>"><br><br>
<input type=hidden name=m value=profile><input type=hidden name=a value="do_login">
<a href="index.php?m=profile&amp;a=rules"><?=MSG_register;?></a><br>
<a href="index.php?m=profile&amp;a=password"><?=MSG_input_forgot;?>?</a>
<td width=8></table>
<tr><TD class=mn>
</table></form>
<? }

function action_end() { ?>
</table>
<? }

function menu_start() { ?>
<td valign=top width=152 bgcolor=#003399>
<TABLE width=150 border=0 cellspacing=0 cellpadding=0>
<? }

function menu_cat_entry(&$ctdata) { ?>
<TR><TD class="menucat">
<a style="color: #003366; text-decoration: none" href="index.php?ct=<?=$ctdata['ct_id'];?>"><?=substr($ctdata['ct_name'],0,18);?></a>
<TR><TD class="mf">
<TABLE cellspacing=0 cellpadding=0 border=0 width="100%">
<TR><TD width="8">
<TD class="menuentry">
<? }

function menu_entry(&$fdata) { ?>
<IMG src="/styles/<?=$GLOBALS['inuser']['st_file'];?>/1.gif" width="7" height="5" alt=""> <a title="<?=$fdata['f_descr'];?>" href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a><br>
<? }

function menu_cat_end() { ?>
<td width=6> </table>
<tr><td class="mn">
<? }

function menu_end() { ?>
</table>
<? }

function right_menu() {
if ($GLOBALS['inuser']['pmcount']) $blink="class=\"newpm\"";
else $blink="class=\"menuitem\"";
if ($GLOBALS['inuserid']<3) right_login_form();
else {?>
<table width=150 cellspacing=0 cellpadding=0>
<TR><TD class="menucat"><?=MSG_u_greetings;?>, <?=$GLOBALS['inuser']['u__name'];?>!

<TR><TD class="mf">
<TABLE cellspacing=0 cellpadding=0 border=0 width=150>
<TR><TD width="8">&nbsp;
<TD class="menuentry">
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?a=view_rules&amp;m=misc"><?=MSG_forum_rules;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=newpost"><?=MSG_shownewposts;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=newpost&amp;a=view_unanswered"><?=MSG_t_unanswered;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=messages"><?=MSG_a_pm;?>
<span <?=$blink;?>>[<?=format_word($GLOBALS['inuser']['pmcount'],MSG_pmnew1,MSG_pmnew2,MSG_pmnew3);?>]</span></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?a=edit&amp;m=profile"><?=MSG_a_profile;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=search"><?=MSG_search;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=newpost&amp;a=view_updated"><?=MSG_a_updated;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=bookmark"><?=MSG_a_bookmarks;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=subscr"><?=MSG_a_subscribe;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?a=listusers&amp;m=profile"><?=MSG_a_users;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=group"><?=MSG_a_groups;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="feedback.php"><?=MSG_a_feedback;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=addrbook"><?=MSG_a_addrbook;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=filelist"><?=MSG_fl_files;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?m=drafts"><?=MSG_dr_drafts;?></a><br>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="index.php?a=do_logout&amp;m=profile"><?=MSG_a_logout;?></a>
<td width=8></table>
<tr><TD class=mn>
</table>
<? }
}

function main_end() { ?>
</table><!--</table>-->
<? }

function main_copyright() { ?>
<address class="copyright">
&copy; <?=$GLOBALS['opt_copyright'];?><br>
<?=MSG_forum_powered;?> <?=sprintf("%.2f",($GLOBALS['opt_ibversion']/100));?>, &copy; <?=MSG_forum_copyright;?> <br>
</address>
<? }

function tlist_start($msg) { ?>
<table width=150 cellspacing=0 cellpadding=0>
<TR><TD class="menucat"><?=substr($msg,0,18);?>
<TR><TD class="mf">
<TABLE cellspacing=0 cellpadding=0 border=0 width=150>
<TR><TD width="8">&nbsp;
<TD class="menuentry">
<? }

function tlist_entry(&$tdata) { ?>
<IMG src="images/1.gif" width="7" height="5" alt=""> <a href="<?=build_url($tdata);?>"><?=$tdata['t_title'];?></a><br>
<? }

function tlist_end($rsslink="") { ?>
<? if ($rsslink) { ?><a href="<?=$rsslink;?>"><img border=0 alt="RSS" src="<?=$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/rss.png";?>"></a><? } ?>
<td width=8></table>
<tr><TD class="mn">
</table>
<? }

function small_search_form() { ?>
<form action="index.php" method=POST>
<table width=150 cellspacing=0 cellpadding=0 border=0>
<TR><TD class="menucat"><?=MSG_search?>
<TR><TD class="mf">
<TABLE cellspacing=0 cellpadding=0 border=0 width="100%">
<TR><TD width="8">
<TD class="menuentry">
<table width="100%"><tr><td>
<input type=hidden name=o value="relevancy">
<input type=hidden name=a value="do_post">
<input type=hidden name=res value="post">
<input type=hidden name=m value="search">
<input type=hidden name=fs value="all">
<input type=text name=text style="width: 100%" maxlength=255>
<td><input type=submit value="&gt;&gt;" style="font-size: 10px; "></table>
<td width=8></table>

<tr><TD class=mn>
</table></form>
<? }


function news_feed_start($msg,$rsslink) { ?>
<br>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" width="100%">
<?=$msg;?><td class="tablehead" width=32><a href="<?=$rsslink;?>">
<img border=0 alt="RSS" src="<?=$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/rss.png";?>"></a>
<tr><td colspan=2><? }

function news_feed_entry(&$tdata) { ?>
<?=short_date_out($tdata['p__time']);?> &mdash; <a href="<?=build_url($tdata);?>"><?=$tdata['t_title'];?></a><div class="descr"><?=$tdata['t_descr'];?></div>
<? }

function news_feed_noentries(&$tdata) { ?>
<?=MSG_n_nonews;?>
<? }

function news_feed_end() { ?>
</table>
<? }
