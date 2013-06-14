<?php

function pm_newlist_start() { ?>
<table class="innertable" width="100%" cellspacing=1><tr>
<td class="tablehead" colspan=6><?=MSG_pm;?>
<tr><td width="15%" class="tablehead"><?=MSG_pm_correspondent;?>
<td class="tablehead"><?=MSG_pm_subj;?>
<td width="10%" class="tablehead"><?=MSG_pm_count;?>
<td width="10%" class="tablehead"><?=MSG_pm_unread;?>
<td width="15%" class="tablehead"><?=MSG_pm_startdate;?>
<td width="15%" class="tablehead"><?=MSG_pm_lastdate;?>
<? }

function pm_newlist_entry(&$pmdata) {
if ($pmdata['pm_unread']>0) $style='style="font-weight: bold"'; ?>
<tr <?=$style;?>><td style='text-align: center'><?=user_out($pmdata['u__name'],$pmdata['u_id']);?>
<td><? if ($pmdata['pm_unread']) { ?><b><? } ?><a href="index.php?m=messages&amp;a=view_msg_list&amp;pm_id=<?=$pmdata['pm_id'];?>"><?=$pmdata['pm_subj'];?></a>
<? if ($pmdata['pm_unread']) { ?></b><? } ?>
<td style='text-align: center'><?=$pmdata['pm_count'];?>
<td style='text-align: center'><?=$pmdata['pm_unread'];?>
<td style='text-align: center'><?=long_date_out($pmdata['pm_start']);?>
<td style='text-align: center'><?=long_date_out($pmdata['pm_last']);?>
<? }

function pm_newlist_end() { ?>
</table>
<? }

function pm_msglist_start($subj) { ?>
<script type="text/javascript"><!--
function SetStatus(status) {
        for (i=0; i<document.msglist.elements.length; i++)
  if (document.msglist.elements[i].name && document.msglist.elements[i].name.indexOf("delete")>-1) { document.msglist.elements[i].checked=status; }
}
//--></script>
<form action="index.php" method=POST name="msglist">
<table class="innertable posttable" style="width: 100%; border-spacing: 1px"><tr>
<td class="tablehead"><div style="float: left"><input type=checkbox onClick="SetStatus(this.checked)"></div><?=MSG_pm_subj_list;?>: "<?=$subj;?>"
<? }

function pm_msglist_entry(&$pmdata) {
  if ($pmdata['pm__box']==1) $class="postentry2";
  else $class="postentry";
  $counter++;
?><tr><td class="<?=$class;?>"><div class="descr postlinks">
<? if ($pmdata['pm__box']==0 || $pmdata['pm__box']==3) { ?><?=MSG_pm_receiver;?>:
<?=user_out($pmdata['u__name'],$pmdata['u_id']);?>
 (<? if ($pmdata['u_status']!=-1) { ?><a onClick="return confirm('<?=MSG_p_ignore_warn1.' '.$pmdata['u__name'].' '.MSG_p_ignore_warn2;?>')" href="index.php?m=addrbook&amp;a=do_ignore&amp;uid=<?=$pmdata['u_id'];?>"><?=MSG_p_ignore;?></a><?
    if ($pmdata['u_status']!=1) { ?> | <? }
}
if ($pmdata['u_status']!=1) { ?><a onClick="return confirm('<?=MSG_p_friend_warn1.' '.$pmdata['u__name'].' '.MSG_p_friend_warn2;?>')" href="index.php?m=addrbook&amp;a=do_friend&amp;uid=<?=$pmdata['u_id'];?>"><?=MSG_p_friend;?></a><? } ?>)
<? }
else { ?><?=MSG_pm_sender;?>: <?=user_out($GLOBALS['inuser']['u__name'],$GLOBALS['inuser']['u_id']);?><? } ?>
<? if ($pmdata['pm__box']!=2) { ?>
<?=MSG_pm_sendtime;?>: <?=long_date_out($pmdata['pm__senddate']);?> &nbsp;
<?=MSG_pm_recvtime;?>:
<? if ($pmdata['pm__readdate']) { ?><?=long_date_out($pmdata['pm__readdate']);?><? }
else { ?><?=MSG_none;?><? }
} ?><br>
<input type=checkbox name="delete[<?=$pmdata['pm_id'];?>]" value=1>
<a href="index.php?m=messages&a=do_delete&msg=<?=$pmdata['pm_id'];?>"><?=MSG_delete;?></a> &nbsp;
<a href="<?=$_SERVER['REQUEST_URI'];?>#answer"><?=MSG_pm_answer;?></a> &nbsp;
<a onmouseover="copyQN('<?=$pdata['p_uname'];?>','p<?=$pdata['p_id'];?>');"
href="<?=$_SERVER['REQUEST_URI'];?>" onClick="javascript:pasteQ(); moveForm('<?=$pdata['p_id'];?>');return false;"
title="<?=MSG_p_quotehelp;?>"><?=MSG_p_quote;?></a>
</div><div style="overflow: auto;">
<?=textout($pmdata['pm_text'],1,$pmdata['pm_bcode'],$pmdata['pm_smiles']);?>
<?
if ($pmdata['pm__box']==0 || $pmdata['pm__box']==3) {
if ($pmdata['pm_signature'] && $pmdata['u_signature'] && $GLOBALS['inuser']['u_nosigns']==0) { ?>
<div class="sign">---<br>
<?=textout($pmdata['u_signature'],1,1,1);?></div>
<? }
}
else {
if ($pmdata['pm_signature'] && $GLOBALS['inuser']['u_signature']) { ?><div class="sign">---<br>
<?=textout($GLOBALS['inuser']['u_signature'],1,1,1);?></div>
<? }
}
?></div><tr><td class="<?=$class;?>" style="padding: 0">
<? }

function pm_msglist_end() { ?>
<tr><td class="tablehead"><input type=hidden name=m value=messages>
<input type=hidden name=a value=do_delall><input type=submit value="<?=MSG_pm_delete;?>">
</table></form>
<? }

function pm_head() { ?>
<table class="innertable" width="100%" cellspacing=1><tr>
<td class="tablehead"><?=MSG_pm_yours;?>
<tr><td align=center>
<? if ($GLOBALS['action']!='view') { ?><a href="index.php?m=messages"><?=MSG_pm;?></a> &nbsp;
<? } else { ?><?=MSG_pm;?> &nbsp; <? }
if (getvar("box")!=2) { ?><a href="index.php?m=messages&amp;a=viewbox&amp;box=2"><?=MSG_pm_drafts;?></a> &nbsp; <? }
else { ?><?=MSG_pm_drafts;?> &nbsp; <? }
if ($GLOBALS['action']!="newmsg") { ?><a href="index.php?m=messages&amp;a=newmsg"><?=MSG_pm_newmsg;?></a><? }
else { ?><?=MSG_pm_newmsg;?><? } ?>
</table><br>
<? }

function pm_list_start() { ?>
<script type="text/javascript"><!--
function SetStatus(status) {
        for (i=0; i<document.msglist.elements.length; i++)
  if (document.msglist.elements[i].name && document.msglist.elements[i].name.indexOf("delete")>-1) { document.msglist.elements[i].checked=status; }
}
//--></script>
<form action="index.php" method=POST name="msglist">
<table class="innertable" width="100%" cellspacing=1><tr>
<td class="tablehead" colspan=5><?=MSG_pm_list;?>
<tr><td class="tablehead"><input type=checkbox onClick="SetStatus(this.checked)">
<td class="tablehead"><? if (getvar('box')==0 || getvar('box')==3) { ?><?=MSG_pm_sender;?> <? }
else { ?><?=MSG_pm_receiver;?> <? } ?>
<td class="tablehead"><?=MSG_pm_sendtime;?><td class="tablehead"><?=MSG_pm_recvtime;?>
<td class="tablehead"><?=MSG_pm_subj;?>
<? }

function pm_list_entry($pmdata) { ?>
<tr>
<td width="5%" align=center><input type=checkbox name="delete[<?=$pmdata['pm_id'];?>]" value=1>
<td width="15%" align=center><?=user_out($pmdata['u__name'],$pmdata['u_id']);?>
<td width="12%" align=center><?=short_date_out($pmdata['pm__senddate']);?>
<td width="12%" align=center><? if ($pmdata['pm__readdate']) echo short_date_out($pmdata['pm__readdate']);
else echo MSG_pm_new; ?>
<td><a href="index.php?m=messages&amp;a=viewmsg&amp;msg=<?=$pmdata['pm_id'];?>"><?=$pmdata['pm_subj'];?></a>
<? }

function pm_list_noentries() { ?>
<tr><td width="20%" colspan=5 align=center><?=MSG_pm_nomsg;?>
<? }

function pm_list_end() { ?>
<tr><td class="tablehead" colspan=5><input type=hidden name=m value=messages>
<input type=hidden name=a value=do_delall><input type=submit value="<?=MSG_pm_delete;?>">
</table></form>
<? }

function pm_edit($pmdata) { ?>
<script type="text/javascript"><!--
function checkform (f) {
rq = ["pm_subj","pm_text","u__name"];
rqs = ["<?=MSG_e_pm_empty;?>","<?=MSG_e_pm_empty;?>","<?=MSG_e_u_nosuchuser;?>"];
var i, j;
for(j=0; j<rq.length; j++) {
for (i=0; i<f.length; i++) {
if (f.elements[i].name == rq[j] && f.elements[i].value == "" ) {
alert(rqs[j]);
f.elements[i].focus();
return false;
}
}
}
return true; }
//-->
</script><a name="answer"></a>
<form action="index.php" method=POST enctype="multipart/form-data" onsubmit="return checkform(this);" name=postform>
<table class="innertable" width="100%" cellspacing=1><tr>
<td class="tablehead" colspan=2><?=MSG_pm_send;?>
<tr><td width="33%">
<?=MSG_pm_receiver;?>:
<td>
<input tabindex=1 type=text name=u__name value="<?=$pmdata['u__name'];?>">
 &nbsp; <? if ($pmdata['u__name']=='') echo msg_friend_list();?>
<tr><td><?=MSG_pm_subj;?>:
<td>
<input tabindex=2 type=text name=pm_subj size=30 maxlength=80 value="<?=$pmdata['pm_subj'];?>">
<tr><td>
<?=MSG_pm_newmsg;?>:
<? if ($GLOBALS['inuser']['u_extform']) { ?><br>
<div style='display: none' id='smiles'><?=list_smiles("AddText",4);?></div>
<? } ?><td style="vertical-align: top">
<div style='display: none; background-image: url("images/clean_small.png"); width: 98%' id='codes'>
<script type="text/javascript" src="langs/<?=$GLOBALS['inuser']['ln_file'];?>/post.js"></script>
<script type="text/javascript" src="styles/<?=$GLOBALS['inuser']['st_file'];?>/post.js"></script>
<? if ($GLOBALS['inuser']['u_extform']) { ?>
<script type="text/javascript">insertcodes();</script>
<? } ?></div><?
if ($GLOBALS['inuser']['u_extform']) { ?>
<textarea tabindex=3 name=p_text rows=12 cols=60 onselect="javascript:storeCaret(this);" onFocus="focused=true" onBlur="focused=false" onclick="javascript:storeCaret(this);" onkeyup="javascript:storeCaret(this);" onchange="javascript:storeCaret(this);" onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}"><? }
else { ?><textarea tabindex=3 name=p_text cols=60 rows=12 onFocus="focused=true" onBlur="focused=false" onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}"><? } ?><?=textarea($pdata['p_text']);?></textarea><?
if (getvar("msg")) echo $pmdata['pm_text'];?></textarea>
<tr><td>
<?=MSG_pm_options;?>:
<td>
<label><input type=checkbox value=1 name=pm_bcode <? check($pmdata['pm_bcode']);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_bcode">BoardCode</a><br>
<label><input type=checkbox value=1 name=pm_smiles <? check($pmdata['pm_smiles']==1);?>><?=MSG_usage;?> </label><a target="_blank" href="index.php?m=misc&amp;a=show_smiles"><?=MSG_p_smiles;?></a><br>
<label><input type=checkbox name=pm_signature value=1 <? check($GLOBALS['inuser']['u_usesignature']==1);?>><?=MSG_attachsign;?></label><br>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=messages><input type=hidden name=a value=do_send>
<input type=submit value="<?=MSG_send;?>"> &nbsp; <input type=submit name=drafts value="<?=MSG_pm_todrafts;?>">
</table></form>
<? }

function pm_message($pmdata) { ?>
<table class="innertable" width="100%" cellspacing=1 cellpadding=2><tr>
<td class="tablehead"><?=MSG_pm_pm;?>
<tr><td><?=MSG_pm_subj;?>: <?=$pmdata['pm_subj'];?><br>
<? if ($pmdata['pm__box']==0 || $pmdata['pm__box']==3) { ?><?=MSG_pm_sender;?>: <? }
else { ?><?=MSG_pm_receiver;?>: <? } ?>
<?=user_out($pmdata['u__name'],$pmdata['u_id']);?><br>
<? if ($pmdata['pm__box']!=2) { ?>
<?=MSG_pm_sendtime;?>: <?=long_date_out($pmdata['pm__senddate']);?><br>
<?=MSG_pm_recvtime;?>:
<? if ($pmdata['pm__readdate']) { ?><?=long_date_out($pmdata['pm__readdate']);?><? }
else { ?><?=MSG_none;?><? }
} ?><br><hr>
<? if ($pmdata['pm__box']==0 || $pmdata['pm__box']==3) { ?>
<a href="index.php?m=messages&a=reply&reply=<?=$pmdata['pm_id'];?>"><?=MSG_pm_answer;?></a> &nbsp;
<? } ?>
<a href="index.php?m=messages&a=do_delete&msg=<?=$pmdata['pm_id'];?>"><?=MSG_delete;?></a> &nbsp;
<? if ($pmdata['pm__box']==0) { ?>
<a href="index.php?m=messages&a=do_move&msg=<?=$pmdata['pm_id'];?>"><?=MSG_pm_toarchive;?></a> &nbsp;
<? }
if ($pmdata['pm__box']==2) { ?>
<a href="index.php?m=messages&a=reply&msg=<?=$pmdata['pm_id'];?>"><?=MSG_pm_editsend;?></a> &nbsp;
<? } ?>
<tr><td><br><?=textout($pmdata['pm_text'],1,$pmdata['pm_bcode'],$pmdata['pm_smiles']);?><br>
<?
if ($pmdata['pm__box']==0 || $pmdata['pm__box']==3) {
if ($pmdata['pm_signature'] && $pmdata['u_signature']) { ?>---<br>
<?=textout($pmdata['u_signature'],0,1,1);?>
<? }
}
else {
if ($pmdata['pm_signature'] && $GLOBALS['inuser']['u_signature']) { ?>---<br>
<?=textout($GLOBALS['inuser']['u_signature'],0,1,1);?>
<? }
}?>
</table></form>
<? }
