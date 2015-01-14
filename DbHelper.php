<?php

if (!defined('MYSQLI_ASYNC')) {
  define('MYSQLI_ASYNC', MYSQLI_STORE_RESULT);
}

if (!class_exists("DbHelper")) {

  class DbHelper {

    var $valid, $host, $database, $dbPrefix, $user, $password, $mailTo;
    static $stackTrace = false;
    static $cfgIsValid = false;
    var $ms;

    function __construct() {
      $pwd = dirname(__FILE__);
      $this->valid = false;
      // Check if the config file exists. If not, set up the parent app first
      if (EZ::$isInstallingWP || EZ::$isInWP || EZ::isLoggedInWP()) {
        $cfgFile = "$pwd/dbCfg-WP.php";
      }
      else {
        $cfgFile = "$pwd/dbCfg.php";
      }
      if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['dbSetup'])) {
        if (!session_id()) {
          session_start();
        }
        $_SESSION['posted'] = $_POST;
        self::mkCfg();
      }
      if (file_exists($cfgFile) && filesize($cfgFile) > 10) {
        include ($cfgFile);
      }
      else {
        if (file_exists('dbSetup.php')) {
          header('location: dbSetup.php');
        }
        else if (file_exists('admin/dbSetup.php')) {
          header('location: admin/dbSetup.php');
        }
        die("Application not set up yet!");
      }
      $configured = !empty($dbHost) && !empty($dbName) && !empty($dbUsr) && !empty($dbPwd);
      if (!$configured) {
        header('location: dbSetup.php?error=1');
        exit();
      }
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
      $this->ms = @new mysqli($this->host, $this->user, $this->password, $this->database);
      if ($this->ms->connect_errno) {
        $ms = @new mysqli($this->host, $this->user, $this->password);
        if (!$ms->connect_errno) {
          $err = "Failed to connect to MySQL: ({$this->ms->connect_errno})\n{$this->ms->connect_error}\nCould not create the database either: ({$ms->connect_errno})\n{$ms->connect_error}";
          self::sdie($err);
        }
        else {
          $sql = "CREATE DATABASE IF NOT EXISTS $this->database";
          $status = $ms->query($sql);
          $ms->close;
          if (!$status) {
            $err = "Failed to connect to MySQL: ({$this->ms->connect_errno})\n{$this->ms->connect_error}\nCould not create the database after re-connecting.";
            self::sdie($err);
          }
          else {
            $this->ms = @new mysqli($this->host, $this->user, $this->password, $this->database);
            if ($this->ms->connect_errno) {
              $err = "Failed to connect to MySQL: ({$this->ms->connect_errno})\n{$this->ms->connect_error}\nCreated the database {$this->database} but could not connect to it.";
              self::sdie($err);
            }
          }
        }
        $this->ms->set_charset('utf8');
      }
    }

    function DbHelper() {
      if (version_compare(PHP_VERSION, "5.0.0", "<")) {
        $this->__construct();
        register_shutdown_function(array($this, "__destruct"));
      }
    }

    function redo($dbHost, $dbUsr, $dbPwd, $dbName, $dbPrefix) {
      $configured = !empty($dbHost) && !empty($dbName) && !empty($dbUsr) && !empty($dbPwd);
      if ($configured) {
        $this->host = $dbHost;
        $this->user = $dbUsr;
        $this->password = $dbPwd;
        $this->database = $dbName;
        $this->dbPrefix = $dbPrefix;
        $this->ms = @new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->ms->connect_errno) {
          $err = "Failed to reconnect to MySQL: (" . $this->ms->connect_errno . ") "
                  . $this->ms->connect_error;
          self::sdie($err);
        }
        $this->ms->set_charset('utf8');
        return true;
      }
      else {
        return false;
      }
    }

    function mailTo() {
      return $this->mailTo;
    }

    function error() {
      return "Error: " . $this->ms->error;
    }

    static function sdie($str, $dieOnError = true) {
      if ($dieOnError) {
        if (self::$stackTrace) {
          echo "<pre>";
          debug_print_backtrace();
          echo "</pre>";
        }
        throw new Exception($str);
      }
      else {
        return $str;
      }
    }

    function edie($str, $dieOnError = true) {
      $str .= "<br />" . $this->error();
      self::sdie($str, $dieOnError);
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

    function query($sql, $resultMode = MYSQLI_STORE_RESULT) {
      $this->link();
      $result = $this->ms->query($sql, $resultMode);
      if ($result === false) {
        throw new Exception("SQL Query fails: <pre>$sql</pre>");
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
      $colTypes = $this->getColTypes($table);
      $colNames = array_keys($colTypes);
      return $colNames;
    }

    function getColTypes0($table) { // doesn't work on empty tables
      $table = $this->prefix($table);
      $sql = "select * from $table limit 1";
      $result = $this->query($sql);
      $finfo = $result->fetch_fields();
      $colTypes = array();
      foreach ($finfo as $meta) {
        $colTypes[$meta->name] = $meta->type;
      }
      return $colTypes;
    }

    function getColTypes($table) {
      $table = $this->prefix($table);
      $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$this->database' AND TABLE_NAME = '$table'";
      $rows = $this->getQuery($sql);
      $colTypes = array();
      foreach ($rows as $r) {
        $colTypes[$r['COLUMN_NAME']] = $r['DATA_TYPE'];
      }
      return $colTypes;
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
        $result = $this->ms->query($sql);
        $ret = $result->fetch_row();
        if (empty($ret)) {
          $ret = "SQL Error: SHOW CREATE TABLE $table fails";
        }
        else {
          $ret = $ret[1];
        }
      }
      return $ret;
    }

    function getTableNames($matchPrefix = false) {
      $tableList = array();
      $res = $this->query("SHOW TABLES");
      while ($cRow = mysqli_fetch_array($res)) {
        if ($matchPrefix && strpos($cRow[0], $this->dbPrefix) !== 0) {
          continue;
        }
        $tableList[] = $cRow[0];
      }
      return $tableList;
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
      $result = $this->ms->query("SHOW CREATE TABLE $database.$table");
      $createSql = '';
      if (empty($result)) {
        return $ret;
      }
      while ($r = $result->fetch_row()) {
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
        if (isset($r[$what])) {
          $ret[] = $r[$what];
        }
      }
      return $ret;
    }

    function getColData2($table, $key, $what, $when = 1) {
      $ret = array();
      $rows = $this->getData($table, array($key, $what), $when);
      if (empty($rows)) {
        return $ret;
      }
      foreach ($rows as $r) {
        if (isset($r[$what])) {
          $ret[$r[$key]] = $r[$what];
        }
      }
      return $ret;
    }

    function getColData2Sum($table, $key, $what, $when = 1) {
      if (empty($table)) {
        trigger_error("Empty table.", E_USER_ERROR);
      }
      $table = $this->prefix($table);
      $sql = "SELECT DISTINCT $key, sum($what) FROM $table where $when GROUP BY $key";
      $rows = $this->getQuery($sql);
      $ret = array();
      foreach ($rows as $r) {
        if (isset($r[$key])) {
          $ret[$r[$key]] = $r["sum($what)"];
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

    function putRowData($table, $row, $execute = true, $resultMode = MYSQLI_STORE_RESULT) {
      if (empty($table) || empty($row)) {
        trigger_error("Empty table or row to update.", E_USER_ERROR);
      }
      $table = $this->prefix($table);
      $colTypes = $this->getColTypes($table);
      $row = array_intersect_key($row, $colTypes);
      $escaped = $this->mapFunc($row, 'escape');
      $setClause = "";
      $count = count($row);
      foreach ($row as $k => $v) {
        $count--;
        if ($k == 'created') {
          $setClause .= "  $k = now()";
        }
        else {
          if ($colTypes[$k] == MYSQLI_TYPE_LONG) {
            $setClause .= "  $k = $v";
          }
          else {
            $setClause .= "  $k = '{$escaped[$k]}'";
          }
        }
        if ($count) {
          $setClause .= ",\n";
        }
      }
      $sql = sprintf("INSERT IGNORE INTO `%s`\nSET\n%s", $table, $setClause);
      $sql .= sprintf("\nON DUPLICATE KEY UPDATE\n%s", $setClause);
      if ($execute) {
        return $this->query($sql, $resultMode);
      }
      return $sql;
    }

    function putRowDataDelayed($table, $row) {
      $this->putRowData($table, $row, true, MYSQLI_ASYNC);
    }

    function updateRowData($table, $row) {
      $this->putRowData($table, $row, true);
    }

    function mkMetaTableName($table) {
      if (substr($table, -5) == "_meta") {
        return $this->prefix($table);
      }
      if (substr($table, -1) == "s") {
        return $this->prefix(substr($table, 0, strlen($table) - 1) . "_meta");
      }
    }

    function getMetaData($table, $when = 1, $mkName = false, $what = 'value') {
      if ($mkName) {
        $table = $this->mkMetaTableName($table);
      }
      if (is_array($when)) {
        $colTypes = $this->getColTypes($table);
        unset($when['id'], $when['created']);
        $when = array_intersect_key($when, $colTypes);
      }
      $rows = $this->getData($table, "`name`,`$what`", $when);
      $ret = array();
      if (is_array($rows)) {
        foreach ($rows as $v) {
          $ret[$v['name']] = $v[$what];
        }
      }
      return $ret;
    }

    function putMetaData($table, $data, $mkName = false, $what = 'value') {
      if ($mkName) {
        $table = $this->mkMetaTableName($table);
      }
      $ret = false;
      if (is_array($data)) {
        $colTypes = $this->getColTypes($table);
        $colData = array_intersect_key($data, $colTypes);
        $colKeys = array_keys($colData);
        if (in_array("name", $colKeys) && in_array($what, $colKeys)) { // full single row data
          $ret = $this->putRowData($table, $colData);
        }
        else { // multi-row data
          $metaData = array_diff($data, $colData); // TODO: is this a bug?
          $ret = true;
          foreach ($metaData as $k => $v) {
            $colData["name"] = $k;
            $colData[$what] = $v;
            $ret = $ret && $this->putRowData($table, $colData);
          }
        }
      }
      return $ret;
    }

    function putData($table, $data) { // multi row data insert
      $status = true;
      foreach ($data as $row) {
        $status = $status && $this->putRowData($table, $row);
      }
      return $status;
    }

    function incrementCount($table, $row, $counter = 'count', $execute = true) {
      $table = $this->prefix($table);
      $colTypes = $this->getColTypes($table);
      $row = array_intersect_key($row, $colTypes);
      $escaped = $this->mapFunc($row, 'escape');
      $setClause = "";
      $count = count($row);
      foreach ($row as $k => $v) {
        $count--;
        if ($k == 'created') {
          $setClause .= "  $k = now()";
        }
        else {
          if ($colTypes[$k] == MYSQLI_TYPE_LONG) {
            $setClause .= "  $k = $v";
          }
          else {
            $setClause .= "  $k = '{$escaped[$k]}'";
          }
        }
        if ($count) {
          $setClause .= ",\n";
        }
      }
      $sql = sprintf("INSERT IGNORE INTO `%s`\nSET\n%s", $table, $setClause);
      $sql .= sprintf("\nON DUPLICATE KEY UPDATE\n$counter=$counter+1");
      if ($execute) {
        return $this->query($sql);
      }
      return $sql;
    }

    function getCount($table, $when = 1) {
      $rows = $this->getData($table, '*', $when);
      return count($rows);
    }

    function getData($table, $what = "*", $when = 1, $order = 'created', $asc = 'desc') {
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
        $orderBy = "ORDER BY $order $asc";
      }
      $sql = "SELECT DISTINCT $what FROM $table $where $orderBy";
      $rows = $this->getQuery($sql);
      return $rows;
    }

    function getQuery($sql) {
      $rows = array();
      $result = $this->query($sql);
      while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
      }
      return $rows;
    }

    function getInsertId() {
      return $this->ms->insert_id;
    }

    function importSQL($sqlFile, $prefix = "", $gzip = false) {
      if (!is_readable($sqlFile)) {
        return false;
      }
      $prefix = $this->dbPrefix . $prefix;
      $sql = file_get_contents($sqlFile);
      if ($gzip) {
        $sql = gzdecode($sql);
      }
      $sql = str_replace(array("{prefix}", "%TABLE_PREFIX%"), $prefix, $sql);
      if ($this->ms->multi_query($sql)) {
        do {
          if ($result = $this->ms->store_result()) {
            $result->free();
          }
        } while ($this->ms->more_results() && $this->ms->next_result());
      }
      else {
        throw new Exception("Importing <code>$sqlFile</code> fails!");
      }
    }

    static function cfgIsValid($cfg = array()) {
      if (self::$cfgIsValid) { // cache it within a process
        return true;
      }
      if (!empty($cfg)) {
        extract($cfg);
      }
      else {
        include('dbCfg.php');
      }
      if (empty($dbHost) || empty($dbName) || empty($dbUsr) || empty($dbPwd)) {
        return false;
      }
      $link = mysqli_connect($dbHost, $dbUsr, $dbPwd, $dbName);
      if (!$link) { // connect error
        return false;
      }
      else { // verified
        mysqli_close($link);
        self::$cfgIsValid = true;
        return true;
      }
    }

    static function mkCfg() {
      if (self::cfgIsValid()) { // Config already valid. Update requires server access
        header('location: dbSetup.php?error=4');
        exit;
      }
      if (self::cfgIsValid($_POST)) {
        extract($_POST);
        $cfg = "<?php\n\$dbHost = '$dbHost';\n\$dbName = '$dbName';\n\$dbPrefix = '$dbPrefix';\n\$dbUsr = '$dbUsr';\n\$dbPwd = '$dbPwd';\n\$dbEmail = '$dbEmail';\n";
        $cfgFile = "../dbCfg.php";
        if (@file_put_contents($cfgFile, $cfg)) { // success
          header('location: dbSetup.php?error=4');
          exit;
        }
        else { // file write error
          $cfg = urlencode($cfg);
          header("location: dbSetup.php?error=2&cfg=$cfg");
          exit;
        }
      }
      else {
        header('location: dbSetup.php?error=1');
        exit;
      }
    }

    function saveTables($tables, $force = false, $prefix = 'bak_') {
      $needMigration = false;
      // If the administrator table exists, it is probably from a new installation.
      // Even if it is from somewhere else, it would be dangerous to migrate.
      if (!$this->tableExists('administrator')) {
        if (!is_array($tables)) {
          $tables = array($tables);
        }
        foreach ($tables as $table) {
          if ($this->tableExists($table)) {
            $oldName = $this->prefix($table);
            if ($this->tableExists($oldName)) {
              $newName = $prefix . $oldName;
              if ($force || !$this->tableExists($newName)) {
                $this->query("SET foreign_key_checks = 0");
                $sql = "DROP TABLE IF EXISTS `$newName`";
                $this->query($sql);
                $this->query("SET foreign_key_checks = 1");
                $sql = "RENAME TABLE `$oldName` TO `$newName`";
                $this->query($sql);
                $needMigration = true;
              }
            }
          }
        }
      }
      return $needMigration;
    }

  }

}