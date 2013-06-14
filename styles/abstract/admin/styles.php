<? function st_select_form_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3>
<?=MSG_st_selection;?>
<tr><td class="tablehead" width="50%"><?=MSG_st_name;?>
<td class="tablehead" width="25%"><?=MSG_st_status;?>
<td class="tablehead" width="25%"><?=MSG_st_parent;?>
<? }

function st_select_form_entry(&$sdata) { ?>
<tr><td><a href="admin/index.php?m=styles&amp;a=st_list&amp;style=<?=$sdata['st_file'];?>"><?=$sdata['st_name'];?> (<?=$sdata['st_file'];?>)</a> &mdash; <a href="admin/index.php?m=styles&a=st_edit_css&style=<?=$sdata['st_file'];?>">CSS</a><?
if ($sdata['st_integrated']) { ?> &mdash; <a href="admin/index.php?m=styles&amp;a=st_edit_template&amp;style=<?=$sdata['st_file'];?>"><?=MSG_st_template;?></a>
<?  } ?><td align=center>
<? if ($sdata['st_show']) { ?>
<?=MSG_st_visible;?> (<a href="admin/index.php?m=styles&amp;a=st_hide&amp;style=<?=$sdata['st_file'];?>&amp;st_show=0"><?=MSG_hide;?></a>)<? }
else { ?>
<?=MSG_st_hidden;?> (<a href="admin/index.php?m=styles&amp;a=st_hide&amp;style=<?=$sdata['st_file'];?>&amp;st_show=1"><?=MSG_show;?></a>)
<? } ?>
<td align=center><?=$sdata['st_parent'];?><?
}

function st_select_form_end() { ?>
</table>
<? }

function st_delete_form($styleselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_st_selfordelete;?>
<tr><td width="50%"><?=MSG_st_select;?>:
<td><select name=style><?=$styleselect;?></select>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=styles><input type=hidden name=a value=st_confirm_set>
<input type=submit value="<?=MSG_st_delete;?>">
</table></form>
<? }

function st_change_form($styleselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_st_change_style;?>
<tr><td width="50%"><?=MSG_st_select;?>:
<td><select name=style><?=$styleselect;?></select>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=styles><input type=hidden name=a value=st_change>
<input type=submit value="<?=MSG_st_change;?>">
</table></form>
<? }

function st_create_form($styleselect) { ?>
<br><form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_st_create;?>
<tr><td width="50%"><?=MSG_st_base;?>:
<td><select name=st_parent><?=$styleselect;?></select>
<tr><td><?=MSG_st_newname;?>:<td>
<input type=text name=st_name size=30 maxlength=40>
<tr><td><?=MSG_st_newfile;?>:<td>
<input type=text name=st_file size=20 maxlength=20>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=styles>
<input type=hidden name=a value=st_create_set>
<input type=submit value="<?=MSG_st_docreate;?>">
</table></form>
<? }

function st_list_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_st_list;?>
<? }

function st_list_entry($name,$data,$descr,$styleset) { ?>
<tr><td width="75%"><b><?=$name;?></b> - <?=$descr;?><td>
<a href="admin/index.php?m=styles&a=st_edit&style=<?=$styleset;?>&name=<?=$name;?>"><?=MSG_edit;?></a> &nbsp; <a href="admin/index.php?m=styles&a=st_confirm&style=<?=$styleset;?>&name=<?=$name;?>"><?=MSG_delete;?></a>
<? }

function st_list_end() { ?>
<tr><td class="tablehead" colspan=2><form action="admin/index.php">
<input type=hidden name=m value=styles><input type=hidden name=a value=st_select>
<input type=submit value="<?=MSG_st_return;?>"></form></table>
<? }

function st_replace_form() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_st_replacecolor;?>
<tr><td width="70%"><?=MSG_st_replacefrom;?>:<td>
<input type=text name=oldcolor size=6 maxlength=6>
<tr><td><?=MSG_st_replaceto;?>:<td>
<input type=text name=newcolor size=6 maxlength=6>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=styles><input type=hidden name=a value=st_replace>
<input type=hidden name=style value="<?=getvar("style");?>"><input type=submit value="<?=MSG_edit;?>">
</table></form>
<? }

function st_edit_start($style,$name) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_st_edit;?> <?=$name;?> <?=MSG_st_fromset;?> "<?=$style;?>"
<? }

function st_edit_entry($stname,$stdata,$stdescr="") { ?>
<tr><td width="50%"><?=$stdescr;?><br>
<b><?=$stname;?>:</b><td>
<input type=text name="st[<?=$stname;?>]" size=30 maxlength=255 value="<?=$stdata;?>">
<? }

function st_edit_end() { ?>
<tr><td><?=MSG_st_miscparams;?>:<td>
<textarea name=more rows=6 cols=30></textarea>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=styles>
<input type=hidden name=style value="<?=getvar("style");?>"><input type=hidden name=name value="<?=getvar("name");?>">
<input type=hidden name=a value=st_save><input type=submit value="<?=MSG_save;?>">
</table></form>
<div class="descr" style="text-align: center"><a href="admin/index.php?m=styles&a=st_list&style=<?=getvar("style");?>"><?=MSG_st_return_list;?></a></div>
<? }

function st_edit_form($style,$stdata,$newaction) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1>
<tr><td class="tablehead"><? if ($newaction=="st_save_template") { ?>
<?=MSG_st_edit_template;?> <?=$style;?><? }
else { ?><?=MSG_st_cssedit;?> <?=$style;?>.css<? } ?>
<tr><td style="text-align: center"><textarea name=stdata_text cols=80 rows=40><?=htmlspecialchars($stdata);?></textarea>
<tr><td style="text-align:center">
<input type=hidden name=m value="styles"><input type=hidden name=a value="<?=$newaction;?>">
<input type=hidden name=style value="<?=$style;?>">
<input type=submit value="<?=MSG_save;?>"> &nbsp; <input type=submit onClick="document.getElementById('draft_msg').style.display='';setTimeout('document.getElementById(\'draft_msg\').style.display=\'none\';return true',10000)" name=continue value="<?=MSG_save_continue;?>">
<div class="descr" id="draft_msg" style="display: none"><?=MSG_send_continue;?></div>
</table></form>
<div class="descr" style="text-align: center"><a href="admin/index.php?m=styles&a=st_select"><?=MSG_st_return;?></a></div>
<? }

function tp_edit_form($style,$stdata,$newaction) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1>
<tr><td class="tablehead"><?=MSG_st_edit_template;?> <?=$style;?>
<tr><td><?=MSG_template_descr;?><br>
<?=MSG_template_comment3;?><br>
<?=MSG_template_comment15;?><br>
<?=MSG_template_comment5;?><br>
<?=MSG_template_comment16;?><br>
<?=MSG_template_comment6;?><br>
<?=MSG_template_comment8;?><br>
<?=MSG_template_comment9;?><br>
<?=MSG_template_comment10;?><br>
<?=MSG_template_comment11;?><br>
<?=MSG_template_comment12;?><br>
<?=MSG_template_comment13;?><br>
<?=MSG_template_comment14;?><br>
<?=MSG_template_comment17;?><br>
<?=MSG_template_comment18;?><br>
<?=MSG_template_comment19;?><br>
<?=MSG_template_comment20;?><br>
<?=MSG_template_comment21;?><br>
<?=MSG_template_comment22;?>
<tr><td><?=MSG_sys_oldpass;?>: <input type=password name=sys_pass size=32 maxlength=32>
<tr><td style="text-align: center" colspan=2><textarea name=stdata_text cols=80 rows=40><?=htmlspecialchars($stdata);?></textarea>
<tr><td style="text-align:center">
<input type=hidden name=m value="styles"><input type=hidden name=a value="<?=$newaction;?>">
<input type=hidden name=style value="<?=$style;?>">
<input type=submit value="<?=MSG_save;?>"> &nbsp; <input type=submit onClick="document.getElementById('draft_msg').style.display=''; setTimeout('document.getElementById(\'draft_msg\').style.display=\'none\';',10000);  return true" name=continue value="<?=MSG_save_continue;?>">
<div class="descr" id="draft_msg" style="display: none"><?=MSG_send_continue;?></div>
</table></form>
<div class="descr" style="text-align: center"><a href="admin/index.php?m=styles&a=st_select"><?=MSG_st_return;?></a></div>
<? }
