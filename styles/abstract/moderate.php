<? function mod_topic_form($tdata,$pldata,$pv_text,$forumlist) { ?>
<form action="index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_mod_topic;?>
<tr><td width="50%"><?=MSG_t_title;?>:<td>
<input type=text name=t_title size=30 maxlength=60 value="<?=htmlspecialchars($tdata['t_title']);?>">
<tr><td><?=MSG_t_descr;?>:<td>
<textarea name=t_descr rows=3 cols=40><?=textarea(htmlspecialchars($tdata['t_descr']));?></textarea>
<? if ($GLOBALS['opt_hurl']) { ?>
<tr><td><?=MSG_t_link;?>:<br><span class="descr"><?=MSG_t_link_descr;?>
<td><input tabindex=4 type=text name=t_link size=30 maxlength=60 value="<?=$tdata['t_link'];?>" onkeypress="if((event.ctrlKey) && ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}">
<? } ?>
<tr><td><?=MSG_t_sticky;?>?<td>
<input type=radio name=t__sticky value=0 <? check($tdata['t__sticky']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=t__sticky value=1 <? check($tdata['t__sticky']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_t_stickypost;?>?<td>
<input type=radio name=t__stickypost value=0 <? check($tdata['t__stickypost']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=t__stickypost value=1 <? check($tdata['t__stickypost']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_t_closed;?>?<td>
<input type=radio name=t__status value=0 <? check($tdata['t__status']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=t__status value=1 <? check($tdata['t__status']==1);?>><?=MSG_yes;?> &nbsp; 
<input type=radio name=t__status value=2 <? check($tdata['t__status']==2);?>><?=MSG_p_onlymods;?>
<tr><td><?=MSG_t_israted;?>?<td>
<input type=radio name=t__rate value=0 <? check($tdata['t__rate']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=t__rate value=1 <? check($tdata['t__rate']==1);?>><?=MSG_yes;?>
<tr><td><?=MSG_mod_action;?>:<td>
<label><input type=radio name=mode value=0 checked><?=MSG_mod_keep;?></label><br>
<label><input type=radio name=mode value=1><?=MSG_mod_move;?></label><br>
<label><input type=radio name=mode value=3><?=MSG_mod_copy;?></label><br>
<input type=radio name=mode value=2><?=MSG_mod_delete;?>
<tr><td><?=MSG_mod_moveto;?><td>
<select name="newforum"><?=$forumlist;?></select>
<? if ($pldata['pl_id']) { ?>
<tr><td class="tablehead" colspan=2><?=MSG_mod_voteparams;?>
<tr><td><?=MSG_vote_question;?>:<td>
<input type=hidden name=pl_id value=<?=$pldata['pl_id'];?>>
<input type=text name=pl_title size=30 maxlength=60 value="<?=$pldata['pl_title'];?>">
<tr><td><?=MSG_vote_beforeend;?>:<br><?=MSG_vote_endless;?><td>
<input type=text name=enddate size=5 maxlength=8 value="<?=$pldata['enddate'];?>">
<? $counter=1;
foreach ($pv_text as $id=>$text) { ?>
<tr><td><?=MSG_vote_variant;?> <?=$counter;?><td>
<input type=text name=pv_text[<?=$id;?>] size=30 maxlength=80 value="<?=htmlspecialchars($text);?>">
<? $counter++; }
} ?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=moderate>
<input type=hidden name=a value=do_mod_topic><input type=hidden name=t value=<?=$tdata['t_id'];?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function clear_form() { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr>
<td class="tablehead" colspan=2><?=MSG_mod_clean;?> "<?=$GLOBALS['inforum']['f_title'];?>"
<tr><td colspan=2><?=MSG_mod_condition;?>
<tr><td width="50%"><?=MSG_mod_cleandays;?>:<td>
<input type=text name=days size=4 maxlength=4 value="30">
<tr><td><?=MSG_mod_cleancount;?>:<td>
<input type=text name=count size=4 maxlength=8 value=1>
<tr><td><?=MSG_mod_cleantitle;?>:<td>
<input type=text name=title size=30 maxlength=60>
<tr><td><?=MSG_mod_cleanuser;?>:<td>
<input type=text name=user size=30 maxlength=32>
<tr class="tablehead"><td colspan=2><input type=hidden name=m value=moderate>
<input type=hidden name=a value=do_clear_forum><input type=hidden name=f value=<?=$GLOBALS['forum'];?>>
<input type=submit value="<?=MSG_mod_startclean;?>">
</table></form>
<? }

function mod_split_start($flist,$pages) { ?>
<form action="index.php" method=POST enctype="multipart/form-data">
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=5>
<?=MSG_mod_split;?><tr><td colspan=5>
<input type=radio name=newtopic value=1 checked> <?=MSG_mod_splitnew;?>
<tr><td width="60%" colspan=2>
<?=MSG_mod_newtitle;?>:<td colspan=3><input type=text name=t_title size=30 maxlength=60>
<tr><td colspan=2><?=MSG_mod_newdescr;?>:<td colspan=3>
<textarea name=t_descr rows=8 cols=30></textarea>
<tr><td colspan=2>
<?=MSG_mod_newforum;?>:<td colspan=3>
<select name="newforum"><?=set_select($flist,$GLOBALS['forum']);?></select>
<tr><td colspan=5>
<input type=radio name=newtopic value=0> <?=MSG_mod_splitadd;?>
<tr><td colspan=2>
<?=MSG_mod_topicnumber;?>:
<td colspan=3>
<input type=text id=tid name=tid size=8 maxlength=8>
<input type=button value="<?=MSG_mod_searchtopic;?>" onClick="javascript:window.open('index.php?m=misc&a=do_select_topic&f=<?=$GLOBALS['forum'];?>','TopicWin','toolbar=no, menubar=no, height=300, width=500, status=no, location=no, top=170, left=70')" >

<tr><td colspan=2><?=MSG_mod_putlink;?>:<td colspan=3>
<input type=checkbox name=putlink value=1 checked>
<tr><td class="tablehead" colspan=2><?=MSG_mod_posts;?>
<td class="tablehead" colspan=3><?=$pages;?>
<tr><td class="tablehead"><?=MSG_author;?><td class="tablehead"><?=MSG_post;?>
<td class="tablehead"><?=MSG_move;?><td class="tablehead"><?=MSG_copy;?>
<td class="tablehead"><?=MSG_delete;?>
<? }

function mod_split_entry($pdata) { ?>
<tr><td width="10%"><?=user_out($pdata['p_uname'],$pdata['p_uid']);?>
<td><div style="height: 120px; overflow: auto">
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?></div>
<input type=hidden name=pid[<?=$pdata['p_id'];?>] value=<?=$pdata['p_id'];?>>
<td width="15%" align=center>
<input type=checkbox name=move[<?=$pdata['p_id'];?>] value=1><td width="15%" align=center>
<input type=checkbox name=copy[<?=$pdata['p_id'];?>] value=1><td width="15%" align=center>
<input type=checkbox name=delete[<?=$pdata['p_id'];?>] value=1>
<? }

function mod_split_end() { ?>
<tr><td class="tablehead" colspan=5><input type=hidden name=m value=moderate>
<input type=hidden name=a value=do_split_topic><input type=hidden name=t value=<?=$GLOBALS['topic'];?>>
<input type=submit value="<?=MSG_mod_dosplit;?>">
</table></form>
<? }

function mod_forum_start($flist) { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=7>
<?=MSG_mod_forum;?> "<?=$GLOBALS['inforum']['f_title'];?>"
<tr class="tablehead"><td width="50%"><?=MSG_mod_moveto2;?>:
<td colspan=6><select name=newforum><?=$flist;?></select>
<tr><td class="tablehead"><?=MSG_topic;?><td class="tablehead"><?=MSG_sticky;?>
<td class="tablehead"><?=MSG_stickypost;?><td class="tablehead"><?=MSG_t_closed;?>
<td class="tablehead"><?=MSG_inrating;?><td class="tablehead"><?=MSG_move;?>
<td class="tablehead"><?=MSG_delete;?>
<? }

function mod_forum_entry($tdata) { ?>
<tr><td><a href="index.php?t=<?=$tdata['t_id'];?>"><?=$tdata['t_title'];?></a>
<input type=hidden name=topics[<?=$tdata['t_id'];?>] value=<?=$tdata['t_id'];?>>
<td align=center><input type=checkbox name=sticky[<?=$tdata['t_id'];?>] value=1 <? check($tdata['t__sticky']);?>>
<td align=center><input type=checkbox name=stickypost[<?=$tdata['t_id'];?>] value=1 <? check($tdata['t__stickypost']);?>>
<td align=center><input type=checkbox name=status[<?=$tdata['t_id'];?>] value=1 <? check($tdata['t__status']);?>>
<td align=center><input type=checkbox name=rate[<?=$tdata['t_id'];?>] value=1 <? check($tdata['t__rate']);?>>
<td align=center><input type=checkbox name=move[<?=$tdata['t_id'];?>] value=1>
<td align=center><input type=checkbox name=delete[<?=$tdata['t_id'];?>] value=1>
<? }

function mod_forum_end() { ?>
<tr><td class="tablehead" colspan=7><input type=hidden name=m value=moderate>
<input type=hidden name=a value=do_mod_forum><input type=hidden name=f value=<?=$GLOBALS['forum'];?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function join_form($tlist) { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_mod_join;?>
<tr><td width="50%"><?=MSG_mod_addtopic;?> "<?=$GLOBALS['intopic']['t_title'];?>"<?=MSG_mod_totopic;?>
<td><select name="newtid"><?=$tlist;?></select>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=moderate>
<input type=hidden name=a value=do_join_topic><input type=hidden name=t value=<?=$GLOBALS['topic'];?>>
<input type=submit value="<?=MSG_mod_dojoin;?>">
</table></form>
<? }

function rules_form(&$rules) { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_f_rules_edit;?>
<tr><td width="50%"><?=MSG_f_rules;?>
<td><textarea name=rules_text rows=8 cols=40><?=$rules;?></textarea>
<tr><td class="tablehead" colspan=2><input type=hidden name=f value=<?=$GLOBALS['forum'];?>>
<input type=hidden name=m value=moderate><input type=hidden name=a value=do_edit_rules>
<input type=submit value=<?=MSG_save;?>>
</table></form>
<? }

function premod_start() { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=3>
<?=MSG_t_premoderation;?> "<?=$GLOBALS['inforum']['f_title'];?>"
<? }

function premod_topic(&$pdata) { ?>
<tr><td colspan=3 style="text-align:center"><h5><?=MSG_topic;?>: <?=$pdata['t_title'];?></H5>
<? }

function premod_entry(&$pdata) { ?>
<tr><td width="20%"><?=user_out($pdata['p_uname'],$pdata['p_uid']);?><br><?=long_date_out($pdata['p__time']);?>
<td width="50%"><div style="height: 120px; overflow: auto">
<?=textout($pdata['p_text'],$pdata['p__html'],$pdata['p__bcode'],
$pdata['p__smiles'],$pdata['p_tid'],$pdata['p_id']);?><?
if ($pdata['p_attach']) { if (strpos($pdata['file_type'],"image")===false) { ?>
<br><a href="file.php?fid=<?=$pdata['p_attach'];?>"><?=MSG_p_attachfile;?> (<?=urldecode($pdata['file_name']);?>, <?=$pdata['file_size'];?> <?=MSG_bytes;?>)</a><? }
else { ?><br><a href="file.php?fid=<?=$pdata['p_attach'];?>" target=_blank><img src="file.php?a=preview&amp;fid=<?=$pdata['p_attach'];?>" alt="<?=MSG_p_attachfile;?> (<?=urldecode($pdata['file_name']);?>, <?=$pdata['file_size'];?> <?=MSG_bytes;?>)"></a><? }
} ?>
</div>
<td width="30%"><input type=radio name=pid[<?=$pdata['p_id'];?>] value=0 checked><?=MSG_p_nochange;?><br>
<input type=radio name=pid[<?=$pdata['p_id'];?>] value=1><?=MSG_p_allow;?><br>
<input type=radio name=pid[<?=$pdata['p_id'];?>] value=2><?=MSG_delete;?>
<br><br><a href='index.php?a=edit&amp;t=<?=$pdata['p_tid'];?>&amp;p=<?=$pdata['p_id'];?>' target="_blank"><?=MSG_edit;?></a>
<? if ($pdata['ph_id']) { ?>
<br><br><a target="_blank" href="file.php?a=photo&amp;ph=<?=$pdata['ph_id'];?>&amp;key=<?=$pdata['ph_key'];?>"><?=MSG_preview;?></a>
<? }
}

function premod_end() { ?>
<tr><td class="tablehead" colspan=3>
<input type=hidden name=m value=moderate><input type=hidden name=a value=do_premod>
<input type=hidden name=f value=<?=$GLOBALS['forum'];?>><input type=submit value=<?=MSG_save;?>>
</table></form>
<? }

function mod_ban_start() { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_mod_bannedlist;?> "<?=$GLOBALS['inforum']['f_title'];?>"
<? }

function mod_ban_entry(&$udata) { ?>
<tr><td><?=user_out($udata['u__name'],$udata['u_id']);?>
<td><a href="index.php?m=moderate&a=do_mod_clearban&f=<?=$GLOBALS['forum'];?>&u=<?=$udata['u_id'];?>&key=<?=md5($GLOBALS['inuser']['u__key'].$GLOBALS['forum']);?>"><?=MSG_mod_unban;?></a>
<? }

function mod_ban_noentries() { ?>
<tr><td colspan=2 style="text-align: center"><?=MSG_mod_nobanned;?>
<? }

function mod_ban_end() { ?>
<tr><td class="tablehead" colspan=2><?=MSG_mod_banuser;?>
<tr><td width="50%"><?=MSG_mod_banname;?><td><input type=text name=uname size=32 maxlength=32>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=moderate>
<input type=hidden name=a value=do_mod_addban><input type=hidden name=f value=<?=$GLOBALS['forum'];?>>
<input type=submit value="<?=MSG_mod_ban;?>">
</table></form>
<? }

function complain_form() { ?>
<form action="index.php" method=POST>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_p_sendtomoder;?> "<?=$GLOBALS['intopic']['t_title'];?>" <?=MSG_inforum;?> "<?=$GLOBALS['inforum']['f_title'];?>"

<tr><td width="50%"><?=MSG_p_complain;?>:<br>
<span class="descr"><?=MSG_p_complaindescr;?></span>
<td>
<textarea name=text rows=8 cols=40></textarea>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=moderate><input type=hidden name=a value=do_complain>
<input type=hidden name=t value=<?=$GLOBALS['topic'];?>><input type=hidden name=p value=<?=getvar("p");?>>
<input type=submit value="<?=MSG_send;?>">
</table></form>
<? }

function vote_view_start($pldata) { ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=MSG_mod_voteview;?>: "<?=$pldata['pl_title'];?>"
<? }

function vote_view_entry($pvdata) { ?>
<tr><td width="20%"><?=user_out($pvdata['u__name'],$pvdata['u_id']);?>
<td><?=MSG_mod_votedfor;?> "<?=$pvdata['pv_text'];?>"
<? }

function vote_view_end() { ?>
</table>
<? }