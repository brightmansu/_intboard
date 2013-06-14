<? function irc_params($catselect,$levelselect,$fdata,$fcontainer,$langselect) { ?>
<form action="admin/index.php" method=POST>
<table class="innertable" cellspacing=1><tr><td class="tablehead" colspan=2><?=MSG_f_params;?>
<tr><td width="50%"><?=MSG_f_title;?>:
<td>
<input type=text name=f_title size=30 maxlength=60 value="<?=htmlspecialchars($fdata['f_title']);?>">
<tr><td><?=MSG_f_descr;?>:
<td>
<textarea name=f_descr rows=3 cols=30><?=htmlspecialchars($fdata['f_descr']);?></textarea>
<tr><td>
<?=MSG_f_cat;?>:
<td>
<select name=f_ctid><? set_select($catselect,$_POST['ctid']);?></select>
<tr><td>
<?=MSG_f_show_in;?>:
<td>
<select name=f_parent><option value=0><?=MSG_f_inmainpage;?><? set_select($fcontainer,$_POST['f_parent']);?></select>
<tr><td>
<?=MSG_f_langs;?>?
<td>
<select name=f_lnid><?=set_select($langselect,$fdata['f_lnid']);?></select>
<tr><td>
<?=MSG_f_rules;?><td>
<textarea name=f_rules rows=12 cols=30><?=$fdata['f_rules'];?></textarea>
<tr><td>
<?=MSG_f_nonewpic;?><td>
<input type=text name=f_nonewpic size=20 maxlength=20 value=<?=$fdata['f_nonewpic'];?>>
<tr><td>
<?=MSG_f_newpic;?><td>
<input type=text name=f_newpic size=20 maxlength=20 value=<?=$fdata['f_newpic'];?>>
<tr><td>
<?=MSG_f_smiles;?>?
<td>
<input type=radio name=f_smiles value=0 <? check($fdata['f_smiles']==0);?>><?=MSG_no;?> &nbsp;
<input type=radio name=f_smiles value=1 <? check($fdata['f_smiles']==1);?>><?=MSG_yes;?> &nbsp;
<tr><td>
<?=MSG_irc_server;?>
<td>
<input type=text name=f_url size=30 maxlength=255 value=<?=$fdata['f_url'];?>>
<tr><td>
<?=MSG_irc_channel;?>
<td>
<input type=text name=f_text size=30 maxlength=255 value=<?=$fdata['f_text'];?>>
<tr><td class="tablehead" colspan=2><?=MSG_f_levels;?>
<tr><td>
<?=MSG_f_lview;?>
<td>
<select name=f_lview><? set_select($levelselect,$fdata['f_lview']);?></select>
<tr><td>
<?=MSG_f_lread;?>
<td>
<select name=f_lread><? set_select($levelselect,$fdata['f_lread']);?></select>

<tr><td class="tablehead" colspan=2>
<input type=hidden name=a value="<?=$GLOBALS['newaction'];?>">
<input type=hidden name=m value="<?=$GLOBALS['newmodule'];?>">
<input type=hidden name=f_tpid value="<?=$fdata['f_tpid'];?>">
<input type=hidden name=fid value=<?=getvar("fid");?>>
<input type=submit value="<?=MSG_save;?>">
</table></form>
<? }

function irc_form($name,$smiles,$server,$port,$channel) { ?>
<? if ($GLOBALS['inforum']['f_rules']=1) { ?><a class="descr" href="index.php?f=<?=$GLOBALS['forum'];?>&a=f_rules"><?=MSG_f_rules;?></a><br><br><? } ?>
<applet code="IRCApplet.class" codebase="<?=$GLOBALS['opt_url'];?>/irc/" archive="irc.jar,pixx.jar" width="100%" height=400>
<param name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">

<param name="nick" value="<?=$name;?>">
<param name="name" value="<?=$GLOBALS['inuser']['u__name'];?>">
<param name="host" value="<?=$server;?>">
<? if ($port) { ?><param name="port" value="<?=$port;?>"><? } ?>
<param name="gui" value="pixx">

<param name="quitmessage" value="<?=$GLOBALS['inuser']['u_signature'];?>">
<param name="asl" value="true">
<param name="useinfo" value="true">
<param name="coding" value="3">
<param name="languageencoding" value="<?=$GLOBALS['inuser']['ln_charset'];?>">
<param name="command1" value="/join <?=$channel;?>">
<param name="language" value="<?=$GLOBALS['inuser']['ln_file'];?>">
<param name="pixx:showconnect" value="false">
<param name="pixx:language" value="pixx-<?=$GLOBALS['inuser']['ln_file'];?>">

<?=$smiles;?>

<param name="style:backgroundimage" value="false">
<param name="style:sourcefontrule1" value="all all Arial 12">
<param name="style:floatingasl" value="true">

<param name="pixx:timestamp" value="true">
<param name="pixx:highlight" value="true">
<param name="pixx:highlightnick" value="true">
<param name="pixx:nickfield" value="false">
<param name="pixx:styleselector" value="true">
<param name="pixx:setfontonstyle" value="true">
<param name="pixx:showchanlist" value="false">
<param name="pixx:showabout" value="false">
<param name="pixx:showhelp" value="false">
<param name="pixx:showclose" value="false">

</applet>
<iframe src="agent.php?u=<?=$GLOBALS['inuserid'];?>&a=chat&f=<?=$GLOBALS['forum'];?>&key=<?=md5($GLOBALS['inuser']['u__password'].$GLOBALS['inuser']['u__key']);?>&time=<?=$GLOBALS['curtime'];?>" height=1 width=1></iframe>
<? }