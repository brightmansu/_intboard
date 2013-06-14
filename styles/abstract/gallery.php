<?
function gallery_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
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

<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="<?=$GLOBALS['newaction'];?>">
<input type=hidden name=m value="<?=$GLOBALS['newmodule'];?>">
<input type=hidden name=f_tpid value="<?=$fdata['f_tpid'];?>">
<input type=hidden name=fid value=<?=getvar("fid");?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function gallery_list_start($pages,$rows,$owner) {
if ($GLOBALS['opt_exttopic']) { ?>
<form class="descr" style="text-align: right" action="<?=build_url($GLOBALS['inforum']);?>" method=GET>
<?=MSG_f_sortby;?> <select name=o><?=set_select("<option value=posttime>".MSG_a_byadddate.
"<option value=posttime>".MSG_ph_bylastcomment."<option value=t_title>".MSG_f_bytitle.
"<option value=t__views>".MSG_f_byviews."<option value=pcount>".MSG_ph_bycomments.
"<option value=trating>".MSG_f_byrating."<option value=visited>".MSG_f_byvisit,getvar("o"));?></select>
, <?=MSG_showby;?> <select name=desc><?=set_select("<option value=\"\">".MSG_asc."<option value=desc>".MSG_desc,getvar("desc"));?></select>
<input type=hidden name=f value=<?=$GLOBALS['forum'];?>> <input type=submit value="<?=MSG_show;?>"></form>
<?
if ($GLOBALS['inuserid']>3) {
  if ($GLOBALS['userlast2']) { ?>
<div class="descr"><?=MSG_lastvisit;?> "<?=$GLOBALS['inforum']['f_title'];?>" <?=long_date_out($GLOBALS['userlast2']);?></div>
<? }
  else { ?><div class="descr"><?=MSG_welcome;?> "<?=$GLOBALS['inforum']['f_title'];?>".</div><?}
} ?><br>
<? if ($GLOBALS['inforum']['f_rules']=1) { ?><a class="rules" href="<?=build_url($GLOBALS['inforum'],'a=f_rules');?>"><?=MSG_f_rules;?></a><br><? } ?>
<? if ($GLOBALS['inuserid']>3) { ?><a class="descr" href="index.php?m=newpost&amp;fs=<?=$GLOBALS['forum'];?>&amp;a=do_mark_read"><?=MSG_f_marktopics;?></a><br><br><? } ?>

<? } ?><br>
<?=MSG_gallery_owner;?>: <?=user_out($owner[0],$owner[1]);?> <?
if ($owner[2]!=1 && $GLOBALS['inuserid']!=$owner[1]) { 
  ?>(<a onClick="return confirm('<?=MSG_p_friend_warn1.' '.$owner[0].' '.MSG_p_friend_warn2;?>')" href="index.php?m=addrbook&amp;a=do_friend&amp;uid=<?=$owner[0];?>"><?=MSG_p_friend;?></a>)<? 
} ?>
<table class="innertable" width="100%" cellspacing=1><tr>
<td class="tablehead">
<?=$GLOBALS['inforum']['f_title'];?>
<? if ($pages) { ?>
<div style="text-align: right; float: right; width: 25%"><?=$pages;?></div>
<? } ?>
<tr><td style="text-align:center">
<? }

function gallery_list_entry(&$phdata) { ?>
<div style="padding: 8px; display: table-cell; float: left; height: <?=$GLOBALS['opt_photo_thumb_y']+100;?>px">
<a href="<?=build_url($phdata);?>">
<img <?
if (is_new($phdata['visited'],$phdata['lastpost'])) {
?>border=5 <?
} ?>src="file.php?a=thumb&amp;ph=<?=$phdata['ph_id'];?>&amp;key=<?=$phdata['ph_key'];?>" height="<?=intval($GLOBALS['opt_photo_thumb_y']);?>" hspace=5 vspace=5 alt="<?=$phdata['t_title'];?>"></a><br>
<a href="<?=build_url($phdata);?>"><?=$phdata['t_title'];?></a>
<div class="descr" style="white-space: nowrap"><?=MSG_ph_publisher;?>: <?=user_out($phdata['u__name'],$phdata['u_id']);?><br>
<?=MSG_t_views;?>/<?=MSG_ph_comments;?>:
<?=intval($phdata['t__views']);?>/<?=intval($phdata['pcount']-1);?><br>
<?=MSG_t_rating;?>: <?=floatval($phdata['trating']);?>
</div>
</div>
<? }

function gallery_list_end($pages,$inforum,$autosub) { ?>
<tr><td class="tablehead" style="text-algin: right"><?=$pages;?>
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
<br><div class="modlinks"><a href="<?=build_url($GLOBALS['inforum'],'a=add_photo');?>"><?=MSG_ph_add;?></a>
<?
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
  if ($premodcount=get_premod()) { ?>
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=premod');?>"><?=MSG_f_premoderate;?></a> (<?=format_word($premodcount,MSG_p1,MSG_p2,MSG_p3);?>)
<? } ?> | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_banlist');?>"><?=MSG_f_banusers;?></a>
 | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_forum');?>"><?=MSG_f_moderate;?></a>
<? }
} ?>
</div>
<? }

function gallery_display(&$tdata,&$phdata,&$pdata,$rated,$thumbs,$curpos,$owner) {
?>
<?=MSG_gallery_owner;?>: <?=user_out($owner[0],$owner[1]);?> <?
if ($owner[2]!=1 && $GLOBALS['inuserid']!=$owner[1]) { 
  ?>(<a onClick="return confirm('<?=MSG_p_friend_warn1.' '.$owner[0].' '.MSG_p_friend_warn2;?>')" href="index.php?m=addrbook&amp;a=do_friend&amp;uid=<?=$owner[0];?>"><?=MSG_p_friend;?></a>)<? 
} ?>
<table width="100%" cellpadding=5><tr><td><H3 style="text-align: center"><?=$tdata['t_title'];?></H3>
<tr><td style="text-align: center">
<img src="file.php?a=photo&amp;ph=<?=$phdata['ph_id'];?>&amp;key=<?=$phdata['ph_key'];?>" vspace=8 alt="<?=$tdata['t_title'];?>"><br>
<?=MSG_ph_publisher;?>: <?=user_out($pdata['p_uname'],$pdata['p_uid']);?><br>
<br>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?>
<?
if ($tdata['t__rate'] && $GLOBALS['inforum']['f_rate']) { ?>
<tr><td><br>
<? rating($rated,$tdata); ?>
<? } ?></table>
<div style="height: <?=$GLOBALS['opt_photo_thumb_y']+28;?>px;" class="photo_nav" id="photo_nav">
<? $count=count($thumbs);
for ($i=0; $i<$count; $i++) {
  if ($i>=$curpos-2 && $i<=$curpos+2 && $curpos!=$i) { ?><a href="<?=build_url($thumbs[$i]);?>"><img
  src="file.php?a=thumb&amp;ph=<?=$thumbs[$i]['ph_id'];?>&amp;key=<?=$thumbs[$i]['ph_key'];?>" alt="<?=$thumbs[$i]['t_title'];?>" title="<?=$thumbs[$i]['t_title'];?>" style="height: <?=$GLOBALS['opt_photo_thumb_y'];?>px;" class="nav1"></a>&nbsp;<? }
  elseif ($curpos==$i) { ?><img id="img_act" src="file.php?a=thumb&amp;ph=<?=$thumbs[$i]['ph_id'];?>&amp;key=<?=$thumbs[$i]['ph_key'];?>" alt="<?=$thumbs[$i]['t_title'];?>" style="height: <?=$GLOBALS['opt_photo_thumb_y'];?>px;" class="nav2">&nbsp;<? }
  else { ?><a href="<?=build_url($thumbs[$i]);?>"><img src="images/preview.gif" alt="<?=$thumbs[$i]['t_title'];?>" onmouseover="this.src='file.php?a=thumb&amp;ph=<?=$thumbs[$i]['ph_id'];?>&amp;key=<?=$thumbs[$i]['ph_key'];?>'" style="height: <?=$GLOBALS['opt_photo_thumb_y'];?>px;" class="nav3"></a>&nbsp;<? }
}
?>
</div><br>
<div class="modlinks"><a href="<?=build_url($tdata,'a=do_print');?>"><?=MSG_a_print;?></a> |
<a href="<?=build_url($tdata,'m=misc&amp;a=friend&amp;');?>"><?=MSG_t_mailtofriend;?></a>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
 | <a href="<?=build_url($GLOBALS['inforum'],'a=add_photo');?>"><?=MSG_ph_add;?></a>
<? }
if (($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ledit'] && $GLOBALS['intopic']['t_author']==$GLOBALS['inuserid']) || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
 | <a href="<?=build_url($tdata,'a=edit_photo');?>"><?=MSG_ph_edit;?></a>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
| <a href="<?=build_url($tdata,'a=delete_confirm');?>"><?=MSG_ph_delete;?></a> |
<a href="<?=build_url($tdata,'m=moderate&amp;a=split_topic');?>"><?=MSG_ph_split;?></a>
<? } ?></div><br>
<script type="text/javascript"><!--
if (document.getElementById("img_act")) {
      var source = document.getElementById("img_act");
      var ifrm = document.getElementById("photo_nav");
      ifrm.scrollLeft = source.offsetLeft - ifrm.offsetWidth/2 + Math.round(source.offsetWidth/2);
}
//--></script>
<? }

function gallery_discuss_form(&$pdata,$msg,$newaction) {
$inforum=$GLOBALS['inforum'];
?><form name=postform action="index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$msg;?>
<? if ($GLOBALS['inuserid']<=3) quick_login_form(); ?>
<? if ($GLOBALS['inforum']['f_rules']) { ?>
<tr><td colspan=2><?=MSG_ph_commentrules;?> <a href="<?=build_url($GLOBALS['inforum'],'','f_rules','a');?>"><?=MSG_a_ruleslink;?></a>
<? }
common_post($pdata,MSG_a_discusstext);?>
<tr><td><?=MSG_ph_discussoptions;?><td>
<? if ($GLOBALS['inuserlevel']>=$inforum['f_lhtml']) { ?>
<label><input type=checkbox value=1 name=p__html <? check($pdata['p__html']);?>><?=MSG_p_usehtml;?>?</label><br>
<? }
if ($inforum['f_bcode']) { ?>
<label><input type=checkbox value=1 name=p__bcode <? check($pdata['p__bcode']);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_bcode">BoardCode</a></label><br>
<? }
if ($inforum['f_smiles']) { ?>
<label><input type=checkbox value=1 name=p__smiles <? check($pdata['p__smiles']==1);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_smiles"><?=MSG_p_smiles;?></a></label><br>
<? }
if ($GLOBALS['inuserid']>3) { ?>
<input type=checkbox value=1 name=del_draft <? check($GLOBALS['action']=='edit_from_draft');?>><?=MSG_draft_senddel;?>
<? } ?>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value=<?=$newaction;?>><input type=hidden name=t value=<?=$GLOBALS['topic'];?>>
<input type=hidden name=p value="<?=getvar("p");?>"><input type=submit title="<?=MSG_ctrlenter;?>" value="<?=MSG_post;?>">
<? if ($GLOBALS['inuserid']>3) { ?>
&nbsp; <input type=submit name=continue onClick="document.getElementById('draft_msg').style.display='';setTimeout('document.getElementById(\'draft_msg\').style.display=\'none\'',10000);this.form.del_draft.checked=true; return true" accesskey="d" title="<?=MSG_draft_save;?>" value="<?=MSG_todraft;?>">
<div class="descr" id="draft_msg" style="display: none"><?=MSG_dr_sent;?></div>
<? } ?></table></form>
<? }

function gallery_edit_form(&$tdata,&$pdata,$newaction,$msg) {
$inforum=$GLOBALS['inforum'];
?><form name=postform action="index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$msg;?>
<? if ($GLOBALS['inuserid']<=3) quick_login_form(); ?>
<tr><td width="33%"><?=MSG_ph_title;?><td>
<input type=text name=t_title size=30 maxlength=60 value="<?=$tdata['t_title'];?>">
<? if ($GLOBALS['opt_hurl']) { ?>
<tr><td><?=MSG_t_link;?>:<br><span class="descr"><?=MSG_t_link_descr;?>
<td><input tabindex=4 type=text name=t_link size=30 maxlength=60 value="<?=$pdata['t_link'];?>" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}">
<? } ?>
<? common_post($pdata,MSG_a_text);?>
<tr><td><?=MSG_ph_file;?><td>
<input type=file name=photo> <span class="descr"><?=MSG_maxfile;?>: <?=max_file_attach($GLOBALS['opt_photo_maxsize']);?> Kb</span>
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
<label><input type=checkbox value=1 name="t__rate" checked><?=MSG_t_israted;?></label><br>
<? }
if ($GLOBALS['inuserid']>3) { ?>
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

function gallery_print_form(&$tdata,&$phdata,&$pdata) { ?>
<H3><?=MSG_t_print;?></H3>
<H4>- &nbsp; <?=$GLOBALS['opt_title'];?> <?=$GLOBALS['opt_url'];?><br>
-- &nbsp;<?=$GLOBALS['inforum']['f_title'];?> <?=$GLOBALS['opt_url']."/".build_url($GLOBALS['inforum']);?><br>
--- <?=$GLOBALS['intopic']['t_title'];?> <?=$GLOBALS['opt_url']."/".build_url($GLOBALS['intopic']);?></H4><br><br>
<table width="90%" border=0 align=center><tr><td style="text-align: center">
<img src="file.php?a=photo&amp;ph=<?=$phdata['ph_id'];?>&amp;key=<?=$phdata['ph_key'];?>" width="<?=$GLOBALS['opt_photo_size_x'];?>" vspace=10>
<br>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles']);?>
<br><br><br>
<hr>
</table>
<center><?=$GLOBALS['opt_copyright'];?><br>
<?=MSG_forum_powered;?><br>
&copy; <?=MSG_forum_copyright;?> http://intboard.ru</center>
<? }