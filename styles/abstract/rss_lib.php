<?

function list_rss_start_short() { ?>
<form action="index.php" method="post">
<table class="innertable" style="width: 100%"><tr class="tablehead"><td colspan="5">
<?=MSG_rss_list;?>
<tr class="tablehead"><td><?=MSG_rss_url;?><td><?=MSG_rss_name;?>
<td><?=MSG_rss_source;?><td><?=MSG_rss_premoderate;?>
<td><?=MSG_delete;?>
<? }
  
function list_rss_entry_short($data) { 
  static $param_count;
  if ($data['rss_id']) $param='rss';
  else {
    $param='new_rss';
    $data['rss_id']=++$param_count;
  };
  ?>
<tr><td><input type="text" size=40 maxlength=255 name="<?=$param;?>[<?=$data['rss_id'];?>][rss_url]" value="<?=$data['rss_url'];?>">
<td><input type="text" size=40 maxlength=255 name="<?=$param;?>[<?=$data['rss_id'];?>][rss_name]" value="<?=$data['rss_name'];?>">
<td><input type="text" size=20 maxlength=255 name="<?=$param;?>[<?=$data['rss_id'];?>][rss_source]" value="<?=$data['rss_source'];?>">
<td style="text-align: center"><input type="checkbox" name="<?=$param;?>[<?=$data['rss_id'];?>][rss_premoderated]" value="1" <?=check($data['rss_premoderated']);?>>
<td style="text-align: center"><input type="checkbox" name="<?=$param;?>[<?=$data['rss_id'];?>][delete]" value="1">
<? }
  
function list_rss_end_short($fid) { ?>
<tr class="tablehead"><td colspan="5">
<input type="hidden" name="m" value="rss_lib">
<input type="hidden" name="a" value="do_save_rss_imports">
<input type="hidden" name="fid" value="<?=$fid;?>">
<input type="submit" value="<?=MSG_save;?>">
</table></form>
<? }
