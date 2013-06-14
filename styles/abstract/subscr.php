<? function subscr_forum_select($fselect) { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr class="tablehead"><td colspan=2>
<?=MSG_subscr_select;?>
<tr><td width="50%"><?=MSG_subscr_forum;?>:
<td><select name="f"><?=$fselect;?></select>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=subscr><input type=hidden name=a value=list_subscr>
<input type=submit value="<?=MSG_edit;?>">
</table></form>
<? } 

function subscr_subfunctions() { ?>
<table class="innertable" cellspacing=1 width="100%">
<tr class="tablehead"><td>
<form action="index.php">
<input type="hidden" name="m" value="subscr">
<input type="hidden" name="a" value="do_process_all">
<input type="hidden" name="sa" value="unsub_all">
<input type="submit" style="width:350px" value="<?=MSG_subscr_unsub_all;?>"></form>
<tr class="tablehead"><td>
<form action="index.php">
<input type="hidden" name="m" value="subscr">
<input type="hidden" name="a" value="do_process_all">
<input type="hidden" name="sa" value="unsub_notify">
<input type="submit" style="width:350px" value="<?=MSG_subscr_unsub_notify;?>"></form>
<tr class="tablehead"><td>
<form action="index.php">
<input type="hidden" name="m" value="subscr">
<input type="hidden" name="a" value="do_process_all">
<input type="hidden" name="sa" value="sa=unsub_auto">
<input type="submit" style="width:350px" value="<?=MSG_subscr_unsub_auto;?>"></form>
<tr class="tablehead"><td>
<form action="index.php">
<input type="hidden" name="m" value="subscr">
<input type="hidden" name="a" value="do_process_all">
<input type="hidden" name="sa" value="unsub_topics">
<input type="submit" style="width:350px" value="<?=MSG_subscr_unsub_topics;?>"></form>
<tr class="tablehead"><td>
<form action="index.php">
<input type="hidden" name="m" value="subscr">
<input type="hidden" name="a" value="do_process_all">
<input type="hidden" name="sa" value="sub_notify">
<input type="submit" style="width:350px" value="<?=MSG_subscr_sub_notify;?>"></form>
<tr class="tablehead"><td>
<form action="index.php">
<input type="hidden" name="m" value="subscr">
<input type="hidden" name="a" value="do_process_all">
<input type="hidden" name="sa" value="sub_auto">
<input type="submit" style="width:350px" value="<?=MSG_subscr_sub_auto;?>"></form>
<tr class="tablehead"><td>
<form action="index.php">
<input type="hidden" name="m" value="subscr">
<input type="hidden" name="a" value="do_process_all">
<input type="hidden" name="sa" value="sub_topics">
<input type="submit" style="width:350px" value="<?=MSG_subscr_sub_topics;?>"></form>
<tr class="tablehead"><td>
<form action="index.php">
<input type="hidden" name="m" value="subscr">
<input type="hidden" name="a" value="do_process_all">
<input type="hidden" name="sa" value="sub_all">
<input type="submit" style="width:350px" value="<?=MSG_subscr_sub_all;?>"></form>
</table>
<? }

function subscr_list_start() { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_subscr_list;?>
<? }

function subscr_list_entry(&$tdata) { ?>
<tr><td width="70%"><a href="<?=build_url($tdata);?>"><?=$tdata['t_title'];?></a>
<td align=center><input type=checkbox name="subscr[<?=$tdata['t_id'];?>]" value=1 <? check($tdata['subscr']==1);?>>
<? }

function subscr_list_end($inform,$autosub) { ?>
<tr><td class="tablehead" colspan=2><?=MSG_subscr_params;?>
<tr><td><?=MSG_subscr_newtopics;?>
<td align=center><input type=checkbox name="subscr[4294967294]" value=1 <? check($inform==1);?>>
<tr><td><?=MSG_subscr_auto;?>
<td align=center><input type=checkbox name="subscr[4294967295]" value=1 <? check($autosub==1);?>>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=subscr>
<input type=hidden name=a value=do_process><input type=hidden name=f value=<?=$GLOBALS['forum'];?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }
