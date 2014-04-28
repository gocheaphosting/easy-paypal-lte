<?php

if (class_exists("DbHelper")) {
  return;
}

class DbHelper {

  var $valid;

  function __construct() {
    $pwd = dirname(__FILE__);

    $this->valid = false;
    $forceStandAlone = false; // set it to true to use standalone DB details within plugin
    $cfgFile = "$pwd/ezppCfg.php";
    if (!$forceStandAlone && function_exists('plugins_url')) {
      $cfgFile = "$pwd/ezppCfg-WP.php";
    }
    if (file_exists($cfgFile) && filesize($cfgFile) > 10) {
      include ($cfgFile);
      $this->host = $dbHost;
      $this->database = $dbName;
      $this->dbPrefix = $dbPrefix;
      $this->user = $dbUsr;
      $this->password = $dbPwd;
      if (!empty($dbEmail)) {
        $this->mailTo = $dbEmail;
      }
      else {
        $this->mailTo = '';
      }
      $this->ms = new mysqli($this->host, $this->user,
              $this->password, $this->database);
      if ($this->ms->connect_errno) {
        $err = "Failed to connect to MySQL: ("
                . $this->ms->connect_errno . ") "
                . $this->ms->connect_error;
        DbHelper::sdie($err);
      }
    }
    else {
      $err = "Please configure DB first."
              . " [Config file $cfgFile not found or is too small]";
      DbHelper::sdie($err);
    }
  }

  function DbHelper() {
    if (version_compare(PHP_VERSION, "5.0.0", "<")) {
      $this->__construct();
      register_shutdown_function(array($this, "__destruct"));
    }
  }

  function __destruct() {
    $this->ms->close();
  }

  function mailTo() {
    return $this->mailTo;
  }

  function error() {
    return "Error: " . $this->ms->error;
  }

  static function sdie($str, $dieOnError = true) {
    if ($dieOnError) {
      echo "<pre>";
      debug_print_backtrace();
      echo "</pre>";
      die($str);
    }
    else {
      return $str;
    }
  }

  function edie($str, $dieOnError = true) {
    if ($dieOnError) {
      echo "<pre>";
      debug_print_backtrace();
      echo "</pre>";
      die($str . "<br />" . $this->error());
    }
    else {
      return $str . "<br />" . $this->error();
    }
  }

  function link($dieOnError = true, $createDB = false) {
    if ($createDB) {
      $sql = "CREATE DATABASE IF NOT EXISTS `" . $this->database . "`";
      if (!$this->ms->query($sql)) {
        return $this->edie("Database cannot be created.\nPlease grant yourself all privileges.", $dieOnError);
      }
    }
    $this->valid = true;
  }

  function doOrDie(&$html) {
    $err = @$this->link(false);
    if (!empty($err)) {
      $html->ezDie($err);
    }
  }

  function query($sql) {
    $this->link();
    $result = $this->ms->query($sql) or $this->edie("SQL Query fails: <pre>$sql</pre>");
    if (!empty($GLOBALS['ezDebug']) && $GLOBALS['ezDebug']) {
      echo "<!-- $sql -->";
    }
    return $result;
  }

  function prefix($table) {
    $prefix = substr($table, 0, strlen($this->dbPrefix));
    if ($prefix == $this->dbPrefix) {
      return $table;
    }
    else {
      return $this->dbPrefix . $table;
    }
  }

  function getColNames($table) {
    $table = $this->prefix($table);
    $sql = "select * from $table limit 1";
    $result = $this->query($sql);
    $finfo = $result->fetch_fields();
    $colNames = array();
    foreach ($finfo as $meta) {
      $colNames[] = $meta->name;
    }
    return $colNames;
  }

  function mapFunc($array, $func) {
    $newarray = array();
    foreach ($array as $key => $val) {
      $newarray[$key] = $this->$func($val);
    }
    return $newarray;
  }

  function escape($s) {
    $this->link();
    if (get_magic_quotes_gpc()) {
      $s = stripslashes($s);
    }
    $s = $this->ms->real_escape_string($s);
    return $s;
  }

  function hasInnoDB() {
    $this->link();
    $res = $this->ms->query("SHOW ENGINES");
    $ret = false;
    while ($r = $res->fetch_assoc()) {
      if ($r['Engine'] == 'InnoDB' && $r['Support'] == 'YES') {
        $ret = true;
        break;
      }
    }
    return $ret;
  }

  function getTableCreate($table) {
    $ret = "";
    if ($this->tableExists($table)) {
      $sql = "SHOW CREATE TABLE $table";
      $ret = $this->ms->fetch_row($this->ms->query($sql));
      if (empty($ret)) {
        $ret = "SQL Error: SHOW CREATE TABLE $table fails";
      }
      else {
        $ret = $ret[1];
      }
    }
    return $ret;
  }

  function getTableNames($matchPrefix = false, $constrainedLast = false) {
    $ret = array();
    $this->link();
    $database = $this->database;
    if ($matchPrefix) {
      $where = "LIKE '{$this->dbPrefix}%'";
      $keySpec = " ({$this->dbPrefix}%)";
    }
    else {
      $where = '';
      $keySpec = '';
    }
    $res = $this->ms->query("SHOW TABLES IN $database $where");
    $key = "Tables_in_$database$keySpec";
    if (empty($res)) {
      return $ret;
    }
    while ($r = $res->fetch_assoc()) {
      if ($matchPrefix) {
        $pos = strpos($r[$key], $this->dbPrefix);
        $matched = $pos !== false;
      }
      else {
        $matched = true;
      }
      if ($matched) {
        $ret[] = $r[$key];
      }
    }
    if ($constrainedLast) {
      $free = array();
      $constrained = array();
      foreach ($ret as $t) {
        $fks = $this->getFKs($t);
        if (count($fks) > 0) {
          $constrained[] = $t;
        }
        else {
          $free[] = $t;
        }
      }
      $ret = array_merge($free, $constrained);
    }
    return $ret;
  }

  function tableExists($table, $noPrefix = false) {
    $this->link();
    if (!$noPrefix) {
      $table = $this->prefix($table);
    }
    $allTables = $this->getTableNames();
    $ret = in_array($table, $allTables);
    return $ret;
  }

  function columnExists($table, $column) {
    $this->link();
    $table = $this->prefix($table);
    if (!$this->tableExists($table)) {
      return false;
    }
    $database = $this->database;
    $res = $this->ms->query("SHOW COLUMNS FROM $table IN $database");
    $key = "Field";
    $ret = false;
    while ($r = $res->fetch_assoc()) {
      if ($r[$key] == $column) {
        $ret = true;
        break;
      }
    }
    return $ret;
  }

  function rowExists($table, $column, $value) {
    $this->link();
    $table = $this->prefix($table);
    if (!$this->tableExists($table)) {
      return false;
    }
    if (!$this->columnExists($table, $column)) {
      return false;
    }
    $res = $this->ms->query("
        SELECT COUNT(*) AS count
        FROM $table
        WHERE $column = '$value'
        ");
    $res->data_seek(0);
    $row = $res->fetch_array();
    return $row[0] == 1;
  }

  function getFKs($table) {
    $ret = array();
    $this->link();
    $table = $this->prefix($table);
    $database = $this->database;
    $res = $this->ms->query("SHOW CREATE TABLE $database.$table");
    $createSql = '';
    if (empty($res)) {
      return $ret;
    }
    while ($r = $this->ms->fetch_row($res)) {
      $createSql .= $r[1];
    }
    $regExp = '#,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
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
        ); */
      $ret[] = $match[1];
    }
    return $ret;
  }

  function fkExists($table, $fk) {
    $table = $this->prefix($table);
    $allFKs = $this->getFKs($table);
    return in_array($fk, $allFKs);
  }

  function dropFK($table, $fk) {
    $table = $this->prefix($table);
    if ($this->fkExists($table, $fk)) {
      $sql = "ALTER TABLE $table DROP FOREIGN KEY $fk ;";
      $this->query($sql);
    }
  }

  function getIndices($table) {
    $ret = array();
    $this->link();
    $table = $this->prefix($table);
    $database = $this->database;
    $sql = "SHOW INDEX FROM $table IN $database";
    $res = $this->ms->query($sql);
    if (empty($res)) {
      return $ret;
    }
    while ($r = $res->fetch_assoc()) {
      $ret[] = $r['Key_name'];
    }
    return $ret;
  }

  function idxExists($table, $idx) {
    $table = $this->prefix($table);
    $allIndices = $this->getIndices($table);
    return in_array($idx, $allIndices);
  }

  function getSaleRow($table, $ID) {
    // ID is the transaction id in the normal case.
    // if it is not a transaction ID, it could be that the user is
    // trying to retrive using his email id.
    if ($table == "sales") {
      $emailClause = "' or customer_email = '" . $this->escape(strtolower($ID));
      $orderBy = "' ORDER BY purchase_date desc LIMIT 1";
    }
    else if ($table == "sale_details") {
      $emailClause = "' or payer_email = '" . $this->escape(strtolower($ID));
      $orderBy = "' ORDER BY payment_date desc LIMIT 1";
    }
    else {
      $emailClause = "";
      $orderBy = "";
    }
    $table = $this->prefix($table);
    $sql = "SELECT * FROM $table WHERE txn_id = '" . $this->escape(strtoupper($ID)) .
            $emailClause .
            $orderBy;
    $result = $this->query($sql);
    $row = $result->fetch_assoc();
    return $row;
  }

  function getColData($table, $what) {
    $ret = array();
    $rows = $this->getData($table, $what);
    if (empty($rows)) {
      return $ret;
    }
    foreach ($rows as $r) {
      if (!empty($r[$what])) {
        $ret[] = $r[$what];
      }
    }
    return $ret;
  }

  function getRowData($table, $when = 1) { // returns only the latest row
    $ret = array();
    $rows = $this->getData($table, '*', $when);
    if (empty($rows)) {
      return $ret;
    }
    if (!empty($rows[0])) {
      $ret = $rows[0];
    }
    return $ret;
  }

  function putRowData($table, $row, $execute = true) {
    $table = $this->prefix($table);
    $colNames = $this->getColNames($table);
    $row = array_intersect_key($row, array_flip($colNames));
    $escaped = $this->mapFunc($row, 'escape');
    $setClause = "";
    $count = count($row);
    foreach ($row as $k => $v) {
      $count--;
      if ($k == 'created') {
        $setClause .= "  $k = now()";
      }
      else {
        $setClause .= "  $k = '{$escaped[$k]}'";
      }
      if ($count) {
        $setClause .= ",\n";
      }
    }
    $sql = sprintf("INSERT IGNORE INTO `%s`\nSET\n%s", $table, $setClause);
    $sql .= sprintf("\nON DUPLICATE KEY UPDATE\n%s", $setClause);
    if ($execute) {
      $this->query($sql);
    }
    return $sql;
  }

  function updateRowData($table, $row) {
    $this->putRowData($table, $row, true);
  }

  function mkMetaTableName($table) {
    if (substr($table, -5) == "_meta") {
      return $this->prefix($table);
    }
    if (substr($table, -1) == "s") {
      return $this->prefix(substr($table, 0, strlen($tabke) - 1) . "_meta");
    }
  }

  function getMetaData($table, $when = 1, $mkName = false) {
    if ($mkName) {
      $table = $this->mkMetaTableName($table);
    }
    if (is_array($when)) {
      $colNames = $this->getColNames($table);
      unset($when['id'], $when['created']);
      $when = array_intersect_key($when, array_flip($colNames));
    }
    $rows = $this->getData($table, 'name,value', $when);
    $ret = array();
    if (is_array($rows)) {
      foreach ($rows as $v) {
        $ret[$v['name']] = $v['value'];
      }
    }
    return $ret;
  }

  function putMetaData($table, $data, $mkName = false) {
    if ($mkName) {
      $table = $this->mkMetaTableName($table);
    }
    if (is_array($data)) {
      $colNames = $this->getColNames($table);
      $colData = array_intersect_key($data, array_flip($colNames));
      $colKeys = array_keys($colData);
      if (in_array("name", $colKeys) && in_array("value", $colKeys)) { // full single row data
        $this->putRowData($table, $colData);
      }
      else { // multi-row data
        $metaData = array_diff($data, $colData);
        foreach ($metaData as $k => $v) {
          $colData["name"] = $k;
          $colData["value"] = $v;
          $this->putRowData($table, $colData);
        }
      }
    }
  }

  function getData($table, $what = "*", $when = 1, $order = 'created') {
    $row = array();
    $table = $this->prefix($table);
    if (is_array($when)) {
      $where = " WHERE 1 ";
      foreach ($when as $k => $v) {
        $where .= " AND " . $k . " = '" . $this->escape($v) . "'";
      }
    }
    // else $where = "WHERE " . $this->escape($when) ;
    else {
      $where = "WHERE " . $when;
    }
    if (is_array($what)) {
      $what = implode(", ", $what);
    }
    $orderBy = '';
    if ($this->columnExists($table, $order)) {
      $orderBy = "ORDER BY $order desc";
    }
    $sql = "SELECT DISTINCT $what FROM $table $where $orderBy";
    $result = $this->query($sql);
    while ($r = $result->fetch_assoc()) {
      $row[] = $r;
    }
    return $row;
  }

}
