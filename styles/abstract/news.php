<?
function news_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_params;?>
<tr><td width="50%"><?=MSG_f_title;?>:
<td>
<input type=text name=f_title size=30 maxlength=60 value="<?=htmlspecialchars($fdata['f_title']);?>">
<tr><td><?=MSG_f_descr;?>:
<td>
<textarea name=f_descr rows=3 cols=30><?=$fdata['f_descr'];?></textarea>
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
<textarea name=f_rules rows=12 cols=30><?=htmlspecialchars($fdata['f_rules']);?></textarea>
<tr><td>
<?=MSG_f_nonewpic;?><td>
<input type=text name=f_nonewpic size=20 maxlength=20 value=<?=$fdata['f_nonewpic'];?>>
<tr><td>
<?=MSG_f_newpic;?><td>
<input type=text name=f_newpic size=20 maxlength=20 value=<?=$fdata['f_newpic'];?>>
<tr><td>
<?=MSG_f_rate;?>?
<td>
<input type=radio name=f_rate value=0 <? check($fdata['f_rate']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_rate value=1 <? check($fdata['f_rate']==1);?>><?=MSG_yes;?> &nbsp;
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
<?=MSG_f_lpost;?>
<td>
<select name=f_lpost><? set_select($levelselect,$fdata['f_lpost']);?></select>
<tr><td>
<?=MSG_f_ltopic;?>
<td>
<select name=f_ltopic><? set_select($levelselect,$fdata['f_ltopic']);?></select>
<tr><td>
<?=MSG_f_ledit;?>
<td>
<select name=f_ledit><? set_select($levelselect,$fdata['f_ledit']);?></select>
<tr><td>
<?=MSG_f_lvote;?>
<td>
<select name=f_lvote><? set_select($levelselect,$fdata['f_lvote']);?></select>
<tr><td>
<?=MSG_f_lsticky;?>
<td>
<select name=f_lsticky><? set_select($levelselect,$fdata['f_lsticky']);?></select>
<tr><td>
<?=MSG_f_lhtml;?>
<td>
<select name=f_lhtml><? set_select($levelselect,$fdata['f_lhtml']);?></select>
<tr><td>
<?=MSG_f_lpremod;?>
<td>
<select name=f_lpremod><? set_select($levelselect,$fdata['f_lpremod']);?></select>
<tr><td>
<?=MSG_f_ltopicpremod;?>
<td>
<select name=f_ltopicpremod><? set_select($levelselect,$fdata['f_ltopicpremod']);?></select>
<tr><td>
<?=MSG_f_lmoderate;?>
<td>
<select name=f_lmoderate><? set_select($levelselect,$fdata['f_lmoderate']);?></select>
<tr><td>
<?=MSG_f_lip;?>
<td>
<select name=f_lip><? set_select($levelselect,$fdata['f_lip']);?></select>
<tr><td>
<?=MSG_f_lattach;?>
<td>
<select name=f_lattach><? set_select($levelselect,$fdata['f_lattach']);?></select>
<input type=hidden name=f_attachpics value=1>

<tr><td class="tablehead" colspan=2>
<input type=hidden name=f_text value="">
<input type=hidden name=a value="<?=$GLOBALS['newaction'];?>">
<input type=hidden name=m value="<?=$GLOBALS['newmodule'];?>">
<input type=hidden name=f_tpid value="<?=$fdata['f_tpid'];?>">
<input type=hidden name=fid value=<?=getvar("fid");?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function news_list_start($pages) {
if ($GLOBALS['inuserid']>3 && $GLOBALS['opt_exttopic']) {
  if ($GLOBALS['userlast2']) { ?>
<div class="descr"><?=MSG_lastvisit;?> "<?=$GLOBALS['inforum']['f_title'];?>" <?=long_date_out($GLOBALS['userlast2']);?></div>
<? }
  else { ?><div class="descr"><?=MSG_welcome;?> "<?=$GLOBALS['inforum']['f_title'];?>".</div><?}
} ?>
<? if ($GLOBALS['inforum']['f_rules']=1) { ?><a class="rules" href="<?=build_url($GLOBALS['inforum'],'a=f_rules');?>"><?=MSG_f_rules;?></a><br><? } ?>
<? if ($GLOBALS['inuserid']>3) { ?><a class="descr" href="index.php?m=newpost&amp;fs=<?=$GLOBALS['forum'];?>&amp;a=do_mark_read"><?=MSG_f_marktopics;?></a><br><br><? } ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead">
<?=$GLOBALS['inforum']['f_title'];?><div style="text-align:right"><?=$pages;?></div>
<? }

function news_list_entry(&$ndata) {
?><tr><td>
<?=long_date_out($ndata['p__time']);?> - <b><?=$ndata['t_title'];?></b> &nbsp;
<span class="descr">(<?=MSG_n_addedby;?>: <?=user_out($ndata['p_uname'],$ndata['p_uid']);?>)</span><br><?
if ($ndata['p_attach']) {
  if (strpos($ndata['file_type'],"image")===false) {
    ?><br><a href="file.php?fid=<?=$ndata['p_attach'];?>&amp;key=<?=$ndata['file_key'];?>"><?=MSG_p_attachfile;?> (<?=urldecode($ndata['file_name']);?>, <?=$ndata['file_size'];?> <?=MSG_bytes;?>)</a><?
  }
  else { ?><br><a href="file.php?fid=<?=$ndata['p_attach'];?>&amp;key=<?=$ndata['file_key'];?>" target=_blank><img style="float:left; margin-right: 1em" src="file.php?a=preview&amp;fid=<?=$ndata['p_attach'];?>&amp;key=<?=$ndata['file_key'];?>" alt="<?=MSG_p_attachfile;?> (<?=urldecode($ndata['file_name']);?>, <?=$ndata['file_size'];?> <?=MSG_bytes;?>)"></a><?
  }
} ?>
<?=textout($ndata['p_text'],$ndata['p__html'],$ndata['p__bcode'],$ndata['p__smiles'],$ndata['n_tid'],$ndata['n_id']);?><br>
<div class="descr" style="text-align: right"><a href="<?=build_url($ndata);?>"><?=format_word($ndata['pcount'],MSG_comment1,MSG_comment2,MSG_comment3);?></a> | <a href="<?=build_url($ndata);?>"><?=MSG_n_comment;?></a></div>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
<br><a class="descr" href="<?=build_url($ndata,'m=news&amp;a=edit_news');?>"><?=MSG_n_edit;?></a>
<? } ?>
<? }

function news_list_noentries() { ?>
<tr><td><?=MSG_n_nonews;?>
<? }

function news_list_end($inforum,$autosub) { ?>
</table><?
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
<br><div class="modlinks"><a href="<?=build_url($GLOBALS['inforum'],'a=add_news');?>"><?=MSG_n_add;?></a>
<?
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
        if ($premodcount=get_premod()) { ?>
 | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=premod');?>"><?=MSG_f_premoderate;?></a> (<?=format_word($premodcount,MSG_p1,MSG_p2,MSG_p3);?>)
<? } ?> | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_banlist');?>"><?=MSG_f_banusers;?></a>
 | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_forum');?>"><?=MSG_f_moderate;?></a>
 | <a href="index.php?m=rss_lib&amp;a=list_rss_imports&amp;fid=<?=$GLOBALS['forum'];?>"><?=MSG_rss_import;?></a> 
<? } ?></div><?
}
}

function news_display(&$tdata,&$pdata,$rated) {
?>
<table class="innertable" width="100%" cellpadding=15 cellspacing=1><tr><td class="tablehead"><?=$tdata['t_title'];?>
<tr><td>
<div style="text-align: center"><b><?=textout($tdata['t_descr'],1,$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?></b></div><br>
<? if ($pdata['p_attach']) { if (strpos($pdata['file_type'],"image")===false) { ?>
<br><a href="file.php?fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>"><?=MSG_p_attachfile;?></a><? }
else { ?><br><div style="float:left"> &nbsp; <a href="file.php?fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>"><img src="file.php?a=preview&amp;fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>" alt=""></a> &nbsp; </div>
<? }
} ?>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?><br>
<br>
<div class="descr" style="text-align: right">
<?=MSG_n_addedby;?>: <?=user_out($pdata['p_uname'],$pdata['p_uid']);?></div><?
if ($tdata['t__rate'] && $GLOBALS['inforum']['f_rate']) { ?>
<hr align=center width="96%">
<? rating($rated,$tdata); ?>
<? } ?></table><br>
<div class="modlinks"><a href="<?=build_url($GLOBALS['intopic'],'a=do_print');?>"><?=MSG_t_print;?></a> |
<a href="<?=build_url($GLOBALS['intopic'],'m=misc&amp;a=friend');?>"><?=MSG_t_mailtofriend;?></a>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
 | <a href="<?=build_url($GLOBALS['inforum'],'a=add_news');?>"><?=MSG_n_add;?></a>
<? }

if (($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ledit'] && $GLOBALS['intopic']['t_author']==$GLOBALS['inuserid']) || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
 | <a href="<?=build_url($tdata,'a=edit_news');?>"><?=MSG_n_edit;?></a>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
 |
<a href="<?=build_url($tdata,'a=delete_confirm');?>"><?=MSG_n_delete;?></a> |
<a href="<?=build_url($tdata,'m=moderate&amp;a=split_topic');?>"><?=MSG_n_split;?></a>
<? } ?></div><br>
<? }

function news_edit_form(&$tdata,&$pdata,$newaction,$msg) {
$inforum=$GLOBALS['inforum'];
?><form name=postform action="index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$msg;?>
<? if ($GLOBALS['inuserid']<=3) quick_login_form(); ?>
<tr><td><?=MSG_n_title;?><td>
<input type=text name=t_title size=30 maxlength=60 value="<?=$tdata['t_title'];?>">
<tr><td><?=MSG_n_descr;?><td>
<textarea name=t_descr rows=6 cols=30><?=$tdata['t_descr'];?></textarea>
<? if ($GLOBALS['opt_hurl']) { ?>
<tr><td><?=MSG_t_link;?>:<br><span class="descr"><?=MSG_t_link_descr;?>
<td><input tabindex=4 type=text name=t_link size=30 maxlength=60 value="<?=$pdata['t_link'];?>"
onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}">
<? } ?>
<? common_post($pdata,MSG_n_text);?>
<? if ($GLOBALS['inuserlevel']>=$inforum['f_lattach']) { ?>
<tr><td><?=MSG_n_attach;?>:
<td><input type=hidden name=MAX_FILE_SIZE value="<?=$GLOBALS['opt_maxfileattach'];?>">
<input type=file name=attach size=30 maxlength=255> <span class="descr"><?=MSG_maxfile;?>: <?=max_file_attach($GLOBALS['opt_maxfileattach']);?> Kb
<? if ($newaction=="do_edit" && $pdata['p_attach']!=0) { ?>
<br><input type=checkbox name=delattach value=1><?=MSG_p_delattach;?>
<? }
 } ?>
<tr><td>
<?=MSG_p_options;?>:<br>
<div class="descr"><?=MSG_n_optionsdescr;?></div>
<td>
<? if ($GLOBALS['inuserlevel']>=$inforum['f_lhtml']) { ?>
<label><input type=checkbox value=1 name=p__html <? check($pdata['p__html']);?>><?=MSG_p_usehtml;?>?</label><br>
<? } ?>
<? if ($inforum['f_bcode']) { ?>
<label><input type=checkbox value=1 name=p__bcode <? check($pdata['p__bcode']);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_bcode">BoardCode</a></label><br>
<? }
if ($inforum['f_smiles']) { ?>
<label><input type=checkbox value=1 name=p__smiles <? check($pdata['p__smiles']==1);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_smiles"><?=MSG_p_smiles;?></a></label><br>
<? }
if ($newaction=="do_topic" && $inforum['f_rate']) { ?>
<label><input type=checkbox value=1 name=t__rate checked><?=MSG_t_rate;?></label><br>
<? }
if ($GLOBALS['inuserid']>3) { ?>
<input type=checkbox value=1 name=del_draft <? check($GLOBALS['action']=='edit_from_draft');?>><?=MSG_draft_senddel;?>
<? } ?>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="<?=$newaction;?>">
<input type=hidden name=f value="<?=$GLOBALS['forum'];?>">
<input type=hidden name=t value="<?=$GLOBALS['topic'];?>">
<input type=submit title="<?=MSG_ctrlenter;?>" value="<?=MSG_post;?>"> &nbsp; 
<input type=submit accesskey="p" name="preview" value="<?=MSG_preview;?>"> 
<? if ($GLOBALS['inuserid']>3) { ?>
&nbsp; <input type=submit name=continue onClick="document.getElementById('draft_msg').style.display='';setTimeout('document.getElementById(\'draft_msg\').style.display=\'none\'',10000);this.form.del_draft.checked=true; return true" accesskey="d" title="<?=MSG_draft_save;?>" value="<?=MSG_todraft;?>">
<div class="descr" id="draft_msg" style="display: none"><?=MSG_dr_sent;?></div>
<? } ?></table></form>
<?  }

function news_print_form(&$tdata,&$pdata) { ?>
<H4>- &nbsp; <?=$GLOBALS['opt_title'];?> <?=$GLOBALS['opt_url'];?><br>
-- &nbsp;<?=$GLOBALS['inforum']['f_title'];?> <?=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['inforum']);?></H4><br><br>
<table width="90%" border=0 align=center><tr><td>
<H4><?=long_date_out($pdata['p__time']);?> - <?=$GLOBALS['intopic']['t_title'];?><br>
<?=$GLOBALS['opt_url'].'/'.build_url($GLOBALS['intopic']);?></H4><br>
<tr><td>
<hr><br>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles']);?>
<br><br><br>
<hr>
</table>
<center><?=$GLOBALS['opt_copyright'];?><br>
<?=MSG_forum_powered;?><br>
&copy; <?=MSG_forum_copyright;?> http://intboard.ru</center>
<? }
