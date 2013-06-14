<? /*

Forum & category administration script for Intellect Board 2

(c) 2004-2007, XXXX Pro, United Open Project
Visit us online: http://intboard.ru
*/

if (!$IBOARD) die("Hack attempt!");

function ct_list() {
  global $link;
  ad_ct_list_start();
  
  $sql = "SELECT ct_id,ct_name,ct_sortfield, f_title, f_id, f_sortfield, f_parent, tp_container, tp_title ".
  "FROM ".$GLOBALS['DBprefix']."Category ct ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."Forum f ON (ct_id=f_ctid) ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."ForumType ON (f_tpid=tp_id) ".
  "ORDER BY ct_sortfield";
  $res=db_query($sql,$link);
  $oldcat = "";
  $forums=array();
  while ($fdata=db_fetch_array($res)) {
    $forums[]=$fdata;
  }
  $forums=sort_forums_recurse($forums);
  $oldcat=0;
  for ($i=0, $count=count($forums); $i<$count; $i++) {
    if ($oldcat!=$forums[$i]['ct_id']) {
      ad_ct_entry($forums[$i]);
      $oldcat=$forums[$i]['ct_id'];
    }
    if ($forums[$i]['f_id']) ad_f_entry($forums[$i]);
  }
  
  ad_ct_list_end();
}

function ct_new() {
  $link = $GLOBALS['link'];
  $sql = "SELECT MAX(ct_sortfield) FROM ".$GLOBALS['DBprefix']."Category";
  $res= db_query($sql,$link);
  $sort = db_fetch_row($res);
  db_free_result($res);
  $ct_name = getvar("ct_name");
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Category SET ct_name=\"".$ct_name."\", ct_sortfield=\"".($sort[0]+1)."\"";
  $res = db_query($sql,$link);
  ad_message(MSG_ct_created,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}

function ct_edit() {
  $link = $GLOBALS['link'];
  $ctid = getvar("ctid");
  $sql = "SELECT ct_name FROM ".$GLOBALS['DBprefix']."Category WHERE ct_id=$ctid";
  $res = db_query($sql,$link);
  $name = db_fetch_row($res);
  db_free_result($res);
  ad_ct_editform($name[0]);
}

function ct_delete() {
  $link = $GLOBALS['link'];
  $ctid = getvar("ctid");
  $sql = "SELECT MAX(ct_id) FROM ".$GLOBALS['DBprefix']."Category WHERE ct_id<$ctid GROUP BY ct_id";
  $res = db_query($sql,$link);
  $newctid = db_fetch_row($res);
  if ($newctid==0) {
    db_free_result($res);
    $sql = "SELECT MIN(ct_id) FROM ".$GLOBALS['DBprefix']."Category WHERE ct_id>$ctid GROUP BY ct_id";
    $res = db_query($sql,$link);
    $newctid = db_fetch_row($res);
  }
  if (!$newctid) error(MSG_e_ctlast);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_ctid=".$newctid[0]." WHERE f_ctid=$ctid";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Category WHERE ct_id=$ctid";
  $res = db_query($sql,$link);
  ad_message(MSG_ct_deleted,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}

function ct_save() {
  check_post();
  $link = $GLOBALS['link'];
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Category SET ct_name=\"".getvar("ct_name")."\" WHERE ct_id=".getvar("ctid");
  $res = db_query($sql,$link);
  ad_message(MSG_ct_saved,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}

function f_new_type() {
  $link = $GLOBALS['link'];
  $ctid = getvar("ctid");
  $sql = "SELECT tp_id,tp_title FROM ".$GLOBALS['DBprefix']."ForumType";
  $res = db_query($sql,$link);
  while ($tpdata=db_fetch_row($res)) {
    $typeselect.="<input type=radio name=f_tpid value=\"".$tpdata[0]."\"";
    if ($tpdata[0]==1) $typeselect.=" checked";
    $typeselect.=">".constant($tpdata[1])."<br>";
  }
  ad_f_new_type($typeselect);
}

function f_new() {
  $link = $GLOBALS['link'];
//  $sql = "SELECT ct_id,ct_name FROM ".$GLOBALS['DBprefix']."Category";
//  $catselect = build_select($sql,$fdata['f_ctid']);
  $f_tpid = getvar("f_tpid");
  $ctid = getvar("ctid");
  $sql = "SELECT tp_template FROM ".$GLOBALS['DBprefix']."ForumType WHERE tp_id=\"$f_tpid\"";
  $res = db_query($sql,$link);
  $tpname = db_fetch_row($res);
  load_style($tpname[0].".php");
  global $newaction;
  $newaction = "f_create";
  global $newmodule;
  $newmodule = "forum";
  $langselect = "<option value=0>".MSG_alllangs.build_select("SELECT ln_id,ln_name FROM ".$GLOBALS['DBprefix']."Language");
    $levelselect = build_level_select();
  $catselect = build_select("SELECT ct_id,ct_name FROM ".$GLOBALS['DBprefix']."Category");
  $fdata['f_lview']=-1;
  $fdata['f_lread']=-1;
  $fdata['f_lpost']=100;
  $fdata['f_ltopic']=100;
  $fdata['f_ledit']=100;
  $fdata['f_lvote']=100;
  $fdata['f_lpoll']=100;
  $fdata['f_lsticky']=500;
  $fdata['f_lattach']=100;
  $fdata['f_lhtml']=1000;
  $fdata['f_lmoderate']=500;
  $fdata['f_lip']=500;
  $fdata['f_rate']=1;
  $fdata['f_bcode']=1;
  $fdata['f_smiles']=1;
  $fdata['f_tpid']=$_POST['f_tpid'];
  $fcontainer = "<option value=0>".MSG_f_mainpage.build_forum_select('f_lview',0,'tp_container=1');
  call_user_func($tpname[0]."_params",$catselect,$levelselect,$fdata,$fcontainer,$langselect);
}

function f_create() {
  check_post();
  $link = $GLOBALS['link'];
  $ctid = getvar("ctid");
  $rules=db_slashes($_POST['f_rules']);
  unset($_POST['f_rules']);
  if ($GLOBALS['opt_hurl']) {
    $flink=getvar('f_link');
    if (!preg_match('/^[\w\d\-]+$/i',$flink)) unset($_POST['f_link']);
    else {
      $sql = "SELECT f_id FROM ".$GLOBALS['DBprefix']."Forum WHERE f_link=\"".$flink."\"";
      $res=db_query($sql,$link);
      if (db_num_rows($res)>0) unset($_POST['f_link']);
      db_free_result($res);
    }
    if (file_exists($GLOBALS['opt_dir'].'/'.$_POST['f_link'])) unset($_POST['f_link']);
  }

  $fdata = build_sql("f_").", f_rules=\"$rules\"";
  $sql = "SELECT MAX(f_sortfield) FROM ".$GLOBALS['DBprefix']."Forum";
  $res = db_query($sql,$link);
  $tmp = db_fetch_row($res);
  db_free_result($res);
  $count = $tmp[0]+1;
  $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Forum SET f_sortfield=$count, $fdata";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res = db_query($sql,$link);
  if (file_exists($GLOBALS['opt_dir'].'/config/guest.txt')) unlink($GLOBALS['opt_dir'].'/config/guest.txt');
  if (file_exists($GLOBALS['opt_dir'].'/config/fselect.txt')) unlink($GLOBALS['opt_dir'].'/config/fselect.txt');
  ad_message(MSG_f_created,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}

function f_confrim() {
//  $link = $GLOBALS['link'];
  $newaction = "f_delete";
  $params['fid']=getvar($f_id);
  $newmodule = "forum";
  confirm($newmodule,$newaction,$params);
}

function f_delete() {
  check_post();
  $link = $GLOBALS['link'];
  $fid = getvar("fid");
  $sql = "SELECT t_id FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=$fid";
  $res = db_query($sql,$link);
  while ($num=db_fetch_row($res)) {
    if ($buffer) $buffer .= " OR ";
    $buffer .= "tid=".$num[0];
    if ($buffer2) $buffer2 .= " OR ";
    $buffer2 .= "p_tid=".$num[0];
    if ($buffer3) $buffer3 .= " OR ";
    $buffer3 .= "pl_tid=".$num[0];
  }
  db_free_result($res);

  $sql = "SELECT f_parent FROM ".$GLOBALS['DBprefix']."Forum WHERE f_id=\"$fid\"";
  $res = db_query($sql,$link);
  $parent=db_fetch_row($res);
  db_free_result($res);

  $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_parent=\"".$parent[0]."\" WHERE f_parent=\"$fid\"";
  $res = db_query($sql,$link);

  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Forum WHERE f_id=$fid";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Topic WHERE t_fid=$fid";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."LastVisit WHERE fid=\"$fid\"";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserAccess WHERE fid=\"$fid\"";
  $res = db_query($sql,$link);


  if ($buffer) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicView WHERE $buffer";
    $res = db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Bookmark WHERE $buffer";
    $res = db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Subscription WHERE $buffer";
    $res = db_query($sql,$link);
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."TopicRate WHERE $buffer";
    $res = db_query($sql,$link);
  }

  if ($buffer2) {
    $sql = "SELECT p_attach FROM ".$GLOBALS['DBprefix']."Post WHERE ($buffer2) AND p_attach!=0";
    $res = db_query($sql,$link);
    while ($num=db_fetch_row($res)) {
      if ($attach) $attach.=" OR ";
      $attach.="file_id=".$num[0];
    }
    db_free_result($res);
    if ($attach) {
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."File WHERE $attach";
      $res = db_query($sql,$link);
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE $buffer2";
    $res = db_query($sql,$link);
  }

  if ($buffer4) {
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Poll WHERE $buffer3";
    $res = db_query($sql,$link);
    while ($num=db_fetch_row($res)) {
      if ($buffer4) $buffer4 .= " OR ";
      $buffer4 .= "pv_plid=".$num[0];
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Poll WHERE $buffer3";
    $res = db_query($sql,$link);
    $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."PollVariant WHERE $buffer4";
    $res = db_query($sql,$link);
    while ($num=db_fetch_row($res)) {
      if ($buffer5) $buffer5 .= " OR ";
      $buffer5 .= "pvid=".$num[0];
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."PollVariant WHERE $buffer4";
    $res = db_query($sql,$link);
    if ($buffer5) {
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Vote WHERE $buffer5";
      $res = db_query($sql,$link);
    }
  }
  delete_cache($fid);
  if (file_exists($GLOBALS['opt_dir'].'/config/fselect.txt')) unlink($GLOBALS['opt_dir'].'/config/fselect.txt');
  ad_message(MSG_f_deleted,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}

function f_edit() {
  $link = $GLOBALS['link'];
  $fid = getvar("fid");
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."ForumType tp WHERE f.f_id=\"$fid\" AND f.f_tpid=tp.tp_id";
  $res = db_query($sql,$link);
  if (db_num_rows($res)==0) error(MSG_e_noforum);
  $fdata = db_fetch_array($res);
  $sql = "SELECT ct_id,ct_name FROM ".$GLOBALS['DBprefix']."Category";
  $catselect = build_select($sql,$fdata['f_ctid']);
  $levelselect = build_level_select();
    $langselect = "<option value=0>".MSG_alllangs.build_select("SELECT ln_id,ln_name FROM ".$GLOBALS['DBprefix']."Language");
  load_style($fdata['tp_template'].".php");
  global $newaction,$newmodule;
  $newaction = "f_save";
  $newmodule = "forum";
    $sql = "SELECT f_id,f_title FROM ".$GLOBALS['DBprefix']."Forum, ".$GLOBALS['DBprefix']."ForumType WHERE f_tpid=tp_id AND tp_container=1";
  $fcontainer = "<option value=\"\">".MSG_nochanges."<option value=\"0\">".MSG_f_mainpage.build_select($sql);
  //$fcontainer = "<option value=0>".MSG_f_mainpage.build_forum_select('f_lview',2,'tp_container=1');
  call_user_func($fdata['tp_template']."_params",$catselect,$levelselect,$fdata,$fcontainer,$langselect);
}

function delete_cache($forum) {
  if (is_file($GLOBALS['opt_dir'].'/config/moders'.$forum.'.php')) {
    unlink($GLOBALS['opt_dir'].'/config/moders'.$forum.'.php');
  }
}

function f_save() {
  check_post();
  $link = $GLOBALS['link'];
  $fid = getvar("fid");
  $rules=db_slashes($_POST['f_rules']);
  unset($_POST['f_rules']);
  $fdata = build_sql("f_").", f_rules=\"$rules\"";
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET $fdata WHERE f_id=$fid";
  $res = db_query($sql,$link);
  delete_cache($fid);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Online";
  $res = db_query($sql,$link);
  if (file_exists($GLOBALS['opt_dir'].'/config/guest.txt')) unlink($GLOBALS['opt_dir'].'/config/guest.txt');
  if (file_exists($GLOBALS['opt_dir'].'/config/fselect.txt')) unlink($GLOBALS['opt_dir'].'/config/fselect.txt');
  ad_message(MSG_f_saved,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}

/*function f_up() {
  $link = $GLOBALS['link'];
  $fid = getvar("fid");
  $sql = "SELECT f_sortfield,f_ctid,f_parent FROM ".$GLOBALS['DBprefix']."Forum WHERE f_id=$fid";
  $res = db_query($sql,$link);
  $sort = db_fetch_row($res);
  db_free_result($res);
  $sql = "SELECT MAX(f_sortfield) FROM ".$GLOBALS['DBprefix']."Forum WHERE f_sortfield<".$sort[0]." AND f_ctid=".$sort[1]." AND f_parent=".$sort[2];
  $res = db_query($sql,$link);
  $prev_sort = db_fetch_row($res);
  db_free_result($res);
  $sql="UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".$sort[0]." WHERE f_sortfield=".$prev_sort[0];
  db_query($sql,$link);
  $sql="UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".$prev_sort[0]." WHERE f_id=".$fid;
  db_query($sql,$link);
  ad_message(MSG_f_moved,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}

function f_down() {
  $link = $GLOBALS['link'];
  $fid = getvar("fid");
  $sql = "SELECT f_sortfield,f_ctid,f_parent FROM ".$GLOBALS['DBprefix']."Forum WHERE f_id=$fid";
  $res = db_query($sql,$link);
  $sort = db_fetch_row($res);
  db_free_result($res);
  $sql = "SELECT MIN(f_sortfield) FROM ".$GLOBALS['DBprefix']."Forum WHERE f_sortfield>".$sort[0]." AND f_ctid=".$sort[1]." AND f_parent=".$sort[2];
  $res = db_query($sql,$link);
  $prev_sort = db_fetch_row($res);
  db_free_result($res);
  $sql="UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".$sort[0]." WHERE f_sortfield=".$prev_sort[0];
  db_query($sql,$link);
  $sql="UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".($prev_sort[0])." WHERE f_id=".$fid;
  db_query($sql,$link);
  ad_message(MSG_f_moved,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}*/

function f_group() {
  $link = $GLOBALS['link'];
  $sql = "SELECT f_id,f_title FROM ".$GLOBALS['DBprefix']."Forum f, ".$GLOBALS['DBprefix']."Category ct ".
         "WHERE f_ctid=ct_id ORDER BY ct_sortfield,f_sortfield";
  $res = db_query($sql,$link);
  $count = db_num_rows($res);
  while ($fdata=db_fetch_row($res)) {
    $buffer.="<input type=checkbox name=fs[".$fdata[0]."] value=".$fdata[0].">".$fdata[1]."<br>";
  }
  $langselect = "<option value=\"\">".MSG_nochanges."<option value=0>".MSG_alllangs.build_select("SELECT ln_id,ln_name FROM ".$GLOBALS['DBprefix']."Language");
    $levelselect = "<option value=\"\">".MSG_nochanges.build_level_select();
  $catselect = "<option value=\"\">".MSG_nochanges.build_select("SELECT ct_id,ct_name FROM ".$GLOBALS['DBprefix']."Category");
    $sql = "SELECT f_id,f_title FROM ".$GLOBALS['DBprefix']."Forum, ".$GLOBALS['DBprefix']."ForumType WHERE f_tpid=tp_id AND tp_container=1";
  $fcontainer = "<option value=\"\">".MSG_nochanges."<option value=\"0\">".MSG_f_mainpage.build_select($sql);
  ad_f_group($buffer,$catselect,$fcontainer,$levelselect,$langselect,$count);
}

function f_group_process() {
  check_post();
  $link = $GLOBALS['link'];
  if (!is_array($_POST['fs'])) error(MSG_e_f_noselected);
  foreach ($_POST['fs'] as $forum=>$value) {
    if ($sqldata) $sqldata.=" OR ";
    $sqldata.="f_id=$forum";
  }
  $sqldata2 = "";
  foreach ($_POST as $name=>$value) {
    if (substr($name,0,2)=="f_" && $value!="") {
       if ($sqldata2) $sqldata2.=", ";
       $sqldata2.= "$name=\"".db_slashes($value)."\"";
     }
  }
  if (!$sqldata2) error(MSG_e_f_noparams);
  $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET $sqldata2 WHERE $sqldata";
  $res = db_query($sql,$link);
  ad_message(MSG_f_groupdone,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}

/*function f_exchange() {
  global $link;
  $sql = "SELECT f_sortfield FROM ".$GLOBALS['DBprefix']."Forum WHERE f_id=\"".getvar('forum1')."\"";
  $res = db_query($sql,$link);
  list($forum1pos)=db_fetch_row($res);
  $sql = "SELECT f_sortfield FROM ".$GLOBALS['DBprefix']."Forum WHERE f_id=\"".getvar('forum2')."\"";
  $res = db_query($sql,$link);
  list($forum2pos)=db_fetch_row($res);
  if (getvar('exchange')) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".$forum2pos." WHERE f_id=\"".getvar('forum1')."\"";
    $res = db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".$forum1pos." WHERE f_id=\"".getvar('forum2')."\"";
    $res = db_query($sql,$link);
  }
  if (getvar('move_before')) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=f_sortfield+1 WHERE f_sortfield>=\"".$forum2pos."\"";
    $res = db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".$forum2pos." WHERE f_id=\"".getvar('forum1')."\"";
    $res = db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=f_sortfield-1 WHERE f_sortfield>\"".$forum1pos."\"";
    $res = db_query($sql,$link);
  }
  if (getvar('move_after')) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=f_sortfield+1 WHERE f_sortfield>\"".$forum2pos."\"";
    $res = db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".$forum2pos." WHERE f_id=\"".getvar('forum1')."\"";
    $res = db_query($sql,$link);
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=f_sortfield-1 WHERE f_sortfield>\"".$forum1pos."\"";
    $res = db_query($sql,$link);
    }
    ad_message(MSG_f_moved,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");
}*/

function f_exchange() {
  global $link;
  if (is_array($_POST['cat'])) foreach ($_POST['cat'] as $curid=>$curvalue) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Category SET ct_sortfield=".intval($curvalue).
    " WHERE ct_id=".intval($curid);
    $res = db_query($sql,$link);
  }
  if (is_array($_POST['fid'])) foreach ($_POST['fid'] as $curid=>$curvalue) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."Forum SET f_sortfield=".intval($curvalue).
    " WHERE f_id=".intval($curid);
    $res = db_query($sql,$link);
  }
  ad_message(MSG_f_moved,MSG_ct_list,"admin/index.php?m=forum&a=ct_list");  
}

function f_confirm() {
  $params['fid']=getvar('fid');
  confirm("forum","f_delete",$params,MSG_f_confirm."?","admin/index.php?m=forum&a=ct_list");
}

function ct_confirm() {
  $params['ctid']=getvar('ctid');
  confirm("forum","ct_delete",$params,MSG_ct_confirm."?","admin/index.php?m=forum&a=ct_list");
}

function f_losttopic() {
  global $link;
  $sql = "SELECT DISTINCT p_tid FROM ".$GLOBALS['DBprefix']."Post LEFT JOIN ".$GLOBALS['DBprefix']."Topic ON (t_id=p_tid) WHERE t_id IS NULL";
  $res=db_query($sql,$link);
  while ($pdata=db_fetch_row($res)) {
    if ($sqldata) $sqldata.=" OR ";
    $sqldata.="p_tid=".$pdata[0];
  }
  if ($sqldata) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Post WHERE $sqldata";
    $res=db_query($sql,$link);
    $posts=db_affected_rows($res);
  }
  unset($sqldata);
  $sql = "SELECT DISTINCT t_id FROM ".$GLOBALS['DBprefix']."Topic LEFT JOIN ".$GLOBALS['DBprefix']."Post ON (p_tid=t_id) WHERE p_tid IS NULL";
  $res=db_query($sql,$link);
  while ($pdata=db_fetch_row($res)) {
    if ($sqldata) $sqldata.=" OR ";
    $sqldata.="t_id=".$pdata[0];
  }
  if ($sqldata) {
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."Topic WHERE $sqldata";
    $res=db_query($sql,$link);
    $topics=db_affected_rows($res);
  }
  load_lang("format.php");
  ad_message(MSG_f_cleared." ".format_word($posts,MSG_p1,MSG_p2,MSG_p3)." ".format_word($topics,MSG_t1,MSG_t2,MSG_t3),MSG_go_stats,"admin/index.php");
}