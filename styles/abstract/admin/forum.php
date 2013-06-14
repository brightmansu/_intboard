<? function ad_ct_list_start() { ?>
<div class="descr"><?=MSG_f_move;?>.<br>
<?=MSG_f_move2;?>.<br>
<?=MSG_f_move3;?>.</div>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=4>
<?=MSG_ct_list;?>

<? }

function ad_ct_entry($cdata) { ?>
<tr height=24><td width="50%">
<b><a href="admin/index.php?m=forum&a=ct_edit&ctid=<?=$cdata['ct_id'];?>"><?=$cdata['ct_name'];?></a></b>
<td width="20%">&nbsp;
<td width="10%">
<input type="text" size=3 maxlength=5 name="cat[<?=$cdata['ct_id'];?>]" value="<?=$cdata['ct_sortfield'];?>">
<td width="20%">
<a href="admin/index.php?m=forum&a=f_new_type&ctid=<?=$cdata['ct_id'];?>"><?=MSG_ct_newf;?></a> &nbsp; 
<a href="admin/index.php?m=forum&a=ct_confirm&ctid=<?=$cdata['ct_id'];?>"><?=MSG_delete;?></a>
<? }

function ad_f_entry($fdata) { ?>
<tr><td>
&nbsp;&nbsp;<a href="admin/index.php?m=forum&a=f_edit&fid=<?=$fdata['f_id'];?>"><?=$fdata['f_title'];?></a>
<? if ($GLOBALS['opt_hurl'] && $fdata['f_link']) { ?> (<?=$fdata['f_link'];?>)<? } ?>
<td>
<?=constant($fdata['tp_title']);?>
<td>
<input type="text" size=3 maxlength=5 name="fid[<?=$fdata['f_id'];?>]" value="<?=$fdata['f_sortfield'];?>">
<td>
<a href="admin/index.php?m=forum&a=f_confirm&fid=<?=$fdata['f_id'];?>"><?=MSG_delete;?></a>
<? }

function ad_ct_list_end() { ?>
<tr><td class="tablehead" colspan=6>
<input type=hidden name=m value="forum"><input type=hidden name=a value="f_exchange">
<input type=submit name=exchange value="<?=MSG_f_exchange;?>">
</table></form><br>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_ct_create;?>

<tr><td>
<?=MSG_ct_newtitle;?>
<td>
<input type=text name=ct_name size=30 maxlength=60>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value=ct_new>
<input type=hidden name=m value=forum>
<input type=submit value="<?=MSG_create;?>">
</table>
</form>
<? }

function ad_ct_editform($name) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_ct_edit;?>

<tr><td>
<?=MSG_ct_title;?>
<td>
<input type=text name=ct_name size=30 maxlength=60 value="<?=$name;?>">
<tr><td class="tablehead" colspan=2 align=center>
<input type=hidden name=a value=ct_save>
<input type=hidden name=m value=forum>
<input type=hidden name=ctid value=<?=getvar("ctid");?>>
<input type=submit value="<?=MSG_save;?>">
</table>
</form>
<? }

function ad_f_new_type($typeselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_ct_createf;?>
<tr><td width="50%">
<?=MSG_ct_newtype;?>
<td>
<?=$typeselect;?>

<tr><td class="tablehead" colspan=2>
<input type=hidden name=m value=forum>
<input type=hidden name=a value=f_new>
<input type=hidden name=ctid value=<?=getvar('ctid');?>>
<input type=submit value="<?=MSG_create;?>">
</table></form>
<? }

function ad_f_group($forumlist,$catselect,$fcontainer,$levelselect,$langselect,$count) { ?>
<script><!--
function SetAll(status) {
  for (i=1; i<=<?=$count+1;?>; i++) { document.forumparams.elements[i].checked=status; }
}
--></script>
<form action="admin/index.php" method=POST name=forumparams>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_gparams;?>
<tr><td valign=top width="40%">
<?=MSG_f_forumlist;?>:<br><br>
<input type=button onClick="SetAll(true)" value="<?=MSG_select;?>" style="width: 200 px"><br>
<input type=button onClick="SetAll(false)" value="<?=MSG_unselect;?>" style="width: 200 px">
<td><?=$forumlist;?>
<tr><td>
<?=MSG_f_cat;?>:
<td>
<select name=f_ctid><?=$catselect;?></select>
<tr><td>
<?=MSG_f_show_in;?>:
<td>
<select name=f_parent><?=$fcontainer;?></select>
<tr><td>
<?=MSG_f_langs;?>?
<td>
<select name=f_lnid><?=$langselect;?></select>
<tr><td>
<?=MSG_f_close;?>?
<td>
<input type=radio name=f_status value="" checked><?=MSG_nochanges;?> &nbsp;
<input type=radio name=f_status value=0><?=MSG_no;?> &nbsp;
<input type=radio name=f_status value=1><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_rate;?>?
<td>
<input type=radio name=f_rate value="" checked><?=MSG_nochanges;?> &nbsp;
<input type=radio name=f_rate value=0><?=MSG_no;?> &nbsp;
<input type=radio name=f_rate value=1><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_bcode;?>?
<td>
<input type=radio name=f_bcode value="" checked><?=MSG_nochanges;?> &nbsp;
<input type=radio name=f_bcode value=0><?=MSG_no;?> &nbsp;
<input type=radio name=f_bcode value=1><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_smiles;?>?
<td>
<input type=radio name=f_smiles value="" checked><?=MSG_nochanges;?> &nbsp;
<input type=radio name=f_smiles value=0><?=MSG_no;?> &nbsp;
<input type=radio name=f_smiles value=1><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_stats;?>?
<td>
<input type=radio name=f_nostats value="" checked><?=MSG_nochanges;?> &nbsp;
<input type=radio name=f_nostats value=1><?=MSG_no;?> &nbsp;
<input type=radio name=f_nostats value=0><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_selfmod;?>?
<td>
<input type=radio name=f_selfmod value="" checked><?=MSG_nochanges;?> &nbsp;
<input type=radio name=f_selfmod value=0><?=MSG_no;?> &nbsp;
<input type=radio name=f_selfmod value=1><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_attachpics;?>?
<td>
<input type=radio name=f_attachpics value="" checked><?=MSG_nochanges;?> &nbsp;
<input type=radio name=f_attachpics value=0><?=MSG_no;?> &nbsp;
<input type=radio name=f_attachpics value=1><?=MSG_yes;?> &nbsp;
<tr><td class="tablehead" colspan=2><?=MSG_f_levels;?>
<tr><td>
<?=MSG_f_lview;?>
<td>
<select name=f_lview><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lread;?>
<td>
<select name=f_lread><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lpost;?>
<td>
<select name=f_lpost><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_ltopic;?>
<td>
<select name=f_ltopic><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_ledit;?>
<td>
<select name=f_ledit><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lpoll;?>
<td>
<select name=f_lpoll><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lvote;?>
<td>
<select name=f_lvote><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lsticky;?>
<td>
<select name=f_lsticky><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lattach;?>
<td>
<select name=f_lattach><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lhtml;?>
<td>
<select name=f_lhtml><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lpremod;?>
<td>
<select name=f_lpremod><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_ltopicpremod;?>
<td>
<select name=f_ltopicpremod><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lmoderate;?>
<td>
<select name=f_lmoderate><?=$levelselect;?></select>
<tr><td>
<?=MSG_f_lip;?>
<td>
<select name=f_lip><?=$levelselect;?></select>

<tr><td class="tablehead" colspan=2>
<input type=hidden name=m value="forum">
<input type=hidden name=a value="f_group_process">
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }