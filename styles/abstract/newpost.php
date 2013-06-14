<? function new_start($time,$text) { ?>
<form class="descr" style="text-align: right" action="index.php" method=GET>
<?=MSG_show_last;?> <? timelimiter($time,"time");?>
<input type=hidden name=m value=newpost><input type=hidden name=a value="<?=$GLOBALS['action'];?>">
<input type=submit value="<?=MSG_show;?>">
</form>
<a class="descr" href="index.php?m=newpost&amp;fs=<?=getvar('fs');?>&amp;a=do_mark_read"><?=MSG_f_marktopics;?></a>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead"><?=$text;?>
<? }

function new_forum(&$newdata) { ?>
<tr><td class="tablehead"><H3><a class="inverse" href="<?=build_url($newdata);?>"><?=$newdata['f_title'];?></a>
<span class="maintext" style="font-weight:normal"><? if ($newdata['lvtime']) { ?><?=MSG_f_lastvisit;?>: <?=long_date_out($newdata['lvtime']);?></span></H3><? }
else { ?><?=MSG_f_notvisited;?><? } ?>

<? }

function new_entry(&$newdata) { ?>
<tr><td><a href="<?=build_url($newdata,'','new','st');?>#new"
<? if ($GLOBALS['opt_hinttext']) { ?>title="<?=clipword(textout($newdata['hint'],$newdata['p__html'],$newdata['p__bcode'],$newdata['p__smile']),$GLOBALS['opt_hinttext']);?>"<? } ?>><?=$newdata['t_title'];?></a>
&mdash; <?=MSG_t_lastposter;?> <?=user_out($newdata['p_uname'],$newdata['p_uid']);?> <?=long_date_out($newdata['p__time']);?>
<? }

function new_noentries() { ?>
<tr><td style="text-align: center"><?=MSG_nonewposts;?>
<? }

function new_end() { ?>
</table>
<? }

function upd_start($time,$pages) { ?>
<form class="descr" style="text-align: right" action="index.php" method=GET>
<?=MSG_show_last;?> <? timelimiter($time,"time");?>
<input type=hidden name=m value=newpost><input type=hidden name=a value=view_updated>
<input type=submit value="<?=MSG_show;?>">
</form>
<table class="innertable" width="100%" cellspacing=1>
<tr class="tablehead">
<td colspan=2 width="45%"><?=MSG_t_title;?>
<td width="20%"><?=MSG_f_title;?>
<td width="5%"><?=MSG_t_count;?>/<?=MSG_t_views;?>
<td width="15%"><?=MSG_t_start;?>
<td width="15%"><?=MSG_t_last;?>
<? if ($pages) { ?>
<tr><td colspan=6 style="text-align: right"><?=$pages;?>
<? }
}

function upd_entry($tdata,$tpages) {
  $fdata['f_id']=$tdata['f_id'];
  $fdata['f_url']=$tdata['f_url'];
  ?>
<tr class="topicentry" style="text-align: center"><td width="5%">
<? $dir="styles/".$GLOBALS['inuser']['st_file'];
if (is_new($tdata['visited'],$tdata['lp__time'],$tdata['lv_markall'])) {
  if ($tdata['t__status'] && $tdata['tl_count']>=$GLOBALS['opt_hot']) { $pic="$dir/closedhot.png"; $alt="CLOSED!"; }
  elseif ($tdata['t__status'] && $tdata['tl_count']<$GLOBALS['opt_hot']) { $pic="$dir/closednew.png"; $alt="NEW!"; }
  elseif ($tdata['tl_count']>=$GLOBALS['opt_hot']) { $pic="$dir/hot.png"; $alt="HOT!"; }
  else { $pic="$dir/new.png"; $alt="NEW!"; }
}
else {
  if ($tdata['t__status']) $pic="$dir/closed.png";
  else $pic="$dir/nonew.png";
};?><img src="<?=$pic;?>" height=20 width=20 alt="<?=$alt;?>">
<? if ($tdata['t__sticky']) { ?><img src="<?=$dir."/sticky.png";?>" height=20 width=20 alt=""> <? }
if ($tdata['pl_tid']) { ?><img src="<?=$dir."/vote.png";?>" height=20 width=20 alt="<?=$alt;?>"><? }
?>
<td style="text-align: left"><h5><a href="<?=build_url($tdata);?>"
<? if ($GLOBALS['opt_hinttext']) { ?>title="<?=clipword(textout($tdata['hint'],$tdata['p__html'],$tdata['p__bcode'],$tdata['p__smile']),$GLOBALS['opt_hinttext']);?>"<? } ?>><?=$tdata['t_title'];?></a> <?
if ($tpages) { ?><span class="descr"><?=$tpages;?>
<? if (is_new($tdata['visited'],$tdata['lp__time']) && $tpages) { ?><a href="<?=build_url($tdata,'','new','st');?>">NEW!</a><? } ?></span><? } ?></h5>
<?=$tdata['t_descr'];?>
<td><a href="<?=build_url($fdata);?>"><?=$tdata['f_title'];?></a>
<td><?=intval($tdata['tl_count']);?>/<?=intval($tdata['t__views']);?>
<td><?=user_out($tdata['p_uname'],$tdata['p_uid']);?><br>
<? if ($GLOBALS['inuser']['u_sortposts']==0 || $tdata['t__pcount']<=$GLOBALS['inuser']['u_mperpage']) {?>
<a href="<?=build_url($tdata,'','0','st');?>">&raquo;</a>
<? } else { ?><a href="<?=build_url($tdata,'',intval($tdata['t__pcount']-$GLOBALS['inuser']['u_mperpage']+1),'st');?>">&raquo;</a><? } ?>

<?=long_date_out($tdata['fp__time']); ?>
<td><?=user_out($tdata['lp_uname'],$tdata['lp_uid']);?><br>
<? if ($GLOBALS['inuser']['u_sortposts']==0) { ?>
<a href="<?=build_url($tdata);?>#last">&raquo;</a>
<? } else { ?><a href="<?=build_url($tdata,'','0','st');?>">&raquo;</a>
<? } ?><?=long_date_out($tdata['lp__time']); ?>
<? }

function upd_noentries() { ?>
<tr><td colspan=6 style="text-align:center"><?=MSG_t_noupdtopics;?>
<? }

function upd_end($pages) {
$path="styles/".$GLOBALS['inuser']['st_file'];
if ($pages) { ?>
<tr><td colspan=6 style="text-align: right"><?=$pages;?>
<? } ?>
</table><br>
<table width="100%" border=0 cellspacing=4>
<tr class="descr"><td width="5%"><img src="<?=$path;?>/nonew.png" alt=""><td width="45%"><?=MSG_t_nonew;?>
<td width="5%"><img src="<?=$path;?>/closed.png" alt=""><td width="45%"><?=MSG_t_closed;?>
<tr class="descr"><td><img src="<?=$path;?>/new.png" alt=""><td><?=MSG_t_new;?>
<td><img src="<?=$path;?>/closednew.png" alt=""><td><?=MSG_t_closednew;?>
<tr class="descr"><td><img src="<?=$path;?>/hot.png" alt=""><td><?=MSG_t_hot;?>
<td><img src="<?=$path;?>/closedhot.png" alt=""><td><?=MSG_t_closedhot;?>
<tr class="descr"><td><img src="<?=$path;?>/vote.png" alt=""><td><?=MSG_t_vote;?>
<td><img src="<?=$path;?>/sticky.png" alt=""><td><?=MSG_t_sticky;?>
</table>
<? }
