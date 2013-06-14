<? function link_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_params;?>
<tr><td width="50%"><?=MSG_f_title;?>:
<td>
<input type=text name=f_title size=30 maxlength=60 value="<?=htmlspecialchars($fdata['f_title']);?>">
<tr><td><?=MSG_f_descr;?>:
<td>
<textarea name=f_descr rows=3 cols=30><?=htmlspecialchars($fdata['f_descr']);?></textarea>
<tr><td>
<?=MSG_f_cat;?>:
<td>
<select name=f_ctid><? set_select($catselect,$_POST['ctid']);?></select>
<tr><td>
<?=MSG_f_show_in;?>:
<td>
<select name=f_parent><? set_select($fcontainer,$fdata['f_parent']);?></select>
<tr><td>
<?=MSG_f_langs;?>?
<td>
<select name=f_lnid><?=set_select($langselect,$fdata['f_lnid']);?></select>
<tr><td>
<?=MSG_f_nonewpic;?><td>
<input type=text name=f_nonewpic size=20 maxlength=20 value="<?=$fdata['f_nonewpic'];?>">
<tr><td>
<?=MSG_f_url;?><td>
<input type=text name=f_url size=40 maxlength=255 value="<?=$fdata['f_url'];?>">
<tr><td>
<?=MSG_f_lview;?>
<td>
<select name=f_lview><? set_select($levelselect,$fdata['f_lview']);?></select>
<tr><td>
<?=MSG_f_lread;?>
<td>
<select name=f_lread><? set_select($levelselect,$fdata['f_lread']);?></select>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="<?=$GLOBALS['newaction'];?>">
<input type=hidden name=m value="<?=$GLOBALS['newmodule'];?>">
<input type=hidden name=f_tpid value="<?=$fdata['f_tpid'];?>">
<input type=hidden name=fid value=<?=getvar("fid");?>>
<input type=hidden name="f_text" value="">
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function link_view() { ?>
<meta http-equiv="refresh" content="0; <?=$GLOBALS['inforum']['f_url'];?>">
<table width="100%" class="innertable" cellspacing=1><tr><td class="tablehead"><?=MSG_extlink;?>
<tr><td><span class="descr"><?=MSG_extlinkclick;?></span><br>
<a href="<?=$GLOBALS['inforum']['f_url'];?>"><?=$GLOBALS['inforum']['f_url'];?></a>
</table>
<? }