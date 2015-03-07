<?php

global $wpdb;
if (!defined('DB_HOST')) {
  $wpConfigFile = '../../../wp-config.php';
  if (!@file_exists($wpConfigFile)) {
    $wpConfigFile = '../../../../wp-config.php';
  }
  if (!@file_exists($wpConfigFile)) {
    die("Cannot locate the config file $wpConfigFile");
  }
  $wpConfig = file_get_contents($wpConfigFile);
  $lines = explode("\n", $wpConfig);
  $dbDefines = '';
  foreach ($lines as $l) {
    if (strpos($l, 'define') !== false && strpos($l, 'DB_') !== false) {
      $dbDefines .= $l . "\n";
    }
    if (strpos($l, 'table_prefix') !== false) {
      $dbDefines .= $l . "\n";
    }
  }
  eval($dbDefines);
}
if (!empty($wpdb)) {
  $table_prefix = $wpdb->prefix;
}

$dbHost = DB_HOST;
$dbName = DB_NAME;
if (empty($table_prefix)) {
  $table_prefix = 'wp_';
}
$dbPrefix = $table_prefix . "ezpp_";
$dbUsr = DB_USER;
$dbPwd = DB_PASSWORD;
$dbEmail = "";
