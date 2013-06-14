<?  /*

MySQL database driver for Intellect Board 2
(c) 2004, XXXX Pro, United Open Project
Visit us online: http://intboard.ru

*/

global $lastlink;

function &db_connect($host,$user,$password,$name) {
  if ($GLOBALS['DBcompress'])  $lastlink = &mysql_connect($host,$user,$password,false,MYSQL_CLIENT_COMPRESS) or global_error(mysql_errno().": ".mysql_error());
  else $lastlink = &mysql_connect($host,$user,$password) or global_error(mysql_errno().": ".mysql_error()) ;
  mysql_select_db($name,$lastlink) or global_error(mysql_errno().": ".mysql_error());;
//  Uncomment this line if errors with codepages occur
//  mysql_query("SET NAMES 'cp1251'",$lastlink);
  return $lastlink;
}

function &db_pconnect($host,$user,$password,$name) {
  if ($GLOBALS['DBcompress'])  $lastlink = &mysql_pconnect($host,$user,$password,false,MYSQL_CLIENT_COMPRESS) or global_error(mysql_errno().": ".mysql_error());
  else $lastlink = &mysql_pconnect($host,$user,$password) or global_error(mysql_errno().": ".mysql_error());
  mysql_select_db($name,$lastlink) or global_error(mysql_errno().": ".mysql_error());
//  Uncomment this line if errors with codepages occur
//  mysql_query("SET NAMES 'cp1251'",$lastlink);
  return $lastlink;
}

function db_affected_rows() {
  return mysql_affected_rows();
}

function db_close(&$link) {
  if($link) return mysql_close($link);
  else return 0;
}

function &db_data_seek(&$res,$offset) {
  return mysql_data_seek($res,$offset);
}

function &db_fetch_array(&$res,$restype=MYSQL_ASSOC) {
  return mysql_fetch_array($res,$restype);
}

function &db_fetch_assoc(&$res) {
  return mysql_fetch_assoc($res);
}

function &db_fetch_row(&$res) {
  return mysql_fetch_row($res);
}

function &db_fetch_field(&$res,$offset) {
  return mysql_fetch_field($res,$offset);
}

function &db_fetch_lengths(&$res) {
  return mysql_fetch_lengths($res);
}

function db_free_result(&$res) {
  return mysql_free_result($res);
}

function db_insert_id(&$res) {
  return mysql_insert_id();
}

function db_num_rows(&$res) {
  return mysql_num_rows($res);
}

function db_num_fields(&$res) {
  return mysql_num_fields($res);
}

function db_field_name(&$res,$index) {
  return mysql_field_name($res,$index);
}

function &db_query($query,$link,$debug=0) {
  if(!isset($GLOBALS['query_count'])) $GLOBALS['query_count'] = 0;
  $GLOBALS['query_count']++;
//  if (strpos(strtoupper(trim($query)),"SELECT")===0) {
//    $query=preg_replace("/^\s*SELECT\s+?.*FROM([^\"\']+?)LEFT\s+?JOIN/is","FROM ($1) LEFT JOIN",$query);
//  }
  if ($debug==1) {
    echo "<br>Query: $query<br>";
  }
  $time1 = microtime();
  $tmpres = &mysql_query($query,$link);
  if ($tmpres===false) {
    $errno=mysql_errno();
    ${DB_ERROR} = TRUE;
    if ($errno!=1016) trigger_error((mysql_error($link) ."\nquery: ". $query), E_USER_WARNING); 
    $sql2 = "REPAIR TABLE ".$GLOBALS['DBprefix']."Topic";
    $res=mysql_query($sql2,$link);
    $sql2 = "REPAIR TABLE ".$GLOBALS['DBprefix']."Post";
    $res=mysql_query($sql2,$link);
    $tmpres = &mysql_query($query,$link);
    if ($tmpres===false) trigger_error((mysql_error($link) ."\nquery: ". $query), E_USER_WARNING);
  }
  $time2 = &microtime();
  $qtime = db_query_time($time1,$time2);
  if ($qtime>5) {
    $fh=fopen($GLOBALS['opt_dir']."/temp/mysql.res","a");
    flock($fh,LOCK_EX);
    fputs($fh,"QUERY: ".$query."\nAction: ".$GLOBALS['action'].", Module: ".$GLOBALS['module'].", Time: ".date("r",time())."\nExec time: $qtime\n\n");
    fclose($fh);
  }
  if(!isset($GLOBALS['query_time'])) $GLOBALS['query_time'] = 0;
  if ($GLOBALS['common']) $GLOBALS['query_time']+=$qtime;
  return $tmpres;
}

function db_test() {
  global $link;
  $sql = "CREATE TABLE IbXpTeSt (test1 INT NOT NULL, test2 CHAR(9))";
  $res = db_query($sql,$link);
  $sql = "ALTER TABLE IbXpTeSt ADD PRIMARY KEY (test1)";
  $res = db_query($sql,$link);
  $sql = "INSERT INTO IbXpTeSt SET test1=20, test2=\"just test\"";
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
    $res2 = mysql_query($sql2,$link);
    echo "<table width=\"100%\" border=1><tr>";
    for ($i=0; $i<mysql_num_fields($res2); $i++) echo "<td><b>".mysql_field_name($res2,$i)."</b>";
    echo "";
    while ($row=mysql_fetch_row($res2)) {
      echo "<tr>";
      foreach ($row as $column) echo "<td>$column ";
      echo "";
    }
    echo "</table><br>";
    mysql_free_result($res2);
  }
}

function db_backup($filename,$mode="f") {
    global $link;

    $open=$mode."open";
    $write=$mode."write";
    $close=$mode."close";
    $fh=call_user_func($open,$filename,"wb");


    $output = "-- ---------------------------------------------\n";
    $output.= "-- \n";
    $output.= "-- Dump of database ".$GLOBALS['DBname']." from forum \"".$GLOBALS['opt_title']."\"\n";
    $output.= "-- Time of dump: ".date("l, d F Y  G:i:s")."\n";
    $output.= "-- \n";
    $output.= "-- ---------------------------------------------\n\n";

    $tablequery = mysql_list_tables($GLOBALS['DBname']);
    if (!$tablequery) { error(MSG_e_dump_tables); }

    while ($table=mysql_fetch_row($tablequery)) {
         $blobs[$table[0]]=array();
         $fieldsquery = mysql_query("SHOW FIELDS FROM $table[0];",$link);
         $output .="\n\n-- Table $table[0]\n\nDROP TABLE IF EXISTS $table[0];\n\n" ;
         $output .= "CREATE TABLE $table[0] (\n";
         $count=0;
         while ($row = mysql_fetch_array($fieldsquery)) {
           if ($count>0) $output.= ",\n";
           $output .=  " ".$row["Field"];
           $output .=  " ".$row["Type"];
           if (is_numeric($row["Default"])) $output .=  " DEFAULT ".$row["Default"];
           elseif ($row["Default"]!="") $output .=  " DEFAULT '".db_slashes($row["Default"])."'";
           if ($row["Null"]!="YES") { $output .=  " NOT NULL "; }
           if ($row["Extra"]) { $output .= " ".$row["Extra"]; }
           if (strpos(strtoupper($row["Type"]),"BLOB")!==false) $blobs[$table[0]][$count]=1;
           $count++;
         }
         $keys=array();
         $querykeys=mysql_query("SHOW KEYS FROM ".$table[0]);
         $num_keys=mysql_num_rows($querykeys);
         if ($num_rows>0) { $output.= ","; }
         $primary=0;
         for($i=0; $i<$num_keys; $i++){
           $row = mysql_fetch_array($querykeys);
           if (!$keys[$row["Key_name"]]) { $keys[$row["Key_name"]]=$row["Column_name"]; }
           else { $keys[$row["Key_name"]].=",".$row["Column_name"]; }
           if ($row['Non_unique']==0) $keytype[$row["Key_name"]]="UNIQUE ";
           if ($row['Index_type']=="FULLTEXT") $keytype[$row["Key_name"]]="FULLTEXT ";
         }
         foreach ($keys as $curkey=>$curcolumns) {
           if ($curkey=="PRIMARY") {
              $output .=  ",\n PRIMARY KEY ($curcolumns)";
           }
           else {
            $output .=  ",\n ".$keytype[$curkey]."INDEX $curkey ($curcolumns)";
           }
         }
         $output.="\n);";
    }
    $output.="\n";
    call_user_func($write,$fh,$output);

    $tablequery = mysql_list_tables($GLOBALS['DBname']);
    if (!$tablequery) error(MSG_e_dump_tables);

    while ($table=mysql_fetch_row($tablequery)) {
      $output .= "\n\n-- Table ".$table[0]." data:\n";
      $query = mysql_query("SELECT * FROM $table[0]");
      $count = mysql_num_fields($query);
      while ($row=mysql_fetch_row($query)) {
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
              $row[$i]=str_replace("\n","\\n",db_slashes($row[$i]));
              $output .= "\"".$row[$i]."\"";
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

function db_query_time($start,$stop) {
    list($startusec,$startsec) = explode(" ",$start);
    list($stopusec,$stopsec) = explode(" ",$stop);
    return ($stopsec-$startsec)+($stopusec-$startusec);
}

function db_slashes($string) {
  return mysql_real_escape_string($string);
}

function db_table_status() {
  return "SHOW TABLE STATUS";
}

function db_match($table,$mode,$text,$col1,$col2="") {
  if ($col2) return "MATCH($col1,$col2) AGAINST(\"$text\" $mode)";
  else return "MATCH($col1) AGAINST(\"$text\" $mode) AS rel";
}

function db_match2($table,$mode,$text,$col1,$col2="") {
  if ($col2) return "MATCH($col1,$col2) AGAINST(\"$text\" $mode)";
  else return "MATCH($col1) AGAINST(\"$text\" $mode)";
}


function db_exist_check($host,$user,$database,$password) {
  if ($GLOBALS['DBcompress'])  $link = &mysql_connect($host,$user,$password,false,MYSQL_CLIENT_COMPRESS) or global_error(mysql_errno().": ".mysql_error());
  else $link = &mysql_connect($host,$user,$password) or global_error(mysql_errno().": ".mysql_error()) ;
  $sql = "SELECT User, Db FROM mysql.db WHERE Db=\"$database\"";
  $res=mysql_query($sql,$link);
  if ($res===false) return 0;
  if (mysql_num_rows($res)==0) {
    $sql = "SELECT Create_priv FROM mysql.user WHERE User=\"$user\" AND Create_priv=\"Y\"";
    $res=mysql_query($sql,$link);
    if (!$res || mysql_num_rows($res)==0) return -1;
    else {
      $sql = "CREATE DATABASE $database";
      mysql_query($sql,$link);
      return 2;
    }
  }
  else return 1;
}

function db_optimize() {
  global $link;
  $sql = "SHOW TABLES";
  $res = db_query($sql,$link);
  $tables = array();
  while ($tabdata=db_fetch_row($res)) array_push($tables,$tabdata[0]);
  db_free_result($res);
  foreach ($tables as $curtable) {
    if (substr($curtable,0,strlen($GLOBALS['DBprefix'])==$GLOBALS['DBprefix'])) {
      $sql = "OPTIMIZE TABLE $curtable";
      $res = db_query($sql,$link);
    }
  }
}

function db_backup_cmd() {
  return 'mysqldump -h '.$GLOBALS['DBhost'].' -u '.$GLOBALS['DBusername'].' -p'.$GLOBALS['DBpassword'].' '.$GLOBALS['DBname'];
}
?>