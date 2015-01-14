<?php

require_once('../EZ.php');

if (EZ::isLoggedInWP()) { // DB setup will be done in the plugin activation hook
  return;
}
else if (EZ::$isInWP) { // If in plugin mode, use WP login
  header("location: " . wp_login_url($_SERVER['PHP_SELF']));
  exit();
}

// DB is setup?
$tablesRequired = array('administrator', 'products', 'categories', 'sales', 'sale_details', 'product_meta', 'options_meta', 'templates');
foreach ($tablesRequired as $table) {
  if (!$db->tableExists($table)) {
    header('location: dbSetup.php?error=1');
    exit;
  }
}

// Admin is setup?
$table = 'administrator';
$row = $db->getData($table);
if (empty($row)) {
    header('location: adminSetup.php');
    exit;
}

// Logged in?
if (!EZ::isLoggedIn()) {
  header("Location: login.php?error=3");
  exit;
}
