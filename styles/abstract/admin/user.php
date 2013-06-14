<? function ad_u_select() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_u_edit;?>
<tr><td width="50%">
<?=MSG_u_inputname;?><br>
<?=MSG_u_editguest;?><br>
<?=MSG_u_editnewuser;?>
<td><input type=text name=uname size=32 maxlength=32>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=user>
<input type=hidden name=a value=u_edit><input type=submit value="<?=MSG_edit;?>">
</table></form>
<br>
<? }

function user_params_start($uname,$levels,$ulevel) { ?>
<form action="admin/index.php" method=POST>
<input type=hidden name=unames value="<?=$uname;?>">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_u_levels;?>

<tr><td><?=MSG_u_basiclevel;?>
<td><select name=u__level><?=set_select($levels,$ulevel);?></select>
<? }

function user_level_start($levels) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_u_levels;?>
<tr><td width="50%">
<?=MSG_u_inputname;?><br>
<?=MSG_u_guestnew;?>
<td><input type=text name=unames size=32 maxlength=255>
<tr><td><?=MSG_u_basiclevel;?>
<td><select name=u__level><option value=0><?=MSG_u_nochange;?><?=$levels;?></select>
<tr><td class="tablehead" colspan=2><?=MSG_u_forumlevels;?>
<? }

function user_level_forum($fdata,$levels,$curlevel) { ?>
<tr><td><?=MSG_forum;?> "<?=$fdata['f_title'];?>"<td>
<select name=uforum[<?=$fdata['f_id'];?>]><?=set_select("<option value=0>".MSG_u_nochange."<option value=\"common\">".MSG_u_nospecial.$levels,$curlevel);?></select>
<? }

function user_level_end() { ?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=user>
<input type=hidden name=a value=u_setlevel><input type=submit value="<?=MSG_set;?>">
</table></form>
<? }

function ad_u_level_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=5>
<?=MSG_forumlevels;?>
<tr><td class="tablehead" width="30%"><?=MSG_title_name;?>
<td class="tablehead" width="20%"><?=MSG_title_level;?>
<td class="tablehead" width="10%"><?=MSG_title_posts;?>
<td class="tablehead" width="20%"><?=MSG_title_actions;?>
<td class="tablehead" width="20%"><?=MSG_title_pic;?>
<? }

function ad_u_level_entry($ldata) { 
  if (!$ldata['l_title']) $ldata['l_title']=MSG_l_noname;
  ?>
<tr><td><? if ($ldata['l_level']<=0 || $ldata['l_level']>=1024) echo $ldata['l_title'];
else { ?><a href="admin/index.php?m=user&a=u_level_edit&l=<?=$ldata['l_level'];?>"><?=$ldata['l_title'];?></a><? } ?>
<td align=center><?=$ldata['l_level'];?>
<td align=center><? if (!$ldata['l_custom']) echo $ldata['l_minpost']; else { ?><?=MSG_title_unique;?><? } ?>
<td align=center><? if ($ldata['l_level']>0 && $ldata['l_level']<1024) { ?>
<a href="admin/index.php?m=user&a=u_level_confirm&l=<?=$ldata['l_level'];?>"><?=MSG_delete;?></a><? } ?>
<td align=center><? if ($ldata['l_pic']) { ?><img src="images/<?=$ldata['l_pic'];?>" alt=""><? } ?>
<? }

function ad_u_level_end() { ?>
</table>
<? }

function ad_u_level_edit($newaction,$ldata,$msg) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$msg;?>
<? if ($newaction=="u_level_add") { ?>
<tr><td width="50%"><?=MSG_title_levelvalue;?>:
<td><input type=text name=l_level size=4 maxlength=4 value="<?=$ldata['l_level'];?>">
<? } ?>
<tr><td><?=MSG_title_name;?>:
<td><input type=text name=l_title size=40 maxlength=48 value="<?=$ldata['l_title'];?>">
<tr><td><?=MSG_title_pic;?>:
<td><input type=text name=l_pic size=40 maxlength=255 value="<?=$ldata['l_pic'];?>">
<tr><td><?=MSG_title_isunique;?>?
<td><input type=radio name=l_custom value=1 <? check($ldata['l_custom']==1); ?>><?=MSG_yes;?> &nbsp;
<input type=radio name=l_custom value=0 <? check($ldata['l_custom']==0); ?>><?=MSG_no;?>
<tr><td><?=MSG_title_minposts;?><br>
<?=MSG_title_minposts_descr;?><td>
<input type=text name=l_minpost size=5 maxlength=8 value="<?=$ldata['l_minpost'];?>">
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=user>
<input type=hidden name=a value=<?=$newaction;?>><input type=hidden name=l value=<?=$ldata['l_level'];?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function user_letter_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3>
<?=MSG_u_list;?>
<tr><td class="tablehead" colspan=3><?=MSG_u_letter;?>:
<? }

function user_letter_entry($letter) {
  if (getvar("ltt")!=$letter) { ?><a href="admin/index.php?m=user&a=user_list&ltt=<?=$letter;?>"><?=$letter;?></a> <? }
  else  echo $letter." ";
}

function user_letter_end() { ?>
</table>
<? }

function user_lst_start($msg) { ?>
<table class="innertable" width="100%" cellspacing=1>
<tr><td class="tablehead" colspan=3><?=$msg;?>
<? }

function user_lst_entry($udata) { ?>
<tr><td width="20%">&nbsp;
<td><b><?=$udata['u__name'];?></b> (ID: <?=$udata['u_id'];?>) <a href="admin/index.php?m=user&a=u_edit&uname=<?=$udata['u__name'];?>"><?=MSG_edit;?></a>
<a href="admin/index.php?m=user&a=u_confirm&uname=<?=$udata['u__name'];?>"><?=MSG_delete;?></a>
<? if (!$udata['u__active']) {?><a href="admin/index.php?m=user&a=u_change&sa=1&uid=<?=$udata['u_id'];?>"><?=MSG_user_activate;?></a> <? }
if ($udata['u__level']==-1) {?><a href="admin/index.php?m=user&a=u_change&sa=2&uid=<?=$udata['u_id'];?>"><?=MSG_user_unban;?></a> <? }
else {?><a href="admin/index.php?m=user&a=u_change&sa=3&uid=<?=$udata['u_id'];?>"><?=MSG_user_ban;?></a><? } ?>
<br>
<?=MSG_level;?>: <?=$udata['l_title'];?> (<?
if ($udata['u__active']) { ?><?=MSG_user_active;?><? }
else { ?><?=MSG_user_inactive;?><? } ?>)<br>
<?=MSG_user_email;?>: <a href="mailto:<?=$udata['u__email'];?>"><?=$udata['u__email'];?></a><br>
<?=MSG_user_location;?>: <?=$udata['u_location'];?><br>
<?=MSG_user_total;?>: <?=intval($udata['u_count']);?><br>
<?=MSG_user_lastvisit;?>: <?=long_date_out($udata['u_lastvisit']);?><br>
<td width="20%">&nbsp;
<? }

function user_lst_noentries($msg) { ?>
<tr><td colspan=3><?=$msg;?>
<? }

function user_lst_end() { ?>
</table>
<? }

function user_clear_form() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td colspan=2 class="tablehead"><?=MSG_user_clearing;?>
<tr><td width="50%"><?=MSG_user_lvdays;?>
<td><input type=text name=lvtime size=5 maxlength=5> <?=MSG_user_daysago;?>
<tr><td width="50%"><?=MSG_user_pcount;?>
<td><input type=text name=pcount size=5 maxlength=6> <?=MSG_p3;?>
<tr><td width="50%"><?=MSG_user_lpdays;?>
<td><input type=text name=lptime size=5 maxlength=5> <?=MSG_user_daysago;?>
<tr><td colspan="2"><label><input type=checkbox name=inactive value=1> <?=MSG_user_only_inactive;?></label>
<tr><td colspan=2 style="text-align: center"><?=MSG_user_cleardecr;?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=user>
<input type=hidden name=a value=u_clear_confirm><input type=submit value=<?=MSG_clear;?>>
</table></form>
<? }

function uw_input_form() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td colspan=2 class="tablehead"><?=MSG_uw_params;?>
<tr><td width="50%"><?=MSG_uw_users;?><br><div class="descr"><?=MSG_uw_users_descr;?></div>
<td><input type=text name=uname size=40 maxlength=255>
<tr><td width="50%"><?=MSG_uw_onlyactive;?>
<td><input type=radio name=active value=1><?=MSG_yes;?> &nbsp;
<input type=radio name=active value=0 checked><?=MSG_no;?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=user>
<input type=hidden name=a value=uw_list><input type=submit value=<?=MSG_show;?>>
</table></form>
<? }

function uw_list_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td colspan=3 class="tablehead"><?=MSG_uw_list;?>: <?=getvar("uname");?>
<? }

function uw_list_entry($uwdata) { ?>
<tr><td width="70%"><a href="admin/index.php?m=user&a=u_edit&uname=<?=$uwdata['u__name'];?>"><?=$uwdata['u__name'];?></a><br><br>
<?=textout($uwdata['uw_comment'],1,1,1);?><br><td width="10%">
<? if ($uwdata['uw_value']==1) { ?><?=MSG_warn_award;?><? }
elseif ($uwdata['uw_value']==-1) { ?><?=MSG_warn_warning;?><? }
elseif ($uwdata['uw_value']==-5) { ?><?=MSG_warn_ban;?><? } ?>
<td><a href="admin/index.php?m=user&a=uw_delete&uwid=<?=$uwdata['uw_id'];?>"><?=MSG_delete;?></a>
<? }

function uw_list_end() { ?>
</table>
<? }

function del_byname_form() { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_u_do_delete;?>
<tr><td width="40%"><?=MSG_u_deletename;?>
<td><input type=text name=uname size=32 maxlength=32>
<tr><td><?=MSG_u_deloptions;?>
<td><input type=checkbox name=delmsg value=1> <?=MSG_u_delmsg;?><br>
<input type=checkbox name=delguest value=1> <?=MSG_u_delguest;?>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=m value=user><input type=hidden name=a value=u_del_process>
<input type=submit value="<?=MSG_delete;?>">
</table></form>
<? }