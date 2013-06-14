<? function lang_select_form($langselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_lang_selection;?>
<tr><td width="50%"><?=MSG_lang_select;?>:<td><select name=lang><?=$langselect;?></select>
<tr><td width="50%"><?=MSG_msg_toedit;?>:<td>
<input type=radio name=mode value=main checked><?=MSG_msg_user;?> &nbsp;
<input type=radio name=mode value=admin><?=MSG_msg_edit;?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=msg><input type=hidden name=a value=msg_list>
<input type=submit value="<?=MSG_edit;?>">
</table></form>
<? }

function list_msg_start() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_msg_edit;?>
<? }

function list_msg_entry($msg,$value) { ?>
<tr><td width="40%"><?=$msg;?><td>
<input type=text name=msg[<?=$msg;?>] size=60 maxlength=255 value="<?=htmlspecialchars($value);?>">

<? }

function list_msg_end() { ?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=msg><input type=hidden name=a value=msg_save>
<input type=hidden name=lang value=<?=getvar("lang");?>><input type=hidden name=mode value=<?=getvar("mode");?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function msg_add_form() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_msg_add;?>
<tr><td width="50%"><?=MSG_msg_code;?>:<br>
<?=MSG_msg_descr;?><td>
<input type=text name=msg size=30 maxlength=255 value="MSG_">
<tr><td><?=MSG_msg_text;?>:<td>
<input type=text name=value size=30 maxlength=255>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=msg><input type=hidden name=a value=msg_add>
<input type=hidden name=lang value=<?=getvar("lang");?>><input type=hidden name=mode value=<?=getvar("mode");?>>
<input type=submit value="<?=MSG_add;?>">
</table></form>
<? }
