<? function std_title_form(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_forum_new($fdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<div class="descr" style="text-align: left"><?=$GLOBALS['modlist'];?></div>
<td align=center><?=intval($fdata['tf_tcount']);?>
<td align=center><?=intval($fdata['tf_pcount']);?>
<td align=center><? if ($fdata['tf_lasttime']) { ?>
<?=user_out($fdata['p_uname'],$fdata['p_uid']);?><br><?=long_date_out($fdata['tf_lasttime']);?>
<?if($GLOBALS['opt_last_post']){
	$lpdata["t_id"] = $fdata['lp_id'];
	$lpdata["f_id"] = $fdata['f_id'];
    $lpdata["f_link"] = $fdata['f_link'];
	$lpdata["t_link"] = $fdata['lp_link'];
?>
<br><a href="<?=build_url($lpdata);?>#last"><?=$fdata['lp_title'];?></a>
<?}?>
<? } else { ?><?=MSG_none;?><? }
if ($new && !$GLOBALS['opt_last_post'] && $GLOBALS['inuserid']>3 && $fdata['tf_pcount']) { ?><br><a class="newposts" href="index.php?m=newpost&amp;fs=<?=$fdata['f_id'];?>"><?=MSG_shownewposts;?></a><? }
if ($fdata['f_premoderate'] && $fdata['f__premodcount']) { ?><br><a class="newposts" href="index.php?m=moderate&amp;a=premod&amp;f=<?=$fdata['f_id'];?>"><?=MSG_f_premoderate;?></a><? }
?><?
}

function contnr_title_form(&$fdata,&$resdata) {
  $f=($resdata['tf_pcount'] || $resdata['tf_tcount']);
  if (!$f) $colspan="colspan=4";
?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_subforum_new($fdata,$resdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td <?=$colspan;?>><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<div class="descr" style="text-align: left"><?=$resdata['sublinks'];?></div>
<? if ($f) { ?>
<td align=center><?=intval($resdata['tf_tcount']);?>
<td align=center><?=intval($resdata['tf_pcount']);?>
<td align=center><? if ($resdata['tf_lasttime']) { ?>
<?=user_out($resdata['p_uname'],$resdata['p_uid']);?><br><?=long_date_out($resdata['tf_lasttime']);?>
<?if($GLOBALS['opt_last_post']){
    $lpdata["t_id"] = $resdata['lp_id'];
    $lpdata["f_id"] = $resdata['lp_fid'];
    $lpdata["f_link"] = $resdata['lp_flink'];
    $lpdata["t_link"] = $resdata['lp_link'];
    $lpdata["f_id"] = $fdata['f_id'];
    $lpdata["f_link"] = $fdata['f_link'];
?>
<br><a href="<?=build_url($lpdata);?>#last"><?=$resdata['lp_title'];?></a>
<?}?>
<? } else { ?><?=MSG_none;?><? }
if ($new && !$GLOBALS['opt_last_post'] && $GLOBALS['inuserid']>3 && $resdata['tf_pcount']) { ?><br><a class="newposts" href="index.php?m=newpost&amp;fs=<?=$fdata['f_id'];?>"><?=MSG_shownewposts;?></a><? }
if ($fdata['f_premoderate'] && $fdata['f__premodcount']) { ?><br><a class="newposts" href="index.php?m=moderate&amp;a=premod&amp;f=<?=$resdata['f_id'];?>"><?=MSG_f_premoderate;?></a><? }
}?>
<? }


function irc_title_form(&$fdata,$userlist,$guestcount) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($userlist || $guestcount) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<td colspan=3>
<? if (!$guestcount && !$userlist) { ?><?=MSG_f_nochatpresent;?><? }
else { ?>
<div class="descr" style="text-align: left"><?=MSG_f_chatpresent;?>: <?=$userlist;?>
<? if ($guestcount) {
  if ($userlist) { ?> <?=MSG_and;?><? } ?> <?=format_word($guestcount,MSG_ug1,MSG_ug2,MSG_ug3);?>
<? } ?></div>

<? }
}

function link_title(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td colspan=4><h3><a href="<?=$fdata['f_url'];?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>

<? }

function article_title_form(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_forum_new($fdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<div class="descr" style="text-align: left"><?=$GLOBALS['modlist'];?></div>
<td align=center colspan=2><?=format_word($fdata['tf_tcount'],MSG_a1,MSG_a2,MSG_a3);?>
<td align=center><?if($GLOBALS['opt_last_post'] && $fdata['tf_lasttime']){
    $lpdata["t_id"] = $fdata['lp_id'];
    $lpdata["f_id"] = $fdata['f_id'];
    $lpdata["f_link"] = $fdata['f_link'];
    $lpdata["t_link"] = $fdata['lp_link'];
?>
<?=user_out($fdata['p_uname'],$fdata['p_uid']);?><br><?=long_date_out($fdata['tf_lasttime']);?>
<br><a href="<?=build_url($lpdata);?>#last"><?=$fdata['lp_title'];?></a>
<?} elseif(!$GLOBALS['opt_last_post'] && $fdata['tf_laststart']) {?>
<?=MSG_a_lastpublish;?><br>
<?=long_date_out($fdata['tf_laststart']);?>
<? } else { ?><?=MSG_none;?><? }
if ($fdata['f_premoderate'] && ($fdata['f__premodcount'])) { ?><br><a class="newposts" href="index.php?m=moderate&amp;a=premod&amp;f=<?=$fdata['f_id'];?>"><?=MSG_f_premoderate;?></a><? }
?><?
}

function download_title_form(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_forum_new($fdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<div class="descr" style="text-align: left"><?=$GLOBALS['modlist'];?></div>
<td align=center colspan=2><?=format_word($fdata['tf_tcount'],MSG_dl1,MSG_dl2,MSG_dl3);?>
<td align=center><?if($GLOBALS['opt_last_post'] && $fdata['tf_lasttime']){
    $lpdata["t_id"] = $fdata['lp_id'];
    $lpdata["f_id"] = $fdata['f_id'];
    $lpdata["f_link"] = $fdata['f_link'];
    $lpdata["t_link"] = $fdata['lp_link'];
?>
<?=user_out($fdata['p_uname'],$fdata['p_uid']);?><br><?=long_date_out($fdata['tf_lasttime']);?>
<br><a href="<?=build_url($lpdata);?>#last"><?=$fdata['lp_title'];?></a>
<?} elseif(!$GLOBALS['opt_last_post'] && $fdata['tf_laststart']) {?>
<?=MSG_dl_lastadded;?><br>
<?=long_date_out($fdata['tf_laststart']);?>
<? } else { ?><?=MSG_none;?><? }
if ($fdata['f_premoderate'] && ($fdata['f__premodcount'])) { ?><br><a class="newposts" href="index.php?m=moderate&amp;a=premod&amp;f=<?=$fdata['f_id'];?>"><?=MSG_f_premoderate;?></a><? }
?><?
}

function news_start(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_forum_new($fdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td colspan=4 style="text-align:left"><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<ul>
<? }

function news_entry(&$ndata) { ?>
<li><?=short_date_out($ndata['time']);?> - <a href="<?=build_url($ndata);?>"><?=$ndata['t_title'];?></a><br>
<div class="descr"><?=textout($ndata['t_descr'],1,$ndata['p__bcode'],$ndata['p__smiles']);?>&nbsp;</div>
<? }

function news_noentries() { ?>
<li class="descr"><?=MSG_n_nonews;?>
<? }

function news_end(&$fdata) { ?>
</ul>
<? if ($fdata['f_premoderate'] && ($fdata['f__premodcount'])) { ?><a class="descr" href="<?=build_url($fdata,'m=moderate&amp;a=premod');?>"><?=MSG_f_premoderate;?></a><br><? }
if ($fdata['f_ltopic']<=$GLOBALS['inuserbasic']) { ?><a href="<?=build_url($fdata,'a=add_news');?>"><?=MSG_n_add;?></a><? } ?>
<? }

function epedia_title_form(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_forum_new($fdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<div class="descr" style="text-align: left"><?=$GLOBALS['modlist'];?></div>
<td align=center colspan=2><?=format_word($fdata['tf_tcount'],MSG_en1,MSG_en2,MSG_en3);?>
<td align=center><? if ($fdata['tf_lasttime']) { ?>
<?=user_out($fdata['p_uname'],$fdata['p_uid']);?><br><?=long_date_out($fdata['tf_lasttime']);?>
<?if($GLOBALS['opt_last_post']){
    $lpdata["t_id"] = $fdata['lp_id'];
    $lpdata["f_id"] = $fdata['f_id'];
    $lpdata["f_link"] = $fdata['f_link'];
    $lpdata["t_link"] = $fdata['lp_link'];
?>
<br><a href="<?=build_url($lpdata);?>#last"><?=$fdata['lp_title'];?></a>
<?}?>
<? } else { ?><?=MSG_none;?><? }
if ($new && !$GLOBALS['opt_last_post'] && $GLOBALS['inuserid']>3 && $fdata['tf_pcount']) { ?><br><a class="newposts" href="index.php?m=newpost&amp;fs=<?=$fdata['f_id'];?>"><?=MSG_shownewposts;?></a><? }
if ($fdata['f_premoderate'] && ($fdata['f__premodcount'])) { ?><br><a class="newposts" href="index.php?m=moderate&amp;a=premod&f=<?=$fdata['f_id'];?>"><?=MSG_f_premoderate;?></a><? }
?><?
}

function photos_title_form(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_forum_new($fdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<div class="descr" style="text-align: left"><?=$GLOBALS['modlist'];?></div>
<td align=center colspan=2><?=format_word($fdata['tf_tcount'],MSG_ph1,MSG_ph2,MSG_ph3);?>
<td align=center><? if ($fdata['tf_lasttime']) { ?>
<?=user_out($fdata['p_uname'],$fdata['p_uid']);?><br><?=long_date_out($fdata['tf_lasttime']);?>
<?if($GLOBALS['opt_last_post']){
    $lpdata["t_id"] = $fdata['lp_id'];
    $lpdata["f_id"] = $fdata['f_id'];
    $lpdata["f_link"] = $fdata['f_link'];
    $lpdata["t_link"] = $fdata['lp_link'];
?>
<br><a href="<?=build_url($lpdata);?>#last"><?=$fdata['lp_title'];?></a>
<?}?>
<? } else { ?><?=MSG_none;?><? }
if ($new && !$GLOBALS['opt_last_post'] && $GLOBALS['inuserid']>3 && $fdata['tf_pcount']) { ?><br><a class="newposts" href="index.php?m=newpost&amp;fs=<?=$fdata['f_id'];?>"><?=MSG_shownewposts;?></a><? }
if ($fdata['f_premoderate'] && ($fdata['f__premodcount'])) { ?><br><a class="newposts" href="index.php?m=moderate&amp;a=premod&f=<?=$fdata['f_id'];?>"><?=MSG_f_premoderate;?></a><? }
?><?
}

function blog_title_form(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_forum_new($fdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<div class="descr" style="text-align: left"><?=$GLOBALS['modlist'];?></div>
<td align=center><?=intval($fdata['tf_tcount']);?>
<td align=center><?=intval($fdata['tf_pcount']);?>
<td align=center><? if ($fdata['tf_lasttime']) { ?>
<?=user_out($fdata['p_uname'],$fdata['p_uid']);?><br><?=long_date_out($fdata['tf_lasttime']);?>
<?if($GLOBALS['opt_last_post']){
    $lpdata["t_id"] = $fdata['lp_id'];
    $lpdata["f_id"] = $fdata['f_id'];
    $lpdata["f_link"] = $fdata['f_link'];
    $lpdata["t_link"] = $fdata['lp_link'];
?>
<br><a href="<?=build_url($lpdata);?>#last"><?=$fdata['lp_title'];?></a>
<?}?>
<? } else { ?><?=MSG_none;?><? }
if ($new && !$GLOBALS['opt_last_post'] && $GLOBALS['inuserid']>3 && $fdata['tf_pcount']) { ?><br><a class="newposts" href="index.php?m=newpost&amp;fs=<?=$fdata['f_id'];?>"><?=MSG_shownewposts;?></a><? }
if ($fdata['f_premoderate'] && ($fdata['f__premodcount'])) { ?><br><a class="newposts" href="index.php?m=moderate&amp;a=premod&f=<?=$fdata['f_id'];?>"><?=MSG_f_premoderate;?></a><? }
}

function gallery_title_form(&$fdata) { ?>
<tr class="forumentry"><td width="10%" style="text-align: center"><?
if ($new=is_forum_new($fdata)) {
  if ($fdata['f_newpic']) $pic="images/".$fdata['f_newpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forumnew.png";
  $alt="NEW!";
}
else {
  if ($fdata['f_nonewpic']) $pic="images/".$fdata['f_nonewpic'];
  else $pic="styles/".$GLOBALS['inuser']['st_file']."/forum.png";
  $alt="";
} ?><img src="<?=$pic;?>" alt="<?=$alt;?>">
<td><h3><a href="<?=build_url($fdata);?>"><?=$fdata['f_title'];?></a></h3>
<? if ($fdata['f_descr']) { ?><h4><?=textout($fdata['f_descr'],1,1,0);?></h4><? } ?>
<div class="descr" style="text-align: left"><?=$GLOBALS['modlist'];?></div>
<td align=center colspan=2><?=format_word($fdata['tf_tcount'],MSG_ph1,MSG_ph2,MSG_ph3);?>
<td align=center><? if ($fdata['tf_lasttime']) { ?>
<?=user_out($fdata['p_uname'],$fdata['p_uid']);?><br><?=long_date_out($fdata['tf_lasttime']);?>
<?if($GLOBALS['opt_last_post']){
    $lpdata["t_id"] = $fdata['lp_id'];
    $lpdata["f_id"] = $fdata['f_id'];
    $lpdata["f_link"] = $fdata['f_link'];
    $lpdata["t_link"] = $fdata['lp_link'];
?>
<br><a href="<?=build_url($lpdata);?>#last"><?=$fdata['lp_title'];?></a>
<?}?>
<? } else { ?><?=MSG_none;?><? }
if ($new && !$GLOBALS['opt_last_post'] && $GLOBALS['inuserid']>3 && $fdata['tf_pcount']) { ?><br><a class="newposts" href="index.php?m=newpost&amp;fs=<?=$fdata['f_id'];?>"><?=MSG_shownewposts;?></a><? }
if ($fdata['f_premoderate'] && ($fdata['f__premodcount'])) { ?><br><a class="newposts" href="index.php?m=moderate&amp;a=premod&f=<?=$fdata['f_id'];?>"><?=MSG_f_premoderate;?></a><? }
}