<?
function stdforum_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_params;?>
<tr><td width="50%"><?=MSG_f_title;?>:
<td>
<input type=text name=f_title size=30 maxlength=60 value="<?=htmlspecialchars($fdata['f_title']);?>">
<tr><td><?=MSG_f_descr;?>:<br>
<span class="descr"><?=MSG_f_descr_descr;?>
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
<tr><td>
<?=MSG_f_selfmod;?>?
<td>
<input type=radio name=f_selfmod value=0 <? check($fdata['f_selfmod']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_selfmod value=1 <? check($fdata['f_selfmod']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_f_attachpics;?>?
<td>
<input type=radio name=f_attachpics value=0 <? check($fdata['f_attachpics']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_attachpics value=1 <? check($fdata['f_attachpics']==1);?>><?=MSG_yes;?> &nbsp;
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
<?=MSG_f_lpoll;?>
<td>
<select name=f_lpoll><? set_select($levelselect,$fdata['f_lpoll']);?></select>
<tr><td>
<?=MSG_f_lvote;?>
<td>
<select name=f_lvote><? set_select($levelselect,$fdata['f_lvote']);?></select>
<tr><td>
<?=MSG_f_lsticky;?>
<td>
<select name=f_lsticky><? set_select($levelselect,$fdata['f_lsticky']);?></select>
<tr><td>
<?=MSG_f_lattach;?>
<td>
<select name=f_lattach><? set_select($levelselect,$fdata['f_lattach']);?></select>
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

function std_welcome($forum=0) { ?>
<table width="100%" border=0><tr><td width="50%"><?
if ($GLOBALS['inuserid']>3) {
  if ($GLOBALS['userlast2']) { ?>
<div class="descr"><?=MSG_lastvisit;?> "<?=$GLOBALS['inforum']['f_title'];?>" <?=long_date_out($GLOBALS['userlast2']);?></div>
<? }
  else { ?><div class="descr"><?=MSG_welcome;?> "<?=$GLOBALS['inforum']['f_title'];?>".</div><?}
} ?>
<? if ($GLOBALS['inforum']['f_rules']) { ?><a class="rules" href="<?=build_url($GLOBALS['inforum'],'','f_rules','a');?>"><?=MSG_f_rules;?></a><br><? } ?>
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
<? } ?></TABLE><?
}

function std_forum_head($pages) { ?>
<table class="innertable" width="100%" cellspacing=1><tr>
<td width="60%" class="tablehead" style="text-align:left"><? if ($GLOBALS['modlist']) { ?><?=$GLOBALS['modlist'];?><? } ?>
<td width="40%" class="tablehead"><?=$pages;?>
</table>
<? }

function std_forum_start($pages,$perpage,$filter,$time) { ?>
<? if (!getvar("preview") && $GLOBALS['opt_fwelcome']==1) std_welcome(1); ?>
<? if ($GLOBALS['modlist'] || $pages) std_forum_head($pages) ?>
<table class="innertable" width="100%" cellspacing=1 style="table-layout: fixed">
<col width="5%">
<col width="41%">
<col width="6%">
<col width="9%">
<col width="9%">
<col width="15%">
<col width="15%">
<tr><td class="tablehead" colspan=7 style="text-align: left">
&nbsp;
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic');?>"><?=MSG_newtopic;?></a> &nbsp;
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic'] && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpoll']) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic&amp;vote=1');?>"><?=MSG_newpoll;?></a>
<? } ?>

<tr class="tablehead">
<td colspan=2 width="40%"><?=MSG_t_title;?>
<td><?=MSG_t_rating;?>
<td><?=MSG_t_count;?>
<td><?=MSG_t_views;?>
<td><?=MSG_t_start;?>
<td><?=MSG_t_last;?>
<? }

function std_topic_entry(&$tdata,$pages) { ?>
<tr class="topicentry" style="text-align: center"><td width="5%">
<? $dir="styles/".$GLOBALS['inuser']['st_file'];
if (is_new($tdata['visited'],$tdata['lp__time'])) {
  if ($tdata['t__status'] && $tdata['tl_count']>=$GLOBALS['opt_hot']) { $pic="$dir/closedhot.png"; $alt="CLOSED!"; }
  elseif ($tdata['t__status'] && $tdata['tl_count']<$GLOBALS['opt_hot']) { $pic="$dir/closednew.png"; $alt="NEW!"; }
  elseif ($tdata['tl_count']>=$GLOBALS['opt_hot']) { $pic="$dir/hot.png"; $alt="HOT!"; }
  else { $pic="$dir/new.png"; $alt="NEW!"; }
}
else {
  if ($tdata['t__status']) $pic="$dir/closed.png";
  else $pic="$dir/nonew.png";
};?><img src="<?=$pic;?>" height=20 width=20 alt="<?=$alt;?>">
<? if ($tdata['t__sticky']) { ?><img src="<?=$dir."/sticky.png";?>" height=20 width=20 alt=""> <? }
if ($tdata['pl_tid']) { ?><img src="<?=$dir."/vote.png";?>" height=20 width=20 alt="<?=$alt;?>"><? }
?>
<td style="text-align: left"><h5><a href="<?=build_url($tdata);?>"
<? if ($GLOBALS['opt_hinttext']) { ?>title="<?=clipword(textout($tdata['hint'],$tdata['p__html'],$tdata['p__bcode'],$tdata['p__smile']),$GLOBALS['opt_hinttext']);?>"<? } ?>>
<?=$tdata['t_title'];?></a>
<? if ($pages) { ?><span class="descr"><?=$pages;?>
<? if (is_new($tdata['visited'],$tdata['lp__time']) && $pages) { ?><a href="<?=build_url($tdata,'','new','st');?>">NEW!</a><? } ?> </span><? } ?></h5>
<?=$tdata['t_descr'];?>
<? if ($GLOBALS['inforum']['f_lmoderate']<=$GLOBALS['inuserlevel']  ||
($GLOBALS['inforum']['f_selfmod'] && $tdata['p_uid']==$GLOBALS['inuserid'])) { ?>
<br><a class="descr" href="<?=build_url($tdata,'m=moderate&amp;a=mod_topic');?>"><?=MSG_t_moderate;?></a><? } ?>
<td><? if ($tdata['t__rate'] && $GLOBALS['inforum']['f_rate']) { ?><?=intval($tdata['trating']);?>
<? } else { ?><?=MSG_none;?><? } ?>
<td><?=intval($tdata['tl_count']);?>
<td><?=intval($tdata['t__views']);?>
<td><?=user_out($tdata['p_uname'],$tdata['p_uid']);?><br>
<? if ($GLOBALS['inuser']['u_sortposts']==0 || $tdata['t__pcount']<=$GLOBALS['inuser']['u_mperpage']) {?>
<a href="<?=build_url($tdata,'','0','st');?>">&raquo;</a>
<? } else { ?><a href="<?=build_url($tdata,'',intval($tdata['t__pcount']-$GLOBALS['inuser']['u_mperpage']+1,'st'));?>">&raquo;</a><? } ?>

<?=long_date_out($tdata['fp__time']); ?>
<td><?=user_out($tdata['lp_uname'],$tdata['lp_uid']);?><br>
<? if ($GLOBALS['inuser']['u_sortposts']==0) { ?>
<a href="<?=build_url($tdata);?>#last">&raquo;</a>
<? } else { ?><a href="<?=build_url($tdata,'',0,'st');?>">&raquo;</a>
<? } ?><?=long_date_out($tdata['lp__time']); ?>
<? }

function std_forum_noentries() { ?>
<tr><td colspan=7 style="text-align:center"><?=MSG_f_notopics;?>
<? }

function std_topic_separator() { ?>
<tr><td colspan=7 height=6>
<? }

function std_forum_end($pages,$inforum,$autosub) { ?>
<tr><td colspan=7 class="tablehead" style="text-align: left">
&nbsp;
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic');?>"><?=MSG_newtopic;?></a> &nbsp;
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic'] && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpoll']) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic&amp;vote=1');?>"><?=MSG_newpoll;?></a>
<? } ?>
</table>

<br><? if ($GLOBALS['modlist'] || $pages) std_forum_head($pages) ?><br>
<? if (!getvar("preview") && $GLOBALS['opt_fwelcome']==2) std_welcome(1); ?>
<? if ($GLOBALS['inuserid']>3) { ?>
<div class="descr">
<?=MSG_subscr_newtopics;?> - <?
if ($inforum) { ?><a href="<?=build_url($GLOBALS['inforum'],'a=do_sub&amp;tid=4294967294');?>"><?=MSG_enabled;?></a><? }
else { ?><a href="<?=build_url($GLOBALS['inforum'],'a=do_sub&amp;tid=4294967294&amp;sub=1');?>"><?=MSG_disabled;?></a><? } ?><br>
<?=MSG_subscr_auto;?> - <?
if ($autosub) { ?><a href="<?=build_url($GLOBALS['inforum'],'a=do_sub&amp;tid=4294967295');?>"><?=MSG_enabled;?></a><? }
else { ?><a href="<?=build_url($GLOBALS['inforum'],'a=do_sub&amp;tid=4294967295&amp;sub=1');?>"><?=MSG_disabled;?></a><? } ?>
</div>
<? }
if ($GLOBALS['inforum']['f_lmoderate']<=$GLOBALS['inuserlevel']) { ?><br>
<div class="modlinks" style="text-align: right">
<? if ($premodcount=get_premod()) { ?>
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=premod');?>"><?=MSG_f_premoderate;?></a> (<?=format_word($premodcount,MSG_p1,MSG_p2,MSG_p3);?>) |
<? } ?>
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_banlist');?>"><?=MSG_f_banusers;?></a> |
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=edit_rules');?>"><?=MSG_f_editrules;?></a> |
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_forum');?>"><?=MSG_f_moderate;?></a> |
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=clear_forum');?>"><?=MSG_f_clean;?></a></div>
<? }
$path="styles/".$GLOBALS['inuser']['st_file']; ?><br>
<table width="100%" border=0 cellspacing=4>
<tr class="descr"><td width="5%"><img src="<?=$path;?>/nonew.png" alt=""><td width="45%"><?=MSG_t_nonew;?>
<td width="5%"><img src="<?=$path;?>/closed.png" alt=""><td width="45%"><?=MSG_t_closed;?>
<tr class="descr"><td><img src="<?=$path;?>/new.png" alt=""><td><?=MSG_t_new;?>
<td><img src="<?=$path;?>/closednew.png" alt=""><td><?=MSG_t_closednew;?>
<tr class="descr"><td><img src="<?=$path;?>/hot.png" alt=""><td><?=MSG_t_hot;?>
<td><img src="<?=$path;?>/closedhot.png" alt=""><td><?=MSG_t_closedhot;?>
<tr class="descr"><td><img src="<?=$path;?>/vote.png" alt=""><td><?=MSG_t_vote;?>
<td><img src="<?=$path;?>/sticky.png" alt=""><td><?=MSG_t_sticky;?>
</table><?
}

function std_vote_resbegin(&$tdata) { ?>
<table class="innertable" cellspacing=1 width="100%" cellpadding=3><tr>
<td class="tablehead" colspan=3><?=$tdata['pl_title'];?>
<? }

function std_vote_resentry($text,$count,$total) { ?>
<tr><td><?=$text;?>
<td><?=$count;?>
<? if ($total==0) $width=1;
else $width=floor(300*$count/$total)+1;  ?>
<td width=310><img src="<?=$GLOBALS['opt_url'];?>/styles/<?=$GLOBALS['inuser']['st_file'];?>/votepnt.png"
height=10 width="<?=$width;?>" alt="<?=str_repeat('*',$count);?>" border=1>
<? }

function std_vote_resend($total) { ?>
<tr><td class="tablehead" colspan=3><?=MSG_vote_total;?>: <?=$total;?>
</table><br>
<? }

function std_vote_begin(&$tdata) { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%" cellpadding=3><tr>
<td class="tablehead" colspan=2><?=$tdata['pl_title'];?>
<? }

function std_vote_entry($text,$id) { ?>
<tr><td width="80%"><?=$text;?>
<td align=center><input type=radio name=pv_id value="<?=$id;?>">

<? }

function std_vote_end() { ?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=stdforum><input type=hidden name=t value=<?=$GLOBALS['topic'];?>>
<input type=hidden name=a value=do_vote><input type=submit value="Голосовать!">
</table></form>
<? }

function std_topic_head(&$pages,&$topic,$min_tid,$max_tid) { ?>
<table class="innertable" cellspacing=1 width="100%" cellpadding=3><tr>
<td class="tablehead" width="20%">
<? if ($topic['t_id']!=$min_tid) { ?><a class="inverse" href="<?=build_url($topic,"step=prev&amp;f=".$topic['t_fid']);?>">&lt;&lt;<?=MSG_prev;?></a><? } ?>&nbsp;
<? if ($topic['t_id']!=$max_tid) { ?><a class="inverse" href="<?=build_url($topic,"step=next&amp;f=".$topic['t_fid']);?>"><?=MSG_next;?>&gt;&gt;</a><? } ?>
<td class="tablehead" style="text-align: left"><? if ($pages) { ?><?=$pages;?><br><? } ?>
<? if ($GLOBALS['modlist']) { ?><?=$GLOBALS['modlist'];?><? } ?>
<td class="tablehead" width="30%"><?
if ($GLOBALS['inuserid']>3) {
if (!$topic['bmk']) { ?><a class="inverse" href="<?=build_url($topic,'a=do_add&amp;m=bookmark');?>"><?=MSG_t_tobookmark;?></a> &nbsp; <?
}
if (!$topic['subscr'] ) { ?><a class="inverse" href="<?=build_url($topic,'a=do_subscr&amp;m=subscr');?>"><?=MSG_t_subscribe;?></a> &nbsp; <? }
else { ?><a class=inverse href="<?=build_url($topic,'a=do_unsubscr&amp;m=subscr');?>"><?=MSG_t_unsubscribe;?></a><br>
<? } ?>
<a class="inverse" href="<?=build_url($GLOBALS['inforum'],'m=misc&amp;a=friend');?>"><?=MSG_t_mailtofriend;?></a> &nbsp;
<? } ?><a class="inverse" href="/print/<?=build_url($topic);?>"><?=MSG_t_forprint;?></a>
</table>
<? }

function std_topic_start($pages,$topic,$rate,$sort,$prev_tid,$next_tid) { ?>
<h3><?=$topic['t_title'];?></h3>
<h4><?=$topic['t_descr'];?></h4>
<? if (!getvar("preview") && $GLOBALS['opt_fwelcome']==1) {
if ($topic['t__rate'] && $GLOBALS['inforum']['f_rate']) rating($rate,$topic);
std_welcome();
}
if ($GLOBALS['opt_exttopic']) { ?>
<form class="descr" style="text-align: left" action="<?=build_url($topic);?>" method=GET>
<?=MSG_t_msgorder;?> <select name=o><?=set_select("<option value=\"0\">".MSG_t_normal.
"<option value=\"1\">".MSG_t_reverse,$sort);?></select><input type=hidden name=t value=<?=$GLOBALS['topic'];?>>
<input type=hidden name=st value="<?=getvar("st");?>"><input type=hidden name=p value="<?=getvar("p");?>">
<input type=submit value="<?=MSG_show;?>"></form>
<? } ?>
<? std_topic_head($pages,$topic,$prev_tid,$next_tid); ?><br>
<table class="innertable posttable">
<col width=160>
<col>
<tr>
<td colspan=2 class="tablehead" style="text-align:left"><? /*  */ ?>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpost'] && !$GLOBALS['inforum']['f_status'] && !$topic['t__status']) { ?>
<a class=inverse href="<?=$_SERVER['REQUEST_URI'];?>#answer" onClick="moveForm('0'); return true"><?=MSG_p_answer;?></a> &nbsp;<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic');?>"><?=MSG_newtopic;?></a> &nbsp;
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic'] && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpoll']) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'a=std_newtopic&amp;vote=1');?>"><?=MSG_newpoll;?></a>
<? } ?>
&nbsp;
<? }

function std_post_entry(&$pdata,$first=0) {
  static $counter;
  if ($counter % 2 == 1) $class="postentry2";
  else $class="postentry";
  $counter++;
  if ($first) $class.=" first";
  ?>
<tr><td class="<?=$class;?>">
<a name="pp<?=$pdata['p_id'];?>"></a><? if ($GLOBALS['intopic']['t__lastpostid']==$pdata['p_id']) { ?>
<a name="last"></a><? }
if ($GLOBALS['inforum']['f_status']==0 && $GLOBALS['intopic']['t__status']==0 && $GLOBALS['inforum']['f_lpost']<=$GLOBALS['inuserlevel']) { ?>
<a class="username" href="javascript:pasteN('<?=$pdata['p_uname'];?>')"><?=$pdata['p_uname'];?></a><? }
else { ?><a class="username" href="#"><?=$pdata['p_uname'];?></a><? } ?>
<div class="descr">
<?=$pdata['l_title'];?><br>
<? if ($pdata['l_pic']) { ?><img src="images/<?=$pdata['l_pic'];?>" alt=""><br><? } ?>
<?=$pdata['u__title'];?><br>
<? if ($pdata['p_uid']>3) {
if ($GLOBALS['inuser']['u_showavatars']) { echo show_avatar($pdata);} ?><br>
<? if ($pdata['u_location']) { ?><?=MSG_user_location;?>: <?=$pdata['u_location'];?><br><? } ?>
<?=MSG_user_total;?>: <?=intval($pdata['ud_count']);?><br>
<? if ($GLOBALS['opt_rating']==0 && $GLOBALS['inuserlevel']>=$GLOBALS['opt_ratinglevel']) { ?>
<?=MSG_user_rating;?>: <?=intval($pdata['ud_rating']);?><br>
<? if (!$pdata['rated'] && $GLOBALS['inuserid']>3 && $GLOBALS['inuserid']!=$pdata['p_uid']) {
?> [<a href="index.php?a=do_user_rate&amp;m=profile&amp;dir=pro&amp;u=<?=$pdata['p_uid'];?>">+</a>] [<a href="index.php?a=do_user_rate&amp;m=profile&amp;dir=contra&amp;u=<?=$pdata['p_uid'];?>">-</a>]<? } ?><br>
<? if ($pdata['uw_count']!=0 && ($GLOBALS['opt_reputation']==0 || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate'])) {
?><?=MSG_user_reputation;?>: <a href="index.php?m=profile&amp;a=list_warn&amp;u=<?=$pdata['u_id'];?>"><?=intval($pdata['uw_count']);?></a><? } ?><br>
<? }
} ?>
<? if ($GLOBALS['opt_hurl']==1) { ?>
<a href="post/<?=build_url($GLOBALS['intopic']).'p'.$pdata['p_id'];?>.htm#pp<?=$pdata['p_id'];?>"><?=MSG_p_link;?><br></a>
<? } elseif ($GLOBALS['opt_hurl']==2) { ?>
<a href="index.php/post/<?=str_replace('index.php/','',build_url($GLOBALS['intopic'])).'p'.$pdata['p_id'];?>.htm#pp<?=$pdata['p_id'];?>"><?=MSG_p_link;?><br></a>
<? } else { ?>
<a href="index.php?t=<?=$pdata['p_tid'];?>&p=<?=$pdata['p_id'];?>#pp<?=$pdata['p_id'];?>"><?=MSG_p_link;?><br></a>
<? }
if ($pdata['p_uid']>3) {
echo '<br>'.MSG_user_regdate.':<br>'.short_date_out($pdata['u__regdate']).'<br>';
if ($pdata['pu_lasttime']>=$GLOBALS['curtime']-$GLOBALS['opt_heretime']*60) {
?><div class="online"><?=MSG_user_online;?></div><br><? }
}
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lip']) {
?>IP: <a target=_blank href="https://www.nic.ru/whois/?ip=<?=numtoip($pdata['p__ip']);?>"><?=numtoip($pdata['p__ip']);?></a><? }
?></div>
<td class="<?=$class;?>"><div class="descr postlinks"><?
$links = array(); $links2 = array();
if ($GLOBALS['inforum']['f_status']==0 && $GLOBALS['intopic']['t__status']==0 && $GLOBALS['inforum']['f_lpost']<=$GLOBALS['inuserlevel']) {
  array_push($links2,'<a href="'.$_SERVER['REQUEST_URI'].'#answer" onClick="moveForm(\''.$pdata['p_id'].'\'); return false;">'.MSG_p_answer.'</a>');
  array_push($links2,"<a onmouseover=\"copyQN('".$pdata['p_uname']."','p".$pdata['p_id']."');\" href=\"".$_SERVER['REQUEST_URI']."\" onClick=\"javascript:pasteQ(); moveForm('".$pdata['p_id']."'); return false;\" title=\"".MSG_p_quotehelp."\">".MSG_p_quote."</a>");
}
if (($pdata['p_uid']==$GLOBALS['inuserid'] && $pdata['p_uid']>3 && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ledit']) || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
  array_push($links2,"<a href=\"".build_url($GLOBALS['intopic'],"a=edit&amp;p=".$pdata['p_id'])."\">".MSG_p_edit."</a>");
}
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate'] || check_selfmod()) {
  array_push($links2,"<a onClick=\"return confirm('".MSG_p_confirm."?')\" href=\"".build_url($GLOBALS['intopic'],'a=do_delete_comment&amp;p='.$pdata[$i]['p_id'])."\">".MSG_p_delete."</a>");
}
array_push($links2,"<a href=\"".$_SERVER['REQUEST_URI']."#top\">".MSG_go_top."</a>");
if ($pdata['p_uid']>3) {
  if ($GLOBALS['opt_hurl']) array_push($links,"<a href=\"user/".urlencode($pdata['p_uname'])."\">".MSG_p_profile."</a>");
  else array_push($links,"<a href=\"index.php?m=profile&amp;u=".$pdata['u_id']."\">".MSG_p_profile."</a>");
  if ($pdata['p_uid']!=$GLOBALS['inuserid'] && $GLOBALS['inuserid']>3) {
   array_push($links,"<a href=\"index.php?m=messages&amp;a=newmsg&amp;u=".$pdata['u_id']."\">".MSG_p_pm."</a>");
  }
  if ($pdata['u_showmail']>0) {
   array_push($links,show_email_q($pdata['u__email'],$pdata['u_showmail'],$pdata['p_uid']));
  }
  if ($pdata['u_homepage']!="") {
   array_push($links,"<a href=\"".$pdata['u_homepage']."\" target=_blank>WWW</a>");
  }
  if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate'] && $GLOBALS['inuserid']!=$pdata['u_id']) {
   array_push($links,"<a href=\"".build_url($GLOBALS['intopic'],'m=profile&amp;a=warn&amp;u='.$pdata['p_uid'])."\">".MSG_p_reputation."</a>");
  }
}
if (!check_moderate($pdata,$GLOBALS['inforum']['f_lmoderate']) && $pdata['p_uid']!=$GLOBALS['inuserid'] && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && ($GLOBALS['opt_complain']==0 || ($GLOBALS['opt_complain']==1 && $GLOBALS['iuserid']>3))) {
 array_push($links,"<a rel=\"nofollow\" href=\"".build_url($GLOBALS['intopic'],'m=moderate&amp;a=complain&amp;p='.$pdata['p_id'])."\">".MSG_p_tomoder."</a>");
} ?>
<? if (!getvar("preview")) { ?><?=join(" | ",$links);?><? } ?>
<br>
<? if ($pdata['p__time']>max($GLOBALS['inuser']['lv_time2'],$_SESSION['t'.$pdata['p_tid']]) && $GLOBALS['inuserid']!=$pdata['u_id']) {
  static $newcount;
  if ($newcount==0) { ?><a name="new"></a><? }
?><span style="color: red">NEW! </span><?
$newcount++; } ?>
<?=MSG_p_post;?>: <?=long_date_out($pdata['p__time']);?>
<? if ($pdata['p__edittime']) { ?><br><?=MSG_p_lastedited;?>: <?=long_date_out($pdata['p__edittime']);?><? }
if ($pdata['p_title']) { ?><br><?=MSG_p_title;?>: <b><?=$pdata['p_title'];?></b>
<? } ?></div>
<div id="p<?=$pdata['p_id'];?>" style="overflow: auto;"><?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?></div>
<? if ($pdata['p__modcomment']) { ?><br><br><div class="modcomment"><?=MSG_p_modcomment;?>: <br>
<?=textout($pdata['p__modcomment'],1,$pdata['p__bcode'],$pdata['p__smiles']);?></div><br>
<? }
if ($pdata['p_attach']) { if (strpos($pdata['file_type'],"image")===false) { ?>
<br><a href="file.php?fid=<?=$pdata['p_attach'];?>"><?=MSG_p_attachfile;?> (<?=urldecode($pdata['file_name']);?>, <?=$pdata['file_size'];?> <?=MSG_bytes;?>)</a><? }
else { ?><br><a href="file.php?fid=<?=$pdata['p_attach'];?>" target=_blank><img src="file.php?a=preview&amp;fid=<?=$pdata['p_attach'];?>" alt="<?=MSG_p_attachfile;?> (<?=urldecode($pdata['file_name']);?>, <?=$pdata['file_size'];?> <?=MSG_bytes;?>)"></a>
<? }
}
if ($pdata['u_signature']!="" && $pdata['p_signature'] && $GLOBALS['inuser']['u_nosigns']==0) { ?><br><div class="sign">---<br><?=sign_code($pdata['u_signature']);?></div>
<? } ?><br>&nbsp;
<tr><td colspan=2 style="padding: 0">
<? if (!getvar("preview")) { ?><div class="descr postlinks2"><?=join(' | ',$links2);?></div><? } ?>
<div id="a<?=$pdata['p_id'];?>"></div><?
}

function std_post_hidden($pdata) { ?>
<tr><td class=postentry colspan=2 style="text-align: center" class="descr"><?=MSG_p_hidden1;?>
 <?=user_out($pdata['p_uname'],$pdata['p_id']);?> (<?=long_date_out($pdata['p__time']);?>) <?=MSG_p_hidden2;?>.
<? }

function std_system_post($text,$date=0) { ?>
<tr><td colspan=2 style="text-align: center"><?=textout($text,1,1,0);?> (<?=long_date_out($date);?>)
<? }

function std_post_separator() { ?>
<tr><td colspan=2 style="height: 6px">
<? }

function std_topic_end($pages,$tdata,$prev_tid,$next_tid,$vote,$rate) { ?>
<tr><td class="tablehead" colspan=2 style="text-align: left">
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpost'] && !$GLOBALS['inforum']['f_status'] && !$topic['t__status']) { ?>
<a class=inverse href="<?=$_SERVER['REQUEST_URI'];?>#answer" onClick="moveForm('0'); return true"><?=MSG_p_answer;?></a> &nbsp;<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic']) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'m=stdforum&amp;a=std_newtopic');?>"><?=MSG_newtopic;?></a> &nbsp;
<? }
if ($GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_ltopic'] && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lpoll']) { ?>
<a class=inverse href="<?=build_url($GLOBALS['inforum'],'m=stdforum&amp;a=std_newtopic&amp;vote=1');?>"><?=MSG_newpoll;?></a>
<? } ?>
</table>
<br><? std_topic_head($pages,$tdata,$prev_tid,$next_tid); ?>
<? if ($GLOBALS['inforum']['f_lmoderate']<=$GLOBALS['inuserlevel'] || check_selfmod()) { ?><br>
<div class="modlinks" style="text-align: right">
<? if ($vote) { ?>
<a href="<?=build_url($GLOBALS['intopic'],'m=moderate&amp;a=view_vote&amp;');?>"><?=MSG_t_viewvote;?></a> |
<? } ?>
<a href="<?=build_url($GLOBALS['inforum'],'m=moderate&amp;a=mod_banlist');?>"><?=MSG_f_banusers;?></a> |
<a href="<?=build_url($GLOBALS['intopic'],'m=moderate&amp;a=join_topic');?>"><?=MSG_t_join;?></a> |
<a href="<?=build_url($GLOBALS['intopic'],'m=moderate&amp;a=split_topic'.(isset($_GET['st']) ? '&amp;st='.$_GET['st'] : ''));?>"><?=MSG_t_split;?></a> |
<a href="<?=build_url($GLOBALS['intopic'],'m=moderate&amp;a=mod_topic');?>"><?=MSG_t_moderate;?></a></div><br>
<? } ?><br><?
if (!getvar("preview") && $GLOBALS['opt_fwelcome']==2) {
if ($tdata['t__rate'] && $GLOBALS['inforum']['f_rate']) rating($rate,$tdata);
std_welcome();
}
}

function std_post_form($outmsg,$newaction,&$pdata,$votecount) {
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
<form action="index.php" method=POST name=postform enctype="multipart/form-data" onsubmit="return checkform(this);">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2><?=$outmsg;?>
<? if ($GLOBALS['inuserid']==1) quick_login_form(); ?>
<? if (!$GLOBALS['topic']) { ?>
<tr><td><?=MSG_t_title;?>:
<td><input tabindex=1 type=text name=t_title size=30 maxlength=60 value="<?=$pdata['t_title'];?>" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}">
<tr><td><?=MSG_t_descr;?>:<td>
<textarea tabindex=2 name=t_descr rows=3 cols=40 onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}"><?=textarea($pdata['t_descr']);?></textarea>
<? if ($GLOBALS['opt_hurl']) { ?>
<tr><td><?=MSG_t_link;?>:<br><span class="descr"><?=MSG_t_link_descr;?>
<td><input tabindex=4 type=text name=t_link size=30 maxlength=60 value="<?=$pdata['t_link'];?>" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}">
<? }
}
if ($votecount) { ?>
<tr><td><?=MSG_vote_question;?>:<td>
<input type=text tabindex=5 name=pl_title size=30 maxlength=60 value="<?=$pdata['pl_title'];?>">
<input type=hidden name=vote value=1>
<tr><td><?=MSG_vote_beforeend;?>:<br><?=MSG_vote_endless;?><td>
<input type=text tabindex=6 name=voteend size=5 maxlength=8 value="<?=$pdata['voteend'];?>">
<? foreach ($votecount as $i) { ?>
<tr><td><?=MSG_vote_variant;?> <?=($i+1);?>:<td>
<input type=text name=pl_text[<?=$i;?>] size=30 maxlength=80 value="<?=$pdata['votevar'][$i];?>">
<? }
}
if ($GLOBALS['opt_posttitles']) { ?>
<tr><td><?=MSG_p_title;?>:<td>
<input type=text tabindex=3 name=p_title size=30 maxlength=64 value="<?=$pdata['p_title'];?>">
<? }
common_post($pdata,MSG_p_text);?>
<tr><td>
<? if ($GLOBALS['inuserlevel']>=$inforum['f_lmoderate'] && $newaction=="do_edit_post") { ?>
<?=MSG_p_modcomment;?>:
<td>
<textarea name=p__modcomment rows=10 cols=70><?=textarea($pdata['p__modcomment']);?></textarea>
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
if ($GLOBALS['inuserlevel']>=$inforum['f_lsticky'] && $newaction=="do_topic") { ?>
<label><input type=checkbox value=1 name=t__sticky><?=MSG_t_sticky;?>?</label><br>
<? }
if (($GLOBALS['inforum']['f_lmoderate']<=$GLOBALS['inuserlevel'] || check_selfmod()) &&
$GLOBALS['inforum']['f_status']==0 && $GLOBALS['intopic']['t__status']==0 && $newaction!="do_edit") { ?>
<label><input type=checkbox name=close value=1><?=MSG_p_close;?></label><br>
<label><input type=checkbox name=close value=2><?=MSG_p_onlymods;?></label><br>
<? } ?>
<td style="border: 0"><?
if ($GLOBALS['inuser']['u_signature']) { ?>
<label><input type=checkbox value=1 name=p_signature <? check($pdata['p_signature']==1);?>><?=MSG_p_attachsignature;?></label><br>
<? }
if ($newaction=="do_topic" && $inforum['f_rate']) { ?>
<label><input type=checkbox value=1 name=t__rate checked><?=MSG_t_israted;?></label><br>
<? }
if ($GLOBALS['inuserid']>3 && !$GLOBALS['intopic']['subscr'] && $newaction!="do_edit") { ?>
<label><input type=checkbox name=subscr value=1><?=MSG_t_subscribe;?></label><br>
<? }
if ($GLOBALS['inuserlevel']>=$inforum['f_lsticky'] && $newaction=="do_topic") { ?>
<label><input type=checkbox value=1 name=t__stickypost><?=MSG_t_stickypost;?>?</label><br>
<? }
if ($newaction=="do_edit_post" && $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) { ?>
<label><input type=checkbox name=delete value=1><?=MSG_p_delete;?>?</label><br>
<? }
if ($newaction=="do_edit_post" && $pdata['p_attach']!=0) { ?>
<input type=checkbox name=delattach value=1><?=MSG_p_delattach;?>
<? }
if ($GLOBALS['inuserid']>3) { ?>
<input type=checkbox value=1 name=del_draft <? check($GLOBALS['action']=='edit_from_draft');?>><?=MSG_draft_senddel;?>
<? } ?>
</table>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=m value=stdforum>
<input type=hidden name=a value="<?=$newaction;?>">
<input type=hidden name=f value="<?=$GLOBALS['forum'];?>">
<input type=hidden name=t value="<?=$GLOBALS['topic'];?>">
<input type=hidden name=p value="<?=getvar("p");?>">
<input type=submit title="<?=MSG_ctrlenter;?>" value="<?=MSG_post;?>">
<? if ($newaction!="do_edit_post") { ?>
&nbsp; <input type=submit name=preview value="<?=MSG_preview;?>">
<? } ?>
<? if (is_array($votecount) && count($votecount)) { ?><input type=submit name=more value="<?=MSG_addvariants;?>"><? } ?>
<? if ($GLOBALS['inuserid']>3) { ?>
&nbsp; <input type=submit name=continue onClick="document.getElementById('draft_msg').style.display='';setTimeout('document.getElementById(\'draft_msg\').style.display=\'none\'',10000);this.form.del_draft.checked=true; return true" accesskey="d" title="<?=MSG_draft_save;?>" value="<?=MSG_todraft;?>">
<div class="descr" id="draft_msg" style="display: none"><?=MSG_dr_sent;?></div>
<? } ?>
</table></form></div>
<? }

function print_start() { ?>
<H3><?=MSG_t_print;?></H3>
<H4>- &nbsp; <?=$GLOBALS['opt_title'];?> <?=$GLOBALS['opt_url'];?><br>
-- &nbsp;<?=$GLOBALS['inforum']['f_title'];?> <?=$GLOBALS['opt_url']."/".build_url($GLOBALS['inforum']);?><br>
--- <?=$GLOBALS['intopic']['t_title'];?> <?=$GLOBALS['opt_url']."/".build_url($GLOBALS['intopic']);?></H4><br><br>
<? }

function print_entry($pdata) { ?>
<hr>
-- <?=$pdata['p_uname'];?> <?=MSG_p_posted;?> <?=long_date_out($pdata['p__time']);?><br>
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles']);?><br><br>
<? }

function print_end() { ?>
<hr><p style="text-align: center; font-size: 60%"><?=MSG_forum_powered;?><br>
<?=$GLOBALS['opt_copyright'];?></p>
<? }

