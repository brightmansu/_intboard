<? /*

Group administration script for Intellect Board 2 Project

(C) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

if (!$IBOARD) die("Hack attempt!");

function g_view() {
  $link = $GLOBALS['link'];
  $sql = "SELECT g.*, COUNT(gm.uid) AS gm_count FROM ".$GLOBALS['DBprefix']."UGroup g ".
  "LEFT JOIN ".$GLOBALS['DBprefix']."UGroupMember gm ON (gm.gid=g.g_id) GROUP BY g.g_id,g.g_title,g.g_setlevel,g.g_ljoin,g.g_lview,g.g_lautojoin,g.g_descr,g.g_allowquit";
  $res = db_query($sql,$link);
  ad_group_start();
  while ($gdata=db_fetch_array($res)) {
    ad_group_entry($gdata);
  }
  ad_group_end();
}

function g_new() {
  $levels = build_level_select();
  ad_group_form(array(),"g_create",$levels);
}

function g_create() {
  check_post();
  if (!getvar("g_title")) error(MSG_e_g_notitle);
  $coords = getvar("coord");
  if ($coords) {
    $coords = str_replace(";",",",$coords);
    $coords = preg_replace("/, +/",",",$coords);
    $coords = explode(",",$coords);
    foreach ($coords as $curuser) {
      if ($userlist) $userlist.=" OR ";
      $userlist.="u__name=\"$curuser\"";
    }
  }
  $sqldata = build_sql("g_");
  global $link;
  $sql="INSERT INTO ".$GLOBALS['DBprefix']."UGroup SET $sqldata";
  $res=db_query($sql,$link);
  $gid=db_insert_id($res);

  if ($coords) {
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UGroupMember (uid,gid,gm_status) SELECT u_id,\"$gid\",\"2\" FROM ".$GLOBALS['DBprefix']."User WHERE $userlist";
    $res = db_query($sql,$link);
  }
  ad_message(MSG_g_created,MSG_g_list,"admin/index.php?m=group&a=g_view");
}

function g_edit() {
  global $link;
  $gid=getvar("g");
  $sql = "SELECT * FROM ".$GLOBALS['DBprefix']."UGroup WHERE g_id=\"$gid\"";
  $res = db_query($sql,$link);
  $gdata = db_fetch_array($res);
  db_free_result($res);
  $levels = build_level_select();
  ad_group_form($gdata,"g_save",$levels);
  $sql = "SELECT u.u_id,u.u__name FROM ".$GLOBALS['DBprefix']."User u, ".$GLOBALS['DBprefix']."UGroupMember gm WHERE gm.gid=\"$gid\" AND gm.gm_status=2 AND u.u_id=gm.uid";
  $res = db_query($sql,$link);
  ad_coord_start();
  while ($udata=db_fetch_array($res)) {
    ad_coord_entry($udata);
  }
  ad_coord_end();
}

function g_save() {
  check_post();
  if (!getvar("g_title")) error(MSG_e_g_notitle);
  $sqldata = build_sql("g_");
  global $link;
  $gid=getvar("g");
  $sql="UPDATE ".$GLOBALS['DBprefix']."UGroup SET $sqldata WHERE g_id=\"$gid\"";
  $res=db_query($sql,$link);
  ad_message(MSG_g_saved,MSG_g_list,"admin/index.php?m=group&a=g_view");
}

function g_coord() {
  $coords = getvar("coord");
  $gid=getvar("g");
  global $link;
  if ($coords) {
    $coords = str_replace(";",",",$coords);
    $coords = preg_replace("/, +/",",",$coords);
    $coords = explode(",",$coords);
    foreach ($coords as $curuser) {
      if ($userlist) $userlist.=" OR ";
      $userlist.="u__name=\"$curuser\"";
    }
  }
  $delete=$_POST['delete'];
  if (is_array($delete)) {
    foreach ($delete as $curid=>$curvalue) {
      if ($deldata) $deldata.=" OR ";
      $deldata.="uid=\"".addslashes($curid)."\"";
    }
    $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupMember WHERE gid=\"$gid\" AND ($deldata)";
    $res = db_query($sql,$link);
  }
  if ($coords) {
    $sql = "SELECT u_id FROM ".$GLOBALS['DBprefix']."User WHERE $userlist";
    $res = db_query($sql,$link);
    while ($uid=db_fetch_row($res)) {
      $sql2="DELETE FROM ".$GLOBALS['DBprefix']."UGroupMember WHERE gid=\"$gid\" AND uid=".$uid[0];
      $res2 = db_query($sql2,$link);
      $sql2="INSERT INTO ".$GLOBALS['DBprefix']."UGroupMember SET gid=\"$gid\", uid=".$uid[0].", gm_status=2";
      $res2 = db_query($sql2,$link);      
    }
  }
  ad_message(MSG_g_changed,MSG_g_list,"admin/index.php?m=group&a=g_view");
}

function g_confirm() {
  $newaction="g_delete";
  $newmodule = "group";
  $params['g']=getvar("g");
  confirm($newmodule,$newaction,$params,MSG_g_delete,"admin/index.php?m=group&a=g_view");
}

function g_delete() {
  check_post();
  global $link;
  $gid=getvar("g");
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupMember WHERE gid=\"$gid\"";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupAccess WHERE gid=\"$gid\"";
  $res = db_query($sql,$link);
  $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroup WHERE g_id=\"$gid\"";
  $res = db_query($sql,$link);
  ad_message(MSG_g_deleted,MSG_g_list,"admin/index.php?m=group&a=g_view");
}

function g_setlevel() {
  global $link;
  $sql = "SELECT g_id,g_title FROM ".$GLOBALS['DBprefix']."UGroup";
  $groupselect = build_select($sql,getvar("g"));
  if (!$groupselect) error(MSG_e_g_nogroups);
  $levelselect = build_level_select();
  $sql = "SELECT f_id,f_title FROM ".$GLOBALS['DBprefix']."Forum";
  $res = db_query($sql,$link);
  ad_g_forum_start($groupselect,$levelselect);
  while ($fdata=db_fetch_array($res)) {
    ad_g_forum_entry($fdata,$levelselect);
  }
  ad_g_forum_end();
}

function g_change_level() {
  check_post();
  $link = $GLOBALS['link'];
  $mode = getvar("mode");
  $gid = getvar("g");
  if ($mode==1 || $mode==2) {
    $sql = "SELECT uid FROM ".$GLOBALS['DBprefix']."UGroupMember WHERE gid=\"$gid\"";
    $res = db_query($sql,$link);
    $uids = array();
    while ($uid=db_fetch_row($res)) {
      if ($sqldata) $sqldata.=" OR ";
      $sqldata.="uid=\"".$uid[0]."\"";
      if ($sqldata2) $sqldata2.=" OR ";
      $sqldata2.="u_id=\"".$uid[0]."\"";
      array_push($uids,$uid[0]);
    }
    db_free_result($res);
  }
  if ($mode==0 || $mode==2) {
    $ulevel = getvar("ulevel");
    if ($ulevel) {
      $sql = "UPDATE ".$GLOBALS['DBprefix']."UGroup SET g_setlevel=\"$ulevel\" WHERE g_id=\"$gid\"";
      $res = db_query($sql,$link);
    }
    $forums = $_POST['forum'];
    foreach ($forums as $curforum=>$curvalue) {
      if ($curvalue) {
        $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupAccess WHERE gid=\"$gid\" AND fid=\"".addslashes($curforum)."\"";
        $res = db_query($sql,$link);
        $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UGroupAccess SET gid=\"$gid\", fid=\"".addslashes($curforum)."\", ga_level=\"".addslashes($curvalue)."\"";
        $res = db_query($sql,$link);
      }
      elseif ($curvalue=="common") {
        $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UGroupAccess WHERE gid=\"$gid\" AND fid=\"$fid\"";
        $res = db_query($sql,$link);
      }
    }
  }
  if ($mode==1 || $mode==2) {
    $ulevel = getvar("ulevel");
    if ($ulevel && $sqldata2) {
      $sql = "UPDATE ".$GLOBALS['DBprefix']."User SET u__level=\"$ulevel\" WHERE ($sqldata2) AND u__level<\"$ulevel\"";
      $res = db_query($sql,$link);
    }
    $forums = $_POST['forum'];
    foreach ($forums as $curforum=>$curvalue) {
      if ($curvalue) {
        if ($sqldata) {
          $sql = "DELETE FROM ".$GLOBALS['DBprefix']."UserAccess WHERE fid=\"".addslashes($curforum)."\" AND ($sqldata)";
          $res = db_query($sql,$link);
        }  
        if ($curvalue!="common") {
          $sqldata3="";
          foreach ($uids as $curuid) {
            if ($sqldata3) $sqldata3.=", ";
            $sqldata3.="($curuid,\"".addslashes($curforum)."\",\"".addslashes($curvalue)."\")";
          }
          if ($sqldata3) {
            $sql = "INSERT INTO ".$GLOBALS['DBprefix']."UserAccess (uid,fid,ua_level) VALUES $sqldata3";
            $res = db_query($sql,$link);
          }
        }
      }
    }
  }
  ad_message(MSG_g_setlevelset,MSG_g_list,"admin/index.php?m=group&a=g_view");
}