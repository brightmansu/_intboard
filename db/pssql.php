<?  /*

PostgreSQL database driver for Intellect Board 2
(c) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

global $lastlink;

function &db_connect($host,$user,$password,$database) {
  $lastlink = &pg_connect("host=$host user=$user password=$password dbname=".$database) or global_error(pg_result_error());
  pg_query($lastlink,"SET client_encoding='win1251'");
  pg_query($lastlink,"SELECT set_curcfg('default_russian')");
  return $lastlink;
}

function &db_pconnect($host,$user,$password,$database) {
  $lastlink = &pg_pconnect("host=$host user=$user password=$password dbname=".$database) or global_error(pg_result_error());
  pg_query($lastlink,"SET client_encoding='win1251'");
  pg_query($lastlink,"SELECT set_curcfg('default_russian')");
  return $lastlink;
}

function db_affected_rows(&$res) {
  return pg_affected_rows($res);
}

function db_close(&$link) {
  return pg_close($link);
}

function &db_data_seek(&$res,$offset) {
  return pg_result_seek($res,$offset);
}

function &db_fetch_array(&$res,$restype=PGSQL_ASSOC) {
  return pg_fetch_array($res,NULL,$restype);
}

function &db_fetch_assoc(&$res) {
  return pg_fetch_assoc($res);
}

function &db_fetch_row(&$res) {
  return pg_fetch_row($res);
}

function db_free_result(&$res) {
  return pg_free_result($res);
}

function db_insert_id(&$res) {
  $sql = "SELECT LASTVAL()";
  $res=pg_query($GLOBALS['link'],$sql);
  list($val)=pg_fetch_row($res);
  return $val;
}

function db_num_rows(&$res) {
  return pg_num_rows($res);
}

function db_num_fields(&$res) {
  return pg_num_fields($res);
}

function db_field_name(&$res,$index) {
  return pg_field_name($res,$index);
}

function &db_query($query,$link,$debug=0) {
  $GLOBALS['query_count']++;
  if (preg_match("/^\s*?INSERT\s+?INTO\s+?(\w+?)\s+?SET\s+?(.*)/is",$query,$match)) {
    preg_match_all("/([\w\d]+)\s*=\s*(\".*?(?<!\\\)\")/is",$match[2],$fields1);
    $tmp1=preg_replace("/([\w\d]+)\s*=\s*(\".*?(?<!\\\)\")/is","",$query);
  if (preg_match("/^\s*?INSERT\s+?INTO\s+?(\w+?)\s+?SET\s+?(.*)/is",$tmp1,$match1)) {
    preg_match_all("/([\w\d]+)\s*=\s*(\d+)\s*,/is",$match1[2]." ",$fields2); }
    $fnames=join(",",array_merge($fields1[1],$fields2[1]));
    $fdata=join(",",array_merge($fields1[2],$fields2[2]));
  $query="INSERT INTO ".$match[1]." (".$fnames.") VALUES (".$fdata.")";
  }
  $query=preg_replace("/(?<!\\\)\"/is","'",$query);
  if (preg_match("/^\s*?LOCK\s+?TABLE/is",$query)) return;
  if (preg_match("/^\s*?UNLOCK\s+?TABLE/is",$query)) return;
  $query=preg_replace("/LIMIT\s*?(\d+?),\s*?(\d+?)\s*?\$/","LIMIT $2 OFFSET $1",$query);
  if ($debug) echo "<br>Query: $query <br>";
  $time1 = microtime();
  $tmpres = pg_query($link,$query);
  if ($tmpres==false) global_error($query."<br>".pg_last_error());
  $time2 = microtime();
  $qtime = db_query_time($time1,$time2);
  if ($qtime>5) {
    $fh=fopen($GLOBALS['opt_dir']."/temp/pssql.res","a");
    flock($fh,LOCK_EX);
    fputs($fh,"QUERY: ".$query."\nAction: ".$GLOBALS['action'].", Module: ".$GLOBALS['module']."\nExec time: $qtime\n\n");
    fclose($fh);
  }
  if ($GLOBALS['common']) $GLOBALS['query_time']+=$qtime;
  return $tmpres;
}

function db_test() {
  global $link;
  $sql = "CREATE TABLE IbXpTeSt (test1 INT NOT NULL, test2 CHAR(8))";
  $res = db_query($sql,$link);
  $sql = "ALTER TABLE IbXpTeSt ADD PRIMARY KEY (test1)";
  $res = db_query($sql,$link);
  $sql = "INSERT INTO IbXpTeSt SET test1=20, test2=\"JustTest\"";
  $res = db_query($sql,$link);
  $sql = "UPDATE IbXpTeSt SET test1=30 WHERE test1=20";
  $res = db_query($sql,$link);
  $sql = "SELECT * FROM IbXpTeSt";
  $res = db_query($sql,$link);
  db_free_result($res);
  $sql = "DROP TABLE IbXpTeSt";
  $res = db_query($sql,$link);
}

function db_explain($sql) {
  global $link;
  $pos=strpos(strtoupper($sql),"SELECT");
  if ($pos!==false) {
    $sql = substr($sql,$pos);
    $sql2 = "EXPLAIN $sql";
    $res2 = pg_query($sql2,$link);
    echo "<table width=\"100%\" border=1><tr>";
    for ($i=0; $i<pg_num_fields($res2); $i++) echo "<td><b>".pg_field_name($res2,$i)."</b>";
    echo "";
    while ($row=pg_fetch_row($res2)) {
      echo "<tr>";
      foreach ($row as $column) echo "<td>$column ";
      echo "";
    }
    echo "</table><br>";
    pg_free_result($res2);
  }
}

function db_backup($filename,$mode="f") {
  global $link;
  $open=$mode."open";
  $write=$mode."write";
  $close=$mode."close";
  $fh=call_user_func($open,$filename,"wb");
  $output.= "-- Dump of database ".$GLOBALS['DBname']." from forum \"".$GLOBALS['opt_title']."\"\n";
  $output.= "-- Time of dump: ".date("l, d F Y  G:i:s")."\n";
  $output.= "-- \n\n\n";

  $ddl="SELECT show_ddl_db()";
  $query=db_query($ddl,$link);
  while ($row=pg_fetch_row($query)) {
        $output .=  $row[0];
  }
  $output.="\n";
  call_user_func($write,$fh,$output);

  $tbl = db_table();
  $tablequery = db_query($tbl,$link);
  if (!$tablequery) error(MSG_e_dump_tables);
  while ($table=pg_fetch_row($tablequery)) {
    $output .= "\n\n-- Table ".$table[0]." data:\n";
    $query = pg_query("SELECT * FROM $table[0]");
    $count = pg_num_fields($query);
    while ($row=pg_fetch_row($query)) {
      unset($output);
      $output .= "INSERT INTO $table[0] VALUES (";
      for ($i=0; $i<$count; $i++) {
        if (isset($row[$i])) {
          if ($blobs[$table[0]][$i]==1) {
            $output .= "0x";
            $len=strlen($row[$i]);
            for ($j=0; $j<$len; $j++) $output.=dechex(ord($row[$i][$j]));
          }
          else {
            $row[$i]=str_replace("\n","\\n",pg_escape_string($row[$i]));
            $output .= "'".$row[$i]."'";
          }
        } // WARNING!!!
        else $output .= "NULL";
        if ($i<($count-1)) { $output .= ","; }
      }
      $output .= ");\n";
      call_user_func($write,$fh,$output);
    }
  }
  call_user_func($close,$fh);
}

function db_table() {
  return "SELECT pt.relname AS Name ".
"FROM pg_catalog.pg_class pt ".
"     LEFT JOIN pg_catalog.pg_roles r ON r.oid = pt.relowner ".
"     LEFT JOIN pg_catalog.pg_namespace pn ON pn.oid = pt.relnamespace ".
" WHERE pt.relkind IN ('r','') ".
"      AND pt.relname NOT LIKE ('pg_ts_%') ".
"      AND pn.nspname NOT IN ('pg_catalog', 'pg_toast') ".
"      AND pt.relname LIKE ('".$GLOBALS['DBprefix']."%') ".
"      AND pg_catalog.pg_table_is_visible(pt.oid) ";
}

function db_query_time($start,$stop) {
    list($startusec,$startsec) = explode(" ",$start);
    list($stopusec,$stopsec) = explode(" ",$stop);
    return ($stopsec-$startsec)+($stopusec-$startusec);
}

function db_slashes($text) {
  return addslashes($text);
}

function db_table_status() {
  return "SELECT pt.relname AS Name, pg_catalog.pg_total_relation_size(pt.oid) AS Data_length ".
"FROM pg_catalog.pg_class pt ".
"     LEFT JOIN pg_catalog.pg_roles r ON r.oid = pt.relowner ".
"     LEFT JOIN pg_catalog.pg_namespace pn ON pn.oid = pt.relnamespace ".
" WHERE pt.relkind IN ('r','') ".
"      AND pt.relname NOT LIKE ('pg_ts_%') ".
"      AND pn.nspname NOT IN ('pg_catalog', 'pg_toast') ".
"      AND pg_catalog.pg_table_is_visible(pt.oid) ";
}

function db_match($tab,$mode,$text,$col1,$col2="") {
  $text1=trim($text);
  $text2=preg_replace("/\s*(&amp;)+\s*/is","&",$text1); //&amp; превращаем обрано в &
  $text2=preg_replace("/\s*(&)+\s*/is","&",$text1);
  $text1=preg_replace("/[&|]{2,}/is","|",$text1);   //задвоенные логические операторы превращаем в ИЛИ
  $text1=preg_replace("/(\s*([&|])*\s+([&|])*\s*)+/is","|",$text1);   //пробелы превращаем в ИЛИ
  return "rank(".$tab."fulltext_idx,to_tsquery('$text1')) AS rel";
}

function db_match2($tab,$mode,$text,$col1,$col2="") {
  $text2=trim($text);
  $text2=preg_replace("/\s*(&amp;)+\s*/is","&",$text2);
  $text2=preg_replace("/\s*(&)+\s*/is","&",$text2);
  $text2=preg_replace("/[&|]{2,}/is","|",$text2);
  $text2=preg_replace("/(\s*([&|])*\s+([&|])*\s*)+/is","|",$text2);
  return $tab."fulltext_idx @@ to_tsquery('$text2') AND rank(".$tab."fulltext_idx,to_tsquery('$text2'))";
}

function db_exist_check($host,$user,$database,$password) {
  return 0;
}

function db_optimize() {
  global $link;
  $sql = "VACUUM FULL ANALYZE";
  $res = db_query($sql,$link);
}

?>