<? function ad_statistics($stats,$lastpost,$dbsize,$users,$bots) { ?>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_stats_forum;?>
<tr><td colspan=2 align=center>
<b><?=MSG_stats_common;?></b>
<tr><td width="50%">
<?=MSG_stats_title;?>:
<td>
<?=$GLOBALS['opt_title'];?>
<tr><td>
<?=MSG_stats_url;?>:
<td>
<?=$GLOBALS['opt_url'];?>
<tr><td>
<?=MSG_stats_dir;?>:
<td>
<?=$GLOBALS['opt_dir'];?>
<tr><td>
<?=MSG_stats_server;?>:
<td>
<?=$_SERVER['SERVER_SOFTWARE'];?>
<tr><td>
<?=MSG_stats_user;?>:
<td>
<? eval('echo get_current_user();');?> (<? eval('echo getmyuid();');?>)
<tr><td>
<?=MSG_backup_last;?>:
<td>
<? if ($GLOBALS['opt_last_backup']<(time()-7*24*60*60)) { ?>
<span style="color: #F00000"><?
  if ($GLOBALS['opt_last_backup']) { ?>
<?=long_date_out($GLOBALS['opt_last_backup']);?>
<? } else { ?><?=MSG_none;?><? } ?>
&nbsp; (<a href="admin/index.php?m=basic&a=backup"><?=MSG_backup_now;?></a>)
<? }
else { ?><?=long_date_out($GLOBALS['opt_last_backup']);?>
<? } ?>
<tr><td>
<?=MSG_stats_date;?>:
<td>
<?=long_date_out(time());?>
<tr><td colspan=2 align=center>
<b><?=MSG_stats_qualities;?></b>
<tr><td>
<?=MSG_stats_posts;?>:
<td>
<?=$stats['p_count'];?> (<?=$stats['p_nostats'];?>)
<tr><td>
<?=MSG_stats_premod;?>:
<td>
<?=$stats['premodcount'];?>
<tr><td>
<?=MSG_stats_topics;?>:
<td>
<?=$stats['t_count'];?> (<?=$stats['t_nostats'];?>)
<tr><td>
<?=MSG_stats_users;?>:
<td>
<?=$stats['u_count'];?> 
<tr><td>
<?=MSG_stats_dbsize;?>:
<td>
<?=ceil($dbsize/1024);?> Kb
<tr><td>
<?=MSG_stats_attachments;?>:
<td>
<?=intval($stats['files']);?> (<?=ceil($stats['size']/1024);?> Kb)
<tr><td colspan=2 align=center>
<b><?=MSG_stats_misc;?></b>
<tr><td>
<?=MSG_stats_admins;?>:
<td><?=$users;?>
<tr><td>
<?=MSG_stats_ppert;?>:
<td>
<?=$stats['p_per_t'];?>
<tr><td>
<?=MSG_stats_pperu;?>:
<td>
<?=$stats['p_per_u'];?>
<tr><td>
<?=MSG_stats_tperu;?>:
<td>
<?=$stats['t_per_u'];?>
<tr><td>
<?=MSG_stats_lastpost;?>:
<td>
<?=$lastpost['p_uname'].", \"".$lastpost['t_title']."\" ".long_date_out($lastpost['p__time'])."";?>
<tr><td>
<?=MSG_stats_lastuser;?>:
<td>
<?=$stats['u__name'];?> <?=MSG_u_regged;?> <?=long_date_out($stats['u__regdate'])."";?>
<tr><td>
<?=MSG_stats_dayly;?>:
<td>
<?=$stats['dayly'];?>
<tr><td>
<?=MSG_stats_weekly;?>:
<td>
<?=$stats['weekly'];?>
<tr><td colspan=2 align=center>
<b><?=MSG_stats_search;?></b>
<? foreach ($bots as $curbot) { ?>
  <tr><td><?=$curbot[0];?><td><? if ($curbot) { ?><?=long_date_out($curbot[2]);?><? }
  else { ?><?=MSG_none;?><? } 
 } ?>
</table>
<? }

function stats_form($starttime,$endtime) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_stats_stats;?>
<tr><td width="40%"><?=MSG_stats_showfrom;?>:<td><?=build_date_field("start",$starttime);?>
<tr><td><?=MSG_stats_till;?>:<td><?=build_date_field("end",$endtime);?>
<tr><td><?=MSG_stats_show;?>:<td>
<label><input type=checkbox name=hits value=1 <?=check(getvar('hits'));?>><?=MSG_stats_hits;?></label><br>
<label><input type=checkbox name=truehits value=1 <?=check(getvar('truehits'));?>><?=MSG_stats_truehits;?></label><br>
<label><input type=checkbox name=sessions value=1 <?=check(getvar('sessions'));?>><?=MSG_stats_sessions;?></label><br>
<label><input type=checkbox name=hosts value=1 <?=check(getvar('hosts'));?>><?=MSG_stats_hosts;?></label><br>
<label><input type=checkbox name=users value=1 <?=check(getvar('users'));?>><?=MSG_stats_users;?></label><br>
<label><input type=checkbox name=topics value=1 <?=check(getvar('topics'));?>><?=MSG_stats_topics;?></label><br>
<label><input type=checkbox name=posts value=1 <?=check(getvar('posts'));?>><?=MSG_stats_posts;?></label><br>
<label><input type=checkbox name=searches value=1 <?=check(getvar('searches'));?>><?=MSG_stats_searches;?></label><br>
<label><input type=checkbox name=mainpage value=1 <?=check(getvar('mainpage'));?>><?=MSG_stats_mainpages;?></label><br>
<label><input type=checkbox name=fviews value=1 <?=check(getvar('fviews'));?>><?=MSG_stats_forumviews;?></label><br>
<label><input type=checkbox name=tviews value=1 <?=check(getvar('tviews'));?>><?=MSG_stats_topicviews;?></label><br>
<label><input type=checkbox name=hps value=1 <?=check(getvar('hps'));?>><?=MSG_stats_hpers;?></label><br>
<label><input type=checkbox name=spu value=1 <?=check(getvar('spu'));?>><?=MSG_stats_hperu;?></label><br>
<input type=checkbox name=hpu value=1 <?=check(getvar('hpu'));?>><?=MSG_stats_speru;?>
<tr><td><?=MSG_stats_group;?>:<td>
<label><input type=radio name=period value=all <?=check(getvar('period')=='all');?>><?=MSG_stats_alltime;?></label><br>
<label><input type=radio name=period value=days <?=check(getvar('period')=='days');?>><?=MSG_stats_byday;?></label><br>
<label><input type=radio name=period value=wdays <?=check(getvar('period')=='wdays');?>><?=MSG_stats_byweek;?></label><br>
<input type=radio name=period value=hours <?=check(getvar('period')=='hours');?>><?=MSG_stats_byhour;?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=stats><input type=hidden name=a value=show_stats>
<input type=submit value="<?=MSG_show;?>">
</table></form>
<? }

function stat_start($count) { ?>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=<?=$count+1;?>>
<?=MSG_stats_from;?> <?=long_date_out(get_date_field("start"));?> <?=MSG_stats_till2;?> <?=long_date_out(get_date_field("end"));?>
</table>
<table class="innertable" cellspacing=1 width="100%">
<tr><td class="tablehead" width="25%"><?=MSG_stat_period;?>
<? if (getvar("hits")) { ?><td class="tablehead"><?=MSG_stat_hits;?><? }
if (getvar("truehits")) { ?><td class="tablehead"><?=MSG_stat_truehits;?><? }
if (getvar("sessions")) { ?><td class="tablehead"><?=MSG_stat_sessions;?><? }
if (getvar("hosts")) { ?><td class="tablehead"><?=MSG_stat_hosts;?><? }
if (getvar("users")) { ?><td class="tablehead"><?=MSG_stat_users;?><? }
if (getvar("topics")) { ?><td class="tablehead"><?=MSG_stat_topics;?><? }
if (getvar("posts")) { ?><td class="tablehead"><?=MSG_stat_posts;?><? }
if (getvar("searches")) { ?><td class="tablehead"><?=MSG_stat_searches;?><? }
if (getvar("mainpage")) { ?><td class="tablehead"><?=MSG_stat_mainpage;?><? }
if (getvar("fviews")) { ?><td class="tablehead"><?=MSG_stat_forumviews;?><? }
if (getvar("tviews")) { ?><td class="tablehead"><?=MSG_stat_topicviews;?><? }
if (getvar("hps")) { ?><td class="tablehead"><?=MSG_stat_hpers;?><? }
if (getvar("spu")) { ?><td class="tablehead"><?=MSG_stat_speru;?><? }
if (getvar("hpu")) { ?><td class="tablehead"><?=MSG_stat_hperu;?><? } ?>
<? }

function stat_entry($curout,$curnumber,&$hits,&$truehits,&$sessions,&$hosts,&$users,&$topics,&$posts,&$searches,&$mainpage,&$fviews,&$tviews,&$hps,&$spu,&$hpu) { ?>
<tr style="text-align: center"><td><?=$curout;?>
<? if (getvar("hits")) { ?><td><?=$hits[$curnumber];?><? }
if (getvar("truehits")) { ?><td><?=$truehits[$curnumber];?><? }
if (getvar("sessions")) { ?><td><?=$sessions[$curnumber];?><? }
if (getvar("hosts")) { ?><td><?=$hosts[$curnumber];?><? }
if (getvar("users")) { ?><td><?=$users[$curnumber];?><? }
if (getvar("topics")) { ?><td><?=$topics[$curnumber];?><? }
if (getvar("posts")) { ?><td><?=$posts[$curnumber];?><? }
if (getvar("searches")) { ?><td><?=$searches[$curnumber];?><? }
if (getvar("mainpage")) { ?><td><?=$mainpage[$curnumber];?><? }
if (getvar("fviews")) { ?><td><?=$fviews[$curnumber];?><? }
if (getvar("tviews")) { ?><td><?=$tviews[$curnumber];?><? }
if (getvar("hps")) { ?><td><?=$hps[$curnumber];?><? }
if (getvar("spu")) { ?><td><?=$spu[$curnumber];?><? }
if (getvar("hpu")) { ?><td><?=$hpu[$curnumber];?><? } ?>
<? }

function stat_end() { ?>
</table><br>
<? }

function stat_browser_start() { ?>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=3>
<?=MSG_stats_perbrowser;?>
<tr><td class="tablehead" width="50%"><?=MSG_stats_browser;?><td class="tablehead" width="25%"><?=MSG_stats_percent;?>
<td class="tablehead"><?=MSG_stats_percent_active;?>
<? }

function stat_browser_entry($browser,$count1,$count2,$sum1,$sum2) { ?>
<tr><td><?=$browser;?><td><?=floor($count2/$sum2*100);?>% (<?=$count2;?>/<?=$sum2;?>)
<td><?=floor($count1/$sum1*100);?>% (<?=$count1;?>/<?=$sum1;?>)
<? }

function stat_browser_end() { ?>
</table>
<? }

function stat_select_form($startdate,$enddate,$mode) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_stats_views;?>
<tr><td width="40%"><?=MSG_stats_showfrom;?>:<td><?=build_time_field("start",$startdate);?>
<tr><td><?=MSG_stats_till;?>:<td><?=build_time_field("end",$enddate);?>
<tr><td><?=MSG_stats_show;?>:<td>
<label><input type=radio name=mode value="forum_only" <?=check($mode=="forum_only");?>><?=MSG_stats_forum;?></label><br>
<label><input type=radio name=mode value="forum" <?=check($mode=="forum");?>><?=MSG_stats_forumtopic;?></label><br>
<input type=radio name=mode value="topic" <?=check($mode=="topic");?>><?=MSG_stats_topic;?>
<tr><td><?=MSG_stats_perpage;?>:<td>
<input type=text name=perpage size=3 value="<?=getvar('perpage');?>">
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=stats>
<input type=hidden name=a value=stat_process><input type=submit value="<?=MSG_show;?>">
</table></form><br>
<? }

function stat_process_start($pages) { ?>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_stats_views;?>
<tr><td class="tablehead" colspan=2><?=$pages;?>
<? }

function stat_process_entry($lnk,$msg,$count) { ?>
<tr><td width="80%"><a href="<?=$lnk;?>"><?=$msg;?></a>
<td align=center><?=$count;?>
<? }

function stat_process_end($pages) { ?>
<tr><td class="tablehead" colspan=2><?=$pages;?>
</table><br>
<? }

function stat_detail_form($starttime,$endtime) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_stats_detailed;?>
<tr><td width="50%"><?=MSG_stats_showfrom;?>:<td><?=build_time_field("start",$starttime);?>
<tr><td><?=MSG_stats_till;?>:<td><?=build_time_field("end",$endtime);?>
<tr><td><?=MSG_stats_perpage;?>:<td>
<input type=text name=perpage size=3 value="<?=getvar('perpage');?>">
<tr><td class="tablehead" colspan=2><?=MSG_stats_filter;?>
<tr><td><?=MSG_stats_username;?>:<td><input type=text name=unames size=30 maxlength=32 value="<?=getvar('unames');?>">
<tr><td><?=MSG_stats_usermode;?>:<td>
<input type=radio name=umode value=0 <?=check(getvar('umode')==0);?>><?=MSG_stats_fullmatch;?> 
<input type=radio name=umode value=1 <?=check(getvar('umode')==1);?>><?=MSG_stats_starts;?> 
<input type=radio name=umode value=2 <?=check(getvar('umode')==2);?>><?=MSG_stats_contains;?> 
<tr><td><?=MSG_stats_forumnums;?>:<td><input type=text name=flist size=40 value="<?=getvar('flist');?>">
<tr><td><?=MSG_stats_topicnums;?>:<td><input type=text name=tlist size=40 value="<?=getvar('tlist');?>">
<tr><td><?=MSG_stats_ips;?>:<td><input type=text name=ipstart size=15 maxlength=15 value="<?=getvar('ipstart');?>"> &mdash; 
<input type=text name=ipend size=15 maxlength=15 value="<?=getvar('ipend');?>">
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=stats><input type=hidden name=a value=stat_show_detail>
<input type=submit value="<?=MSG_show;?>">
</table></form>
<? }

function stat_detail_start($starttime,$endtime,$pages) { ?>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_stats_detailedfrom;?> <?=short_date_out($starttime);?> <?=MSG_stats_till;?> <?=short_date_out($endtime);?>
<tr><td class="tablehead" colspan=2><?=$pages;?>
<? }

function stat_detail_entry($uid,$uname,$ip,$time,$comment,$referer,$agent) { ?>
<tr><td width="30%"><b><?=user_out($uname,$uid);?></b><br><a target="_blank" href="https://www.nic.ru/whois/?ip=<?=$ip;?>"><?=$ip;?></a><br><?=long_date_out($time);?>
<td align=center><?=$comment;?><br>
<div style="font-size: 70%; padding-top: 4px; overflow: auto; color: grey"><? if ($referer) { ?><b>REFERER:</b> <?=urldecode($referer);?><br><? } ?>
<?=$agent;?></div>
<? }

function stat_detail_end($pages) { ?>
<tr><td class="tablehead" colspan=2><?=$pages;?>
</table><br>
<? }
