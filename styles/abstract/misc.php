<? function bcode_example() { ?>
<table class="innertable" cellspacing=1 width="100%">
<tr><td colspan="2" class="tablehead"><?=MSG_misc_bcode;?>
<tr><td width="50%">
<font color="#FF0000">[hr]</font>
<td><hr width="50%" align="left">
<tr><td width="50%">
<font color="#FF0000">[quote]</font>Some text<font color="#FF0000">[/quote]</font><br>
<font color="#FF0000">[q]</font>Some text<font color="#FF0000">[/q]</font>
<td>
<blockquote>Some text</blockquote>
<tr><td width="50%">
<font color="#FF0000">[url=</font>http://intboard.ru<font color="#FF0000">]</font>Click
here<font color="#FF0000">[/url]</font>
<td><a href="http://intboard.ru" target="_blank">Click here</a>
<tr><td width="50%">
<font color="#FF0000">[url]</font>http://intboard.ru<font color="#FF0000">[/url]</font>
<td><a href="http://intboard.ru" target="_blank">http://intboard.ru</a>
<tr><td width="50%">
<font color="#FF0000">[email]</font>null@xxxxpro.ru<font color="#FF0000">[/email]</font><br>
<font color="#FF0000">[email=</font>null@xxxxpro.ru<font color="#FF0000">]</font><br>
<font color="#FF0000">[email=</font>null@xxxxpro.ru<font color="#FF0000">]Write here![/email]</font>
<td><a href="mailto:null@xxxxpro.ru">null@xxxxpro.ru</a><br>
<a href="mailto:null@xxxxpro.ru">Write here!</a>
<tr> <td width="50%">
<font color="#FF0000">[b]</font>This is bold text<font color="#FF0000">[/b]</font>
<td><b>This is bold text</b>
<tr><td width="50%">
<font color="#FF0000">[i]</font>This is italic text<font color="#FF0000">[/i]</font>
<td><i>This is italic text</i>
<tr><td width="50%">
<font color="#FF0000">[u]</font>This is underlined text<font color="#FF0000">[/u]</font>
<td>
<u>This is underlined text</u>
<tr><td width="50%">
<font color="#FF0000">[s]</font>This is strike-through text<font color="#FF0000">[/s]</font>
<td>
<s>This is strike-through text</s>
<tr><td width="50%">
<font color="#FF0000">[font=</font>Courier<font color="#FF0000">]</font>Sample
of the font<font color="#FF0000">[/font]</font>
<td>
<font face="Courier New, Courier, mono">Sample of the font</font>
<tr><td width="50%">
<font color="#FF0000">[color=</font>blue<font color="#FF0000">]</font>This
is colored text<font color="#FF0000">[/color]</font>
<td>
<font color="blue">This is colored text</font>
<tr><td width="50%">
<font color="#FF0000">[size=</font>4<font color="#FF0000">]</font>This is big text<font color="#FF0000">[/size]</font>
<td>
<font size="4">This is big text</font>
<tr><td width="50%">
<p><font color="#FF0000">[list]</font><br>
<font color="#FF0000">[*]</font>Element 1<br>
<font color="#FF0000">[*]</font>Element 2<br>
<font color="#FF0000">[*]</font>Element 3<br>
<font color="#FF0000">[/list] </font></p>

<td>
<ul>
<li>Element 1</li>
<li>Element 2</li>
<li>Element 3</li>
</ul>
<tr><td width="50%">
<font color="#FF0000">[img=</font><?=$GLOBALS['opt_url'];?>/images/title.gif<font color="#FF0000">]</font>
<td>
<img src="<?=$GLOBALS['opt_url'];?>/images/title.gif">
<tr><td width="50%">
<font color="#FF0000">[table][tr][td]</font>Column1<font color="#FF0000">[td]</font>
Column2<br>
<font color="#FF0000">[tr][td colspan=</font>2<font color="#FF0000">]</font>Merged
column<font color="#FF0000">[/table]</font>
<td>
<table width="80%" border="1" align="center" class="usertable" cellpadding="0" cellspacing="0">
<tr>
<td>Column1
<td>Column2
<tr>
<td colspan="2">Merged column
</table>
<tr> <td width="50%">
<font color="#FF0000">[center]</font>This is centered text<font color="#FF0000">[/center]</font>
<td>
<div align="center">This is centered text</div>
<tr> <td width="50%">
<font color="#FF0000">[right]</font>This is right-justified text<font color="#FF0000">[/right]</font>
<td>
<div align="right">This is right-justified text</div>
<tr><td width="50%">
<font color="#FF0000">[off]</font>Sample of offtopic<font color="#FF0000">[/off]</font>
<td>
<span class="offtopic">Sample of offtopic</span>
<tr><td width="50%">
<font color="#FF0000">[code]</font>Sample of code<font color="#FF0000">[/code]</font>
<td>
<code>Sample of code </code>
<tr><td width="50%">
<font color="#FF0000">[php]</font>&lt;? phpinfo(); ?&gt;<font color="#FF0000">[/php]</font>
<td>
<?=highlight_string('<? phpinfo(); ?>',true);?>
<tr><td width="50%">
(c)<td>&copy;
<tr><td width="50%">
(r)<td>&reg;
<tr><td width="50%">
(tm)<td>&trade;
<tr><td width="50%">
--<td>&mdash;

<tr><td colspan="2" class="tablehead"><?=MSG_misc_extcode;?>
<tr><td width="50%">
[nocode]<td><?=MSG_misc_nocode;?>
<tr><td width="50%">
[nohtml]<td><?=MSG_misc_nohtml;?>
<tr><td width="50%">
[cut],[cut="Text"]<td><?=MSG_misc_cut;?>
<tr><td width="50%">
[hide=Number]<td><?=MSG_misc_hide;?>
<tr><td width="50%">
[level=Level]<td><?=MSG_misc_level;?>
<tr><td colspan="2" class="tablehead">&nbsp;
</table>
<? }

function smiles_start() { ?>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_misc_smiles;?>:

<? }

function smiles_entry($smile) { ?>
<tr><td><?=$smile['sm_code'];?>
<td><img src="<?=$GLOBALS['opt_url'];?>/smiles/<?=$smile['sm_file'];?>" alt="<?=$smile['sm_code'];?>">
<? }

function smiles_end() { ?>
</table>
<? }

function friend_form() { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_misc_friend;?> "<?=$GLOBALS['intopic']['t_title'];?>"
<tr><td width="50%"><?=MSG_misc_friendname;?>:
<td><input type=text name=name size=32 maxlength=32>
<tr><td><?=MSG_misc_friendaddr;?>:
<td><input type=text name=email size=32 maxlength=32>
<tr><td><?=MSG_misc_subj;?>:
<td><input type=text name=subj size=40 maxlength=80>
<tr><td><?=MSG_misc_text;?><br><?=MSG_misc_linkadded;?>:
<td><textarea name=text rows=8 cols=40></textarea>
<tr><td><?=MSG_user_ddoscode;?>
<td><?=show_ddos_code();?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=misc>
<input type=hidden name=a value=do_friend><input type=hidden name=t value=<?=$GLOBALS['topic'];?>>
<input type=submit value="<?=MSG_send;?>">
</table></form>
<? }

function sendmail_form($udata) { ?>
<form action="index.php" method=POST>
<table class="innertable" cellspacing=1 width="100%"><tr><td class="tablehead" colspan=2>
<?=MSG_misc_mail;?>
<tr><td width="50%"><?=MSG_misc_sender;?>:<td><?=user_out($GLOBALS['inuser']['u__name'],$GLOBALS['inuserid']);?>
&lt;<?=show_email_f($GLOBALS['inuser']['u__email'],$GLOBALS['inuser']['u_showmail'],$GLOBALS['inuserid']);?>&gt;
<tr><td><?=MSG_misc_receiver;?>:<td><?=user_out($udata['u__name'],getvar("u"));?>
&lt;<?=show_email_f($udata['u__email'],$udata['u_showmail'],$udata['u_id']);?>&gt;
<tr><td><?=MSG_misc_subj;?>:<td>
<input type=text name=subj size=30 maxlength=255>
<tr><td><?=MSG_misc_text;?>:
<td><textarea name=text cols=30 rows=12></textarea>
<tr><td><?=MSG_user_ddoscode;?>
<td><?=show_ddos_code();?>
<tr><td class="tablehead" colspan=2><input type=hidden name=m value=misc><input type=hidden name=a value=do_sendmail>
<input type=hidden name=u value=<?=getvar("u");?>><input type=submit value="<?=MSG_send;?>">
</table></form>
<? }

function detrans_form() { ?>
<table class="innertable" cellspacing=1 width="100%">
<tr><td colspan=4 class="tablehead"><?=MSG_misc_detrans;?>
<tr><td class="tablehead" colspan=2 width="50%"><?=MSG_misc_caps;?>
<td class="tablehead" colspan=2 width="50%"><?=MSG_misc_letters;?>
<tr><td class="tablehead" width="25%"><?=MSG_misc_lat;?><td class="tablehead" width="25%"><?=MSG_misc_cyr;?>
<td class="tablehead" width="25%"><?=MSG_misc_lat;?><td class="tablehead" width="25%"><?=MSG_misc_cyr;?>
<tr><td>A<td>À<td>a<td>à
<tr><td>B<td>Á<td>b<td>á
<tr><td>V<td>Â<td>v<td>â
<tr><td>G<td>Ã<td>g<td>ã
<tr><td>D<td>ä<td>d<td>ä
<tr><td>E<td>å<td>e<td>å
<tr><td>Yo<td>¨<td>yo<td>¸
<tr><td>J ZH Zh<td>Æ<td>j zh<td>æ
<tr><td>Z<td>Ç<td>z<td>ç
<tr><td>I<td>È<td>i<td>è
<tr><td>`I<td>É<td>`i<td>é
<tr><td>K<td>Ê<td>k<td>ê
<tr><td>L<td>Ë<td>l<td>ë
<tr><td>M<td>Ì<td>m<td>ì
<tr><td>N<td>Í<td>n<td>í
<tr><td>O<td>Î<td>o<td>î
<tr><td>P<td>Ï<td>p<td>ï
<tr><td>R<td>Ð<td>r<td>ð
<tr><td>S<td>Ñ<td>s<td>ñ
<tr><td>T<td>Ò<td>t<td>ò
<tr><td>U<td>Ó<td>u<td>ó
<tr><td>F<td>Ô<td>f<td>ô
<tr><td>H<td>Õ<td>h<td>õ
<tr><td>C<td>Ö<td>c<td>ö
<tr><td>CH Ch<td>×<td>ch<td>÷
<tr><td>SH Sh<td>Ø<td>sh<td>ø
<tr><td>SCH Sch<td>Ù<td>sch<td>ù
<tr><td>&nbsp;<td>&nbsp;<td>"<td>ú
<tr><td>Y<td>Û<td>y<td>û
<tr><td>&nbsp;<td>&nbsp;<td>'<td>ü
<tr><td>`E<td>Ý<td>`e<td>ý
<tr><td>YU Yu<td>Þ<td>yu<td>þ
<tr><td>YA Ya<td>ß<td>ya<td>ÿ
<tr><td>AY Ay<td>Àé<td>ay<td>àé
<tr><td>OY Oy<td>Îé<td>oy<td>îé
<tr><td>IY Iy<td>Èé<td>iy<td>èé
<tr><td>YY Yy<td>Ûé<td>yy<td>ûé
<tr><td>UY Uy<td>Óé<td>uy<td>óé
<tr><td>EY Ey<td>Åé<td>ey<td>åé
<tr><td colspan=4 align=center><?=MSG_misc_nodetrans;?><br>
<?=MSG_misc_example;?>
</table>
<? }

function tsel_start($title) { ?>
<script><!--
function put(tid) {
  window.opener.document.getElementById('tid').value=tid;
  window.close();
}
//--></script>
<table class="innertable" cellspacing=1 width="100%">
<tr><td class="tablehead"><?=MSG_misc_tsearch_result;?> "<?=$title;?>"
<? }

function tsel_entry($tdata) { ?>
<tr><td><a href="javascript:put('<?=$tdata['t_id'];?>')"><?=$tdata['t_title'];?></a>
<? }

function tsel_noentries() { ?>
<tr><td><?=MSG_misc_tsearch_none;?>
<? }

function tsel_end() { ?>
</table><br>
<? }

function tsel_form($flist,$title) { ?>
<html><head>
<title><?=MSG_misc_tsearch;?></title>
<link rel="stylesheet" href="<?=$GLOBALS['opt_url']."/styles/".$GLOBALS['inuser']['st_file']."/".$GLOBALS['inuser']['st_file'].".css";?>" type="text/css"><?
$opt_url=$GLOBALS['opt_url'];
if (substr($opt_url,-1,1)!='/') $opt_url.='/'; ?>
<base href="<?=$opt_url;?>">
</head><body>
<form action="index.php" method="post">
<table class="innertable" cellspacing=1 width="100%">
<tr><td colspan=2 class="tablehead"><?=MSG_misc_tsearch;?>
<tr><td><?=MSG_misc_tsearch_title;?>
<td><input type=text name=title value="<?=$title;?>">
<tr><td><?=MSG_misc_tsearch_forum;?>
<td><select name=f><?=set_select($flist,$GLOBALS['forum']);?></select>
<tr><td colspan=2 class="tablehead">
<input type=hidden name=a value=do_select_topic><input type=hidden name=m value=misc>
<input type=submit value="<?=MSG_search;?>">
</table></form>
</body></html>
<? }

