<?
function filelist_start($pages,$perpage) { ?>
<form class="descr" style="text-align: right" action="index.php" method=GET>
<?=MSG_fl_sortby;?> <select name=o><?=set_select("<option value=p__time>".MSG_fl_bypostdate.
"<option value=p__edittime>".MSG_fl_bypostedit."<option value=t_id>".MSG_fl_bytopic.
"<option value=f_sortfield>".MSG_fl_byforum."<option value=file_name>".MSG_fl_byname.
"<option value=file_size>".MSG_fl_bysize."<option value=file_type>".MSG_fl_bytype,
getvar("o"));?></select>
, <?=MSG_showby;?> <select name=desc><?=set_select("<option value=\"\">".MSG_asc."<option value=desc>".MSG_desc,getvar("desc"));?>
</select><br>
<?=MSG_fl_per;?> <input type=text name=perpage size=4 value="<?=$perpage;?>"> <?=MSG_t_perpage;?> <br>
<input type=hidden name=m value=filelist><input type=submit value="<?=MSG_show;?>"></form>

<table class="innertable">
<tr><td colspan=4 class="tablehead" style="text-align: right"><?=$pages;?><tr>
<td class="tablehead" width="20%"><?=MSG_fl_name;?>
<td class="tablehead" width="40%"><?=MSG_fl_post;?>
<td class="tablehead" width="20%"><?=MSG_fl_topic;?>
<td class="tablehead" width="20%"><?=MSG_fl_forum;?>
<? }

function filelist_entry(&$fdata) { 
  $forum['f_id']=$fdata['f_id'];
  $forum['f_link']=$fdata['f_link'];?>
<tr><td><?
if (strpos($fdata['file_type'],"image")===false) { ?>
<a href="file.php?fid=<?=$fdata['p_attach'];?>&amp;key=<?=$fdata['file_key'];?>"><b><?=urldecode($fdata['file_name']);?></b></a><? }
else { ?><a href="file.php?fid=<?=$fdata['p_attach'];?>&amp;key=<?=$fdata['file_key'];?>" target=_blank><img src="file.php?a=preview&amp;fid=<?=$fdata['p_attach'];?>&amp;key=<?=$fdata['file_key'];?>" alt="<?=urldecode($fdata['file_name']);?>"></a>
<? } ?><br><br>
<?=MSG_fl_size;?>: <?=$fdata['file_size'];?>, <?=MSG_p_downloaded;?>: <?=$fdata['file_downloads'];?><br>
<?=MSG_fl_type;?>: <?=$fdata['file_type'];?><br>
<?=MSG_fl_lastmod;?>: <?=long_date_out($fdata['p__time']);?>
<td><div style="height: 120px; overflow: auto">
<?=textout($fdata['p_text'],$fdata['p__html'],$fdata['p__bcode'],$fdata['p__smiles']);?>
</div>
<td><a href="<?=build_url($fdata);?>"><?=$fdata['t_title'];?></a>
<td><a href="<?=build_url($forum);?>"><?=$fdata['f_title'];?></a>
<? }

function filelist_noentries() { ?>
<tr><td colspan="4"><?=MSG_fl_noentries;?>  
<? }

function filelist_end($pages) { ?>
<tr><td colspan=4 class="tablehead" style="text-align: right"><div class="pages"><?=$pages;?></div>  
</table>
<? }
