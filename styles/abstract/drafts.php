<?
function drafts_start($pages,$perpage) { ?>
<script type="text/javascript"><!--
function SetStatus(status) { 
	for (i=0; i<=document.drafts.elements.length; i++) 
  if (document.drafts.elements[i].name && document.drafts.elements[i].name.indexOf("draft")>-1) { document.drafts.elements[i].checked=status; } 
}
// -->
</script>
<form action="index.php" method=POST name="drafts"><table class="innertable" width="100%">
<tr><td colspan=4 class="tablehead" style="text-align: right"><div class="pages"><?=$pages;?></div>
<tr>
<td class="tablehead" width="55%"><?=MSG_dr_text;?>
<td class="tablehead" width="20%"><?=MSG_dr_topic;?>
<td class="tablehead" width="20%"><?=MSG_dr_forum;?>
<td class="tablehead" width="5%"><input type=checkbox onClick="SetStatus(this.checked)">
<? }

function drafts_entry(&$fdata) { 
  $forum['f_id']=$fdata['f_id'];
  $forum['f_link']=$fdata['f_link'];?>
<tr><td>
<?=textout($fdata['p_text'],$fdata['p__html'],$fdata['p__bcode'],$fdata['p__smiles']);?>
<td><? if ($fdata['t_id']) { ?>
<a href="<?=build_url($fdata);?>"><?=$fdata['t_title'];?></a><br><br><? }
else { ?>
<?=$fdata['t_title'];?></a><br><br>  
<? } ?>
<a href="<?=build_url($fdata,'&a=edit_from_draft');?>"><?=MSG_dr_continue;?></a>
<td><a href="<?=build_url($forum);?>"><?=$fdata['f_title'];?></a>
<td align=center><input type="checkbox" name='draft[]' value="<?=$fdata['dr_fid'].':'.$fdata['dr_tid'];?>">
<? }

function drafts_noentries() { ?>
<tr><td colspan="4"><?=MSG_dr_noentries;?>  
<? }

function drafts_end($pages) { ?>
<tr><td colspan=4 class="tablehead" style="text-align: right"><div class="pages"><?=$pages;?></div>
<tr><td colspan=4 class="tablehead">
<input type=hidden name=m value="drafts"><input type=hidden name=a value="do_delete">
<input type=submit value="<?=MSG_delete;?>">
</table></form>
<? }
