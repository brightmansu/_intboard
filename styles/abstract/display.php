<?

function display_welcome($forum=0) {
if ($GLOBALS['opt_exttopic']) { ?>
<form class="descr" style="float: right" action="<?=build_url($topic);?>" method=GET>
<?=MSG_t_msgorder;?> <select name=o><?=set_select("<option value=\"0\">".MSG_t_normal.
"<option value=\"1\">".MSG_t_reverse,getvar('o'));?></select><input type=hidden name=t value=<?=$GLOBALS['topic'];?>>
<input type=hidden name=st value="<?=getvar("st");?>"><input type=hidden name=p value="<?=getvar("p");?>">
<input type=submit value="<?=MSG_show;?>"></form>
<? } ?>
<table width="100%" border=0><tr><td width="50%"><?
if ($GLOBALS['inuserid']>3) {
  if ($GLOBALS['userlast2']) { ?>
<div class="descr"><?=MSG_lastvisit;?> "<?=$GLOBALS['inforum']['f_title'];?>" <?=long_date_out($GLOBALS['userlast2']);?></div>
<? }
  else { ?><div class="descr"><?=MSG_welcome;?> "<?=$GLOBALS['inforum']['f_title'];?>".</div><?}
} ?>
<? if ($GLOBALS['inforum']['f_rules']=1) { ?><a class="rules" href="<?=build_url($GLOBALS['inforum'],'','f_rules','a');?>"><?=MSG_f_rules;?></a><br><? } ?>
<? if ($GLOBALS['inuserid']>3) { ?><a class="descr" href="index.php?m=newpost&amp;fs=<?=$GLOBALS['forum'];?>&amp;a=do_mark_read"><?=MSG_f_marktopics;?></a><br><?
if (!$forum) { ?><a class="descr" href='index.php?a=do_unmark_read&amp;m=newpost&amp;ts=<?=$GLOBALS['topic'];?>&amp;f=<?=$GLOBALS['forum'];?>'><?=MSG_t_mark_unread;?></a><br><? }
} ?>
<br><td><? if ($GLOBALS['opt_exttopic'] && $forum) { ?>
<form class="descr" style="text-align: right; float: right" action="<?=build_url($GLOBALS['inforum']);?>" method=GET>
<?=MSG_f_sortby;?> <select name=o><?=set_select("<option value=t__lasttime>".MSG_f_bylast.
"<option value=t__startpostid>".MSG_f_byfirst."<option value=t_title>".MSG_f_bytitle."<option value=t__status>".MSG_f_bystatus.
"<option value=t__pcount>".MSG_f_bycount."<option value=t__views>".MSG_f_byviews.
"<option value=trating>".MSG_f_byrating."<option value=visited>".MSG_f_byvisit,getvar("o"));?></select>
, <?=MSG_showby;?> <select name=desc><?=set_select("<option value=\"\">".MSG_asc."<option value=desc>".MSG_desc,getvar("desc"));?>
</select><br>
<?=MSG_show_last;?> <? timelimiter($time,"time");?>
<?=MSG_t_per;?> <input type=text name=perpage size=4 value="<?=$perpage;?>"> <?=MSG_t_perpage;?> <br>
<?=MSG_t_filterby;?> <input type=text name=filter size=30 maxlength=255 value="
<?=$filter;?>">
<input type=hidden name=f value=<?=$GLOBALS['forum'];?>><input type=submit value="<?=MSG_show;?>"></form>
<? } ?></table>
<? }

function display_topic_border($pages,$topic,$prev_tid,$next_tid,$comments) { ?>
<table class="innertable" cellspacing=1 width="100%" cellpadding=3 style="margin: 8px 0"><tr>
<td class="tablehead" width="20%">
<? if ($comments) $action='a='.$GLOBALS['action'];
if (is_array($prev_tid)) { ?><a class="inverse" title="<?=$prev_tid['t_title'];?>" href="<?=build_url($prev_tid,$action);?>">&lt;&lt;<?=MSG_prev;?></a><? } ?>&nbsp;
<? if (is_array($next_tid)) { ?><a class="inverse" title="<?=$next_tid['t_title'];?>" href="<?=build_url($next_tid,$action);?>"><?=MSG_next;?>&gt;&gt;</a><? } ?>
<td class="tablehead" style="text-align: left"><? if ($pages) { ?><?=$pages;?><br><? } ?>
<? if ($GLOBALS['modlist']) { ?><?=$GLOBALS['modlist'];?><? } ?>
<td class="tablehead" width="30%"><?
if ($GLOBALS['inuserid']>3) {
if (!$topic['bmk']) { ?><a class="inverse" href="<?=build_url($topic,'a=do_add&amp;m=bookmark');?>"><?=MSG_t_tobookmark;?></a> &nbsp; <?
}
if (!$topic['subscr'] ) { ?><a class="inverse" href="<?=build_url($topic,'a=do_subscr&amp;m=subscr');?>"><?=MSG_t_subscribe;?></a> &nbsp; <? }
else { ?><a class=inverse href="<?=build_url($topic,'a=do_unsubscr&amp;m=subscr');?>"><?=MSG_t_unsubscribe;?></a><br>
<? } ?>
<a class="inverse" href="<?=build_url($topic,'m=misc&amp;a=friend');?>"><?=MSG_t_mailtofriend;?></a> &nbsp;
<? } ?><a class="inverse" href="/print/<?=build_url($topic);?>"><?=MSG_t_forprint;?></a>
</table>
<? }

function display_topic_start($pages,$topic,$rate,$prev_tid,$next_tid,$comments) {
if ($comments) { ?><h3><?=MSG_disp_comments_to;?> &laquo;<a href="<?=build_url($topic);?>"><?=$topic['t_title'];?></a>&raquo;</h3><? }
else { ?><h3><?=$topic['t_title'];?></h3><? } ?>
<h4><?=$topic['t_descr'];?></h4>
<? if (!getvar("preview") && $GLOBALS['opt_fwelcome']==1) {
if ($topic['t__rate'] && $GLOBALS['inforum']['f_rate']) rating($rate,$topic);
display_welcome();
}
display_topic_border($pages,$topic,$prev_tid,$next_tid,$comments); ?>
<table class="innertable posttable">
<col width=160>
<col>
<tr>
<td colspan=2 class="tablehead" style="text-align:left"><? /*  */ ?>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpost'] && !$GLOBALS['inforum']['f_status'] && !$topic['t__status']) { ?>
<a class=inverse href="<?=$_SERVER['REQUEST_URI'];?>#answer" onClick="moveForm('0'); return true"><?=MSG_p_answer;?></a> &nbsp;<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic'] && !$comments) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic');?>"><?=MSG_newtopic;?></a> &nbsp;
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic'] && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpoll'] && !$comments) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic&amp;vote=1');?>"><?=MSG_newpoll;?></a>
<? } ?>
&nbsp;
</table>
<? }

function display_topic_entry($pdata,$udata,$class,$links,$links2,$postlink,$newcount,$last=false) { ?>
<a name="pp<?=$pdata['p_id'];?>"></a>
<? if ($last) { ?><a name="last"></a><? } 
if ($newcount==1) { ?><a name="new"></a><? } ?>
<table class="innertable posttable" cellspacing="1" cellpadding="0"><tr>
<td class="<?=$class;?>" width="160"><?
if ($GLOBALS['inforum']['f_status']==0 && $GLOBALS['intopic']['t__status']==0 && $GLOBALS['inforum']['f_lpost']<=$GLOBALS['inuserlevel']) { ?>
<a class="username" href="javascript:pasteN('<?=$pdata['p_uname'];?>')"><?=$pdata['p_uname'];?></a><? }
else { ?><a class="username" href="#"><?=$pdata['p_uname'];?></a><? } ?>
<div class="descr">
<?=$udata['l_title'];?><br>
<? if ($udata['l_pic']) { ?><img src="images/<?=$udata['l_pic'];?>" alt=""><br><? } ?>
<?=$udata['u__title'];?><br>
<? if ($udata['u_id']>3) {
  if ($GLOBALS['inuser']['u_showavatars']) {
    echo show_avatar($udata).'<br>';
  }
  if ($udata['u_location']) { ?><?=MSG_user_location;?>: <?=$udata['u_location'];?><br><? }
  ?><?=MSG_user_total;?>: <?=intval($udata['posts']);?><br><?
  if ($GLOBALS['opt_rating']==0) {
  ?><?=MSG_user_rating;?>: <?=intval($udata['rating']);?><br><?
  if (!$udata['rated'] && $GLOBALS['inuserid']>3 && $GLOBALS['inuserid']!=$pdata['p_uid'] && $GLOBALS['inuserlevel']>=$GLOBALS['opt_ratinglevel']) {
    ?> [<a href="index.php?a=do_user_rate&amp;m=profile&amp;dir=pro&amp;u=<?=$pdata['p_uid'];?>">+</a>] [<a href="index.php?a=do_user_rate&amp;m=profile&amp;dir=contra&amp;u=<?=$pdata['p_uid'];?>">-</a>]<? }
    ?><br><?
    if ($udata['u__warnings']!=0 && ($GLOBALS['opt_reputation']==0 || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate'])) {
      ?><?=MSG_user_reputation;?>: <a href="index.php?m=profile&amp;a=list_warn&amp;u=<?=$udata['u_id'];?>"><?=intval($udata['u__warnings']);?></a><br><?
    }
    ?><br><?
  }
}
?><a href="<?=$postlink;?>"><?=MSG_p_link;?></a><br><br><?
if ($udata['u_id']>3) {
  echo '<br>'.MSG_user_regdate.':<br>'.short_date_out($udata['u__regdate']).'<br>';
}
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lip']) {
  ?>IP: <a target="_blank" href="https://www.nic.ru/whois/?ip=<?=numtoip($pdata['p__ip']);?>"><?=numtoip($pdata['p__ip']);?></a><?
} ?></div>
<td class="<?=$class;?>">
<div class="descr postlinks"><?
if (!getvar("preview")) {
  ?><?=join(" | ",$links);?><br /><?
} 
if ($newcount) { ?><span style="color: red">NEW!</span> <? } ?>
<?=MSG_p_post;?>: <?=long_date_out($pdata['p__time']);?><?
if ($pdata['p__edittime']) {
  ?><br><?=MSG_p_lastedited;?>: <?=long_date_out($pdata['p__edittime']);?><?
}
if ($pdata['p_title']) {
  ?><br><?=MSG_p_title;?>: <b><?=$pdata['p_title'];?></b><?
} ?>
</div>
<div id="p<?=$pdata['p_id'];?>" style="overflow: auto;">
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?>
</div><?
if ($pdata['p__modcomment']) {
  ?><br><br><div class="modcomment"><?=MSG_p_modcomment;?>: <br>
  <?=textout($pdata['p__modcomment'],1,$pdata['p__bcode'],$pdata['p__smiles']);?></div><br><?
}
if ($pdata['p_attach']) {
  if (strpos($pdata['file_type'],"image")===false) {
    ?><br><a href="file.php?fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>"><?=MSG_p_attachfile;?> (<?=urldecode($pdata['file_name']);?>, <?=$pdata['file_size'];?> <?=MSG_bytes;?>, <?=MSG_p_downloaded;?>: <?=format_word($pdata['file_downloads'],MSG_fdl1,MSG_fdl2,MSG_fdl3);?>)</a><?
  }
  else { ?><br><a href="file.php?fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>" target=_blank><img src="file.php?a=preview&amp;fid=<?=$pdata['p_attach'];?>&amp;key=<?=$pdata['file_key'];?>" alt="<?=MSG_p_attachfile;?> (<?=urldecode($pdata['file_name']);?>, <?=$pdata['file_size'];?> <?=MSG_bytes;?>, <?=MSG_p_downloaded;?>: <?=format_word($pdata['file_downloads'],MSG_fdl1,MSG_fdl2,MSG_fdl3);?>)"></a><?
  }
}
if ($udata['u_signature']!="" && $pdata['p_signature'] && $GLOBALS['inuser']['u_nosigns']==0) {
  ?><br><div class="sign">---<br><?=sign_code($udata['u_signature']);?></div>
<? }
if (!getvar("preview")) {
  ?><tr><td class="<?=$class;?> online"><?  if ($udata['present']) {
  ?><?=MSG_user_online;?><?  } ?>
  <td class="<?=$class;?>"><div class="descr postlinks2"><?=join(' | ',$links2);?></div></td></tr><?
}
?></table>
<?  ?>
<div id="a<?=$pdata['p_id'];?>"></div>
<? }

function display_topic_hidden($pdata,$class) { ?>
<table class="innertable posttable" cellspacing="0"><tr><td class="<?=$class;?>" style="text-align: center" class="descr"><?=MSG_p_hidden1;?>
 <?=user_out($pdata['p_uname'],$pdata['p_id']);?> (<?=long_date_out($pdata['p__time']);?>) <?=MSG_p_hidden2;?>.
</table>
<? }

function display_topic_system($pdata,$class) { ?>
<table class="innertable posttable" cellspacing="0"><tr>
<td class="<?=$class;?>" style="text-align: center"><?=textout($pdata['p_text'],1,1,0);?> (<?=long_date_out($pdata['p__time']);?>)
</table>
<? }

function display_topic_separator() { ?>
<div style="height: 16px"></div>
<? }

function display_topic_end($pages,$topic,$rate,$prev_tid,$next_tid,$comments) { ?>
<table class="innertable posttable">
<tr><td class="tablehead" colspan=2 style="text-align: left">
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpost'] && !$GLOBALS['inforum']['f_status'] && !$topic['t__status']) { ?>
<a class=inverse href="<?=$_SERVER['REQUEST_URI'];?>#answer" onClick="moveForm('0'); return true"><?=MSG_p_answer;?></a> &nbsp;<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic'] && !$comments) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic');?>"><?=MSG_newtopic;?></a> &nbsp;
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic'] && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpoll']  && !$comments) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic&amp;vote=1');?>"><?=MSG_newpoll;?></a>
<? } ?>
</table>
<? display_topic_border($pages,$topic,$prev_tid,$next_tid,$comments); ?>
<? if ($GLOBALS['inforum']['f_lmoderate']<=$GLOBALS['inuserlevel'] || check_selfmod()) { ?><br>
<div class="modlinks" style="text-align: right">
<? if ($GLOBALS['intopic']['pl_tid']) { ?>
<a href="<?=build_url($GLOBALS['intopic'],'m=moderate&amp;a=view_vote&amp;');?>"><?=MSG_t_viewvote;?></a> |
<? } ?>
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_banlist');?>"><?=MSG_f_banusers;?></a> |
<a href="<?=build_url($GLOBALS['intopic'],'m=moderate&amp;a=join_topic');?>"><?=MSG_t_join;?></a> |
<a href="<?=build_url($GLOBALS['intopic'],'m=moderate&amp;a=split_topic'.(isset($_GET['st']) ? '&amp;st='.$_GET['st'] : ''));?>"><?=MSG_t_split;?></a> |
<a href="<?=build_url($GLOBALS['intopic'],'m=moderate&amp;a=mod_topic');?>"><?=MSG_t_moderate;?></a></div><br>
<? } ?><?
if (!getvar("preview") && $GLOBALS['opt_fwelcome']==2) {
if ($tdata['t__rate'] && $GLOBALS['inforum']['f_rate']) rating($rate,$topic);
display_welcome();
}
}

function display_comment_link_form($tdata,$params) { ?>
<br/>
<div class="textmain" style="text-align: center">
  <a href="<?=build_url($tdata,$params);?>"><?=MSG_disp_comments;?> (<?=format_word($tdata['t__pcount']-1,MSG_disp_comment1,MSG_disp_comment2,MSG_disp_comment3);?>)</a>
</div>
<? }

function display_post_form($outmsg,&$pdata,$mode=0) {
$inuserlevel=$GLOBALS['inuserlevel'];
$inforum=$GLOBALS['inforum'];
?><a name="answer"></a><script type="text/javascript"><!--
function checkform (f) {
<? if (!$GLOBALS['topic']) { ?>
rq = ["t_title","p_text"];
rqs = ["<?=MSG_e_p_empty;?>","<?=MSG_e_p_empty;?>"];
<? } else  { ?>
rq = ["p_text"];
rqs = ["<?=MSG_e_p_empty;?>"];
<? } ?>
var i, j;
for(j=0; j<rq.length; j++) {
for (i=0; i<f.elements.length; i++) {
if (f.elements[i].name == rq[j] && f.elements[i].value == "" ) {
alert(rqs[j]);
f.elements[i].focus();
return false;
}
}
}
if (strlen(f.p_text.value)<<?=intval($GLOBALS['opt_minpost']);?>) { alert('<?=MSG_e_p_toosmall;?>'); return false; }
<? if ($GLOBALS['opt_maxpost']) { ?>
if (strlen(f.p_text.value)><?=$GLOBALS['opt_maxpost'];?>) { alert('<?=MSG_e_p_toolarge;?>'); return false; }<? } ?>
return true; }
// --></script>
<div id="a0">
<form style="margin: 2px 0" action="index.php" method=POST name=postform enctype="multipart/form-data" onsubmit="return checkform(this);">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2><?=$outmsg;?>
<? if ($GLOBALS['inuserid']==1) quick_login_form();
if ($GLOBALS['opt_posttitles']) { ?>
<tr><td><?=MSG_p_title;?>:<td>
<input type=text tabindex=3 name=p_title size=30 maxlength=64 value="<?=$pdata['p_title'];?>">
<? }
common_post($pdata,MSG_p_text);?>
<tr><td>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate'] && $mode==1) { ?>
<?=MSG_p_modcomment;?>:
<td>
<textarea name=p__modcomment onBlur="focused=false" onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}" rows=10 cols=70><?=textarea($pdata['p__modcomment']);?></textarea>
<tr><td>
<? }
if ($inuserlevel>=$inforum['f_lattach']) { ?>
<?=MSG_p_attach;?>:
<td><input type=hidden name="MAX_FILE_SIZE" value=<?=$GLOBALS['opt_maxfileattach'];?>>
<input type=file name=attach size=30 maxlength=255> <span class="descr"><?=MSG_maxfile;?>: <?=max_file_attach($GLOBALS['opt_maxfileattach']);?> Kb
<? if ($GLOBALS['inforum']['f_attachpics']) { ?>, <?=MSG_f_picsonly;?><? } ?></span>
<tr><td>
<? }
if ($GLOBALS['opt_ddos']==2 && $GLOBALS['inuserid']<=3) { ?>
<?=MSG_user_ddoscode;?>:
<td><?=show_ddos_code();?>
<tr><td>
<? } ?>
<?=MSG_p_options;?>:
<td>
<table style="border-collapse: collapse; width: 100%; border: 0"><tr><td style="width: 50%; border: 0">
<? if ($inuserlevel>=$inforum['f_lhtml']) { ?>
<label><input type=checkbox value=1 name=p__html <? check($pdata['p__html']);?>><?=MSG_p_usehtml;?>?</label><br>
<? }
if ($inforum['f_bcode']) { ?>
<label><input type=checkbox value=1 name=p__bcode <? check($pdata['p__bcode']);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_bcode">BoardCode</a></label><br>
<? }
if ($inforum['f_smiles']) { ?>
<label><label><input type=checkbox value=1 name=p__smiles <? check($pdata['p__smiles']==1);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_smiles"><?=MSG_p_smiles;?></a></label><br>
<? }
if ($GLOBALS['inuserlevel']>=$inforum['f_lsticky'] && $mode==2) { ?>
<label><input type=checkbox value=1 name=t__sticky><?=MSG_t_sticky;?>?</label><br>
<? }
if (($GLOBALS['inforum']['f_lmoderate']<=$GLOBALS['inuserlevel'] || check_selfmod()) &&
$GLOBALS['inforum']['f_status']==0 && $GLOBALS['intopic']['t__status']==0 && $mode!=1) { ?>
<label><input type=checkbox name=close value=1><?=MSG_p_close;?></label><br>
<label><input type=checkbox name=close value=2><?=MSG_p_onlymods;?></label><br>
<? } ?>
<td style="border: 0"><?
if ($GLOBALS['inuser']['u_signature']) { ?>
<label><input type=checkbox value=1 name=p_signature <? check($pdata['p_signature']==1);?>><?=MSG_p_attachsignature;?></label><br>
<? }
if ($mode==2 && $inforum['f_rate']) { ?>
<label><input type=checkbox value=1 name=t__rate checked><?=MSG_t_israted;?></label><br>
<? }
if ($GLOBALS['inuserid']>3 && !$GLOBALS['intopic']['subscr'] && $mode!=1) { ?>
<label><input type=checkbox name=subscr value=1><?=MSG_t_subscribe;?></label><br>
<? }
if ($GLOBALS['inuserlevel']>=$inforum['f_lsticky'] && $mode==2) { ?>
<label><input type=checkbox value=1 name=t__stickypost><?=MSG_t_stickypost;?>?</label><br>
<? }
if ($mode==1 && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
<label><input type=checkbox name=delete value=1><?=MSG_p_delete;?>?</label><br>
<? }
if ($mode==1 && $pdata['p_attach']!=0) { ?>
<input type=checkbox name=delattach value=1><?=MSG_p_delattach;?>
<? }
if ($GLOBALS['inuserid']>3) { ?>
<input type=checkbox value=1 name=del_draft <? check($GLOBALS['action']=='edit_from_draft');?>><?=MSG_draft_senddel;?>
<? } ?>
</table>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="<?=(($mode!=2) ? (($mode==1) ? 'do_edit_post' : 'do_post') : 'do_topic');?>">
<input type=hidden name=f value="<?=$GLOBALS['forum'];?>">
<input type=hidden name=t value="<?=$pdata['p_tid'];?>">
<input type=hidden name=p value="<?=getvar("p");?>">
<input type=submit title="<?=MSG_ctrlenter;?>" value="<?=MSG_post;?>">
&nbsp; <input type=submit accesskey="p" name=preview value="<?=MSG_preview;?>">
<? if (is_array($votecount) && count($votecount)) { ?><input type=submit name=more value="<?=MSG_addvariants;?>"><? } ?>
<? if ($GLOBALS['inuserid']>3) { ?>
&nbsp; <input type=submit name=continue onClick="document.getElementById('draft_msg').style.display='';setTimeout('document.getElementById(\'draft_msg\').style.display=\'none\'',10000);this.form.del_draft.checked=true; return true" accesskey="d" title="<?=MSG_draft_save;?>" value="<?=MSG_todraft;?>">
<div class="descr" id="draft_msg" style="display: none"><?=MSG_dr_sent;?></div>
<? } ?>
</table></form></div>
<? }
