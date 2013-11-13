<?php
if (class_exists("dbHelper")) return;
class dbHelper {
  var $valid ;
  function __construct($setupIfNeeded = true, $prefix = ''){
    $pwd = dirname(__FILE__) ;
    $this->valid = false ;
    // Check if the config file exists. If not, set up ezPayPal first
    $cfgFile = "$pwd/ezppCfg.php" ;
    if (file_exists($cfgFile) && filesize($cfgFile) > 10) {
      include ($cfgFile) ;
      $this->host = $dbHost ;
      $this->name = $dbName ;
      if (empty($prefix)) $this->dbPrefix = $dbPrefix ;
      else $this->dbPrefix = $prefix ;
      $this->usr = $dbUsr ;
      $this->pwd = $dbPwd ;
      if (!empty($dbEmail)) $this->mailTo = $dbEmail ;
      else $this->mailTo = '' ;
    }
    else if ($setupIfNeeded) {
      include_once('htmlHelper.php') ;
      $err = "Please configure ezPayPal first. [Config file not found or is too small]" ;
      htmlHelper::staticRedirect('setup.php', "err=$err") ;
    }
  }
  function dbHelper($setupIfNeeded = true, $prefix = ''){
    if(version_compare(PHP_VERSION,"5.0.0","<")){
      $this->__construct($setupIfNeeded. $prefix);
      register_shutdown_function(array($this,"__destruct"));
    }
  }
  function __destruct(){
    // mysql_close() ;
  }
  function mailTo() {
    return $this->mailTo ;
  }
  function error() {
    return "Error: [" . mysql_errno() . "] " . mysql_error();
  }
  function edie($str, $dieOnError=true) {
    if ($dieOnError) {
      echo "<pre>" ;
      debug_print_backtrace() ;
      echo "</pre>" ;
      die($str . "<br />" . $this->error()) ;
    }
    else return $str . "<br />" . $this->error() ;
  }
  function link($dieOnError=true, $createDB=false) {
    if (!@mysql_connect($this->host, $this->usr, $this->pwd))
      return $this->edie("Unable to connect to the DB server.\nPlease check your user name and password", $dieOnError);
    if ($createDB) {
      $sql = "CREATE DATABASE IF NOT EXISTS `" . $this->name . "`";
      if (!mysql_query($sql))
        return $this->edie ("Database cannot be created.\nPlease grant yourself all privileges.", $dieOnError);
    }
    if (!mysql_select_db($this->name))
      return $this->edie( "Unable to select database {$this->name}.", $dieOnError);
    $this->valid = true ;
  }
  function doOrDie(&$html) {
    $err = @$this->link(false) ;
    if (!empty($err)) $html->ezDie($err) ;
  }
  function query($sql) {
    $this->link() ;
    $result = mysql_query($sql)
      or $this->edie ("SQL Query fails: <pre>$sql</pre>");
    if (!empty($GLOBALS['ezDebug']) && $GLOBALS['ezDebug'])
      echo "<!-- $sql -->" ;
    mysql_close();
    return $result ;
  }
  function prefix($table) {
    $prefix = substr($table, 0, strlen($this->dbPrefix)) ;
    if ($prefix == $this->dbPrefix) return $table ;
    else return $this->dbPrefix . $table ;
  }
  function getColNames($table) {
    $table = $this->prefix($table) ;
    $sql = "select * from $table" ;
    $result = $this->query($sql) ;
    $colNames = array() ;
    for ($i = 0 ; $i < mysql_num_fields($result); $i++) {
      $meta =  mysql_fetch_field($result, $i);
      $colNames[] = $meta->name ;
    }
    return $colNames ;
  }
  function mapFunc($array, $func) {
    $newarray = array() ;
    foreach ($array as $key => $val) $newarray[$key] = $this->$func($val) ;
    return $newarray ;
  }
  function escape($s) {
    $this->link() ;
    if (get_magic_quotes_gpc()) {
      $s = stripslashes($s) ;
    }
    $s = mysql_real_escape_string($s) ;
    return $s ;
  }
  function hasInnoDB() {
    $this->link() ;
    $res = mysql_query("SHOW ENGINES");
    $ret = false ;
    while ($r = mysql_fetch_assoc($res)) {
      if ($r['Engine'] == 'InnoDB' && $r['Support'] == 'YES') {
        $ret = true ;
        break ;
      }
    }
    return $ret ;
  }
  function getTableCreate($table) {
    $ret = "" ;
    if ($this->tableExists($table)) {
      $sql = "SHOW CREATE TABLE $table" ;
      $ret = mysql_fetch_row(mysql_query($sql));
      if (empty($ret)) $ret = "SQL Error: SHOW CREATE TABLE $table fails" ;
      else $ret = $ret[1] ;
    }
    return $ret ;
  }
  function getTableNames($matchPrefix=false, $constrainedLast=false) {
    $ret = array() ;
    $this->link() ;
    $database = $this->name ;
    if ($matchPrefix) {
      $where = "LIKE '{$this->dbPrefix}%'" ;
      $keySpec = " ({$this->dbPrefix}%)" ;
    }
    else {
      $where = '' ;
      $keySpec = '' ;
    }
    $res = mysql_query("SHOW TABLES IN $database $where");
    $key = "Tables_in_$database$keySpec" ;
    if (empty($res)) return $ret ;
    while ($r = mysql_fetch_assoc($res)) {
      if ($matchPrefix) {
        $pos = strpos($r[$key], $this->dbPrefix) ;
        $matched = $pos !== false ;
      }
      else
        $matched = true ;
      if ($matched) $ret[] = $r[$key]  ;
    }
    if ($constrainedLast) {
      $free = array() ;
      $constrained = array() ;
      foreach ($ret as $t) {
        $fks = $this->getFKs($t) ;
        if (count($fks) > 0) $constrained[] = $t ;
        else $free[] = $t ;
      }
      $ret = array_merge($free, $constrained) ;
    }
    return $ret ;
  }
  function tableExists($table, $noPrefix=false) {
    $this->link() ;
    if (!$noPrefix) $table = $this->prefix($table) ;
    $allTables = $this->getTableNames() ;
    $ret = in_array($table, $allTables) ;
    return $ret ;
  }
  function columnExists($table, $column) {
    $this->link() ;
    $table = $this->prefix($table) ;
    if (!$this->tableExists($table)) return false ;
    $database = $this->name ;
    $res = mysql_query("SHOW COLUMNS FROM $table IN $database");
    $key = "Field" ;
    $ret = false ;
    while ($r = mysql_fetch_assoc($res)) {
      if ($r[$key] == $column) {
        $ret = true ;
        break ;
      }
    }
    return $ret ;
  }
  function rowExists($table, $column, $value) {
    $this->link() ;
    $table = $this->prefix($table) ;
    if (!$this->tableExists($table)) return false ;
    if (!$this->columnExists($table, $column)) return false ;
    $res = mysql_query("
        SELECT COUNT(*) AS count
        FROM $table
        WHERE $column = '$value'
        ");
    return mysql_result($res, 0) == 1;
  }
  function getFKs($table) {
    $ret = array() ;
    $this->link() ;
    $table = $this->prefix($table) ;
    $database = $this->name ;
    $res = mysql_query("SHOW CREATE TABLE $database.$table");
    $createSql = '' ;
    if (empty($res)) return $ret ;
    while ($r = mysql_fetch_row($res)) {
      $createSql .= $r[1]  ;
    }
    $regExp  = '#,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
      . 'REFERENCES (`[^`]*\.)?`([^`]*)` \(`([^`]*)`\)'
      . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
      . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?#';
    $matches = array();
    preg_match_all($regExp, $createSql, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
      /* // This may be useful later
      $ddl[$match[1]] = array(
        'FK_NAME'           => $match[1],
        'SCHEMA_NAME'       => $database,
        'TABLE_NAME'        => $table,
        'COLUMN_NAME'       => $match[2],
        'REF_SHEMA_NAME'    => isset($match[3]) ? $match[3] : $database,
        'REF_TABLE_NAME'    => $match[4],
        'REF_COLUMN_NAME'   => $match[5],
        'ON_DELETE'         => isset($match[6]) ? $match[7] : '',
        'ON_UPDATE'         => isset($match[8]) ? $match[9] : ''
        );*/
      $ret[] = $match[1] ;
    }
    return $ret ;
  }
  function fkExists($table, $fk) {
    $table = $this->prefix($table) ;
    $allFKs = $this->getFKs($table) ;
    return in_array($fk, $allFKs) ;
  }
  function dropFK($table, $fk) {
    $table = $this->prefix($table) ;
    if ($this->fkExists($table, $fk)) {
      $sql = "ALTER TABLE $table DROP FOREIGN KEY $fk ;";
      $this->query($sql);
    }
  }
  function getIndices($table) {
    $ret = array() ;
    $this->link() ;
    $table = $this->prefix($table) ;
    $database = $this->name ;
    $sql = "SHOW INDEX FROM $table IN $database" ;
    $res = mysql_query($sql) ;
    if (empty($res)) return $ret ;
    while ($r = mysql_fetch_assoc($res)) {
      $ret[] = $r['Key_name'] ;
    }
    return $ret ;
  }
  function idxExists($table, $idx) {
    $table = $this->prefix($table) ;
    $allIndices = $this->getIndices($table) ;
    return in_array($idx, $allIndices) ;
  }
  function getSaleRow($table, $ID) {
    // ID is the transaction id in the normal case.
    // if it is not a transaction ID, it could be that the user is
    // trying to retrive using his email id.
    if ($table == "sales") {
      $emailClause = "' or customer_email = '" . $this->escape(strtolower($ID)) ;
      $orderBy = "' ORDER BY purchase_date desc LIMIT 1" ;
    }
    else if ($table == "sale_details") {
      $emailClause = "' or payer_email = '" . $this->escape(strtolower($ID)) ;
      $orderBy = "' ORDER BY payment_date desc LIMIT 1" ;
    }
    else {
      $emailClause = "" ;
      $orderBy = "" ;
    }
    $table = $this->prefix($table) ;
    $sql = "SELECT * FROM $table WHERE txn_id = '" . $this->escape(strtoupper($ID)) .
      $emailClause .
      $orderBy ;
    $result = $this->query($sql) ;
    $row = mysql_fetch_assoc($result) ;
    return $row ;
  }
  function getColData($table, $what) {
    $ret = array() ;
    $rows = $this->getData($table, $what) ;
    if (empty($rows)) return $ret ;
    foreach ($rows as $r) if (!empty($r[$what])) $ret[] = $r[$what] ;
    return $ret ;
  }
  function getRowData($table, $when=1) { // returns only the latest row
    $ret = array() ;
    $rows = $this->getData($table, '*', $when) ;
    if (empty($rows)) return $ret ;
    if (!empty($rows[0])) $ret = $rows[0] ;
    return $ret ;
  }
  function putRowData($table, $row, $execute=true) {
    $table = $this->prefix($table) ;
    $colNames = $this->getColNames($table) ;
    $row = array_intersect_key($row, array_flip($colNames)) ;
    $escaped = $this->mapFunc($row, 'escape');
    $setClause = "" ;
    $count = count($row) ;
    foreach ($row as $k => $v) {
      $count-- ;
      if ($k == 'created') $setClause .= "  $k = now()" ;
      else $setClause .= "  $k = '{$escaped[$k]}'" ;
      if ($count) $setClause .= ",\n" ;
    }
    $sql = sprintf("INSERT IGNORE INTO `%s`\nSET\n%s", $table, $setClause);
    $sql .= sprintf("\nON DUPLICATE KEY UPDATE\n%s", $setClause) ;
    if ($execute) $this->query($sql);
    return $sql ;
  }
  function updateRowData($table, $row) {
    $this->putRowData($table, $row, true) ;
  }
  function getMetaData($table, $when=1) {
    if (is_array($when)) {
      $colNames = $this->getColNames($table) ;
      unset($when['id'],$when['created']) ;
      $when = array_intersect_key($when, array_flip($colNames)) ;
    }
    $rows = $this->getData($table, 'name,value', $when) ;
    $ret = array() ;
    if (is_array($rows)) foreach ($rows as $v) $ret[$v['name']] = $v['value'] ;
    return $ret ;
  }
  function putMetaData($table, $data) {
    if (is_array($data)) {
      $colNames = $this->getColNames($table) ;
      $colData = array_intersect_key($data, array_flip($colNames)) ;
      $colKeys = array_keys($colData) ;
      if (in_array("name", $colKeys) && in_array("value", $colKeys)) { // full single row data
        $this->putRowData($table, $colData) ;
      }
      else { // multi-row data
        $metaData = array_diff($data, $colData) ;
        foreach ($metaData as $k => $v) {
          $colData["name"] = $k ;
          $colData["value"] = $v ;
          $this->putRowData($table, $colData) ;
        }
      }
    }
  }
  function getData($table, $what = "*", $when=1, $order='created') {
    $row = array() ;
    $table = $this->prefix($table) ;
    if (is_array($when)) {
      $where = " WHERE 1 " ;
      foreach ($when as $k => $v) {
        $where .= " AND " . $k . " = '" . $this->escape($v) . "'";
      }
    }
    // else $where = "WHERE " . $this->escape($when) ;
    else $where = "WHERE " . $when ;
    if (is_array($what)) $what = implode(", ", $what) ;
    $orderBy = '' ;
    if ($this->columnExists($table, $order))
      $orderBy = "ORDER BY $order desc" ;
    $sql = "SELECT DISTINCT $what FROM $table $where $orderBy" ;
    $result = $this->query($sql) ;
    while ($r = mysql_fetch_assoc($result)) {
      $row[] = $r ;
    }
    return $row ;
  }

  // The script-source function is a modified version from this post:
  // http://stackoverflow.com/questions/147821/loading-sql-files-from-within-php

  // remove_comments will strip the sql comment lines out of an uploaded sql file
  // specifically for mssql and postgres type files in the install....
  function remove_comments(&$output) {
    $lines = explode("\n", $output);
    $output = "";
    $linecount = count($lines);
    $in_comment = false;
    for($i = 0; $i < $linecount; $i++) {
      if( preg_match("/^\/\*/", preg_quote($lines[$i])) ) {
        $in_comment = true;
      }
      if( !$in_comment ) {
        $output .= $lines[$i] . "\n";
      }
      if( preg_match("/\*\/$/", preg_quote($lines[$i])) ) {
        $in_comment = false;
      }
    }
    unset($lines);
    return $output;
  }
  // remove_remarks will strip the sql comment lines out of an uploaded sql file
  function remove_remarks($sql) {
    $lines = explode("\n", $sql);
    $sql = "";
    $linecount = count($lines);
    $output = "";
    for ($i = 0; $i < $linecount; $i++) {
      if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0)) {
        if (isset($lines[$i][0]) && $lines[$i][0] != "#") {
          $output .= $lines[$i] . "\n";
        }
        else {
          $output .= "\n";
        }
        $lines[$i] = "";
      }
    }
    return $output;
  }
  // split_sql_file will split an uploaded sql file into single sql statements.
  // Note: expects trim() to have already been run on $sql.
  function split_sql_file($sql, $delimiter) {
    $tokens = explode($delimiter, $sql);
    $sql = "";
    $output = array();
    $matches = array();
    // this is faster than calling count($oktens) every time thru the loop.
    $token_count = count($tokens);
    for ($i = 0; $i < $token_count; $i++) {
      // Don't wanna add an empty string as the last thing in the array.
      if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
        // This is the total number of single quotes in the token.
        $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
        // Counts single quotes that are preceded by an odd number of backslashes,
        // which means they're escaped quotes.
        $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
        $unescaped_quotes = $total_quotes - $escaped_quotes;
        // If the number of unescaped quotes is even, then the delimiter
        // did NOT occur inside a string literal.
        if (($unescaped_quotes % 2) == 0) {
          // It's a complete sql statement.
          $output[] = $tokens[$i];
          // save memory.
          $tokens[$i] = "";
        }
        else {
          // incomplete sql statement. keep adding tokens until we have a complete one.
          // $temp will hold what we have so far.
          $temp = $tokens[$i] . $delimiter;
          // save memory..
          $tokens[$i] = "";
          // Do we have a complete statement yet?
          $complete_stmt = false;
          for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++) {
            // This is the total number of single quotes in the token.
            $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
            // Counts single quotes that are preceded by an odd number of backslashes,
            // which means they're escaped quotes.
            $escaped_quotes =
              preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
            $unescaped_quotes = $total_quotes - $escaped_quotes;
            if (($unescaped_quotes % 2) == 1) {
              // odd number of unescaped quotes. In combination with the previous incomplete
              // statement(s), we now have a complete statement. (2 odds always make an even)
              $output[] = $temp . $tokens[$j];
              // save memory.
              $tokens[$j] = "";
              $temp = "";
              // exit the loop.
              $complete_stmt = true;
              // make sure the outer loop continues at the right point.
              $i = $j;
            }
            else {
              // even number of unescaped quotes. We still don't have a complete statement.
              // (1 odd and 1 even always make an odd)
              $temp .= $tokens[$j] . $delimiter;
              // save memory.
              $tokens[$j] = "";
            }
          } // for..
        } // else
      }
    }
    return $output;
  }

  function source($dbms_schema, $verbose=false) {
    $sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema)) ;
    if (!$sql_query) return "Problem sourcing <code>$dbms_schema</code>. Cannot open and read file." ;
    $sql_query = $this->remove_remarks($sql_query);
    $sql_query = $this->split_sql_file($sql_query, ';');

    $i=1;
    foreach($sql_query as $sql){
      if ($verbose) {
        echo $i++ ;
        echo "\n" ;
      }
      $this->query($sql) ;
    }
  }
}
?>