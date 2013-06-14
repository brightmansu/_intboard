<?
function epedia_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_params;?>
<tr><td width="50%"><?=MSG_f_title;?>:
<td>
<input type=text name=f_title size=30 maxlength=60 value="<?=htmlspecialchars($fdata['f_title']);?>">
<tr><td><?=MSG_f_descr;?>:
<td>
<textarea name=f_descr rows=3 cols=30><?=textarea($fdata['f_descr']);?></textarea>
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
<tr><td>
<?=MSG_f_close;?>?
<td>
<input type=radio name=f_status value=0 <? check($fdata['f_status']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_status value=1 <? check($fdata['f_status']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_rules;?><td>
<textarea name=f_rules rows=12 cols=30><?=textarea($fdata['f_rules']);?></textarea>
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
<?=MSG_f_attachpics;?>?
<td>
<input type=radio name=f_attachpics value=0 <? check($fdata['f_attachpics']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_attachpics value=1 <? check($fdata['f_attachpics']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_stats;?>?
<td>
<input type=radio name=f_nostats value=1 <? check($fdata['f_nostats']==1);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_nostats value=0 <? check($fdata['f_nostats']==0);?>><?=MSG_yes;?> &nbsp;
<tr><td class="tablehead" colspan=2><?=MSG_f_levels;?>
<tr><td>
<?=MSG_f_lview;?>
<td>
<select name=f_lview><? set_select($levelselect,$fdata['f_lview']);?></select>
<tr><td>
<?=MSG_f_lread;?>
<td>
<select name=f_lread><? set_select($levelselect,$fdata['f_lread']);?></select>
<tr><td>
<?=MSG_f_ltopic;?>
<td>
<select name=f_ltopic><? set_select($levelselect,$fdata['f_ltopic']);?></select>
<tr><td>
<?=MSG_f_lattach;?>
<td>
<select name=f_lattach><? set_select($levelselect,$fdata['f_lattach']);?></select>
<tr><td>
<?=MSG_f_lhtml;?>
<td>
<select name=f_lhtml><? set_select($levelselect,$fdata['f_lhtml']);?></select>
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


function encyclo_letters($letters) {
if ($GLOBALS['inforum']['f_rules']=1) { ?><a class="rules" href="<?=build_url($GLOBALS['inforum'],'a=f_rules');?>"><?=MSG_f_rules;?></a><br><? } ?>
<? if ($GLOBALS['inuserid']>3) { ?><a class="descr" href="index.php?m=newpost&amp;a=do_mark_read&fs=<?=$GLOBALS['forum'];?>"><?=MSG_f_marktopics;?></a><? } ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$letters;?>
<? }

function encyclo_start() { ?>
<br><tr><td width="50%"><dl><?
}

function encyclo_entry(&$tdata) { ?>
<dt>
<a href="<?=build_url($tdata);?>"><?=$tdata['t_title'];?></a></dt>
<? if ($tdata['t_descr']) { ?><dd><?=$tdata['t_descr'];?></dd><? } ?>
<? }

function encyclo_newcol() { ?></dl><td><dl>
<? }

function encyclo_end($inforum,$autosub) { ?>
</dl></table><?
if ($GLOBALS['inuserid']>3) { ?>
<div class="descr">
<?=MSG_subscr_newtopics;?> - <?
if ($inforum) { ?><a href="<?=build_url($GLOBALS['inforum'],'a=do_sub&amp;tid=4294967294');?>"><?=MSG_enabled;?></a><? }
else { ?><a href="<?=build_url($GLOBALS['inforum'],'a=do_sub&amp;tid=4294967294&amp;sub=1');?>"><?=MSG_disabled;?></a><? } ?><br>
<?=MSG_subscr_auto;?> - <?
if ($autosub) { ?><a href="<?=build_url($GLOBALS['inforum'],'a=do_sub&amp;tid=4294967295');?>"><?=MSG_enabled;?></a><? }
else { ?><a href="<?=build_url($GLOBALS['inforum'],'a=do_sub&amp;tid=4294967295&amp;sub=1');?>"><?=MSG_disabled;?></a><? } ?>
</div>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
<br><div class="modlinks"><a href="<?=build_url($GLOBALS['inforum'],'m=epedia&amp;a=add_article');?>"><?=MSG_en_add;?></a>
</div>
<? }
}

function encyclo_article(&$pdata,$tdata) { ?>
<tr><td colspan=2 class="article"><dfn><?=$tdata['t_title'];?></dfn>
<? if ($tdata['t_descr']) { ?> - <?=$tdata['t_descr'];?><? } ?>
<hr>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?>
<? if ($pdata['p_attach']) { ?>
<div style="text-align: center">
<? if (strpos($pdata['file_type'],"image")===false) { ?>
<br><a href="file.php?fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>"><?=MSG_p_attachfile;?></a><? }
else { ?><br><a href="file.php?fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>"><img src="file.php?a=preview&fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>" alt="<?=$tdata['t_title'];?>"></a>
<? } ?></div><br>
<? } ?>
</table>
<br><div class="modlinks">
<a href="<?=build_url($tdata,'m=misc&amp;a=friend');?>"><?=MSG_t_mailtofriend;?></a>
 | <a href="<?=build_url($tdata,'a=do_print');?>"><?=MSG_t_print;?></a>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
 | <a href="<?=build_url($GLOBALS['inforum'],'a=add_article');?>"><?=MSG_en_add;?></a>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
 | <a href="<?=build_url($tdata,'a=edit_article');?>"><?=MSG_en_edit;?></a> |
<a href="<?=build_url($tdata,'a=delete_confirm');?>"><?=MSG_en_delete;?></a>
<? } ?></div>
<? }

function encyclo_form($tdata,$pdata,$newaction,$msg) {
$inforum=$GLOBALS['inforum'];
?><form name=postform action="index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$msg;?>
<? if ($GLOBALS['inuserid']<=3) quick_login_form(); ?>
<tr><td><?=MSG_en_title;?><td>
<input type=text name=t_title size=30 maxlength=60 value="<?=$tdata['t_title'];?>">
<tr><td><?=MSG_en_descr;?><td>
<textarea name=t_descr rows=4 cols=30><?=textarea($tdata['t_descr']);?></textarea>
<? if ($GLOBALS['opt_hurl']) { ?>
<tr><td><?=MSG_t_link;?>:<br><span class="descr"><?=MSG_t_link_descr;?>
<td><input tabindex=4 type=text name=t_link size=30 maxlength=60 value="<?=$tdata['t_link'];?>" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}">
<? } ?>
<? common_post($pdata,MSG_en_text);?>
<tr><td colspan=2 align=center><?=MSG_en_tag;?>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lattach']) { ?>
<tr><td><?=MSG_en_attach;?>:
<td><input type=hidden name=MAX_FILE_SIZE value="<?=$GLOBALS['opt_maxfileattach'];?>">
<input type=file name=attach size=30 maxlength=255> <span class="descr"><?=MSG_maxfile;?>: <?=max_file_attach($GLOBALS['opt_maxfileattach']);?> Kb</span>
<? } ?>
<tr><td>
<?=MSG_p_options;?>:
<td>
<? if ($inuserlevel>=$inforum['f_lhtml']) { ?>
<label><input type=checkbox value=1 name=p__html <? check($pdata['p__html']);?>><?=MSG_p_usehtml;?>?</label><br>
<? } ?>
<? if ($inforum['f_bcode']) { ?>
<label><input type=checkbox value=1 name=p__bcode <? check($pdata['p__bcode']);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_bcode">BoardCode</a></label><br>
<? }
if ($inforum['f_smiles']) { ?>
<label><input type=checkbox value=1 name=p__smiles <? check($pdata['p__smiles']==1);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_smiles"><?=MSG_p_smiles;?></a></label><br>
<? } if ($GLOBALS['inuserid']>3) { ?>
<input type=checkbox value=1 name=del_draft <? check($GLOBALS['action']=='edit_from_draft');?>><?=MSG_draft_senddel;?>
<? } ?>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=m value=epedia>
<input type=hidden name=a value="<?=$newaction;?>">
<input type=hidden name=f value="<?=$GLOBALS['forum'];?>">
<input type=hidden name=t value="<?=$GLOBALS['topic'];?>">
<input type=hidden name=p value="<?=$pdata['p_id'];?>">
<input type=submit title="<?=MSG_ctrlenter;?>" value="<?=MSG_post;?>">
<? if ($GLOBALS['inuserid']>3) { ?>
&nbsp; <input type=submit name=continue onClick="document.getElementById('draft_msg').style.display='';setTimeout('document.getElementById(\'draft_msg\').style.display=\'none\'',10000);this.form.del_draft.checked=true; return true" accesskey="d" title="<?=MSG_draft_save;?>" value="<?=MSG_todraft;?>">
<div class="descr" id="draft_msg" style="display: none"><?=MSG_dr_sent;?></div>
<? } ?>
</table></form>
<?  }

function article_print_form($tdata,&$pdata) { ?>
<H3><?=MSG_t_print;?></H3>
<H4>- &nbsp; <?=$GLOBALS['opt_title'];?> <?=$GLOBALS['opt_url'];?><br>
-- &nbsp;<?=$GLOBALS['inforum']['f_title'];?> <?=$GLOBALS['opt_url']."/".build_url($GLOBALS['inforum']);?><br>
--- <?=$GLOBALS['intopic']['t_title'];?> <?=$GLOBALS['opt_url']."/".build_url($GLOBALS['intopic']);?></H4><br><br>
<table width="90%" border=0 align=center><tr><td>
<hr><br>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?>
<br><br><br>
<hr>
</table>
<center><?=$GLOBALS['opt_copyright'];?><br>
<?=MSG_forum_powered;?><br>
&copy; <?=MSG_forum_copyright;?></center>
<? }