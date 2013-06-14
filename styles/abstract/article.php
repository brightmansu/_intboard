<?
function article_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
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
<?=MSG_f_lpremod;?>
<td>
<select name=f_lpremod><? set_select($levelselect,$fdata['f_lpremod']);?></select>
<tr><td>
<?=MSG_f_ltopicpremod;?>
<td>
<select name=f_ltopicpremod><? set_select($levelselect,$fdata['f_ltopicpremod']);?></select>
<tr><td>
<?=MSG_f_lhtml;?>
<td>
<select name=f_lhtml><? set_select($levelselect,$fdata['f_lhtml']);?></select>
<tr><td>
<?=MSG_f_lmoderate;?>
<td>
<select name=f_lmoderate><? set_select($levelselect,$fdata['f_lmoderate']);?></select>
<tr><td>
<?=MSG_f_lip;?>
<td>
<select name=f_lip><? set_select($levelselect,$fdata['f_lip']);?></select>

<tr><td class="tablehead" colspan=2>
<input type=hidden name=f_text value="">
<input type=hidden name=a value="<?=$GLOBALS['newaction'];?>">
<input type=hidden name=m value="<?=$GLOBALS['newmodule'];?>">
<input type=hidden name=f_tpid value="<?=$fdata['f_tpid'];?>">
<input type=hidden name=fid value=<?=getvar("fid");?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function article_list_start($pages) {
  if ($GLOBALS['inuserid']>3) {
  if ($GLOBALS['userlast2']) { ?>
      <div class="descr"><?=MSG_lastvisit;?> "<?=$GLOBALS['inforum']['f_title'];?>" 
      <?=long_date_out($GLOBALS['userlast2']);?></div>
                          <? }
  else { ?><div class="descr"><?=MSG_welcome;?> "<?=$GLOBALS['inforum']['f_title'];?>".</div>
<?} 
} ?>
<? if ($GLOBALS['inforum']['f_rules']=1) { ?><a class="rules" href="<?=build_url($GLOBALS['inforum'],'a=f_rules');?>"><?=MSG_f_rules;?></a><br><? } ?>
<? if ($GLOBALS['inuserid']>3) { ?>
<a class="descr" href="index.php?m=newpost&fs=<?=$GLOBALS['forum'];?>&a=do_mark_read">
<?=MSG_f_marktopics;?></a><br><br><? } ?>

<? if ($GLOBALS['opt_exttopic']) { ?>
<form class="descr" style="text-align: right" action="<?=build_url($GLOBALS['inforum']);?>" method=GET>
<?=MSG_f_sortby;?> <select name=o><?=set_select("<option value=posttime>".MSG_a_byadddate.
"<option value=posttime>".MSG_a_bylastcomment."<option value=t_title>".MSG_f_bytitle.
"<option value=t__views>".MSG_f_byviews."<option value=pcount>".MSG_a_bycomments.
"<option value=trating>".MSG_f_byrating."<option value=visited>".MSG_f_byvisit,getvar("o"));?></select>
, <?=MSG_showby;?> <select name=desc><?=set_select("<option value=\"\">".MSG_asc."<option value=desc>".MSG_desc,getvar("desc"));?></select>
<input type=hidden name=f value="<?=$GLOBALS['forum'];?>"> <input type=submit value="<?=MSG_show;?>"></form>

<?}?>

<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=5>
<?=MSG_a_list;?> "<?=$GLOBALS['inforum']['f_title'];?>"
<div style="text-align:right; float: right; width: 25%"><?=$pages;?></div>
<tr><td class="tablehead" colspan=2><?=MSG_a_title;?>
<td class="tablehead" width="15%"><?=MSG_a_author;?>
<td class="tablehead" width="10%"><?=MSG_t_views;?>
<td class="tablehead" width="5%"><?=MSG_t_rating;?>
<? }

function article_list_entry(&$adata) {
?><tr><td style="text-align:center">
<? $dir="styles/".$GLOBALS['inuser']['st_file'];
if (is_new($adata['visited'],$adata['lastpost'])) { $pic="$dir/new.png"; $alt="NEW!"; }
else $pic="$dir/nonew.png"; ?>
<img src="<?=$pic;?>" height=20 width=20 alt="<?=$alt;?>">
<td><h5><a href="<?=build_url($adata);?>"><?=$adata['t_title'];?></a></h5><?=$adata['t_descr'];?>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
<br><a class="descr" href="<?=build_url($adata,'a=edit_article');?>"><?=MSG_a_edit;?></a>
<? } ?>
<td style="text-align:center"><? if ($adata['a_authormail']) { ?><a href="mailto:<?=$adata['a_authormail'];?>"><?=$adata['a_author'];?></a><? }
else { ?><?=$adata['a_author'];?><? } ?><br><?=short_date_out($adata['posttime']);?>
<td style="text-align:center"><?=$adata['t__views'];?> / <?=$adata['pcount']-1;?><td style="text-align:center"><?
if ($adata['trating']) { ?><?=$adata['trating'];?><? }
else { ?><?=MSG_none;?><? } ?>
<? }

function article_list_end($pages,$inforum,$autosub) { ?>
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
<br><div class="modlinks"><a href="<?=build_url($GLOBALS['inforum'],'a=add_article');?>"><?=MSG_a_add;?></a>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
  if ($premodcount=get_premod()) { ?>
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=premod');?>"><?=MSG_f_premoderate;?></a> (<?=format_word($premodcount,MSG_p1,MSG_p2,MSG_p3);?>)
<? } ?> | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_banlist');?>"><?=MSG_f_banusers;?></a>
 | <a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_forum');?>"><?=MSG_f_moderate;?></a>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?></div><? } ?>
<br>
<? }

function article_display(&$tdata,&$adata,&$pdata,$rated,$pages) {
?>
<table class="innertable" width="100%" cellpadding=15 cellspacing=1><tr><td class="tablehead"><?=$tdata['t_title'];?>
<tr><td class="article"><div style="text-align: right">
<?=MSG_a_author;?>: <?
if ($adata['a_authormail']) { ?><a href="mailto:<?=$adata['a_authormail'];?>"><?=$adata['a_author'];?></a><? }
else { ?><?=$adata['a_author'];?><? } ?><br>
<?=MSG_a_origin;?>:
<?  if ($adata['a_origin']) { ?><a href="<?=$adata['a_originurl'];?>" target=_blank><?=$adata['a_origin'];?></a><? }
else { ?><a href="<?=$GLOBALS['opt_url'];?>"><?=$GLOBALS['opt_title'];?></a><? } ?><br>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?><?=MSG_a_publisher;?>: <?=user_out($pdata['p_uname'],$pdata['p_uid']);?><br><? } ?>
<?=$pages;?>
</div><hr><br>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?><br>
<br><?=$pages;?>
<br><br>
<? if ($tdata['t__rate'] && $GLOBALS['inforum']['f_rate']) { ?>
<tr><td>
<? rating($rated,$tdata); ?>
<? } ?></table><br>
<div class="modlinks"><a href="<?=build_url($tdata,'a=do_print');?>"><?=MSG_a_print;?></a> |
<a href="<?=build_url($tdata,'m=misc&amp;a=friend');?>"><?=MSG_t_mailtofriend;?></a>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?> | <a href="<?=build_url($GLOBALS['inforum'],'a=add_article');?>"><?=MSG_a_add;?></a>
<?  }
if (($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ledit'] &&
  $GLOBALS['intopic']['t_author']==$GLOBALS['inuserid']) ||
  $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
?> | <a href="<?=build_url($tdata,'a=edit_article');?>"><?=MSG_a_edit;?></a>
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
 |
<a href="<?=build_url($tdata,'a=delete_confirm');?>"><?=MSG_a_delete;?></a> |
<a href="<?=build_url($tdata,'m=moderate&amp;a=split_topic');?>"><?=MSG_a_split;?></a>
<? } ?></div><br>
<? }

function article_edit_form(&$tdata,&$adata,&$pdata,$newaction,$msg,$forumlist="") {
$inforum=$GLOBALS['inforum'];
?><form name=postform action="index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$msg;?>
<? if ($GLOBALS['inuserid']<=3) quick_login_form(); ?>
<tr><td><?=MSG_a_title;?><td>
<input type=text name=t_title size=30 maxlength=60 value="<?=$tdata['t_title'];?>">
<tr><td><?=MSG_a_descr;?><td>
<textarea name=t_descr rows=4 cols=30><?=textarea($tdata['t_descr']);?></textarea>
<? if ($GLOBALS['opt_hurl']) { ?>
<tr><td><?=MSG_t_link;?>:<br><span class="descr"><?=MSG_t_link_descr;?>
<td><input tabindex=4 type=text name=t_link size=30 maxlength=60 value="<?=$tdata['t_link'];?>" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}">
<? } ?>
<tr><td><?=MSG_a_author;?><td>
<input type=text name=a_author size=30 maxlength=64 value="<?=$adata['a_author'];?>">
<tr><td><?=MSG_a_authormail;?><br><?=MSG_a_amaildescr;?><td>
<input type=text name=a_authormail size=30 maxlength=48 value="<?=$adata['a_authormail'];?>">
<tr><td><?=MSG_a_origin;?><br><?=MSG_a_origindescr;?><td>
<input type=text name=a_origin value="<?=$adata['a_origin'];?>">
<tr><td><?=MSG_a_originurl;?><td>
<input type=text name=a_originurl value="<?=$adata['a_originurl'];?>">
<?
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lhtml'] && $GLOBALS['inuser']['u_extform'] && is_dir($GLOBALS['opt_dir']."/fckedit")) {
  $text=$pdata['p_text'];
  $text=str_replace("\r","",$text);
  $text=str_replace("\"","\\\"",$text);
  $text=str_replace("\n","\"\n+\"\\n",$text);
?>
<tr><td colspan=2>
<script type="text/javascript" src="./fckedit/fckeditor.js"></script>
<script type="text/javascript">
  var oFCKeditor=new FCKeditor('p_text') ;
  oFCKeditor.BasePath="<?=$GLOBALS['opt_url'];?>/fckedit/";
  oFCKeditor.Value="<?=$text;?>";
  oFCKeditor.Height=600;
        oFCKeditor.Config["AutoDetectLanguage"]=false;
        oFCKeditor.Config["DefaultLanguage"]="<?=substr($GLOBALS['inuser']['ln_locale'],0,2);?>";
  oFCKeditor.Create();
</script>
<? } else {
common_post($pdata,MSG_a_text);
} ?>
<tr><td>
<?=MSG_p_options;?>:
<td>
<? if ($GLOBALS['inuserlevel']>=$inforum['f_lhtml'] && $GLOBALS['inuser']['u_extform'] && is_dir($GLOBALS['opt_dir']."/fckedit")) { ?>
<input type=hidden value=1 name=p__html>
<? }
elseif ($GLOBALS['inuserlevel']>=$inforum['f_lhtml']) { ?>
<label><input type=checkbox value=1 name=p__html <? check($pdata['p__html']);?>><?=MSG_p_usehtml;?>?</label><br>
<? } ?>
<label><input type=checkbox value=1 name=nobr <? check($pdata['p__html']);?>><?=MSG_a_nobr;?></label><br>
<? if ($inforum['f_bcode']) { ?>
<label><input type=checkbox value=1 name=p__bcode <? check($pdata['p__bcode']);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_bcode">BoardCode</a></label><br>
<? }
if ($inforum['f_smiles']) { ?>
<label><input type=checkbox value=1 name=p__smiles <? check($pdata['p__smiles']==1);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_smiles"><?=MSG_p_smiles;?></a></label><br>
<? } if ($GLOBALS['inuserid']>3) { ?>
<input type=checkbox value=1 name=del_draft <? check($GLOBALS['action']=='edit_from_draft');?>><?=MSG_draft_senddel;?>
<? }
if ($newaction=="do_topic" && $inforum['f_rate']) { ?>
<label><input type=checkbox value=1 name=t__rate checked><?=MSG_t_israted;?></label><br>
<tr><td><?=MSG_a_discuswhere;?>
<td><select name=fid><option value=""><?=MSG_a_here;?><?=$forumlist;?></select>
<? } ?>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=m value=article>
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

function article_print_form(&$tdata,&$adata,&$pdata) { ?>
<H3><?=MSG_t_print;?></H3>
<H4>- &nbsp; <?=$GLOBALS['opt_title'];?> <?=$GLOBALS['opt_url'];?><br>
-- &nbsp;<?=$GLOBALS['inforum']['f_title'];?> <?=$GLOBALS['opt_url']."/".build_url($GLOBALS['inforum']);?><br>
--- <?=$GLOBALS['intopic']['t_title'];?> <?=$GLOBALS['opt_url']."/".build_url($GLOBALS['intopic']);?></H4><br><br>
<table width="90%" border=0 align=center><tr><td style="text-align: right">
<?=MSG_a_author;?>: <?=$adata['a_author'];?> <?
if ($adata['a_authormail']) { ?>(<?=$adata['a_authormail'];?>)<? } ?>
<br>
<?=MSG_a_origin;?>: <?=$adata['a_origin'];?> <?=$adata['a_originurl'];?>
<tr><td>
<hr><br>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?>
<br><br><br>
<hr>
</table>
<center><?=$GLOBALS['opt_copyright'];?><br>
<?=MSG_forum_powered;?><br>
&copy; <?=MSG_forum_copyright;?> http://intboard.ru</center>
<? }
