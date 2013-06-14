<? function bmk_list_start() { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%" cellpadding=0><tr>
<td class="tablehead" colspan=7><?=MSG_your_bookmarks;?><tr class="tablehead">
<td colspan=2 width="50%"><?=MSG_t_title;?>
<td width="10%"><?=MSG_t_count;?>
<td width="10%"><?=MSG_t_views;?>
<td width="10%"><?=MSG_t_start;?>
<td width="10%"><?=MSG_t_last;?>
<td width="10%"><?=MSG_delete;?>
<? }

function bmk_forum_entry(&$tdata) { ?>
<tr><td class="tablehead" colspan=7><a href="index.php?f=<?=$tdata['f_id'];?>"><?=$tdata['f_title'];?></a>
<? }

function bmk_list_entry(&$tdata,&$max,$pages) { ?>
<tr style="text-align: center"><td width="5%">&nbsp;
<td style="text-align: left"><h5><a href="<?=build_url($tdata);?>"><?=$tdata['t_title'];?></a></h5>
<br><?=$tdata['t_descr'];?>
<? if ($tdata['t__rate'] && $GLOBALS['inforum']['f_rate']) { ?><?=MSG_rating;?>: <?=printf("%.2f",$tdata['t__ratingsum']/$tdata['t__ratingcount']);?><br>
<? } ?><?=$pages;?>
<td><?=$tdata['t__pcount'];?>
<td><?=$tdata['t__views'];?>
<td><?=user_out($tdata['first_name'],$tdata['first_uid']);?><br><?=long_date_out($tdata['first_time']); ?>
<td><?=user_out($tdata['last_name'],$tdata['last_uid']);?><br><?=long_date_out($tdata['last_time']); ?>
<td><input type=checkbox name="delbmk[<?=$tdata['t_id'];?>]" value=1>
<? }

function bmk_no_entries() { ?>
<tr><td colspan=7 align=center><?=MSG_no_bookmarks;?>
<? }

function bmk_list_end() { ?>
<tr><td class="tablehead" colspan=7>
<input type=hidden name=m value=bookmark><input type=hidden name=a value=delbmk>
<input type=submit value="<?=MSG_delete;?>">
</table></form>
<? }
