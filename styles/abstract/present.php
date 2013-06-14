<?
function present_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_params;?>
<tr><td width="50%"><?=MSG_f_title;?>:
<td>
<input type=text name=f_title size=30 maxlength=60 value="<?=htmlspecialchars($fdata['f_title']);?>">
<tr><td><?=MSG_f_descr;?>:
<td>
<textarea name=f_descr rows=3 cols=30><?=htmlspecialchars($fdata['f_descr']);?></textarea>
<? if ($GLOBALS['opt_hurl']) { ?>
<tr><td><?=MSG_f_link;?>:<br>
<span class="descr"><?=MSG_f_link_descr;?></span>
<td>
<input type=text name=f_link size=30 maxlength=60 value="<?=$fdata['f_link'];?>">
<? } ?><tr><td>
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
<tr><td><?=MSG_f_nosubs;?>:
<td>
<input type=radio name=f_nosubs value=0 <?=check($fdata['f_nosubs']==0);?>> <?=MSG_yes;?> &nbsp;
<input type=radio name=f_nosubs value=1 <?=check($fdata['f_nosubs']==1);?>> <?=MSG_no;?>
<tr><td>
<?=MSG_f_nonewpic;?><td>
<input type=text name=f_nonewpic size=20 maxlength=20 value=<?=$fdata['f_nonewpic'];?>>
<tr><td>
<?=MSG_f_newpic;?><td>
<input type=text name=f_newpic size=20 maxlength=20 value=<?=$fdata['f_newpic'];?>>
<tr><td>
<?=MSG_f_bcode;?>?
<td>
<input type=radio name=f_bcode value=0 <? check($fdata['f_bcode']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_bcode value=1 <? check($fdata['f_bcode']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_smiles;?>?
<td>
<input type=radio name=f_smiles value=0 <? check($fdata['f_smiles']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_smiles value=1 <? check($fdata['f_smiles']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_lview;?>
<td>
<select name=f_lview><? set_select($levelselect,$fdata['f_lview']);?></select>
<tr><td>
<?=MSG_f_lread;?>
<td>
<select name=f_lread><? set_select($levelselect,$fdata['f_lread']);?></select>
<tr><td>
<?=MSG_f_lmoderate;?>
<td>
<select name=f_lmoderate><? set_select($levelselect,$fdata['f_lmoderate']);?></select>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="<?=$GLOBALS['newaction'];?>">
<input type=hidden name=m value="<?=$GLOBALS['newmodule'];?>">
<input type=hidden name=f_tpid value="<?=$fdata['f_tpid'];?>">
<input type=hidden name=fid value=<?=getvar("fid");?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function present_text(&$fdata) { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead"><?=$fdata['f_title'];?>
<tr><td class="basictable article">
<? if ($fdata['f_url']) {
?><?=MSG_pr_lastver;?>: <?=long_date_out($fdata['f_update']);?> <a href="index.php?f=<?=$fdata['f_id'];?>#download"><?=MSG_pr_goto_download;?></a><br><br><? } ?>
<?=textout($fdata['f_text'],1,$fdata['f_bcode'],$fdata['f_smiles']);?><br><br>
<? if ($fdata['f_url']) {
?><a name="download">
<div style="font-weight: bold; text-align: center">
<? if ($GLOBALS['opt_directlink']) { ?><a href="<?=$fdata['f_url'];?>"><?=MSG_pr_download;?> <?=$fdata['f_title'];?></a></div>
<? }
else { ?><a href="index.php?f=<?=$fdata['f_id'];?>&amp;a=do_get"><?=MSG_pr_download;?> <?=$fdata['f_title'];?></a></div><br>
<?=MSG_pr_downloads;?>: <?=$fdata['f_downloads'];?>
<? }
} ?><br><br><br>
</table><br>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
<div class="modlinks"><a href="index.php?f=<?=$fdata['f_id'];?>&amp;a=present_edit"><?=MSG_pr_edit;?></a></div><br>
<? }
}

function present_edit_form(&$fdata) { ?>
<form action="index.php" method=POST name="postform">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_pr_edit;?>
<tr><td width="33%">
<?=$msg;?>:
<? show_allowed($GLOBALS['inforum'],$GLOBALS['inuserlevel']);?>
<? if ($GLOBALS['inuser']['u_extform']) { ?><br>
<div style='display: none' id='smiles'><?=list_smiles("AddText",4);?></div>
<? } ?>
<td style="vertical-align: top"><div style='display: none; background-image: url("images/clean_small.png"); width: 98%' id='codes'>
<script type="text/javascript" src="langs/<?=$GLOBALS['inuser']['ln_file'];?>/post.js"></script>
<script type="text/javascript" src="styles/<?=$GLOBALS['inuser']['st_file'];?>/post.js"></script>
<? if ($GLOBALS['inuser']['u_extform']) { ?>
<script type="text/javascript">insertcodes();</script>
<? } ?></div><?
if ($GLOBALS['inuser']['u_extform']) { ?>
<textarea tabindex=3 name=p_text rows=15 cols=60 onselect="javascript:storeCaret(this);" onFocus="focused=true" onBlur="focused=false" onclick="javascript:storeCaret(this);" onkeyup="javascript:storeCaret(this);" onchange="javascript:storeCaret(this);" onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}"><? }
else { ?><textarea tabindex=3 name=p_text cols=60 rows=15 onFocus="focused=true" onBlur="focused=false" onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}"><? } ?><?=textarea($fdata['f_text']);?></textarea>

<tr><td><?=MSG_pr_url;?><br>
<div class="descr"><?=MSG_pr_url_descr;?></div>
<td><input type=text name=f_url size=40 maxlength=255 value="<?=$fdata['f_url'];?>">
<tr><td><?=MSG_pr_refresh;?>?
<td><input type=radio name=update value=1 checked><?=MSG_yes;?> &nbsp;
<input type=radio name=update value=><?=MSG_no;?>
<tr><td class="tablehead" colspan=2><input type=hidden name=a value="do_present_edit">
<input type=hidden name=f value="<?=$fdata['f_id'];?>"><input type=submit value=<?=MSG_save;?>>
</table></form>
<? }