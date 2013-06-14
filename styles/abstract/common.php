<?

function rating($rate,&$topic) { ?>
<div class="descr"><?=MSG_t_currating;?>: <? if ($topic['trating']) echo $topic['trating'];
else { ?><?=MSG_none;?><? } ?></div><?
if (!$rate && $GLOBALS['inuserid']>3) { ?>
<form class="descr" action="index.php" method=POST><?=MSG_t_rate;?>: <select name=tr_value>
<option value=1>1
<option value=2>2
<option value=3>3
<option value=4>4
<option value=5>5
<option value=6>6
<option value=7>7
</select> &nbsp; <input type=submit value="<?=MSG_t_dorate;?>">
<input type=hidden name=a value=do_rate>
<input type=hidden name=t value=<?=$topic['t_id'];?>>
</form>
<? } ?>
<? }

function quick_login_form() { ?>
<tr><td><?=MSG_input_login;?>:
<td><input type=text name=inusername size=32 maxlength=32> &nbsp; <a href="index.php?m=profile&amp;a=rules"><?=MSG_register;?></a>
<tr><td><?=MSG_input_password;?>:
<td><input type=password name=inpassword size=32 maxlength=32> &nbsp; <a href="index.php?m=profile&amp;a=password"><?=MSG_forgot_password;?>?</a>
<? }

function main_time_diff($time,$queries,$time2) { ?>
<br><br>
<div class="maintext" style="text-align: center"><?=MSG_forum_exectime;?>: <?=$time;?>. <?=MSG_forum_numqueries;?>: <?=$queries;?>, <?=MSG_forum_querytime;?> <?=$time2;?>
<? if ($GLOBALS['inuserlevel']>=1000) { ?><br><a href="admin/index.php"><?=MSG_admincenter;?></a><? } ?>
</div>
<? }

function show_allowed($inforum,$inuserlevel) { ?>
<br><br><div class="descr">
<?=MSG_p_html;?> <? if ($inuserlevel<$inforum['f_lhtml']) { ?><?=MSG_forbidden;?><? }
else { ?><?=MSG_allowed;?><? } ?><br>
<?=MSG_automatic;?> <a target=_blank href="index.php?m=misc&amp;a=detrans"><?=MSG_p_detrans;?></a>
<? if ($GLOBALS['inuser']['u_detrans']) { ?><?=MSG_enabled;?><? }
else { ?><?=MSG_disabled;?><? } ?></div>
<? }

function confirm($newmodule,$newaction,$params,$text,$backlink) {
  if ($GLOBALS['admin']) $frmact='admin/index.php';
  else $frmact='index.php'; ?>
<table class="innertable" width="100%" cellspacing=1><tr><td class="tablehead" colspan=2>
<?=$text;?>
<tr><td width="50%" align=right><form action="<?=$frmact;?>" method=POST><input type=hidden name=m value="<?=$newmodule;?>">
<input type=hidden name=a value="<?=$newaction;?>">
<? foreach ($params as $curname=>$curvalue) { ?><input type=hidden name=<?=$curname;?> value="<?=$curvalue;?>"><? } ?>
<input type=submit value="<?=MSG_yes;?>">&nbsp;</form><td>
<form action="<?=$backlink;?>" method=GET>&nbsp;<input type=submit value="<?=MSG_no;?>"></table>
<? }

function timelimiter($time,$name) { ?>
<select name="<?=$name;?>"><?=set_select("<option value=\"0\">".MSG_all_time."<option value=1>".MSG_last_day.
"<option value=2>".MSG_last_2days."<option value=7>".MSG_last_week."<option value=30>".MSG_last_month.
"<option value=365>".MSG_last_year,$time);?></select>
<? }

function calendar_out($vardate,$monthselect,$days_in_month,$mindate,$first_day,$reflink,$calend) {
  $day = date("j",$vardate);
  $month=date("n",$vardate);
  $year=date("Y",$vardate);
  if (mktime(0,0,0,$month-1,$day,$year)<mktime(0,0,0,$month,0,$year))
    $prev_date=mktime(0,0,0,$month-1,$day,$year);
  else $prev_date=mktime(0,0,0,$month,0,$year);
  if (mktime(0,0,0,$month+1,$day,$year)<mktime(0,0,0,$month+2,0,$year))
    $next_date=mktime(0,0,0,$month+1,$day,$year);
  else $next_date=mktime(0,0,0,$month+2,0,$year);
  $test_date2=mktime(0,0,0,$month+1,1,$year);
  $test_date=mktime(0,0,0,$month,1,$year);
?>
<script type="text/javascript"><!--
function jump() {
  vd=document.getElementById('vdate');
  document.location.href='<?=str_replace('&amp;','&',$reflink);?>'+vd[vd.selectedIndex].value;
}
//-->
</script>
<table style="width: 210px" border=0 cellspacing=0 cellpadding=0 class="innertable" align=center>
<tr><td class="tablehead" width="15%"><?
if ($mindate>$test_date) { ?>&nbsp;<? }
else { ?><a class="inverse" href="<?=$reflink;?><?=date('j.n.Y',$prev_date);?>/">&lt;&lt;&lt;</a><? } ?>
<td class="tablehead"><select id="vdate" onChange="jump();"><?=set_select($monthselect,date('d.m.Y',$vardate));?></select>
<td class="tablehead" width="15%"><?
if ($GLOBALS['curtime']<$test_date2) { ?>&nbsp;<? }
else { ?><a class="inverse" href="<?=$reflink;?><?=date('j.n.Y',$next_date);?>/">&gt;&gt;&gt;</a><? } ?>
<tr><td colspan=3><table width="100%" border=0 cellspacing=1><tr class="basictable">
<? for ($i=1; $i<$first_day; $i++) { ?><td>&nbsp;<? }
for ($i=1; $i<=$days_in_month; $i++) {
  if (($first_day+$i-2) % 7 >= 5) $cls="calendar_hday";
  else $cls="calendar";
  if ($i!=$day && $calend[$i]>0) {
 ?><td><a class="<?=$cls;?>" title="<?=format_word($calend[$i],MSG_n1,MSG_n2,MSG_n3);?>" href="<?=$reflink;?><?=date('j.n.Y',$test_date+($i-1)*24*60*60);?>/"><?=$i;?></a><? }
  elseif ($i==$day) { ?><td class="<?=$cls;?>"><b><?=$i;?></b><? }
  else { ?><td class="<?=$cls;?>"><?=$i;?><? }
  if (($i+$first_day) % 7 ==1) { ?><tr><? }
}
for ($i=($first_day+$days_in_month-1) % 7; $i<7 && $i>1; $i++) { ?><td>&nbsp;<? }
?></table>
<tr><td colspan=3 class="tablehead"><a href="<?=build_url($GLOBALS['inforum']);?>"><?=MSG_n_return_cur;?></a></table><br>
<? }

function forum_rules($ref,$rules) { ?>
<table class="innertable" width="100%" cellspacing=1><tr>
<td class="tablehead"><?
if ($GLOBALS['forum']) { ?>
<?=MSG_f_rules;?> "<?=$GLOBALS['inforum']['f_title'];?>"
<? } else { ?>
<?=MSG_forum_rules;?>
<? }?>
<tr><td><br><?=nl2br($rules);?><br><br>
<tr><td class="tablehead"><a href="<?=$ref;?>"><?=MSG_prevpage;?></a>
</table>
<? }

function fast_switch_form($flist,$button) { ?>
<script type="text/javascript"><!--
function JumpTo(sel) {
var URL = sel.options[sel.selectedIndex].value;
top.location.href = "index.php?f="+URL;
}
//--></script>
<form action="index.php" method="get" name="jumpmenu">
<table width="100%" border=0><tr><td align=right class="descr"><?=MSG_f_goto;?>:
<select name=f onChange="JumpTo(this)"><?=$flist;?></select>
<noscript><input type=submit value="<?=MSG_f_go;?>"></noscript>
</table></form>
<? }

function common_post(&$pdata,$msg) {
if ($GLOBALS['opt_impersonation'] && $GLOBALS['inuserlevel']>=1000) { ?>
<tr><td><?=MSG_p_fromname;?>:
<td><input type=text name=inname value="<?=htmlspecialchars($GLOBALS['inuser']['u__name']);?>">
<? } ?>
<tr><td width="33%">
<?=$msg;?>:
<? show_allowed($GLOBALS['inforum'],$GLOBALS['inuserlevel']);?>
<? if ($GLOBALS['inuser']['u_extform']) { ?><br>
<div style='display: none' id='smiles'><?=list_smiles("AddText",4);?></div>
<? } ?>
<td style="vertical-align: top"><div style='display: none; background-image: url("images/clean_small.png"); width: 98%' id='codes'>
<script type="text/javascript" src="langs/<?=$GLOBALS['inuser']['ln_file'];?>/post.js"></script>
<script type="text/javascript" src="styles/<?=$GLOBALS['inuser']['st_file'];?>/post.js"></script>
<? if ($GLOBALS['inuser']['u_extform']) { ?>
<script type="text/javascript">insertcodes();</script>
<? } ?></div><?
if ($GLOBALS['inuser']['u_extform']) { ?>
<textarea tabindex=3 name=p_text rows=15 cols=60 onselect="javascript:storeCaret(this);" onFocus="focused=true" onBlur="focused=false" onclick="javascript:storeCaret(this);" onkeyup="javascript:storeCaret(this);" onchange="javascript:storeCaret(this);" onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}"><? }
else { ?><textarea tabindex=3 name=p_text cols=60 rows=15 onFocus="focused=true" onBlur="focused=false" onkeypress="if((event.ctrlKey) &amp;&amp; ((event.keyCode==10)||(event.keyCode==13))) {this.form.submit();}"><? } ?><?=textarea($pdata['p_text']);?></textarea>
<? }

function show_vote_resbegin(&$tdata) { ?>
<table class="innertable" cellspacing=1 width="100%" cellpadding=3><tr>
<td class="tablehead" colspan=2><?=$tdata['pl_title'];?>
<? }

function show_vote_resentry($text,$count,$total) { ?>
<tr><td><?=$text;?>
<td><?=$count;?>
<? }

function show_vote_resend($total) { ?>
<tr><td class="tablehead" colspan=2><?=MSG_vote_total;?>: <?=$total;?>
</table><br>
<? }

function show_vote_begin(&$tdata) { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%" cellpadding=3><tr>
<td class="tablehead" colspan=2><?=$tdata['pl_title'];?>
<? }

function show_vote_entry($text,$id) { ?>
<tr><td><?=$text;?>
<td align=center width=12><input type=radio name=pv_id value="<?=$id;?>">

<? }

function show_vote_end() { ?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=stdforum><input type=hidden name=t value=<?=$GLOBALS['topic'];?>>
<input type=hidden name=a value=do_vote><input type=submit value="Голосовать!">
</table></form>
<? }

function main_rating_start($title) { ?>
<br><table width="100%" class="innertable" cellspacing=1 cellpadding=0>
<tr><td class="tablehead"><?=$title;?>
<? }

function main_rating_entry(&$udata) { ?>
<tr><td><?=user_out($udata['u__name'],$udata['u_id']);?>
<? }

function main_rating_end() { ?>
</table>
<? }

function allowed_actions() {
if ($GLOBALS['inuser']['u_extform']) { ?>
<br><table width="100%" class="innertable" cellspacing=0 cellpadding=0><tr>
<td width="50%" class="descr"><? if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lpost']) { ?><?=MSG_cant;?> <?=MSG_allow_post;?><? }
else { ?><?=MSG_can;?> <?=MSG_allow_post;?><? } ?><br><?
if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ltopic']) { ?><?=MSG_cant;?> <?=MSG_allow_topic;?><? }
else { ?><?=MSG_can;?> <?=MSG_allow_topic;?><? } ?><br><?
if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lvote']) { ?><?=MSG_cant;?> <?=MSG_allow_vote;?><? }
else { ?><?=MSG_can;?> <?=MSG_allow_vote;?><? } ?><br><?
if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lpoll']) { ?><?=MSG_cant;?> <?=MSG_allow_poll;?><? }
else { ?><?=MSG_can;?> <?=MSG_allow_poll;?><? } ?><td class="descr"><?
if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_ledit']) { ?><?=MSG_cant;?> <?=MSG_allow_edit;?><? }
else { ?><?=MSG_can;?> <?=MSG_allow_edit;?><? } ?><br><?
if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lattach']) { ?><?=MSG_cant;?> <?=MSG_allow_attach;?><? }
else { ?><?=MSG_can;?> <?=MSG_allow_attach;?><? } ?><br><?
if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) { ?><?=MSG_cant;?> <?=MSG_allow_moderate;?><? }
else { ?><?=MSG_can;?> <?=MSG_allow_moderate;?><? } ?><br><?
if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lip']) { ?><?=MSG_cant;?> <?=MSG_allow_ip;?><? }
else { ?><?=MSG_can;?> <?=MSG_allow_ip;?><? } ?>
</table>
<? }
}

function print_template_head() { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<title>
<?
$locations=$GLOBALS['locations'];
if ($locations) $locations=array_reverse($locations);
if ($GLOBALS['action']==$GLOBALS['inforum']['tp_library']."_view") {
  for ($i=0; $i<count($locations)-1; $i++) {
    echo strip_tags($locations[$i]);
    if ($i<count($locations)-2) echo " :: ";
  }
}
elseif (!$locations) echo strip_tags($GLOBALS['opt_title']);
else {
  foreach ($locations as $curloc) {
    echo strip_tags($curloc);
    if (next($locations)) echo " :: ";
  }
} ?></title>
<link rel="SHORTCUT ICON" href="favicon.ico">
<base href="<?=$GLOBALS['opt_url'];?>/">
<script type="text/javascript"><!--
function ch_imgs() {
var imgs=document.images;
for (i=0;i<imgs.length;i++) if (imgs[i].name=="itag") {
<? if ($GLOBALS['opt_imglimit_x']) {?>
if (imgs[i].width><?=$GLOBALS['opt_imglimit_x'];?>) { imgs[i].width=<?=$GLOBALS['opt_imglimit_x'];?>; }
<? }
if ($GLOBALS['opt_imglimit_y']) { ?>
if (imgs[i].height><?=$GLOBALS['opt_imglimit_y'];?>) { imgs[i].height=<?=$GLOBALS['opt_imglimit_y'];?>; }
<? } ?>
}
var ilayer=document.getElementById('smiles');
if (ilayer) { ilayer.style.display=''; }
var clayer=document.getElementById('codes');
if (clayer) { clayer.style.display=''; }
}
//--></script></head><body onLoad="ch_imgs();">
<?
}

function print_template_end() { ?>
</body></html>
<? }