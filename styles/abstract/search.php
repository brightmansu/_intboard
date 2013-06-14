<? function big_search_form($text,$start,$end,$type,$mode,$user='',$dsc=0) {
  if (!$text) {
    $_POST['a']="do_post";
    $_POST['res']="post";
    $_POST['o']="relevancy";
  }  ?>
<script type="text/javascript"><!--
function sbm() {
f=0; e=document.srchforum.elements['fs[]'];
for (i=0;i<e.options.length;i++) if (e.options[i].selected) f++;
if (!f) alert('<?=MSG_search_noforums;?>');
return (f!=0);
}
--></script>
<form action="index.php" name=srchforum method=POST onSubmit="return sbm();">
<table class="innertable" width="100%" cellspacing=1><tr>
<td class="tablehead" colspan=2><?=MSG_search;?>
<tr><td width="50%">
<?=MSG_search_text;?>:
<input type=text name=text size=50 maxlength=255 value="<?=htmlspecialchars($text);?>">
<td>
<?=MSG_search_user2;?>:
<input type=text name=username size=40 maxlength=255 value="<?=htmlspecialchars($user);?>">
<tr><td colspan=2 class="tablehead" style="text-align: left">Опции поиска
<tr><td><?=MSG_search_starttime;?>: <?=build_date_field("start",$start);?>
<td rowspan="5"><?=MSG_search_forums;?>
<br><select style="width: 100%" name='fs[]' multiple size=15><option value="all" selected="selected">Необходимо выбрать один или несколько разделов<?=build_forum_select("f_lread");?></select>

<tr><td><?=MSG_search_endtime;?>: <?=build_date_field("end",$end);?>
<tr><td><?=MSG_search_where;?>:<br>
<input type=radio name=a value=do_post <?=check($type==0);?>><?=MSG_search_postss;?><br>
<input type=radio name=a value=do_topic <?=check($type==1);?>><?=MSG_search_topics;?><br>
<tr><td><?=MSG_search_results_out;?>:<br>
<input type=radio name=res value=post <?=check($mode==0);?>><?=MSG_search_asposts;?><br>
<input type=radio name=res value=topic <?=check($mode==1);?>><?=MSG_search_astopic;?><br>

<tr><td><?=MSG_search_sortby;?>:<br>
<input type=radio name=o value=relevancy <?=check(getvar('o')=="relevancy");?>><?=MSG_search_relevancy;?> &nbsp;
<input type=radio name=o value=p__time <?=check(getvar('o')=="p__time");?>><?=MSG_search_date;?><br>
<input type=checkbox name=desc value=1 <?=check($dsc);?>><?=MSG_search_reverse;?><br>
<input type=checkbox name=nogrp value=1 <?=check(getvar('nogrp')!='');?>><?=MSG_search_nogrp;?>

<tr><td class="tablehead" colspan=2>
<input type=hidden name=m value=search>
<input type=submit value="<?=MSG_searchdo;?>">
<input type="reset" value="Сброс">

<? if ($GLOBALS['opt_search_ext']==1) { ?><tr><td colspan=2 class=descr><b><?=MSG_search_help;?></b>:<br>
<?=MSG_search_help_plus;?><br>
<?=MSG_search_help_minus;?><br>
<?=MSG_search_help_trunc;?><br>
<?=MSG_search_help_tilde;?><br>
<?=MSG_search_help_quotas;?><br>
<?=MSG_search_help_bracket;?><br>
<?=MSG_search_tooshort;?>.<? } ?>

</table></form>
<? }

function search_result_start($pages) { ?>
<script type="text/javascript"><!--
function ch_img(img) {
<? if ($GLOBALS['opt_imglimit_x']) {?>
if (img.width><?=$GLOBALS['opt_imglimit_x'];?>) { img.width=<?=$GLOBALS['opt_imglimit_x'];?>; }
<? }
if ($GLOBALS['opt_imglimit_y']) { ?>
if (img.height><?=$GLOBALS['opt_imglimit_y'];?>) { img.height=<?=$GLOBALS['opt_imglimit_y'];?>; }
<? } ?>
}
//--></script>
<table class="innertable posttable" width="100%" cellspacing=1>
<tr class="tablehead"><td><?=MSG_search_results;?><div class="pages"><?=$pages;?></div>
<? }

function search_result_forum(&$entry) { ?>
<tr class="category"><td><h3>&raquo; <a class="inverse" href="index.php?f=<?=$entry['f_id'];?>"><?=$entry['f_title'];?></a></h3>
<? }

function search_result_topic(&$entry,$text) { ?>
<tr><td><h5><a href="index.php?t=<?=$entry['t_id'];?>&amp;hl=<?=urlencode($text);?>"><?=htmlspecialchars($entry['t_title']);?></a></h5>

<? }

function search_result_post(&$entry,$text) {
  static $counter;
  if ($counter % 2 == 1) $class="postentry2";
  else $class="postentry";
  $counter++;
  ?>
<tr><td class="<?=$class;?>"><?=MSG_topic;?>: <?
if ($GLOBALS['opt_hurl']) { ?>
<a href="<?=build_url($entry).'p'.$entry['p_id'].'.htm?hl='.urlencode($text);?>#pp<?=$entry['p_id'];?>">
<? } else { ?>
<a href="index.php?t=<?=$entry['p_tid'];?>&amp;p=<?=$entry['p_id'];?>&amp;hl=<?=urlencode($text);?>#pp<?=$entry['p_id'];?>"><? } ?>
<?=$entry['t_title'];?></a><br>
<?=long_date_out($entry['p__time']);?>, <?=user_out($entry['p_uname'],$entry['p_uid']);?>
<hr>
<?=textout($entry['p_text'],$entry['p__html'],$entry['p__bcode'],$entry['p__smiles']);?>
<tr><td style="padding: 5px">
<? }

function search_not_found() { ?>
<tr><td align=center><?=MSG_search_none;?>
<? }

function search_result_end($pages) { ?>
<tr><td class="tablehead"><div class="pages"><?=$pages;?></div>
</table><br>
<? }
