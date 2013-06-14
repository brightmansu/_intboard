<? function group_list_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=4><?=MSG_user_groups;?>
<tr><td class="tablehead" width="50%"><?=MSG_g_title;?><td class="tablehead" width="10%"><?=MSG_g_count;?><td class="tablehead" width="20%"><?=MSG_g_your_status;?><td class="tablehead"><?=MSG_actions;?>
<? }

function group_list_entry(&$gdata) { ?>
<tr><td><a href="index.php?m=group&amp;a=show&amp;g=<?=$gdata['g_id'];?>"><?=$gdata['g_title'];?></a>
<br>
<?=$gdata['g_descr'];?>
<td align=center><?=$gdata['gm_count'];?>
<td align=center>
<? if ($gdata['uid']) {
	if ($gdata['gm_status']==0) { ?><?=MSG_g_request;?><? }
	elseif ($gdata['gm_status']==1) { ?><?=MSG_g_member;?><? }
	elseif ($gdata['gm_status']==2) { ?><?=MSG_g_coord;?><? }
} ?><td align=center>
<? if (!$gdata['uid']) {
	 if ($GLOBALS['inuserbasic']>=$gdata['g_lautojoin']) { ?>
<a href="index.php?m=group&amp;a=do_autojoin&amp;g=<?=$gdata['g_id'];?>"><?=MSG_g_join;?></a><? }
	 elseif ($GLOBALS['inuserbasic']>=$gdata['g_ljoin'] && $GLOBALS['inuserbasic']<$gdata['g_lautojoin']) { ?>
<a href="index.php?m=group&amp;a=sendjoin&amp;g=<?=$gdata['g_id'];?>"><?=MSG_g_send_request;?></a><? }
}
if ($gdata['gm_status']==1 && $gdata['g_allowquit']) { ?><a href="index.php?m=group&amp;a=do_quit&amp;g=<?=$gdata['g_id'];?>"><?=MSG_g_quit;?></a><? } 
?>
<? }

function group_list_noentries() { ?>
<tr><td colspan=4><?=MSG_g_none;?>
<? }

function group_list_end() { ?>
</table>
<? }

function group_join(&$gdata) { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead"><?=MSG_g_request_join;?> "<?=$gdata['g_title'];?>"
<tr><td align=center><?=MSG_g_request_text;?>:<br>
<textarea name=text cols=40 rows=8></textarea>
<tr><td class="tablehead"><input type=hidden name=g value="<?=$gdata['g_id'];?>">
<input type=hidden name=m value=group><input type=hidden name=a value=do_sendjoin>
<input type=submit value="<?=MSG_send;?>">
</table></form>
<? }

function group_show_start(&$gdata) { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3><?=MSG_g_info;?>
<tr><td colspan=3><?=$gdata['g_title'];?><br><?=$gdata['g_descr'];?>
<? }

function group_show_entry(&$udata,$status) { ?>
<tr><td width="40%"><?=user_out($udata['u__name'],$udata['u_id']);?><td align=center width="30%">
<? if ($udata['gm_status']==0) { ?><?=MSG_g_request;?><? }
elseif ($udata['gm_status']==1) { ?><?=MSG_g_member;?><? }
elseif ($udata['gm_status']==2) { ?><?=MSG_g_coord;?><? } ?>
<td align=center>&nbsp;
<? if ($status==2) { 
 if ($udata['gm_status']==0) { ?><a href="index.php?m=group&amp;a=do_add&amp;g=<?=$udata['gid'];?>&amp;u=<?=$udata['u_id'];?>"><?=MSG_g_allowjoin;?></a><? }
 if ($udata['gm_status']<2) { ?><a href="index.php?m=group&amp;a=do_delete&amp;g=<?=$udata['gid'];?>&amp;u=<?=$udata['u_id'];?>"><?=MSG_g_deletefrom;?></a><? }
} ?>
<? }

function group_show_end($status,$gid) { ?>
<? if ($status==2) { ?>
<tr><td colspan=3 align=center><a href="index.php?m=group&amp;a=mailsend&amp;g=<?=$gid;?>"><?=MSG_g_mailsend;?></a>
<tr><td><?=MSG_g_forcejoin;?>:<td colspan=2>
<input type=text name=u__name size=32 maxlength=32>
<tr><td class="tablehead" colspan=3><input type=hidden name=m value=group><input type=hidden name=g value=<?=$gid;?>>
<input type=hidden name=a value=do_forceadd><input type=submit value="<?=MSG_add;?>">
<? } ?>
</table></form>
<? }

function group_send_form() { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_g_mailsend;?>
<tr><td align=center><?=MSG_g_mailtext;?>:<br><textarea name=text cols=40 rows=16></textarea>
<tr><td class="tablehead"><input type=hidden name=m value=group><input type=hidden name=a value=do_mailsend>
<input type=hidden name=g value=<?=getvar("g");?>><input type=submit value="<?=MSG_send;?>">
</table></form>
<? }
