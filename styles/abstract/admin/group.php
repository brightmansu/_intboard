<? function ad_group_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3>
<?=MSG_g_list;?>
<tr><td class="tablehead" width="60%"><?=MSG_g_title;?>
<td class="tablehead" width="10%"><?=MSG_g_count;?>
<td class="tablehead" width="30%"><?=MSG_g_actions;?>
<? }

function ad_group_entry($gdata) { ?>
<tr><td>
<a href="admin/index.php?m=group&a=g_edit&g=<?=$gdata['g_id'];?>"><?=$gdata['g_title'];?></a><br>
<?=$gdata['g_descr'];?>
<td align=center><?=$gdata['gm_count'];?>
<td align=center><a href="index.php?m=group&g=<?=$gdata['g_id'];?>"><?=MSG_g_goto;?></a>
<a href="admin/index.php?m=group&a=g_confirm&g=<?=$gdata['g_id'];?>"><?=MSG_delete;?></a>
<a href="admin/index.php?m=group&a=g_setlevel&g=<?=$gdata['g_id'];?>"><?=MSG_g_setlevel;?></a>
<? }

function ad_group_end() { ?>
<tr><td class="tablehead" colspan=3><form action="admin/index.php" method=GET>
<input type=hidden name=m value=group><input type=hidden name=a value=g_new>
<input type=submit value="<?=MSG_g_create;?>"></form>
</table>
<? }

function ad_group_form($gdata,$newaction,$levels) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_g_params;?>
<tr><td width="50%"><?=MSG_g_title;?>:<td>
<input type=text name=g_title size=32 maxlength=32 value="<?=$gdata['g_title'];?>">
<tr><td><?=MSG_g_descr;?>:<td>
<textarea name=g_descr rows=3 cols=30><?=$gdata['g_descr'];?></textarea>
<tr><td><?=MSG_g_lview;?><td>
<select name=g_lview><?=set_select($levels,$gdata['g_lview']);?></select>
<tr><td><?=MSG_g_ljoin;?><td>
<select name=g_ljoin><?=set_select($levels,$gdata['g_ljoin']);?></select>
<tr><td><?=MSG_g_lautojoin;?><td>
<select name=g_lautojoin><?=set_select($levels,$gdata['g_lautojoin']);?></select>
<tr><td><?=MSG_g_allowquit;?>?<td>
<input type=radio name=g_allowquit value=1 <? check($gdata['g_allowquit']==1);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=g_allowquit value=0 <? check($gdata['g_allowquit']==0);?>><?=MSG_no;?> &nbsp;

<? if ($newaction=="g_create") { ?>
<tr><td><?=MSG_g_coords;?>:<td>
<input type=text name=coord size=32 maxlength=255>
<? } ?>
<tr><td colspan=2><?=MSG_g_setlevel;?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=group>
<input type=hidden name=a value="<?=$newaction;?>"><input type=hidden name=g value=<?=$gdata['g_id'];?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function ad_coord_start() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_g_coords;?>
<? }

function ad_coord_entry($udata) { ?>
<tr><td width="80%"><?=$udata['u__name'];?>
<td><input type=checkbox name=delete[<?=$udata['u_id'];?>] value=1><?=MSG_delete;?>?
<? }

function ad_coord_end() { ?>
<tr><td><?=MSG_g_addcoords;?>:<td>
<input type=text name=coord size=32 maxlength=255>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=group>
<input type=hidden name=a value=g_coord><input type=hidden name=g value=<?=getvar("g");?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function ad_g_forum_start($groupselect,$levelselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_g_levels;?>
<tr><td width="50%"><?=MSG_g_group;?>:<td><select name=g><?=$groupselect;?></select>
<tr><td><?=MSG_g_mainlevel;?>:<td><select name=ulevel><option value=0><?=MSG_nochanges;?>
<?=$levelselect;?></select>
<? }

function ad_g_forum_entry($fdata,$levelselect) { ?>
<tr><td><?=$fdata['f_title'];?>
<td><select name=forum[<?=$fdata['f_id'];?>]><option value=0><?=MSG_nochanges;?>
<option value="common"><?=MSG_nospecial;?><?=$levelselect;?></select>
<? }

function ad_g_forum_end() { ?>
<tr><td><?=MSG_g_applyto;?>:<td>
<label><input type=radio name=mode value=0><?=MSG_g_newusers;?></label><br>
<label><input type=radio name=mode value=1><?=MSG_g_users;?></label><br>
<input type=radio name=mode value=2 checked><?=MSG_g_both;?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=group>
<input type=hidden name=a value=g_change_level><input type=submit value="<?=MSG_save;?>">
</table></form>
<? }
