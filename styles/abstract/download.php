<? function download_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
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
<select name=f_ctid><?=set_select($catselect,$_POST['ctid']);?></select>
<tr><td width="50%"><?=MSG_f_dldir;?>:<br>
<?=MSG_f_dldir_descr;?>
<td>
<input type=text name=f_text size=30 maxlength=60 value="<?=$fdata['f_text'];?>">
<tr><td width="50%"><?=MSG_f_dlurl;?>:
<td>
<input type=text name=f_url size=30 maxlength=60 value="<?=$fdata['f_url'];?>">
<tr><td>
<?=MSG_f_show_in;?>:
<td>
<select name=f_parent><?=set_select($fcontainer,$fdata['f_parent']);?></select>
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
<input type=hidden name=f_attachpics value=1 <? check($fdata['f_attachpics']==1);?>>

<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="<?=$GLOBALS['newaction'];?>">
<input type=hidden name=m value="<?=$GLOBALS['newmodule'];?>">
<input type=hidden name=f_tpid value="<?=$fdata['f_tpid'];?>">
<input type=hidden name=fid value=<?=getvar("fid");?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function soft_list_start($pages) {
if ($GLOBALS['opt_exttopic']) {
  if ($GLOBALS['inuserid']>3) {
    if ($GLOBALS['userlast2']) { ?>
  <div class="descr"><?=MSG_lastvisit;?> "<?=$GLOBALS['inforum']['f_title'];?>" <?=long_date_out($GLOBALS['userlast2']);?></div>
  <? }
    else { ?><div class="descr"><?=MSG_welcome;?> "<?=$GLOBALS['inforum']['f_title'];?>".</div><?}
  }
  if ($GLOBALS['inforum']['f_rules']) { ?><a class="rules" href="<?=build_url($GLOBALS['inforum'],'a=f_rules');?>"><?=MSG_f_rules;?></a><br><? }
  if ($GLOBALS['inuserid']>3) { ?><a class="descr" href="index.php?m=newpost&amp;fs=<?=$GLOBALS['forum'];?>&amp;a=do_mark_read"><?=MSG_f_marktopics;?></a><br><br><? } ?>

<form class="descr" style="text-align: right" action="<?=build_url($GLOBALS['inforum']);?>" method=GET>
<?=MSG_f_sortby;?> <select name=o><?=set_select("<option value=posttime>".MSG_dl_byadddate.
"<option value=posttime>".MSG_dl_bylastcomment."<option value=t_title>".MSG_f_bytitle.
"<option value=dl__downloads>".MSG_dl_bydownloads."<option value=t__views>".MSG_f_byviews.
"<option value=pcount>".MSG_dl_bycomments.
"<option value=trating>".MSG_f_byrating."<option value=visited>".MSG_f_byvisit,getvar("o"));?></select>
, <?=MSG_showby;?> <select name=desc><?=set_select("<option value=\"\">".MSG_asc."<option value=desc>".MSG_desc,getvar("desc"));?></select>
<input type=hidden name=f value=<?=$GLOBALS['forum'];?>> <input type=submit value="<?=MSG_show;?>"></form>
<? } ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=5>
<?=MSG_dl_list;?> "<?=$GLOBALS['inforum']['f_title'];?>"
<div style="float: right; text-align:right; width: 25%"><?=$pages;?></div>

<tr><td class="tablehead" colspan=2><?=MSG_dl_title;?>
<td class="tablehead" width="20%"><?=MSG_dl_download;?>
<td class="tablehead" width="10%"><?=MSG_t_views;?>
<td class="tablehead" width="5%"><?=MSG_t_rating;?>
<? }

function soft_list_entry(&$dldata) {
?><tr><td style="text-align:center" width="10%">
<? $dir="styles/".$GLOBALS['inuser']['st_file'];
if (is_new($dldata['visited'],$dldata['lastpost'])) { $pic="$dir/new.png"; $alt="NEW!"; }
else $pic="$dir/nonew.png"; ?>
<img src="<?=$pic;?>" height=20 width=20 alt="<?=$alt;?>">
<td><h5><a href="<?=build_url($dldata);?>"><?=$dldata['t_title'];?></a></h5><?=$dldata['t_descr'];?>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
<br><a class="descr" href="<?=build_url($dldata,'m=download&amp;a=edit_program');?>"><?=MSG_dl_edit;?></a>
<? } ?>

<td style="text-align:center"><? if ($GLOBALS['opt_directlink']) { ?><a href="<?=$dldata['dl_url'];?>"><?=MSG_dl_download;?></a> (<?=$dldata['dl_size'];?>)<? }
else { ?><a href="<?=build_url($dldata,'m=download&amp;a=do_get');?>"><?=MSG_dl_download;?></a> (<?=$dldata['dl_size'];?>)<? } ?><br>
<?=short_date_out($dldata['posttime']);?>
<td style="text-align:center"><?=$dldata['t__views'];?> / <?=$dldata['pcount']-1;?><td style="text-align:center"><?
if ($dldata['trating']) { ?><?=$dldata['trating'];?><? }
else { ?><?=MSG_none;?><? } ?>
<? }

function soft_list_end($pages,$inforum,$autosub) { ?>
<tr><td colspan=5 class="tablehead" style="text-algin: right"><?=$pages;?>
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
<br><div class="modlinks"><a href="<?=build_url($GLOBALS['inforum'],'a=add_program');?>"><?=MSG_dl_add;?></a>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
  if ($premodcount=get_premod()) { ?>
 | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=premod');?>"><?=MSG_f_premoderate;?></a> (<?=format_word($premodcount,MSG_p1,MSG_p2,MSG_p3);?>)
<? } ?> | <a href="<?=build_url($GLOBALS['inforum'],'a=download_check');?>"><?=MSG_dl_check;?></a>
 | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_banlist');?>"><?=MSG_f_banusers;?></a> |
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_forum');?>"><?=MSG_f_moderate;?></a>
<? } ?>
</div><br>
<?
}

function soft_display(&$tdata,&$dldata,&$pdata,$rated) {
?>
<table class="innertable" width="100%" cellpadding=15 cellspacing=1><tr><td class="tablehead"><?=$tdata['t_title'];?>
<tr><td>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?><br>
<? if ($dldata['dl_size']) { ?><?=MSG_dl_size;?>: <?=$dldata['dl_size'];?><? } ?>
<br><br>
<div style="text-align: center">
<? if (!$GLOBALS['opt_directlink']) { ?>
<?=MSG_dl_count;?>: <?=$dldata['dl__downloads'];?><br><br>
<? } ?>
<? if ($GLOBALS['opt_directlink']) { ?>
<a href="<?=$dldata['dl_url'];?>"><?=MSG_dl_download;?> "<?=$tdata['t_title'];?>"</a><br><br><? }
else { ?><a href="<?=build_url($tdata,'a=do_get');?>"><?=MSG_dl_download;?> "<?=$tdata['t_title'];?>"</a><br><br><? } ?>
<? if ($dldata['dl_homepage']) { ?>
<a href="<?=$dldata['dl_homepage'];?>" target=_blank><?=MSG_dl_homepage;?></a><br><br>
<a href="<?=build_url($tdata,'m=moderate&amp;a=complain');?>"><?=MSG_dl_complain;?></a><? } ?><br><br>
</div>
<? if ($pdata['p_attach']) { ?>
<div style="text-align: center"><?=MSG_dl_screenshot;?>:
<? if (strpos($pdata['file_type'],"image")===false) { ?>
<br><a href="file.php?fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>"><?=MSG_p_attachfile;?></a><? }
else { ?><br><a href="file.php?fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>"><img src="file.php?a=preview&amp;fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>" alt="<?=$tdata['t_title'];?>"></a>
<? } ?></div><br>
<? } ?>
<div class="descr"><?=MSG_dl_homepagedescr;?></div>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?><div class="descr"><?=MSG_dl_publisher;?>: <?=user_out($pdata['p_uname'],$pdata['p_uid']);?></div><? } ?>
<? if ($tdata['t__rate'] && $GLOBALS['inforum']['f_rate']) { ?>
<hr align=center width="96%">
<? rating($rated,$tdata); ?>
<? } ?></table><br>
<div class="modlinks">
<a href="<?=build_url($tdata,'m=misc&amp;a=friend');?>"><?=MSG_t_mailtofriend;?></a>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
 | <a href="<?=build_url($GLOBALS['inforum'],'a=add_program');?>"><?=MSG_dl_add;?></a>
<? }
if (($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ledit'] && $GLOBALS['intopic']['t_author']==$GLOBALS['inuserid']) || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
 | <a href="<?=build_url($tdata,'a=edit_program');?>"><?=MSG_dl_edit;?></a>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
 | <a href="<?=build_url($tdata,'a=delete_confirm');?>"><?=MSG_dl_delete;?></a> |
<a href="<?=build_url($tdata,'m=moderate&amp;a=split_topic');?>"><?=MSG_dl_split;?></a>
<? } ?></div>
<? }

function soft_edit_form(&$tdata,&$dldata,&$pdata,$newaction,$msg,$forumlist="") {
$inforum=$GLOBALS['inforum'];
?><form name=postform action="index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$msg;?>
<? if ($GLOBALS['inuserid']<=3) quick_login_form(); ?>
<tr><td><?=MSG_dl_title;?><td>
<input type=text name=t_title size=30 maxlength=60 value="<?=$tdata['t_title'];?>">
<tr><td><?=MSG_dl_shortdescr;?><td>
<textarea name=t_descr rows=4 cols=30><?=textarea($tdata['t_descr']);?></textarea>
<? if ($GLOBALS['opt_hurl']) { ?>
<tr><td><?=MSG_t_link;?>:<br><span class="descr"><?=MSG_t_link_descr;?>
<td><input tabindex=4 type=text name=t_link size=30 maxlength=60 value="<?=$tdata['t_link'];?>" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}">
<? } ?>
<? common_post($pdata,MSG_dl_fulldescr);?>
<tr><td><?=MSG_dl_url;?>
<td><input type=text name=dl_url size=40 maxlength=255 value="<?=$dldata['dl_url'];?>">
<tr><td><?=MSG_dl_homepage;?>
<td><input type=text name=dl_homepage size=40 maxlength=255 value="<?=$dldata['dl_homepage'];?>">
<tr><td><?=MSG_dl_size;?>
<td><input type=text name=dl_size size=10 maxlength=10 value="<?=$dldata['dl_size'];?>">
<? if ($GLOBALS['inuserlevel']>=$inforum['f_lattach']) {
if ($inforum['f_text'] && $newaction!="do_edit") { ?>
<tr><td><?=MSG_dl_file;?>:<br>
<span class="descr"><?=MSG_dl_filedescr;?></span>
<td><input type=hidden name=MAX_FILE_SIZE value="<?=$GLOBALS['opt_maxfileattach'];?>">
<input type=file name=dlfile size=30 maxlength=255> <span class="descr"><?=MSG_maxfile;?>: <?=max_file_attach($GLOBALS['opt_maxfileattach']);?> Kb</span>
<? } ?>
<tr><td><?=MSG_dl_attach;?>:
<td><input type=hidden name=MAX_FILE_SIZE value="<?=$GLOBALS['opt_maxfileattach'];?>">
<input type=file name=attach size=30 maxlength=255> <span class="descr"><?=MSG_maxfile;?>: <?=max_file_attach($GLOBALS['opt_maxfileattach']);?> Kb</span>
<? } ?>
<tr><td>
<?=MSG_p_options;?>:
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
<label><input type=checkbox value=1 name=t__rate checked><?=MSG_t_israted;?></label><br>

<tr><td><?=MSG_dl_discuswhere;?>
<td><select name=fid><option value=""><?=MSG_dl_here;?><?=$forumlist;?></select>
<? }
if ($newaction=="do_edit" && $pdata['p_attach']!=0) { ?>
<input type=checkbox name=delattach value=1><?=MSG_dl_delattach;?>
<? } if ($GLOBALS['inuserid']>3) { ?>
<input type=checkbox value=1 name=del_draft <? check($GLOBALS['action']=='edit_from_draft');?>><?=MSG_draft_senddel;?>
<? } ?>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="<?=$newaction;?>">
<input type=hidden name=f value="<?=$GLOBALS['forum'];?>">
<input type=hidden name=t value="<?=$GLOBALS['topic'];?>">
<input type=submit title="<?=MSG_ctrlenter;?>" value="<?=MSG_post;?>">
<? if ($GLOBALS['inuserid']>3) { ?>
&nbsp; <input type=submit name=continue onClick="document.getElementById('draft_msg').style.display='';setTimeout('document.getElementById(\'draft_msg\').style.display=\'none\'',10000);this.form.del_draft.checked=true; return true" accesskey="d" title="<?=MSG_draft_save;?>" value="<?=MSG_todraft;?>">
<div class="descr" id="draft_msg" style="display: none"><?=MSG_dr_sent;?></div>
<? } ?></table></form>
<?  }

function soft_check_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3>
<?=MSG_dl_check;?> "<?=$GLOBALS['inforum']['f_title'];?>"
<tr><td class="tablehead" width="40%"><?=MSG_dl_title;?>
<td class="tablehead" width="40%"><?=MSG_dl_url;?>
<td class="tablehead" width="20%"><?=MSG_dl_result;?>
<? }

function soft_check_entry($dlentry,$result) { ?>
<tr><td><?=$dlentry['t_title'];?><td><?=$dlentry['dl_url'];?>
<td><? if ($result==-1) $resmsg=MSG_dl_noconnect;
elseif ($result==200) $resmsg="$result - ".MSG_dl_ok;
elseif ($result==301 || $result==302) $resmsg = "<b>$result - ".MSG_dl_moved."</b>";
elseif ($result==404) $resmsg = "<b>$result - ".MSG_dl_notfound."</b>";
else $resmsg = "<b>$result - ".MSG_dl_servererror."</b>";
?><?=$resmsg;?>
<? }

function soft_check_end() { ?>
</table>
<? }