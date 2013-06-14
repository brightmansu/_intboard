<?

function do_process_post($topic,$newtopic=0) {
  if (getvar("preview")) { process_preview(); return; }
  if ($GLOBALS['inuserid']<=3 && $GLOBALS['opt_ddos']==2) check_ddos('code');
  global $link;
  $inforum=&$GLOBALS['inforum'];
  $inuserlevel=&$GLOBALS['inuserlevel'];
  $inuser=&$GLOBALS['inuser'];
  $inuserid=&$GLOBALS['inuserid'];
  $intopic=&$GLOBALS['intopic'];

  if ($inuserlevel<$inforum['f_lpost']) error(MSG_e_p_norights);
  if ($intopic['t__status']==1 || ($intopic['t__status']==2 && $GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate'] && !check_selfmod())) error(MSG_e_t_closed);
  if ($inforum['f_status']!=0) error(MSG_e_f_closed);
  if (!$text=$_POST['p_text']) error(MSG_e_p_empty);

  if (strlen($text)<$GLOBALS['opt_minpost']) error(MSG_e_p_toosmall);
  if ($GLOBALS['opt_maxpost'] && strlen($text)>$GLOBALS['opt_maxpost']) error(MSG_e_p_toolarge);

  if (is_uploaded_file($_FILES['attach']['tmp_name'])) {
      if ($inuserlevel<$inforum['f_lattach']) error(MSG_e_p_norightsattach);
      if ($inforum['f_attachpics']) {
          check_image("attach",$GLOBALS['opt_maxfileattach'],0,0,MSG_e_p_toobig,MSG_e_p_onlypics,"");
      }
      elseif ($_FILES['attach']['size']>$GLOBALS['opt_maxfileattach']) error(MSG_e_p_toobig);
      $pattach=handle_upload($_FILES['attach']);
  }
  else $pattach="0";

  if ($GLOBALS['inuser']['u_detrans']==1) {
      load_smiles();
      $text=untransliterate($text);
  }

  $text=" ".$text." ";
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."BadWord WHERE w_onlyname=0";
  $res =&db_query($sql,$link);
  while ($wdata=&db_fetch_array($res)) {
    $wdata['w_bad']=str_replace("/","\\/",$wdata['w_bad']);
    $wdata['w_bad']="/([\s,\.:;\-\?!\(\)\[\]\{\}])".str_replace("*","\S*?",$wdata['w_bad'])."([\s,\.:;-\?!\(\)\[\]\{\}])/is";
    $text=preg_replace($wdata['w_bad'],"$1".$wdata['w_good']."$2",$text);
  }
  $text=substr($text,1,strlen($text)-2);
  unset($_POST['p_text']);
  db_free_result($res);

  $inname=&getvar("inname");
  $uid=$inuserid;
  $sqldata = build_sql("p_");
  if ($inuserid<=3) {
    $pname=&getvar("inusername");
    if (!$pname) $pname="Guest";
    $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"$pname\" AND u_id>1";
    $res =&db_query($sql,$link);
    list($count)=db_fetch_row($res);
    if ($count>0) error(MSG_e_p_reguser);
  }
  elseif ($GLOBALS['inuserlevel']>=1000 && $GLOBALS['opt_impersonation'] && $inname!=$GLOBALS['inuser']['u__name'] && $inname!="") {
    $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE u__name=\"$inname\"";
    $res =&db_query($sql,$link);
    if ($tmp=db_fetch_row($res)) {
      $uid=$tmp[0];
    }
    else $uid=1;
    $pname=$inname;
  }
  else $pname=$inuser['u__name'];
  $time = $GLOBALS['curtime'];
  if ($inuserlevel<$inforum['f_lmoderate']) {
      if ($inuserid>3) {
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p_uid=$inuserid AND p__time>".($time-$GLOBALS['opt_flood']);
    }
    else {
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post WHERE p__ip=\"".getip()."\" AND p__time>".($time-$GLOBALS['opt_flood']);
    }
    $res =&db_query($sql,$link);
    $count=db_fetch_row($res);
    if ($count[0]>0) error(MSG_e_p_flood);
    db_free_result($res);
  }

  if ($sqldata) $sqldata.=", ";
  $sqldata.="p_uname=\"$pname\", ";
  $sqldata.="p_uid=\"$uid\", ";
  $sqldata.="p_tid=$topic, ";
  $sqldata.="p__time=\"$time\", ";
  $sqldata.="p__ip=\"".iptonum(getip())."\", ";
  $sqldata.="p_attach=$pattach, ";

  $premoderate=is_premod_need($newtopic);
  $sqldata.="p__premoderate=".intval($premoderate);
  $sqldata.=check_post_params();

  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Post SET p_text=\"".db_slashes($text)."\", $sqldata";
  $res =&db_query($sql,$link);
  $pid = db_insert_id($res);
  if (!$GLOBALS['inuser']['l_custom'] && $inuserid>3 && $GLOBALS['inuserlevel']>0) {
    $sql = "SELECT SUM(us_count) AS pcount FROM ".$GLOBALS['DBprefix']."UserStat us, ". $GLOBALS['DBprefix']."Forum f WHERE us.uid=".$GLOBALS['inuserid']." AND f.f_id=us.fid AND f_nostats=0";
    $res =&db_query($sql,$link);
    list($count)=db_fetch_row($res);
    db_free_result($res);
    $sql = "SELECT l_level FROM ".$GLOBALS['DBprefix']."UserLevel WHERE l_custom=0 AND l_minpost<".intval($count)." ORDER BY l_minpost DESC LIMIT 1";
    $res =&db_query($sql,$link);
    if (db_num_rows($res)>0) {
        $tmp=&db_fetch_array($res);
        $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__level=".$tmp['l_level']." WHERE u_id=".$inuserid." AND u__level<".$tmp['l_level'];
        $res =&db_query($sql,$link);
        $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online WHERE o_uid=\"".$uid.'"';
        $res=&db_query($sql,$link);
    }
  }

  if (!$premoderate) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET t__pcount=t__pcount+1, t__lastpostid=$pid, t__lasttime=".$GLOBALS['curtime']." WHERE t_id=".$topic;
    $res =&db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f__pcount=f__pcount+1, f__lastpostid=$pid WHERE f_id=".$GLOBALS['forum'];
    $res =&db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."UserStat SET us_count=us_count+1 WHERE uid=$uid AND  fid=".$GLOBALS['forum'];
    $res =&db_query($sql,$link);
    if (db_affected_rows($res)==0) {
      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UserStat SET uid=$uid, fid=".$GLOBALS['forum'].", us_count=1";
      $res =&db_query($sql,$link);
    }
  }
  else {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f__premodcount=f__premodcount+1 WHERE f_id=".$GLOBALS['forum'];
    $res =&db_query($sql,$link);
  }

  if ($GLOBALS['opt_fixviews']==1) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicView WHERE tid=\"$topic\"";
    $res =&db_query($sql,$link);
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."TopicView (tid,uid) VALUES (\"$topic\",".$GLOBALS['inuserid'].")";
    $res =&db_query($sql,$link);
    if ($GLOBALS['intopic']['lasttime']) {
      $sql = "UPDATE ".$GLOBALS['DBprefix']."LastVisit SET lv_markcount=lv_markcount-1 WHERE lv_markall>".$GLOBALS['intopic']['lasttime']." AND fid=".$GLOBALS['forum'];
      $res=&db_query($sql,$link);
    }
  }

  if (($GLOBALS['inforum']['f_lmoderate']<=$GLOBALS['inuserlevel'] || check_selfmod()) && $close=getvar("close")) {
      $sql = "UPDATE ".$GLOBALS['DBprefix']."Topic SET t__status=\"$close\" WHERE t_id=\"".$GLOBALS['topic']."\"";
      $res =&db_query($sql,$link);
  }

  if (getvar('del_draft')) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Draft WHERE dr_uid=".$GLOBALS['inuserid']." AND dr_fid=".$GLOBALS['forum'];
    if ($newtopic) $sql.=' AND dr_tid=0';
    else $sql.=' AND dr_tid='.$GLOBALS['topic'];
    $res =&db_query($sql,$link);
  }

  if (getvar("subscr") && $GLOBALS['inuserid']>3 && !$GLOBALS['intopic']['subscr']) {
      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription VALUES (\"$inuserid\",\"$topic\",\"".$inforum['f_id']."\")";
      $res =&db_query($sql,$link);
  }

  if (!$premoderate || $GLOBALS['inuserlevel']>=$GLOBALS['inforum']['f_lmoderate']) {
    $sql = "SELECT u__name,u__email,u_id,u__key FROM ".$GLOBALS['DBprefix']."Subscription sb, ".$GLOBALS['DBprefix']."User u WHERE sb.uid=u.u_id ".
    " AND sb.tid=\"".$GLOBALS['topic']."\" AND sb.uid!=".$GLOBALS['inuserid'];
    $buffer=load_mail("std_post.txt");
  }
  else {
    $sql = "SELECT u__name,u__email,u_id,u__key FROM ".$GLOBALS['DBprefix']."Subscription sb, ".$GLOBALS['DBprefix']."User u ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u.u_id AND ua.fid=".$GLOBALS['forum'].") ".
    "WHERE sb.uid=u.u_id AND sb.tid=\"".$GLOBALS['topic']."\" AND sb.uid!=".$GLOBALS['inuserid'].
    " AND COALESCE(ua_level,u__level)>=".$GLOBALS['inforum']['f_lmoderate']."";
    $buffer=load_mail("std_pmod.txt");
    $GLOBALS['premod_link']=$GLOBALS['opt_url']."/index.php?m=moderate&a=premod&f=".$GLOBALS['forum'];
  }
  $res =&db_query($sql,$link);
  $GLOBALS['ttitle']=$GLOBALS['intopic']['t_title'];
  $GLOBALS['ftitle']=$GLOBALS['inforum']['f_title'];
  $GLOBALS['postername']=$GLOBALS['inuser']['u__name'];
  $GLOBALS['text']=$text;
  while ($email=db_fetch_row($res)) {
    $GLOBALS['username']=$email[0];
    $GLOBALS['flink']=$GLOBALS['opt_url']."/index.php?t=".$GLOBALS['topic'];
    $GLOBALS['unsublink']=$GLOBALS['opt_url']."/agent.php?a=unsub&u=".$email[2].
     "&f=".$GLOBALS['forum']."&t=".$GLOBALS['topic']."&key=".md5($GLOBALS['topic'].$email[3]);
    replace_mail($buffer,$email[1],$GLOBALS['ttitle']);
  }
  return $pid;
}

function process_preview($form=true) {
  global $link;
  load_style('display.php');
  
  $_POST['p_uname']=$GLOBALS['inuser']['u__name'];
  $_POST['p_tid']=$GLOBALS['topic'];
  get_preview_data($GLOBALS['inuserid'],$GLOBALS['inuser']);
  
  display_topic_start('',$_POST,true,false,false,false);  
  display_topic_entry($_POST,$GLOBALS['inuser'],'postentry',array(),array(),'',0,0);
  display_topic_end('',$_POST,true,false,false,false);
  if ($form) display_post_form(MSG_p_create,$_POST,0);
}

function new_topic_mail($topic,$premod) {
  global $link;
  
  $forum=$GLOBALS['forum'];
  $sql = "SELECT uid FROM ".$GLOBALS['DBprefix']."Subscription WHERE fid=".$GLOBALS['forum']." AND tid=4294967295";
  $res =&db_query($sql,$link);
  $sqldata="";
  while ($uid=db_fetch_row($res)) {
    if ($uid[0]!=$GLOBALS['inuserid'] || !getvar('subscr')) {
      if ($sqldata) $sqldata.=", ";
      $sqldata = "(".$uid[0].",$topic,$forum)";
    }
  }
  if ($sqldata) {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Subscription VALUES $sqldata";
    $res =&db_query($sql,$link);
  }
  $GLOBALS['intopic']['t_title']=&getvar('t_title');
  $GLOBALS['intopic']['t_id']=$topic;
  $GLOBALS['intopic']['t_link']=&getvar('t_link');

  if (!$premod) {
    $sql = "SELECT u__name,u__email FROM ".$GLOBALS['DBprefix']."Subscription sb, ".$GLOBALS['DBprefix']."User u WHERE sb.uid=u.u_id AND sb.fid=".$GLOBALS['forum'].
           " AND sb.tid=4294967294 AND sb.uid!=".$GLOBALS['inuserid'];
    $res =&db_query($sql,$link);
    $buffer=load_mail("std_tpc.txt");
  }
  else {
    $sql = "SELECT u__name,u__email FROM ".$GLOBALS['DBprefix']."Subscription sb, ".$GLOBALS['DBprefix']."User u ".
    "LEFT JOIN ".$GLOBALS['DBprefix']."UserAccess ua ON (ua.uid=u.u_id AND ua.fid=".$GLOBALS['forum'].") ".
    " WHERE sb.uid=u.u_id AND sb.fid=".$GLOBALS['forum'].
    " AND sb.tid=4294967294 AND sb.uid!=".$GLOBALS['inuserid'].
    " AND COALESCE(ua_level,u__level)<=".$GLOBALS['inforum']['f_lmoderate'];
    $res =&db_query($sql,$link);
    $buffer=load_mail("std_tpm.txt");
    $GLOBALS['premod_link']=$GLOBALS['opt_url']."/index.php?m=moderate&a=premod&f=".$GLOBALS['forum'];
  }

  while ($email=db_fetch_row($res)) {
    $GLOBALS['username']=$email[0];
    $GLOBALS['postername']=$GLOBALS['inuser']['u__name'];
    $GLOBALS['ftitle']=$GLOBALS['inforum']['f_title'];
    $GLOBALS['text']=$_POST['p_text'];
    $GLOBALS['flink']=$GLOBALS['opt_url']."/index.php?t=".$GLOBALS['topic'];
    $GLOBALS['sublink']=$GLOBALS['opt_url']."/agent.php?a=subscr&u=".$GLOBALS['inuserid']."&t=".
                     $GLOBALS['topic']."&key=".md5($GLOBALS['topic'].$GLOBALS['inuser']['u__key']);
    $GLOBALS['unsublink']=$GLOBALS['opt_url']."/agent.php?a=unsub&u=".$GLOBALS['inuserid']."&t=".
                     $GLOBALS['topic']."&key=".md5($GLOBALS['topic'].$GLOBALS['inuser']['u__key']);
    replace_mail($buffer,$email[1],MSG_p_newmessage." ".$GLOBALS['ttitle']);
  }
}

function check_premod(&$fdata) {
  global $link;
  if ($GLOBALS['inuserbasic']>=$fdata['f_lmoderate'] || $fdata['ua_level']>=$fdata['f_lmoderate']) {
      $sql="SELECT COUNT(*) FROM ".$GLOBALS['DBprefix']."Post p, ".$GLOBALS['DBprefix']."Topic t ".
      "WHERE t_fid=".$fdata['f_id']." AND p_tid=t_id AND p__premoderate=1";
      $res =&db_query($sql,$link);
      $pcount=db_fetch_row($res);
  }
  return $pcount[0];
}

