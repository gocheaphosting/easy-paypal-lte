<?php

require_once 'DbHelper.php';
require_once 'lib/PhpFastCache.php';
$cache = new PhpFastCache();
require_once 'lib/Logger.php';
$log = new Logger();

// Suppress errors on AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  error_reporting(E_ERROR | E_PARSE);
// CORS headers
  header("access-control-allow-origin: *", true);
  header("access-control-allow-methods: GET, POST, PUT, DELETE, OPTIONS", true);
}

if (!class_exists("EZ")) {

  require_once 'EZPro.php';

  class EZ extends EZPro {

    static function getOptions() {
      if (!empty(self::$options)) {
        return self::$options;
      }
      global $db;
      if ($db->tableExists('options_meta')) {
        self::$options = $db->getMetaData('options_meta');
      }
      require 'admin/options-default.php';
      foreach ($paypal as $name => $o) {
        if (empty(self::$options[$name])) {
          self::$options[$name] = $o['value'];
        }
      }
      return self::$options;
    }

    static function proc_storage_location($location) { //  diagnostics about location
      $msg = "Storage location <code>$location</code> is good and valid.";
      $location = realpath("..") . "/" . $location;
      if (!is_dir($location)) {
        $msg = "Please create the folder <code>$location</code> on your server.<br /><br /><code>mkdir $location</code>";
      }
      if (!is_writable($location)) {
        $msg = "Please make <code>$location</code> writable on your server.<br /><br /><code>chmod 777 $location</code>";
      }
      return "data-value='$msg' class='btn-sm btn-success reveal'";
    }

    static function getSaleRow($table, $ID) {
      // ID is the transaction id in the normal case.
      // if it is not a transaction ID, it could be that the user is
      // trying to retrive using his email id.
      global $db;
      if ($table == "sales") {
        $emailClause = "' or customer_email = '" . $db->escape(strtolower($ID));
        $orderBy = "' ORDER BY purchase_date desc LIMIT 1";
      }
      else if ($table == "sale_details") {
        $emailClause = "' or payer_email = '" . $db->escape(strtolower($ID));
        $orderBy = "' ORDER BY payment_date desc LIMIT 1";
      }
      else {
        $emailClause = "";
        $orderBy = "";
      }
      $table = $db->prefix($table);
      $sql = "SELECT * FROM $table WHERE txn_id = '" . $db->escape(strtoupper($ID)) .
              $emailClause .
              $orderBy;
      $result = $db->query($sql);
      $row = $result->fetch_assoc();
      return $row;
    }

    static function getTemplate($name, $filter) {
      global $db;
      $template = '';
      $templateData = $db->getMetaData('templates', $filter);
      if (empty($templateData[$name])) { // less specific template
        unset($filter['product_grouping']);
        $templateData = $db->getMetaData('templates', $filter);
      }
      if (empty($templateData[$name])) { // default template
        $filter = array('name' => $name,
            'active' => 1,
            'category_id' => 0,
            'product_grouping' => '');
        $templateData = $db->getMetaData('templates', $filter);
      }
      if (!empty($templateData[$name])) {
        $template = $templateData[$name];
      }
      return $template;
    }

    static function getProduct($id, $activeOnly = false) {
      global $db;
      $filter = array('id' => $id);
      if ($activeOnly) {
        $filter['active'] = 1;
      }
      $product = $db->getData('products', '*', $filter);
      if (!empty($product)) {
        $product = $product[0];
      }
      if (empty($product)) { // Try product_code
        $filter = array('product_code' => $id);
        $product = $db->getData('products', '*', $filter);
        if (!empty($product)) {
          $product = $product[0];
        }
      }
      if (empty($product)) { // Try meta key
        $prodMeta = $db->getData("product_meta", "*", array('name' => 'key', 'value' => $id));
        if (empty($prodMeta) && strlen($id) > 5) { // Meta key, fuzzy match
          $prodMeta = $db->getData("product_meta", "*", "name = 'key' AND value LIKE '%$id%'");
        }
        if (count($prodMeta) == 1) {
          $prodMeta = $prodMeta[0];
          $filter = array('id' => $prodMeta['product_id']);
          $product = $db->getData('products', '*', $filter);
          $product = $product[0];
        }
      }
      if (empty($product)) { // no product found
        return false;
      }
      $prodMeta = $db->getMetaData("product_meta", array('product_id' => $product['id']));
      $product = array_merge($prodMeta, $product);
      return $product;
    }

    static function getSaleHistory($email) {
      if (method_exists('EZPro', 'getSaleHistory')) {
        return EZPro::getSaleHistory($email);
      }
      global $db;
      if (empty($email)) {
        return array('error' => "Please enter your <strong>PayPal email</strong> and try again.");
      }
      if (EZ::validate_email($email) !== true) {
        return array('error' => "Your email (<code>$email</code>) doesn't look right. Please try again.");
      }

      $sales = $db->getData("sales", "*", array("customer_email" => $email,
          "purchase_mode" => "Live"));
      if (empty($sales)) {
        return array('error' => "Sorry, no previous sales found! Please verify that you entered the correct PayPal email address.");
      }
      foreach ($sales as $k => $s) {
        $product = EZ::getProduct($s['product_id']);
        $txnID = $s['txn_id'];
        if (EZ::mkDateInt($s['expire_date']) > time()) {
          $sales[$k]['action'] = "<a href='return.php?dl=$txnID' class='btn-sm btn-info' title='Download {$product['product_name']} V$prodVersion. Link expires at {$s['expire_date']}' data-toggle='tooltip'>Download</a>";
        }
        else {
          $sales[$k]['action'] = "<a href='#' class='btn-sm btn-danger center' title='Your download link has expired at {$s['expire_date']}.' data-toggle='tooltip'>Link Expired</a>";
        }
      }

      $toShow = self::dbDataProject($sales, explode(",", "purchase_date,product_name,action"));
      $pageHtml = "<p>Your previous sales are shown below. If your link is still live, you can download it.</p>";
      $pageHtml .= self::renderDbTable($toShow);
      return array('pageHtml' => $pageHtml);
    }

    static function renderDbTable($rows) {
      $ret = self::renderDbTableHeader($rows);
      foreach ($rows as $r) {
        $ret .= self::renderDbTableRow($r);
      }
      $ret .= self::renderDbTableFooter($rows);
      return $ret;
    }

    static function renderDbTableHeader($rows) {
      $ret = '';
      if (empty($rows) || !is_array($rows)) {
        return $ret;
      }
      $row = $rows[0];
      if (empty($row) || !is_array($row)) {
        return $ret;
      }
      $ret .= '<table class="table table-striped table-bordered responsive data-table"><thead><tr>';
      foreach ($row as $k => $v) {
        $ret .= sprintf("<th>%s</th>", ucwords(str_replace('_', ' ', $k)));
      }
      $ret .= sprintf("</tr></thead><tbody>");
      return $ret;
    }

    static function renderDbTableRow($row) {
      $ret = sprintf("<tr>\n");
      foreach ($row as $v) {
        $ret .= sprintf("<td>%s</td>\n", $v);
      }
      $ret .= sprintf("</tr>\n");
      return $ret;
    }

    static function renderDbTableFooter($rows) {
      $ret = sprintf("</tbody></table>");
      return $ret;
    }

    static function dbDataProject($allSales, $cols) {
      $ret = array();
      if (empty($allSales)) {
        return $ret;
      }
      $colKeys = array_fill_keys(array_keys(array_flip($cols)), "");
      foreach ($allSales as $sale) {
        $ret[] = array_merge($colKeys, array_intersect_key($sale, $colKeys));
      }
      return $ret;
    }

    // AJAX CRUD implementation. Create.
    static function create($table) { // creates a new DB record
      if (!EZ::isLoggedIn()) {
        http_response_code(400);
        die("Please login before modifying $table!");
      }
      global $db;
      if (!$db->tableExists($table) && $table != 'subscribe_meta') {
        http_response_code(400);
        die("Wrong table name: $table!");
      }
      $row = $_REQUEST;
      if (!empty($row['pk'])) {
        http_response_code(400);
        die("Primary key supplied for new record");
      }
      unset($row['id']);
      if (empty($row)) {
        http_response_code(400);
        die("Empty data");
      }
      switch ($table) {
        case 'products':
          if (isset($row['recurring']) && trim($row['recurring']) == 'Active') {
            $row['recurring'] = 1;
          }
          else {
            $row['recurring'] = 0;
          }
          $row['category_id'] = self::getCatId($row['category_id']);
          break;
        case 'categories':
          if ($row['name'] == 'Empty' || empty($row['name'])) {
            http_response_code(400);
            die("Empty name!");
          }
          break;
        case 'product_meta':
          break;
        default:
          http_response_code(400);
          die("Unknown table accessed: $table");
      }
      if (isset($row['active']) && trim($row['active']) == 'Active') {
        $row['active'] = 1;
      }
      else {
        $row['active'] = 0;
      }
      $lastInsertId = $db->getInsertId();
      if (!$db->putRowData($table, $row)) {
        http_response_code(400);
        die("Database Insert Error in $table!");
      }
      $newInserId = $db->getInsertId();
      if ($lastInsertId == $newInserId) {
        http_response_code(400);
        die("Database Insert Error in $table, duplicate unique key!");
      }
      http_response_code(200);
      return $newInserId;
    }

    // AJAX CRUD implementation. Update.
    static function update($table, $meta = false) { // updates an existing DB record
      if (!EZ::isLoggedIn()) {
        http_response_code(400);
        die("Please login before modifying $table!");
      }
      global $db;
      if (!$db->tableExists($table) && $table != 'subscribe_meta') {
        http_response_code(400);
        die("Wrong table name: $table!");
      }
      $row = array();
      extract($_POST, EXTR_PREFIX_ALL, 'posted');
      if (empty($posted_pk)) {
        http_response_code(400);
        die("Empty primary key");
      }
      if (empty($posted_name)) {
        http_response_code(400);
        die("Empty name ($posted_name) in data");
      }
      if (!isset($posted_value)) { // Checkbox, unchecked
        $posted_value = 0;
      }
      if (is_array($posted_value)) { // Checkbox (from checklist), checked
        $posted_value = 1;
      }
      if (!empty($posted_validator)) { // a server-side validator is specified
        $fun = "validate_$posted_validator";
        if (method_exists('EZ', $fun)) {
          $valid = self::$fun($posted_value);
        }
        else {
          http_response_code(400);
          die("Unknown validator ($posted_validator) specified");
        }
        if ($valid !== true) {
          http_response_code(400);
          die("$valid");
        }
      }
      if ($meta) {
        $status = EZ::updateMetaData($table, $posted_pk, $posted_name, $posted_value);
      }
      else {
        $row['id'] = $posted_pk;
        $row[$posted_name] = $posted_value;
        $status = $db->putRowData($table, $row);
      }
      if (!$status) {
        http_response_code(400);
        die("Database Insert Error in $table!");
      }
      http_response_code(200);
      exit();
    }

  }

}

EZ::$slug = 'easy-paypal';
EZ::$wpslug = 'easy-paypal-lte';
EZ::$class = "EzPayPal6";
EZ::$name = "EZ PayPala";
EZ::$isInWP = isset($_REQUEST['wp']);
EZ::$isUpdating = isset($_REQUEST['update']);
EZ::$isPro = file_exists('options-advanced.php');

// construct DB object after defining EZ
$db = new DbHelper();
$GLOBALS['db'] = $db; // needed for ezSupport module

require_once 'admin/lang.php';

EZ::$options = EZ::getOptions(); // to prime the static variable and the cache
if (!empty(EZ::$options['salt'])) {
  EZ::$salt = EZ::$options['salt'];
}
if (!empty(EZ::$options['cache_timeout'])) {
  EZ::$cacheTimeout = EZ::$options['cache_timeout'];
}

// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code')) {

  function http_response_code($newcode = NULL) {
    static $code = 200;
    if ($newcode !== NULL) {
      if (!headers_sent()) {
        header('X-PHP-Response-Code: ' . $newcode, true, $newcode);
        $code = $newcode;
      }
    }
    return $code;
  }

}
