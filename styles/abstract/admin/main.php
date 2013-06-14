<? function ad_main_start() {
main_header();
main_body();
main_top();
?>
<div class="outertable"><table style="width:100%"><tr><td width=200 valign=top>
<table class=innertable cellspacing=1 width="100%">
<? }
function ad_category($menuitem) {         ?>
<tr><td class="tablehead" style="text-align : left">
<b>:: <?=constant($menuitem['ad_category']);?></b>

<? }
function ad_menuitem($menuitem) {
if (strpos($menuitem['ad_url'],'http://')!==0) $menuitem['ad_url']='admin/'.$menuitem['ad_url']; ?>
<tr><td>
<b><a href="<?=$menuitem['ad_url'];?>"><?=constant($menuitem['ad_name']);?></a></b>

<? }
function ad_main_middle() { ?>
</table>
<td valign=top>
<? }
function ad_main_end() { ?>
</table></div><?
main_copyright();
}
