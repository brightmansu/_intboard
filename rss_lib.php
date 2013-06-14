<? /*

RSS import library script for Intellect Board 2

(c) 2007, XXXX Pro, United Open Project
Visit us online: http://intboard.ru
*/

function parse_rss($data,$time=0) {
  if (strpos($data,'<rss version="2.0"')===false && 
    strpos($data,'<rss version="0.91"')===false &&
    strpos($data,'<rss version="0.92"')===false &&
    strpos($data,'<rss version=\'2.0\'')===false && 
    strpos($data,'<rss version=\'0.91\'')===false &&
    strpos($data,'<rss version=\'0.92\'')===false) return array();
  else {
    if (!preg_match('|<?xml\s+version=[\'"]1.0[\'"]\s+encoding=["\']\w+-?1251[\'"]|s',$data)) {
      $data=utf_cp1251($data);
    }
    $result = array();
    $data=preg_replace('|<!\[CDATA\[(.*?)\]\]>|s','$1',$data);  
    preg_match_all('|<item>(.*?)</item>|s',$data,$items);
    for ($i=0, $count=count($items[1]); $i<$count; $i++) {
      $tmp=array();
      if (preg_match('|<pubDate>(.*?)</pubDate>|s',$items[1][$i],$match)) $tmp['date']=strtotime($match[1]);
      else $tmp['date']=$GLOBALS['curtime'];
      if (($time==0 || $tmp['date']>$time) && $time<=$GLOBALS['curtime']+24*60*60) {
        if (preg_match('|<title>(.*?)</title>|s',$items[1][$i],$match)) $tmp['title']=trim($match[1]);
        if (preg_match('|<link>(.*?)</link>|s',$items[1][$i],$match)) $tmp['link']=trim($match[1]);
        if (preg_match('|<description>(.*?)</description>|s',$items[1][$i],$match)) $tmp['descr']=trim($match[1]);
        $tmp['descr']=html_entity_decode($tmp['descr']);
        $tmp['descr'] = preg_replace("/(<script.*?<\/script>)/ise","\"<br><font color=red><b>HACK ATTEMPT:</b> \".htmlspecialchars(\"$1\").\"</font><br>\"",$tmp['descr']);        
        if (preg_match('|<dc:creator>(.*?)</dc:creator>|s',$items[1][$i],$match)) $tmp['author']=trim($match[1]);
        if (preg_match('|<author>(.*?)</author>|s',$items[1][$i],$match)) $tmp['author']=trim($match[1]);
        if (preg_match('|<comments>(.*?)</comments>|s',$items[1][$i],$match)) $tmp['comments']=$match[1];      
        if ($tmp['descr'] || $tmp['title']) $result[]=$tmp;
      }
    }
  }
  return $result;
}

function fetch_rss($url,$time=0) {
  if (extension_loaded('curl')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($time) {
      curl_setopt($ch, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
      curl_setopt($ch, CURLOPT_TIMEVALUE, $time);
    }
    $result=curl_exec($ch);
    curl_close($ch);  
  }
  else $result=join('',file($url));
  return $result;
}

function get_rss($fid) {
  global $link;
  if (!$GLOBALS['opt_rss_refresh']) $GLOBALS['opt_rss_refresh']=60;
  $filename=$GLOBALS['opt_dir'].'/config/rss_all.txt';
  $result=array();
  if (!$GLOBALS['opt_rss_refresh']) $GLOBALS['opt_rss_refresh']=15;
  $sql = 'SELECT rss_id,rss_url,rss_lastentry '.
         ' FROM '.$GLOBALS['DBprefix'].'RSSImports '.
         ' WHERE rss_lastget<'.($GLOBALS['curtime']-$GLOBALS['opt_rss_refresh']*60).' '.
         ' AND rss_fid="'.intval($fid).'"';
  $res = db_query($sql,$link);
  while($data=db_fetch_array($res)) {
    $rss=fetch_rss($data['rss_url'],$data['rss_lastget']);
    $tmp_rss=parse_rss($rss,$data['rss_lastentry']);
    unset($rss);    
    for ($j=0, $count2=count($tmp_rss); $j<$count2; $j++) {
      $maxtime=max($maxtime,$tmp_rss[$j]['date']);
      $tmp_rss['premoderate']=$data['rss_premoderate'];
      $tmp_rss['source']=$data['rss_source'];
    }
    $result=array_merge($result,$tmp_rss);
    if ($data['rss_lastentry']) $maxtime=$data['rss_lastentry'];
    $sql = "UPDATE ".$GLOBALS['DBprefix'].'RSSImports '.
    'SET rss_lastentry='.$maxtime.', rss_lastget='.$GLOBALS['curtime'].' '.
    'WHERE rss_id='.$data['rss_id'];
    $res2 = db_query($sql,$link);
  }
  db_free_result($res);
  return $result;
}

function import_rss($uname,$uid,$fid) {
  global $link;
  $rss_items=get_rss($fid);
  $counter=0;
  for ($j=0, $count2=count($rss_items); $j<$count2; $j++) {
    if ($rss_items[$j]['source']) $rss_items[$j]['descr'].="\n\n".MSG_rss_source.": ".$rss_items[$j]['source'];
    if (strlen($rss_items[$j]['title'])>60) $rss_items[$j]['title']=substr($rss_items[$j]['title'],0,60).'...';
    $sql = "INSERT INTO ".$GLOBALS['DBprefix']."Topic (t_fid,t_title,t__pcount) ".
      'VALUES ('.$fid.',"'.db_slashes($rss_items[$j]['title']).'",1)';
    $res=db_query($sql,$link);
    $tid=db_insert_id($sql);
    if (!$rss_items[$j]['date']) $rss_items[$j]['date']=$GLOBALS['curtime'];
    if ($rss_items[$j]['title'] || $rss_items[$j]['descr']) {
      $sql = 'INSERT INTO '.$GLOBALS['DBprefix'].'Post '.
        '(p_tid,p_text,p__time,p__smiles,p__bcode,p__html,p_uname,p_uid,p__premoderate) '.
        'VALUES ('.$tid.', "'.db_slashes($rss_items[$j]['descr']).'", '.
        $rss_items[$j]['date'].','.$GLOBALS['inforum']['f_smiles'].','.
        $GLOBALS['inforum']['f_bcode'].',1,"'.db_slashes($uname).'",'.intval($uid).", ".intval($rss_items[$j]['premoderate']).")";
      $res=db_query($sql,$link);
      $pid=db_insert_id($res);
      if (!$rss_items[$j]['premoderate']) {
        topic_increment($fid,$tid,$pid);
      }
      $counter++;
    }
  }

  if ($counter) {
    $sql = "UPDATE ".$GLOBALS['DBprefix']."UserStat SET us_count=us_count+".$counter." WHERE uid=$uid AND  fid=".$GLOBALS['forum'];
    $res =&db_query($sql,$link);
    $GLOBALS['inforum']['f__tcount']=$GLOBALS['inforum']['f__tcount']+$counter;
  }
}

function list_rss_imports() {
  global $link;
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  $sql = 'SELECT * FROM '.$GLOBALS['DBprefix'].'RSSImports '.
         ' WHERE rss_fid="'.intval(getvar('fid')).'"';
  $res=db_query($sql,$link);
  list_rss_start_short();
  while ($data=db_fetch_array($res)) {
    list_rss_entry_short($data);
  }
  for ($i=0; $i<5; $i++)     if ($mode) list_rss_entry_full(false);
    else list_rss_entry_short(false);
  list_rss_end_short(intval(getvar('fid')));
}

function do_save_rss_imports() {
  if ($GLOBALS['inuserlevel']<$GLOBALS['inforum']['f_lmoderate']) error(MSG_e_mod_norights);
  global $link;
  $fid=intval(getvar('fid'));
  
  foreach ($_POST['rss'] as $id=>$currss) {
    if ($currss['delete'] || !$currss['rss_url']) {
      $sql = "DELETE FROM ".$GLOBALS['DBprefix']."RSSImports WHERE rss_id=".intval($id)." AND rss_fid=".$fid;
      $res=db_query($sql,$link);
    }
    else {
      if (!$currss['rss_premoderated']) $sqldata=', rss_premoderated=0';
      else $sqldata='';
      $sql = "UPDATE ".$GLOBALS['DBprefix']."RSSImports SET ".build_sql('rss_',$currss).$sqldata." WHERE rss_id=".intval($id)." AND rss_fid=".$fid;
      $res=db_query($sql,$link);
    }
  }
  foreach ($_POST['new_rss'] as $currss) {
    if ($currss['rss_url']) {
      $currss['rss_fid']=$fid;
      $sql = "INSERT INTO ".$GLOBALS['DBprefix']."RSSImports SET ".build_sql('rss_',$currss);
      $res=db_query($sql,$link);
    }
  }
  message(MSG_rss_saved,1);
}

function utf_cp1251($fcontents) {
    $out = $c1 = '';
    $byte2 = false;
    for ($c = 0;$c < strlen($fcontents);$c++) {
        $i = ord($fcontents[$c]);
        if ($i <= 127) {
            $out .= $fcontents[$c];
        }
        if ($byte2) {
            $new_c2 = ($c1 & 3) * 64 + ($i & 63);
            $new_c1 = ($c1 >> 2) & 5;
            $new_i = $new_c1 * 256 + $new_c2;
            if ($new_i == 1025) {
                $out_i = 168;
            } else {
                if ($new_i == 1105) {
                    $out_i = 184;
                } else {
                    $out_i = $new_i - 848;
                }
            }
            $out .= chr($out_i);
            $byte2 = false;
        }
        if (($i >> 5) == 6) {
            $c1 = $i;
            $byte2 = true;
        }
    }
    return $out;
}
function locations(&$locations) {
  array_push($locations,MSG_rss_list);
  return $locations;
}
