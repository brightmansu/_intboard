<? function addon_list_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3>
<?=MSG_addons;?>
<tr><td class="tablehead" width="30%"><?=MSG_addon_name;?><td class="tablehead" width="10%"><?=MSG_addon_ver;?>
<td class="tablehead"><?=MSG_addon_descr;?>
<? }

function addon_list_entry(&$addon) { ?>
<tr><td><?=$addon['a_fullname'];?><td><?=sprintf("%.2f",$addon['a_ver']/100);?>
<td><?=$addon['a_descr'];?>
<? }

function addon_list_end() { ?>
</table>
<? }

function addon_upload_form()  { ?>
<form action="admin/index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_addon_upload;?>
<tr><td colspan=2><?=MSG_addon_upload_descr;?>
<? if (file_exists($GLOBALS['opt_dir']."/temp/addon.php")) { ?>
<br><a href="admin/index.php?m=addon&a=addon_install"><?=MSG_addon_install;?></a><? } ?>
<tr><td width="50%"><?=MSG_addon_path;?>:
<td><input type=file name=addon>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=addon>
<input type=hidden name=a value=addon_upload><input type=submit value="<?=MSG_upload;?>">
</table></form>
<? }

function addon_install_form($fullname,$descr,$version) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_upload_doinstall;?>:
<tr><td width="50%"><?=MSG_addon_name;?>:<td><?=$fullname;?>
<tr><td><?=MSG_addon_ver;?>:<td><?=sprintf("%.2f",$version/100);?>
<tr><td width="50%"><?=MSG_addon_descr;?>:<td><?=$descr;?>
<tr><td><?=MSG_sys_oldpass;?><td>
<input type=password name=sys_pass size=32 maxlength=32>
<tr><td class="tablehead" style="text-align: right"><input type=hidden name=m value=addon><input type=hidden name=a value=addon_process>
<input type=submit value="<?=MSG_addon_doinstall;?>"></form>&nbsp;<td class="tablehead" style="text-align: left">
<form action="admin/index.php" method=GET>
&nbsp;<input type=hidden name=m value=addon><input type=hidden name=a value=addon_start>
<input type=submit value="<?=MSG_cancel;?>"></form>
</table></form>
<? }
