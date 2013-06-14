<? /*

IRC-web gate script for Intellect Board 2 Project
 
(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function irc_view() { 
	if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lread']) error(MSG_e_f_norightsread);
	$name=$GLOBALS['inuser']['u__name'];
	$name=transliterate($name);
	$name=preg_replace("/\\W+/","",$name);
	if ($GLOBALS['inforum']['f_smiles']) {
		global $link;
		$smiles="<param name=\"style:bitmapsmileys\" value=\"true\">\n";
		$sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Smile";
		$res =&db_query($sql,$link);
		$counter=1;
		while ($smile=&db_fetch_array($res)) {
			$smiles.="<param name=\"style:smiley".$counter."\" value=\"".$smile['sm_code']." ../smiles/".$smile['sm_file']."\">\n";
			$counter++;
		}
	}
	if (strpos($GLOBALS['inforum']['f_url'],":")) list($server,$port)=explode(":",$GLOBALS['inforum']['f_url']);
	else $server=$GLOBALS['inforum']['f_url'];
	if ($GLOBALS['inuserid']<=3) $name="Guest????";
  irc_form($name,$smiles,$server,$port,$GLOBALS['inforum']['f_text']);
}

function irc_locations($locations) {
  push_parents($locations,$GLOBALS['inforum']['f_parent']);	
	if ($GLOBALS['action']=="irc_view") {
		array_push($locations,$GLOBALS['inforum']['f_title']);
	}
	return $locations;
}