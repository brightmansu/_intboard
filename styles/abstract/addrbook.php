<? function addrbook_start() { ?>
<form action="index.php" method="post">
<table style="width: 100%; border: 0"><col width="50%"><col>
<tr><td style="vertical-align: top"><?
}

function addrbook_friends_start() { ?>
<table class="innertable" style="font-size: 1em; width: 100%">
<col width="90%"><col>
<tr><td colspan=2 class="tablehead"><?=MSG_ab_friends;?>
<? }

function addrbook_friends_entry($curitem) { ?>
<tr><td><?=user_out($curitem[1],$curitem[0]);?>
<td><input type="checkbox" name="delete[<?=$curitem[0];?>]" value="<?=$curitem[0];?>">
<? }

function addrbook_friends_noentries() { ?>
<tr><td colspan=2><?=MSG_ab_nofriends;?>
<? }

function addrbook_friends_end() { ?>
</table>
<? }

function addrbook_separator() { ?>
<td style="vertical-align: top">
<? }

function addrbook_enemies_start() { ?>
<table class="innertable" style="font-size: 1em; width: 100%">
<col width="90%"><col>
<tr><td colspan=2 class="tablehead"><?=MSG_ab_enemies;?>
<? }

function addrbook_enemies_entry($curitem) { ?>
<tr><td><?=user_out($curitem[1],$curitem[0]);?>
<td><input type="checkbox" name="delete[<?=$curitem[0];?>]" value="<?=$curitem[0];?>">
<? }

function addrbook_enemies_noentries() { ?>
<tr><td colspan=2><?=MSG_ab_noenemies;?>
<? }

function addrbook_enemies_end() { ?>
</table>
<? }

function addrbook_end() { ?>
</table>
<div class="descr" style="text-align: center"><?=MSG_ab_hint1;?><br><?=MSG_ab_hint2;?></div>
<p class="maintext"><?=MSG_ab_addfriends;?>: <input type="text" size=80 maxlength=255 name="new_friends"></p>
<p class="maintext"><?=MSG_ab_addenemies;?>: <input type="text" size=80 maxlength=255 name="new_enemies"></p>
<div style="text-align: center">
<input type="submit" value="<?=MSG_ab_save;?>">
<input type="hidden" name="m" value="addrbook"><input type="hidden" name="a" value="do_view">
</div>
</form>
<? }