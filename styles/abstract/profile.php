<?
function user_rules() { ?>
<table class="innertable" cellspacing=1><tr><td class="tablehead"><?=MSG_forum_rules;?>
<tr><td>
<? $fh=fopen($GLOBALS['opt_dir']."/langs/".$GLOBALS['inuser']['ln_file']."/rules.txt","r");
while ($buffer=fgets($fh)) echo $buffer."<br>";
fclose($fh);
?>
<tr><td class="tablehead">
<? if (!$GLOBALS['opt_noquickreg']) { ?>
<form action="index.php" method=get style="padding: 2px"><input type=hidden name=m value=profile>
<input type=hidden name=a value=register><input type=hidden name=q value=1>
<input type=hidden name=refpage value="<?=htmlspecialchars($_SERVER['HTTP_REFERER']);?>">
<input type=submit value="<?=MSG_forum_quickreg;?>" style="width : 90%"></form>
<? } ?>
<tr><td class="tablehead">
<form action="index.php" method=get style="padding: 2px"><input type=hidden name=m value=profile>
<input type=hidden name=a value=register><input type=hidden name=q value=0>
<input type=hidden name=refpage value="<?=htmlspecialchars($_SERVER['HTTP_REFERER']);?>">
<input type=submit value="<?=MSG_forum_fullreg;?>" style="width : 90%"></form>
<tr><td class="tablehead">
<form action="index.php" method=get style="padding: 2px"><input type=submit value="<?=MSG_forum_disagree;?>" style="width : 90%"></form>
<? if ($GLOBALS['opt_activate']==1) { ?>
<tr><td class="tablehead">
<form action="index.php" method=GET style="padding: 2px"><input type=hidden name=m value=profile>
<input type=hidden name=a value=resend><input type=submit value="<?=MSG_resend;?>" style="width : 90%"></form><? } ?>
</table>
<? }

function user_profile($newaction,$newmodule,&$udata,$styleselect,$langselect,$avatarselect,$newmodule="profile") {
  if ($newaction=='u_save' || $newaction=="u_docreate") $formact='admin/index.php';
  else $formact='index.php';
  ?>
<script language="javascript"><!--
function checkform () {
<? if ($newaction!="do_edit" && $newaction!="u_save") { ?>
rq = ["u__name","u__email","password1"];
rqs = ["<?=MSG_e_u_noname;?>","<?=MSG_e_u_emptymail;?>","<?=MSG_e_u_emptypass;?>"];
<? } else  { ?>
rq = ["u__name","u__email"];
rqs = ["<?=MSG_e_u_noname;?>","<?=MSG_e_u_emptymail;?>"];
<? } ?>
var i, j;
for(j=0; j<rq.length; j++) {
for (i=0; i<document.profile_form.length; i++) {
if (document.profile_form.elements[i].name == rq[j] && document.profile_form.elements[i].value == "" ) {
alert(rqs[j]);
document.profile_form.elements[i].focus();
return false;
}
}
}
if (document.profile_form.password1.value!="" && document.profile_form.password1.value!=document.profile_form.password2.value) {
alert('<?=MSG_e_u_passnotmatch;?>');
return false;
}
return true; }
//-->
</script><? if ($newaction=='do_edit') { ?>
<table style="width: 100%; border: 0; table-layout: fixed"><tr>
<td><form action='index.php' method='get' style="margin: 5px 5%"><input type=hidden name=m value=profile>
<input type=hidden name="a" value="forums_ignore">
<button type='submit' style="width: 80%">
<?=MSG_u_ignoreforums;?></button></form><?
if ($GLOBALS['opt_blog_level']<=$GLOBALS['inuserbasic'] || $GLOBALS['inuser']['u__blog_fid']!=0) { ?>
<td><form action='index.php' method='get' style="margin: 5px 5%"><input type=hidden name=m value=profile>
<input type=hidden name="a" value="change_blog">
<button type='submit' style="width: 80%">
<?=MSG_u_changeblog;?></button></form><? }
if ($GLOBALS['opt_gallery_level']<=$GLOBALS['inuserbasic'] || $GLOBALS['inuser']['u__gallery_fid']!=0) { ?>
<td><form action='index.php' method='get' style="margin: 5px 5%"><input type=hidden name=m value=profile>
<input type=hidden name="a" value="change_gallery">
<button type='submit' style='width: 80%'>
<?=MSG_u_changegallery;?></button></form><? } ?>
<td><form action='index.php' method='get' style="margin: 5px 5%"><input type=hidden name=m value=profile>
<input type=hidden name="a" value="self_delete">
<button type='submit' style='width: 80%'>
<?=MSG_u_selfdelete;?></button></form>
</table>
<? } ?>
<form action="<?=$formact;?>" method="post" id="profile_form" enctype="multipart/form-data" onsubmit="return checkform();">
<table class="innertable" cellspacing=1><tr>
<td colspan=2 class="tablehead"><?=MSG_user_params;?>:
<tr><td width="50%"><?=MSG_user_name;?>: *<br><div class="descr">
<?=MSG_user_allowed;?>: <?=$GLOBALS['opt_nameletters'];?></div>
<td>
<input type=text name=u__name size=20 maxlength=32 value="<?=$udata['u__name'];?>">
<tr><td>
<? if ($newaction=="do_edit") { ?>
<?=MSG_user_oldpass;?>:<br><div class="descr"><?=MSG_user_change;?></div>
<td>
<input type=password name=oldpassword size=32 maxlength=32>
<tr><td>
<? } ?>
<?=MSG_user_newpass;?>: * <?
if ($newaction=="do_update") { ?><br><div class="descr"><?=MSG_user_emptypass;?></div><? } ?>
<td>
<input type=password name=password1 size=32 maxlength=32>
<tr><td>
<?=MSG_user_passconfirm;?>: *
<td>
<input type=password name=password2 size=32 maxlength=32>
<tr><td>
<? if ($GLOBALS['opt_encrypted']==2) { ?><?=MSG_user_encrypted;?>
<td>
<input type=radio name=u_encrypted value=0 <? check($udata['u_encrypted']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_encrypted value=1 <? check($udata['u_encrypted']==1);?>><?=MSG_yes;?>
<tr><td>
<? } ?>
<?=MSG_user_email;?>: *<?
if ($newaction=="do_update") { ?><br><div class="descr"><?=MSG_user_emptyemail;?></div><? } ?>
<td>
<input type=text name=u__email size=32 maxlength=48 value="<?=$udata['u__email'];?>">
<tr><td>
<?=MSG_user_showmail;?>?
<td>
<input type=radio name=u_showmail value=0 <? check($udata['u_showmail']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_showmail value=1 <? check($udata['u_showmail']==1);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=u_showmail value=2 <? check($udata['u_showmail']==2);?>><?=MSG_user_javascript;?> &nbsp;
<input type=radio name=u_showmail value=3 <? check($udata['u_showmail']==3);?>><?=MSG_user_picture;?>
<tr><td>
<? if ($newaction=="u_save") { ?>
<?=MSG_user_active;?>?
<td>
<input type=radio name=u__active value=1 <? check($udata['u__active']==1);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=u__active value=0 <? check($udata['u__active']==0);?>><?=MSG_no;?>
<tr><td>
<? } ?>
<?=MSG_user_lang;?>:
<td><select name=u_lnid><?=$langselect;?></select>
<tr><td>
<?=MSG_user_style;?>:
<td><select name=u_stid><?=$styleselect;?></select>
<tr><td>
<?=MSG_user_signature;?>:<br><div class="descr"><?=MSG_user_whatissign;?></div>
<td>
<textarea name=u_signature rows=3 cols=40><?=$udata['u_signature'];?></textarea>
<tr><td>
<?=MSG_user_usesign;?><br><div class="descr"><?=MSG_user_usesigndescr;?></div>
<td>
<input type=radio name=u_usesignature value=0 <? check($udata['u_usesignature']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_usesignature value=1 <? check($udata['u_usesignature']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_user_detrans;?>?
<td>
<input type=radio name=u_detrans value=0 <? check($udata['u_detrans']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_detrans value=1 <? check($udata['u_detrans']==1);?>><?=MSG_yes;?>
<tr><td>
<?=MSG_user_multilang;?>?<br><div class="descr"><?=MSG_user_multilang_descr;?></div>
<td>
<input type=radio name=u_multilang value=0 <? check($udata['u_multilang']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_multilang value=1 <? check($udata['u_multilang']==1);?>><?=MSG_yes;?>
<tr><td>
<?=MSG_user_extform;?>?<br><div class="descr"><?=MSG_user_extform_descr;?></div>
<td>
<input type=radio name=u_extform value=0 <? check($udata['u_extform']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_extform value=1 <? check($udata['u_extform']==1);?>><?=MSG_yes;?>
<? if (!$_GET['q']) { ?>
<tr><td>
<?=MSG_user_mails;?>?<td>
<input type=radio name=u_nomails value=0 <? check($udata['u_nomails']==0);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=u_nomails value=1 <? check($udata['u_nomails']==1);?>><?=MSG_no;?>
<tr><td>
<?=MSG_user_pmnotify;?>?<td>
<input type=radio name=u_pmnotify value=0 <? check($udata['u_pmnotify']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_pmnotify value=1 <? check($udata['u_pmnotify']==1);?>><?=MSG_yes;?>
<tr><td>
<?=MSG_user_sortposts;?>:<td>
<label><input type=radio name=u_sortposts value=0 <? check($udata['u_sortposts']==0);?>><?=MSG_user_sortdefault;?></label><br>
<input type=radio name=u_sortposts value=1 <? check($udata['u_sortposts']==1);?>><?=MSG_user_sortreverse;?>
<tr><td>
<?=MSG_user_smiles;?><br><div class="descr"><?=MSG_user_smilesdescr;?></div>
<td>
<input type=radio name=u_usesmiles value=0 <? check($udata['u_usesmiles']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_usesmiles value=1 <? check($udata['u_usesmiles']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_user_nosigns;?><br>
<td>
<input type=radio name=u_nosigns value=0 <? check($udata['u_nosigns']==0);?>><?=MSG_yes;?> &nbsp;
<input type=radio name=u_nosigns value=1 <? check($udata['u_nosigns']==1);?>><?=MSG_no;?> &nbsp;
<tr><td>
<?=MSG_user_showavatars;?><br><div class="descr"><?=MSG_user_showavatars_descr;?></div>
<td>
<input type=radio name=u_showavatars value=0 <? check($udata['u_showavatars']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_showavatars value=1 <? check($udata['u_showavatars']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_user_hidden;?>
<td>
<input type=radio name=u_hidden value=0 <? check($udata['u_hidden']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u_hidden value=1 <? check($udata['u_hidden']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_user_tperpage;?>:
<td>
<input type=text name=u_tperpage size=5 maxlength=5 value="<?=$udata['u_tperpage'];?>">
<tr><td>
<?=MSG_user_mperpage;?>:
<td>
<input type=text name=u_mperpage size=5 maxlength=5 value="<?=$udata['u_mperpage'];?>">
<tr><td>
<?=MSG_user_aperpage;?>:
<td>
<input type=text name=u_aperpage size=5 maxlength=5 value="<?=$udata['u_aperpage'];?>">
<tr><td>
<?=MSG_user_prevmsgs;?>:
<td>
<input type=text name=u_prevmsgs size=5 maxlength=5 value="<?=$udata['u_prevmsgs'];?>">
<tr><td>
<?=MSG_user_shortdate;?>:<br><div class="descr"><?=MSG_user_datedescr;?></div>
<td>
<input type=text name=u_sformat size=20 maxlength=20 value="<?=$udata['u_sformat'];?>">
<tr><td>
<?=MSG_user_longdate;?>:
<td>
<input type=text name=u_lformat size=20 maxlength=20 value="<?=$udata['u_lformat'];?>">

<tr><td>
<?=MSG_user_timezonesel;?>:
<td>
<select name=u_timeregion><?=set_select(MSG_user_timezone,$udata['u_timeregion']);?></select>

<tr><td>
<?=MSG_user_timelimit;?>
<td>
<? timelimiter($udata['u_timelimit'],"u_timelimit");?>
<tr><td>
<?=MSG_user_goto;?><td>
<label><input type=radio name=u_goto value=0 <? check($udata['u_goto']==0);?>><?=MSG_goto_topic;?></label><br>
<label><input type=radio name=u_goto value=1 <? check($udata['u_goto']==1);?>><?=MSG_goto_forum;?></label><br>
<label><input type=radio name=u_goto value=2 <? check($udata['u_goto']==2);?>><?=MSG_goto_updated;?></label><br>
<input type=radio name=u_goto value=3 <? check($udata['u_goto']==3);?>><?=MSG_goto_newpost;?>
<tr><td>
<?=MSG_user_topicview;?><td>
<label><input type=radio name=u_firstpost value=0 <? check($udata['u_firstpost']==0);?>><?=MSG_user_topicview_start;?></label><br>
<input type=radio name=u_firstpost value=1 <? check($udata['u_firstpost']==1);?>><?=MSG_user_topicview_end;?>
<tr><td colspan=2 class="tablehead"><?=MSG_user_personalinfo;?>
<tr><td>
<?=MSG_user_realname;?>:
<td>
<input type=text name=u_realname value="<?=$udata['u_realname'];?>" size=40 maxlength=255>
<tr><td>
<?=MSG_user_gender;?>:
<td>
<input type=radio name=u_gender value=0 <? check($udata['u_gender']==0);?>><?=MSG_user_female;?> &nbsp;
<input type=radio name=u_gender value=1 <? check($udata['u_gender']==1);?>><?=MSG_user_male;?> &nbsp;
<input type=radio name=u_gender value=2 <? check($udata['u_gender']==2);?>><?=MSG_user_nogender;?>
<tr><td>
<?=MSG_user_icq;?>:
<td>
<input type=text name=u_icq size=10 maxlength=10 value="<?=$udata['u_icq'];?>">
<tr><td>
<?=MSG_user_aol;?>:
<td>
<input type=text name=u_aol size=20 maxlength=32 value="<?=$udata['u_aol'];?>">
<tr><td>
<?=MSG_user_yahoo;?>:
<td>
<input type=text name=u_yahoo size=20 maxlength=32 value="<?=$udata['u_yahoo'];?>">
<tr><td>
<?=MSG_user_msn;?>:
<td>
<input type=text name=u_msn size=20 maxlength=64 value="<?=$udata['u_msn'];?>">
<tr><td>
<?=MSG_user_jabber;?>:
<td>
<input type=text name=u_jabber size=20 maxlength=64 value="<?=$udata['u_jabber'];?>">
<tr><td>
<?=MSG_user_diary;?>:<br>
<span class="descr"><?=MSG_user_diary_comment;?></span>
<td>
<input type=text name=u_diary size=40 maxlength=128 value="<?=$udata['u_diary'];?>">
<tr><td>
<?=MSG_user_homepage;?>:
<td>
<input type=text name=u_homepage size=40 maxlength=128 value="<?=$udata['u_homepage'];?>">
<tr><td>
<?=MSG_user_location;?>:
<td>
<input type=text name=u_location size=40 maxlength=40 value="<?=$udata['u_location'];?>">
<tr><td>
<?=MSG_user_interests;?>:
<td>
<textarea name=u_interests rows=5 cols=40><?=textarea($udata['u_interests']);?></textarea>
<tr><td>
<?=MSG_user_birthdate;?><br>
<span class="descr"><?=MSG_user_birthdate_desc;?></span><td>
<input type=text size=2 maxlength=2 name=u_bday value="<?=$udata['u_bday'];?>"> <select name=u_bmonth><?=set_select(
"<option value=\"1\">".MSG_January.
"<option value=\"2\">".MSG_February.
"<option value=\"3\">".MSG_March.
"<option value=\"4\">".MSG_April.
"<option value=\"5\">".MSG_May.
"<option value=\"6\">".MSG_June.
"<option value=\"7\">".MSG_July.
"<option value=\"8\">".MSG_August.
"<option value=\"9\">".MSG_September.
"<option value=\"10\">".MSG_October.
"<option value=\"11\">".MSG_November.
"<option value=\"12\">".MSG_December,
$udata['u_bmonth']);?></select>
<input type=text name=u_byear size=4 maxlength=4 value="<?=$udata['u_byear'];?>">
<tr><td>
<?=MSG_user_birthmode;?><td>
<label><input type=radio name=u_bmode value=0 <? check($udata['u_bmode']==0);?>><?=MSG_user_bnone;?></label><br>
<label><input type=radio name=u_bmode value=1 <? check($udata['u_bmode']==1);?>><?=MSG_user_bdate;?></label><br>
<label><input type=radio name=u_bmode value=2 <? check($udata['u_bmode']==2);?>><?=MSG_user_byear;?></label><br>
<input type=radio name=u_bmode value=3 <? check($udata['u_bmode']==3);?>><?=MSG_user_ball;?>

<tr><td class="tablehead" colspan=2><?=MSG_user_personalisation;?>
<? if ($GLOBALS['inuserlevel']>=$GLOBALS['opt_ltitle'] || $newaction=="u_save" || $newaction=="u_docreate") { ?>
<tr><td><?=MSG_user_customtitle;?>
<td><input type=text name=u__title size=40 maxlength=48 value="<?=$udata['u__title'];?>">

<? }
if ($newaction!="u_save" && $newaction!="u_docreate") { ?>
<tr><td><?=MSG_user_avatartype;?>:
<td><script><!--
function preview() {
var pic=document.getElementById('a_preview');
var f=document.getElementById('profile_form');
if (document.getElementById('at0').checked) { pic.src="avatars/noavatar.gif"; }
if (document.getElementById('at1').checked) { pic.src="avatars/"+f.avatar1.value; }
if (document.getElementById('at2').checked) { pic.src=f.avatar2.value; }
if (document.getElementById('at3').checked) { pic.src="file://"+f.avatar3.value; }
}
function set_sel(num) {
document.getElementById('at'+num).checked=true;
if (num<=1) { preview(); }
}
//--></script>
<input type=radio id=at0 name=u_avatartype value=0 <? check($udata['u_avatartype']==0);?> onClick="preview()"><?=MSG_none;?> &nbsp;
<input type=radio id=at1 name=u_avatartype value=1 <? check($udata['u_avatartype']==1);?> onClick="preview()"><?=MSG_user_avatarstd;?> &nbsp;
<input type=radio id=at2 name=u_avatartype value=2 <? check($udata['u_avatartype']==2);?>><?=MSG_user_avatarext;?> &nbsp;
<? if ($GLOBALS['opt_maxavatarsize']) { ?>
<input type=radio id=at3 name=u_avatartype value=3 <? check($udata['u_avatartype']==3);?>><?=MSG_user_avatarupload;?>
<? } ?>
<tr><td><?=MSG_user_avselect;?>
<td><table width="100%"><tr><td width="50%">
<select name=avatar1 size=5 onChange="set_sel(1)"><?
if ($udata['u_avatartype']==1) echo set_select($avatarselect,$udata['u__avatar']);
else echo $avatarselect; ?></select><td><?
if ($udata['u_avatartype']!=0) echo str_replace('>',' id="a_preview">',show_avatar($udata));
else {
  $tmpdata['u_avatartype']=1;
  echo str_replace('>',' id="a_preview">',show_avatar($tmpdata));
} ?>
</table>
<? if ($GLOBALS['opt_maxavatarsize']) { ?>
<tr><td><?=MSG_user_avurl;?>
<td><input type=text name=avatar2 size=30 maxlength=80 onClick="set_sel(2)" onBlur="preview()"
value="<? if ($udata['u_avatartype']==2) echo $udata['u__avatar'];?>"> &nbsp; <input type=button onClick="preview()" value="<?=MSG_preview;?>">
<? } ?>
<tr><td><?=MSG_user_avupload;?>:
<td><input type=file name=avatar3 size=30 maxlength=255 onClick="set_sel(3)" onBlur="preview()">
Max: <?=$GLOBALS['opt_maxavatarx'];?>x<?=$GLOBALS['opt_maxavatary'];?>, <?=ceil($GLOBALS['opt_maxavatarsize']/1024);?> Kb
<input type="hidden" name="MAX_FILE_SIZE" value="<?=max($GLOBALS['opt_maxavatarsize'],$GLOBALS['opt_maxphoto']);?>">
<? if ($GLOBALS['opt_maxphoto']) { ?>
<tr><td><?=MSG_user_photoupload;?>:<br>
<? if ($newaction=="do_edit") {
?><?=MSG_user_photodescr;?><? } ?>
<td><input type=file name=photo1 size=30 maxlength=255>
Max: <?=$GLOBALS['opt_maxphotox'];?>x<?=$GLOBALS['opt_maxphotoy'];?>, <?=ceil($GLOBALS['opt_maxphoto']/1024);?> Kb
<? if ($newaction=="do_edit") {
?><tr><td><?=MSG_user_photodel;?>
<td><input type=checkbox name=photo_del value=1>
<? }
  }
 }
 elseif ($newaction=="u_save") { ?>
<tr><td><?=MSG_user_photodel;?>
<td><input type=checkbox name=photo_del value=1>
<tr><td><?=MSG_user_avatardel;?>
<td><input type=checkbox name=avatar_del value=1>
<tr><td><?=MSG_user_disallow_edit;?>
<td><input type=radio name=u__noedit value=0 <?=check($udata['u__noedit']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=u__noedit value=1 <?=check($udata['u__noedit']==1);?>><?=MSG_yes;?>
 <? }
}
else { ?>
<tr><td><?=MSG_user_claimyours;?>
<td><input type=radio name=claim value=1 checked><?=MSG_yes;?> &nbsp;
<input type=radio name=claim value=0><?=MSG_no;?>
<? }
if ($newaction=="do_register") {
  if ($GLOBALS['opt_ddos']==1 || $GLOBALS['opt_ddos']==2) { ?>
<tr><td><?=MSG_user_ddoscode;?>
<td><?=show_ddos_code();?>
<? } ?>
<tr><td colspan=2 align=center><?=MSG_user_afterreg;?>
<? } ?>
<tr><td colspan=2 class="tablehead">
<? if (getvar("refpage")) { ?><input type=hidden name=refpage value="<?=getvar("refpage");?>"><input type=hidden name=u value=<?=$udata['u_id'];?>><? }
else { ?><input type=hidden name=refpage value="<?=htmlspecialchars($_SERVER['HTTP_REFERER']);?>"><input type=hidden name=u value=<?=$udata['u_id'];?>><? } ?>
<input type=hidden name=m value=<?=$newmodule;?>><input type=hidden name=a value="<?=$newaction;?>">
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function user_big_login() { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2><?=MSG_login;?>
<tr><td>
<?=MSG_input_login;?>:
<td>
<input type=text name=inusername size=32 maxlength=32>
<tr><td>
<?=MSG_input_password;?>:
<td>
<input type=password name="inpassword" size=32 maxlength=32>
<tr><td><?=MSG_login_mode;?>
<td><input type=radio name=login_mode value=0 checked><?=MSG_login_normal;?> &nbsp;
<input type=radio name=login_mode value=1><?=MSG_login_secure;?> &nbsp;
<input type=radio name=login_mode value=2><?=MSG_login_keep;?>
<tr><td colspan=2 align=center>
<a href="index.php?a=password&amp;m=profile"><?=MSG_input_forgot;?>?</a>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=m value=profile>
<input type=hidden name=a value=do_login>
<input type=hidden name=refpage value="<?=htmlspecialchars($_SERVER['HTTP_REFERER']);?>">
<input type=submit value="<?=MSG_dologin;?>">
</table></form>
<? }

function user_list_start($pages) { ?>
<div class="descr" style="text-align: left"><form action="index.php" method=GET>
<?=MSG_u_orderby;?>: <select name="o"><?=set_select("<option value=\"u__regdate\">".MSG_u_regdate.
"<option value=\"u__name\">".MSG_u_name."<option value=\"u__title\">".MSG_u_title.
"<option value=\"u__level\">".MSG_u_level."<option value=\"urating\">".MSG_u_urating.
"<option value=\"u_gender\">".MSG_u_gender."<option value=\"u_location\">".MSG_u_location."<option value=\"u_icq\">".MSG_u_icq."<option value=\"u_homepage\">".MSG_u_homepage."<option value=\"lastvisit\">".MSG_u_lastvisit,getvar("o"));?></select>
<?=MSG_u_order;?> <select name=desc>
<?=set_select("<option value=\"\">".MSG_asc."<option value=\"desc\">".MSG_desc."",getvar("desc"));?></select>
<input type=hidden name=m value="profile"><input type=hidden name=a value="listusers">
<input type=submit value="<?=MSG_show;?>">
</form></div>
<table class="innertable" cellspacing=1 width="100%"><tr><td colspan=12 class="tablehead">
<?=MSG_user_list;?>
<div style="float: right; width: 25%"><?=$pages;?></div>
<tr class="tablehead">
<td><?=MSG_name;?>
<td><?=MSG_title;?>
<td><?=MSG_level;?>
<td><?=MSG_p_count;?>
<td><?=MSG_rating;?>
<td><?=MSG_gender;?>
<td><?=MSG_location;?>
<td><?=MSG_email;?>
<td><?=MSG_homepage;?>
<td>ICQ
<td><?=MSG_regdate;?>
<td><?=MSG_lastvisit;?>
<? }

function user_list_entry(&$udata) { ?>
<tr style="text-align: center">
<td><?=user_out($udata['u__name'],$udata['u_id']);?>
<td><?=$udata['u__title'];?>
<td><?=$udata['l_title'];?>
<td><?=intval($udata['pcount']);?>
<td><?=intval($udata['urating']);?>
<td><? if ($udata['u_gender']==1) { ?><?=MSG_user_male;?><? }
elseif ($udata['u_gender']==0) { ?><?=MSG_user_female;?><? }
else { ?><?=MSG_user_nogender;?><? } ?>
<td><?=$udata['u_location'];?>
<td><?=show_email_q($udata['u__email'],$udata['u_showmail'],$udata['u_id']);?>
<td> <? if ($udata['u_homepage'] && $udata['u_homepage']!="http://") { ?><a href="<?=$udata['u_homepage'];?>" target=_blank><?=MSG_have;?></a><? }
else { ?><?=MSG_none;?><? } ?>
<td><? if ($udata['u_icq']) { ?><img src="http://wwp.icq.com/scripts/online.dll?icq=<?=$udata['u_icq'];?>&amp;img=5" alt="<?=MSG_have;?>"><? }
else { ?><?=MSG_none;?><? } ?>
<td><?=short_date_out($udata['u__regdate']);?>
<td><?=short_date_out($udata['lastvisit']);?>
<? }

function user_list_end() { ?>
<tr><td colspan=12 class="tablehead"><a href="index.php?a=user_search&amp;m=profile"><?=MSG_us_search;?></a>
</table>
<? }

function user_profile_start(&$udata,$lmsg,$allowrate,$addrbook=false) { ?>
<table class="innertable" cellspacing=1 width="100%"><tr>
<td colspan=2 class="tablehead"><?=MSG_user_profile;?> <div class="username"><?=$udata['u__name'];?></div>
<tr><td width="50%"><?=MSG_user_name;?>:
<td><?=$udata['u__name'];?>
<? if ($udata['blog_link']) { ?><br><a href="<?=$udata['blog_link'];?>"><?=MSG_u_viewblog;?></a><? }
if ($udata['gallery_link']) { ?><br><a href="<?=$udata['gallery_link'];?>"><?=MSG_u_viewgallery;?></a><? } ?>
<tr><td>
<?=MSG_user_realname;?>:
<td>
<?=$udata['u_realname'];?>
<? if ($udata['u_avatartype']>0) { ?>
<tr><td width="50%"><?=MSG_user_avatar;?>:
<td><?=show_avatar($udata);?>
<? }
if ($udata['u__photo_id']) { ?>
<tr><td width="50%"><?=MSG_user_photo;?>:
<td><img src="file.php?fid=<?=$udata['u__photo_id'];?>">
<? } ?>
<tr><td><?=MSG_user_customtitle;?>:
<td><?=$udata['u__title'];?>
<tr><td><?=MSG_user_basiclevel;?>:
<td><?=$udata['l_title'];
?><? if ($udata['l_pic']) { ?><br><img src="images/<?=$udata['l_pic'];?>" alt=""><? } ?>
<tr><td><?=MSG_user_gender;?>:
<td><? if ($udata['u_gender']==1) { ?><?=MSG_user_male;?><? }
elseif ($udata['u_gender']==0) { ?><?=MSG_user_female;?><? }
else { ?><?=MSG_user_nogender;?><? } ?>
<? if ($udata['u_bmode'] && $udata['u_bday'] && $udata['u_bmonth'] && $udata['u_byear']) { ?>
<tr><td>
<? if ($udata['u_bmode']==1 || $udata['u_bmode']==3) { ?><?=MSG_user_birthdate;?><? }
else { ?><?=MSG_user_age;?><? } ?><td><?
if ($udata['u_bmode']==1 && $udata['u_bday'] && $udata['u_bmonth']) { ?><?=$udata['u_bday'];?> <?=month_replace(date("F",mktime(0,0,0,$udata['u_bmonth'],$udata['u_bday'],1980)));?><? }
elseif ($udata['u_bmode']==2 && $udata['u_byear']) { ?><?=date("Y",$GLOBALS['curtime'])-$udata['u_byear']-1;?>
 <?=MSG_or;?> <?=format_word(date("Y",$GLOBALS['curtime'])-$udata['u_byear'],MSG_user_age1,MSG_user_age2,MSG_user_age3);?><? }
elseif ($udata['u_bmode']==3) { ?><?=month_replace(date("d F",mktime(0,0,0,$udata['u_bmonth'],$udata['u_bday'],1980)));?> <?=$udata['u_byear'];?><? } ?>

<? } ?>
<tr><td><?=MSG_user_location;?>:
<td><?=$udata['u_location'];?>
<tr><td>
<?=MSG_user_interests;?>:
<td><?=$udata['u_interests'];?>
<tr><td><?=MSG_user_email;?>:
<td><? show_email_f($udata['u__email'],$udata['u_showmail'],$udata['u_id']);?>
<tr><td><?=MSG_user_homepage;?>:
<td> <? if ($udata['u_homepage'] && $udata['u_homepage']!="http://") { ?><a href="<?=$udata['u_homepage'];?>" target=_blank><?=$udata['u_homepage'];?></a><? }
else { ?><?=MSG_no;?><? } ?>
<tr><td>ICQ:
<td><? if ($udata['u_icq']) { ?><img src="http://wwp.icq.com/scripts/online.dll?icq=<?=$udata['u_icq'];?>&amp;img=5" border=0 alt=""><?=$udata['u_icq'];?> <? }
else { ?><?=MSG_no;?><? } ?>
<tr><td><?=MSG_user_aol;?>:
<td><? if ($udata['u_aol']) { ?><?=$udata['u_aol'];?> <? }
else { ?><?=MSG_no;?><? } ?>
<tr><td><?=MSG_user_yahoo;?>:
<td><? if ($udata['u_yahoo']) { ?><?=$udata['u_yahoo'];?> <? }
else { ?><?=MSG_no;?><? } ?>
<tr><td><?=MSG_user_msn;?>:
<td><? if ($udata['u_msn']) { ?><?=$udata['u_msn'];?> <? }
else { ?><?=MSG_no;?><? } ?>
<tr><td><?=MSG_user_jabber;?>:
<td><? if ($udata['u_jabber']) { ?><?=$udata['u_jabber'];?> <? }
else { ?><?=MSG_no;?><? } ?>
<tr><td><?=MSG_user_diary;?>:
<td><? if ($udata['u_diary']) { ?><a href="<?=$udata['u_diary'];?>" target=_blank><?=$udata['u_diary'];?></a><? }
else { ?><?=MSG_no;?><? } ?>
<? if ($GLOBALS['opt_rating']==0) { ?>
<tr><td><?=MSG_user_rating;?>:
<td><?=intval($udata['u_rating']) ?><? if ($GLOBALS['inuserid']>3 && $GLOBALS['inuserid']!=$udata['u_id'] && $allowrate) { ?>
&nbsp; <a href="index.php?a=do_user_rate&amp;m=profile&amp;dir=pro&amp;u=<?=$udata['u_id'];?>"><?=MSG_user_ratepro;?></a> &nbsp;
<a href="index.php?a=do_user_rate&amp;m=profile&amp;dir=contra&amp;u=<?=$udata['u_id'];?>"><?=MSG_user_ratecontra;?></a>
<? } ?>
<? }
if ($GLOBALS['opt_reputation']==0 || $GLOBALS['inuserbasic']>=1000) { ?>
<tr><td><a href="index.php?m=profile&amp;a=list_warn&amp;u=<?=$udata['u_id'];?>"><?=MSG_user_reputation;?></a>:
<td><?=intval($udata['uw_count']);?>
<tr><td><?=MSG_user_onforum;?>
<td><? if ($GLOBALS['opt_topiccount']!=1) { ?><?=MSG_user_created;?> <?=format_word($udata['u_tcount'],MSG_t1,MSG_t2,MSG_t3);?><br><? } ?>
<?=MSG_user_posted;?> <?=format_word($udata['u_pcount'],MSG_p1,MSG_p2,MSG_p3);?><br>
<? if ($GLOBALS['opt_topiccount']!=1) { ?><?=MSG_user_polled;?> <?=format_word($udata['u_plcount'],MSG_pl1,MSG_pl2,MSG_pl3);?><br><? } ?>
<?=MSG_user_voted;?> <?=format_word($udata['u_vcount'],MSG_vt1,MSG_vt2,MSG_vt3);?>
<? } ?>
<tr><td><?=MSG_user_regdate;?>:
<td><?=long_date_out($udata['u__regdate']);?>
<tr><td><?=MSG_user_lastprofile;?>:
<td><?=long_date_out($udata['u__profileupdate']);?>
<tr><td><?=MSG_user_lastvisit;?>:
<td><?=long_date_out($udata['u_lastvisit']);?>
<? if ($GLOBALS['opt_topiccount']!=1) { ?>
<tr><td><?=MSG_user_lastpost;?>:
<td><? if (!$lmsg) { ?><?=MSG_none;?><? }
elseif ($lmsg['f_lview']<=$GLOBALS['inuserlevel']) { long_date_out($lmsg['p__time']);?> <?=MSG_intopic;?> "<a href="index.php?t=<?=$lmsg['t_id'];?>"><?=$lmsg['t_title'];?></a>"
<?=MSG_inforum;?> "<a href="index.php?f=<?=$lmsg['f_id'];?>"><?=$lmsg['f_title'];?></a>" <? } 
} ?>
<tr><td colspan=2 class="tablehead"><?=MSG_user_relations;?>
<tr><td><?=MSG_user_freinds;?>:
<td><? if ($addrbook['friends']) { echo '('.count($addrbook['friends']).') '.join(', ',$addrbook['friends']); } ?>
<tr><td><?=MSG_user_ignore;?>:
<td><? if ($addrbook['ignore']) { echo '('.count($addrbook['ignore']).') '.join(', ',$addrbook['ignore']); } ?>
<tr><td><?=MSG_user_friended;?>:
<td><? if ($addrbook['friended']) { echo '('.count($addrbook['friended']).') '.join(', ',$addrbook['friended']); } ?>
<tr><td><?=MSG_user_ignored;?>:
<td><? if ($addrbook['ignored']) { echo '('.count($addrbook['ignored']).') '.join(', ',$addrbook['ignored']); } ?>
<? 
}

function user_profile_list() { ?>
<tr><td class="tablehead" colspan=2><?=MSG_user_perforum;?>:
<tr><td colspan=2 style="padding: 0px">
<table cellpadding=0 cellspacing=1 width="100%">
<tr><td class="tablehead" width="50%"><?=MSG_f;?><td class="tablehead" width="20%"><?=MSG_f_speclevel;?>
<td class="tablehead" width="20%"><?=MSG_f_lastvisit;?><td class="tablehead" width="10%"><?=MSG_f_msgcount;?>
<? }

function user_profile_entry(&$fdata,$total) { ?>
<tr><td><a href="index.php?ct=<?=$fdata['ct_id'];?>"><?=$fdata['ct_name'];?></a> &raquo; <a href="index.php?f=<?=$fdata['f_id'];?>"><?=$fdata['f_title'];?></a>
<td align=center><?=$fdata['l_title'];?><td align=center>
<?=long_date_out($fdata['lv_time']); ?>
<td align=center><?=$fdata['f_count'];?> <?
if ($fdata['f_nostats']==0) { ?>(<?=sprintf("%.2f",$fdata['f_count']*100/$total);?>%)<? } ?>

<? }

function user_profile_finish() { ?>
</table>
<? }

function user_profile_end(&$udata,$flist,$addrbook=false) { ?>
<tr><td colspan=2 class="tablehead"><?=MSG_user_connect;?>
<? if ($GLOBALS['inuserid']>3) { ?>
<tr><td colspan=2><a href="index.php?m=messages&amp;a=newmsg&amp;u=<?=$udata['u_id'];?>"><?=MSG_user_pm;?></a>
<? } ?>
<tr><td colspan=2><a href="index.php?m=misc&amp;a=sendmail&amp;u=<?=$udata['u_id'];?>"><?=MSG_user_sendmail;?></a>
<tr><td colspan=2><a href="index.php?m=search&amp;a=do_topic&amp;fs=all&amp;res=topic&amp;username=<?=urlencode($udata['u__name']);?>&amp;order=p__time&amp;nogrp=1"><?=MSG_user_searchtopic;?></a>
<tr><td colspan=2><a href="index.php?m=search&amp;a=do_post&amp;fs=all&amp;res=post&amp;username=<?=urlencode($udata['u__name']);?>&amp;order=p__time&amp;nogrp=1"><?=MSG_user_searchposts;?></a>
<? if (!$addrbook['is_ignored'] && $udata['u_id']!=$GLOBALS['inuserid']) { ?>
<tr><td colspan=2><a onClick="return confirm('<?=MSG_p_ignore_warn1.' '.$udata['u__name'].' '.MSG_p_ignore_warn2;?>')" href="index.php?m=addrbook&amp;a=do_ignore&amp;uid=<?=$udata['u_id'];?>"><?=MSG_p_ignore;?></a>
<? }
if (!$addrbook['is_friend'] && $udata['u_id']!=$GLOBALS['inuserid']) { ?>
<tr><td colspan=2><a onClick="return confirm('<?=MSG_p_friend_warn1.' '.$pmdata['u__name'].' '.MSG_p_friend_warn2;?>')" href="index.php?m=addrbook&amp;a=do_friend&amp;uid=<?=$udata['u_id'];?>"><?=MSG_p_friend;?></a>
<? } ?>
</table>
<? }

function online_start() {
if ($GLOBALS['inuserlevel']>=1000) { ?>
<table cellspacing=1 class="innertable" width="100%"><tr><td class="tablehead" colspan=3>
<? }
else { ?>
<table cellspacing=1 class="innertable" width="100%"><tr><td class="tablehead" colspan=2>
<? } ?>
<?=MSG_present;?>
<? }

function online_entry(&$udata,$comment) { ?>
<tr><td width="25%"><?=user_out($udata['u__name'],$udata['u_id']);?><br>
<?=long_date_out($udata['uo_time']);?>
<? if ($GLOBALS['inuserlevel']>=1000) { ?>
<td align='center' width="15%"><a target=_blank href='http://nic.ru/whois/?ip=<?=numtoip($udata['uo_ip']);?>'>
<?=numtoip($udata['uo_ip']);?></a></td><? } ?>
<td><?=$comment;?>
<? }

function online_end() { ?>
</table>
<? }

function get_password_form() { ?>
<form action=index.php method=POST>
<table cellspacing=1 class="innertable" width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_pass_restore;?>
<tr><td colspan=2 align=center>
<?=MSG_pass_data;?>
<tr><td width="50%"><?=MSG_pass_name;?>:
<td><input type=text name=login size=32 maxlength=32>
<tr><td><?=MSG_pass_email;?>:
<td><input type=text name=email size=32 maxlength=48>
<tr><td><?=MSG_pass_uid;?>:
<td><input type=text name=number size=10 maxlength=10>
<?  if ($GLOBALS['opt_ddos']>0) { ?>
<tr><td><?=MSG_user_ddoscode;?>
<td><?=show_ddos_code();?>
<? } ?>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value=do_password>
<input type=hidden name=m value=profile>
<input type=submit value="<?=MSG_pass_dosend;?>">
</table></form>
<? }

function resend_form() { ?>
<form action=index.php method=POST>
<table cellspacing=1 class="innertable" width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_resend_done;?>
<tr><td colspan=2 align=center>
<?=MSG_resend_text;?>
<tr><td width="50%"><?=MSG_resend_name;?>:
<td><input type=text name=uname size=32 maxlength=32>
<tr><td><?=MSG_resend_pass;?>:
<td><input type=password name=password1 size=32 maxlength=32>
<tr><td><?=MSG_resend_newmail;?>:
<td><input type=text name=newmail size=32 maxlength=48>
<?  if ($GLOBALS['opt_ddos']>0) { ?>
<?=show_ddos_code();?>
<? } ?>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value=do_resend>
<input type=hidden name=m value=profile>
<input type=submit value="<?=MSG_send;?>">
</table></form>
<? }


function warn_form_start(&$pdata,$count) { ?>
<form action=index.php method=POST>
<table cellspacing=1 class="innertable" width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_u_warnlist;?> <?=user_out($pdata['u__name'],$pdata['u_id']);?>
<? }

function warn_form_entry(&$warndata) { ?>
<tr><td colspan=2><b><? if ($warndata['uw_value']==-1) { ?><?=MSG_warn_warning;?><? }
else { ?><?=MSG_warn_award;?><? } ?> <?=MSG_warn_from;?> <?=user_out($warndata['u__name'],$warndata['u_id']);?>:</b><br>
<?=nl2br($warndata['uw_comment']);?>
<? }

function warn_form_noentries() { ?>
<tr><td colspan=2><?=MSG_warn_none;?>.
<? }

function warn_form_end() { ?>
</table>
<? }

function warn_form_input() { ?>
<br>
<form action=index.php method=POST>
<table cellspacing=1 class="innertable" width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_warn;?> <?=MSG_intopic;?> "<a href="index.php?t=<?=$GLOBALS['intopic']['t_id'];?>">
<?=$GLOBALS['intopic']['t_title'];?></a>" <?=MSG_inforum;?> "<a href="index.php?f=<?=$GLOBALS['inforum']['f_id'];?>"><?=$GLOBALS['inforum']['f_title'];?></a>".
<tr><td width="50%">
<?=MSG_warndo;?>:<td>
<label><input type=radio name=mode value=warn checked><?=MSG_warn_warning;?></label><br>
<? if ($GLOBALS['inuserlevel']>=1000) { ?><label><input type=radio name=mode value=ban><?=MSG_warn_ban;?></label><br><? } ?>
<input type=radio name=mode value=award><?=MSG_warn_award;?>
<tr><td>
<?=MSG_warn_validtill;?>:<td>
<label><input type=radio name=valid value=endless><?=MSG_warn_endless;?> &nbsp; </label><input type=radio name=valid value=enddate checked><?=MSG_warn_tilldate;?>
<tr><td>
<?=MSG_warn_enddate;?>:<td>
<?=build_date_field("enddate",$GLOBALS['curtime']+30*24*60*60);?>
<tr><td>
<?=MSG_warn_comment;?>:<br>
<?=MSG_warn_commentdescr;?><td>
<textarea name="comment" rows=4 cols=40></textarea>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=profile>
<input type=hidden name=a value=do_warn><input type=hidden name=t value="<?=$GLOBALS['topic'];?>">
<input type=hidden name=u value="<?=getvar("u");?>"><input type=submit value="<?=MSG_warndo;?>">
</table></form>
<? }

function user_search_form($levels) { ?>
<form action=index.php method=GET>
<table cellspacing=1 class="innertable" width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_us_extsearch;?>
<tr><td>
<?=MSG_user_name;?>:<td><input type=text name=name value="<?=getvar('name');?>" size=32 maxlength=32>
<tr><td>
<?=MSG_us_mode;?>:<td>
<input type=radio name=mode value="" <?=check(!getvar('mode'));?>> <?=MSG_us_exact;?> &nbsp;
<input type=radio name=mode value="1" <?=check(getvar('mode')==1);?>> <?=MSG_us_begins;?> &nbsp;
<input type=radio name=mode value="2" <?=check(getvar('mode')==2);?>> <?=MSG_us_contains;?>
<tr><td>
<?=MSG_us_email;?>:<td>
<input type=checkbox name=email value=1 <?=check(getvar('email'));?>>
<tr><td>
<?=MSG_us_ICQ;?>:<td>
<input type=checkbox name=icq value=1 <?=check(getvar('icq'));?>>
<tr><td>
<?=MSG_us_photo;?>:<td>
<input type=checkbox name=photo value=1 <?=check(getvar('photo'));?>>
<tr><td>
<?=MSG_us_minlevel;?>:<td>
<select name=level><?=set_select($levels,getvar('level'));?></select>
<tr><td>
<?=MSG_user_interests;?><td>
<input type=text size=40 maxlength=255 name=interests value="<?=getvar('interests');?>">

<tr><td colspan=2 class="tablehead"><input type=hidden name=m value=profile>
<input type=hidden name=a value=user_search_result><input type=submit value="<?=MSG_us_search;?>">
</table></form>
<? }

function user_result_start($pages) { ?>
<table class="innertable" cellspacing=1 width="100%"><tr class="tablehead"><td colspan=12>
<?=MSG_us_result;?>
<div style="float: right; width: 25%"><?=$pages;?></div>
<tr class="tablehead">
<td><?=MSG_name;?>
<td><?=MSG_title;?>
<td><?=MSG_level;?>
<td><?=MSG_p_count;?>
<td><?=MSG_rating;?>
<td><?=MSG_gender;?>
<td><?=MSG_location;?>
<td><?=MSG_email;?>
<td><?=MSG_homepage;?>
<td>ICQ
<td><?=MSG_regdate;?>
<td><?=MSG_lastvisit;?>
<? }

function user_result_end($pages) { ?>
<tr class="tablehead"><td colspan=12 style="text-align: right">
<?=$pages;?>
</table><br>
<? }

function change_blog_form($fdata,$levelselect) { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_params;?>
<tr><td width="50%"><?=MSG_blog_maintitle;?>:
<td>
<input type=text name=f_title size=30 maxlength=60 value="<?=$fdata['f_title'];?>">
<tr><td><?=MSG_blog_descr;?>:
<td>
<textarea name=f_descr rows=3 cols=30><?=$fdata['f_descr'];?></textarea>
<tr><td><?=MSG_blog_lview;?>
<td>
<select name=f_lview><? set_select($levelselect,$fdata['f_lview']);?></select>
<tr><td>
<?=MSG_blog_lread;?>
<td>
<select name=f_lread><? set_select($levelselect,$fdata['f_lread']);?></select>
<tr><td>
<?=MSG_blog_lpost;?>
<td>
<select name=f_lpost><? set_select($levelselect,$fdata['f_lpost']);?></select>
<tr><td>
<?=MSG_blog_lpremod;?>
<td>
<select name=f_lpremod><? set_select($levelselect,$fdata['f_lpremod']);?></select>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=f_text value="">
<input type=hidden name=a value="do_change_blog">
<input type=hidden name=m value="profile">
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function change_gallery_form($fdata,$levelselect) { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_params;?>
<tr><td width="50%"><?=MSG_gallery_maintitle;?>:
<td>
<input type=text name=f_title size=30 maxlength=60 value="<?=$fdata['f_title'];?>">
<tr><td><?=MSG_gallery_descr;?>:
<td>
<textarea name=f_descr rows=3 cols=30><?=$fdata['f_descr'];?></textarea>
<tr><td><?=MSG_gallery_lview;?>
<td>
<select name=f_lview><? set_select($levelselect,$fdata['f_lview']);?></select>
<tr><td>
<?=MSG_gallery_lread;?>
<td>
<select name=f_lread><? set_select($levelselect,$fdata['f_lread']);?></select>
<tr><td>
<?=MSG_gallery_lpost;?>
<td>
<select name=f_lpost><? set_select($levelselect,$fdata['f_lpost']);?></select>
<tr><td>
<?=MSG_gallery_lpremod;?>
<td>
<select name=f_lpremod><? set_select($levelselect,$fdata['f_lpremod']);?></select>
<tr><td class="tablehead" colspan=2>
<input type=hidden name=f_text value="">
<input type=hidden name=a value="do_change_gallery">
<input type=hidden name=m value="profile">
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function self_del_confirm_form() { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_user_delete;?>
<tr><td colspan=2 style="text-align: center"><?=MSG_user_delete_info;?>
<tr><td width="50%"><?=MSG_input_password;?>:
<td><input type="password" size=20 name="pass">
<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="do_self_delete">
<input type=hidden name=m value="profile">
<input type=submit value="<?=MSG_delete;?>">
</table></form>
<? }

function ignore_list_start() { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_ignore_forums;?>
<? }

function ignore_list_entry(&$fdata) { ?>
<tr><td width="70%"><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a>
<td align=center><input type=checkbox name="ignore[<?=$fdata['f_id'];?>]" value=1 <? check($fdata['ignored']);?>>
<? }

function ignore_list_end($inform,$autosub) { ?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=profile>
<input type=hidden name=a value=do_forums_ignore>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }
