<?php

if (!class_exists("EZCom")) {

  class EZCom {

    static $cacheTimeout = 1;
    static $options = array();
    static $salt = "";
    static $isInstallingWP = false;
    static $isInWP = false;
    static $isPro = false;
    static $isUpdating = false;
    static $slug, $wpslug;
    static $class;
    static $name;

    static function isInstalled() {
      global $db;
      $table = 'administrator';
      return $db->tableExists($table);
    }

    static function getCategories() {
      global $db;
      $rows = $db->getData('categories', array('id', 'name'), array('active' => 1));
      foreach ($rows as $r) {
        $categories[$r['id']] = $r['name'];
      }
      return $categories;
    }

    static function getCatId($name) {
      $categories = self::getCategories();
      $id = array_keys($categories, $name);
      return $id[0];
    }

    static function getCatName($id) {
      $categories = self::getCategories();
      $name = '';
      if (!empty($categories[$id])) {
        $name = $categories[$id];
      }
      return $name;
    }

    static function catNameIsActive($name) {
      $id = self::getCatId($name);
      return !empty($id);
    }

    static function catIdIsActive($id) {
      $name = self::getCatName($name);
      return !empty($name);
    }

    static function md5($password) {
      return md5($password . self::$salt);
    }

    static function authenticate() {
      global $db;
      if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (!empty($_POST['myusername']) && !empty($_POST['mypassword'])) {
          $myusername = $_POST['myusername'];
          $mypassword = $_POST['mypassword'];
          $mypassword = self::md5($mypassword);
          $result = $db->getDataEx('administrator', '*', array("username" => $myusername, "password" => $mypassword));
          $count = count($result);
          // If result matches $myusername and $mypassword, table row must be 1 row
          if ($count == "1") {
            $row = $result[0];
          }
          else {
            $row = 1;
          }
        }
        else {
          $row = 2;
        }
      }
      return $row;
    }

    static function login() {
      if (!session_id()) {
        session_start();
      }
      $row = self::authenticate();
      if (is_array($row)) {
        $_SESSION[self::$slug . '-admin'] = self::md5($row['username']);
        $_SESSION[self::$slug . '-password'] = self::md5($row['password']);
        session_write_close();
        if (!empty($_REQUEST['back'])) {
          $goBack = $_REQUEST['back'];
          header("location: $goBack");
        }
        else {
          header("location: index.php");
        }
      }
      else {
        $error = $row;
        header("location: login.php?error=$error");
        exit();
      }
    }

    static function logout() {
      session_start();
      session_unset();
      session_destroy();
      session_write_close();
      setcookie(session_name(), '', 0, '/');
      session_regenerate_id(true);
      header("Location: login.php?error=3");
      exit();
    }

    static function isActive() {
      if (strpos(__FILE__, 'mu-plugins') !== false) {
        return true;
      }
      if (class_exists(self::$class)) {
        return true;
      }
      if (!function_exists('is_plugin_active')) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
      }
      $plgSlug = basename(__DIR__) . "/" . self::$slug . ".php";
      if (is_plugin_active($plgSlug)) {
        return true;
      }
      if (is_plugin_active_for_network($plgSlug)) {
        return true;
      }
      return false;
    }

    static function isInWP() {
      self::$isInWP = false;
      if (isset($_REQUEST['wp'])) {
        self::$isInWP = true;
        return true;
      }
      if (function_exists('is_user_logged_in')) {
        self::$isInWP = true;
        return true;
      }
      foreach (array("../../..", "../../../..", "../../../../..") as $dir) {
        $wpHeader = "$dir/wp-blog-header.php";
        if (@file_exists($wpHeader)) {
          self::$isInWP = true;
          return true;
        }
      }
      return self::$isInWP;
    }

    static function isLoggedInWP() {
      if (!defined('WP_USE_THEMES')) {
        define('WP_USE_THEMES', false);
      }
      if (!defined('WP_INSTALLING')) {
        define('WP_INSTALLING', true);
      }
      global $wpdb;
      $isLoggedIn = false;
      // check from front-end, admin and ajax
      foreach (array("../../..", "../../../..", "../../../../..") as $dir) {
        $wpHeader = "$dir/wp-blog-header.php";
        if (@file_exists($wpHeader)) {
          require_once $wpHeader;
          break;
        }
      }
      if (function_exists('current_user_can')) {
        self::$isInWP = true;
        if (current_user_can('activate_plugins')) {
          $isLoggedIn = true;
        }
      }
      return $isLoggedIn;
    }

    static function isLoggedIn() {
      if (!session_id()) {
        session_start();
        session_write_close();
      }
      if (self::isLoggedInWP()) {
        return true;
      }
      else {
        if (self::$isInWP) {
          return false;
        }
      }
      if (empty($_SESSION[self::$slug . '-admin'])) {
        return false;
      }
      if (empty($_SESSION[self::$slug . '-password'])) {
        return false;
      }
      global $db;
      $result = $db->getData('administrator', '*');
      $row = $result[0];
      $admin = self::md5($row['username']);
      $password = self::md5($row['password']);
      $isLoggedin = $_SESSION[self::$slug . '-admin'] == $admin &&
              $_SESSION[self::$slug . '-password'] == $password;
      if (!$isLoggedin) {
        self::logout();
      }
      return $isLoggedin;
    }

    static function __($s) {
      if (!empty(self::$options[$s])) {
        return self::$options[$s];
      }
      else {
        return $s;
      }
    }

    static function mkDateString($intOrStr) {
      if (is_int($intOrStr)) {
        $dateStr = date('Y-m-d H:i:s', $intOrStr);
      }
      else {
        $dateStr = date('Y-m-d H:i:s', strtotime($intOrStr));
      }
      return $dateStr;
    }

    static function mkDateInt($intOrStr) {
      if (is_int($intOrStr)) {
        $dateInt = $intOrStr;
      }
      else {
        $dateInt = strtotime($intOrStr);
      }
      return $dateInt;
    }

    static function getBaseUrl() {
      if (isset($_SERVER['HTTPS']) and ( $_SERVER['HTTPS'] == "on")) {
        $http = "https://";
        $ssl = true;
      }
      else {
        $http = "http://";
        $ssl = false;
      }
      $port = $_SERVER['SERVER_PORT'];
      $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
      $url = $http . $_SERVER['SERVER_NAME'] . $port;
      return $url;
    }

    static function ezppURL() {
      if (function_exists('plugins_url')) {
        $ezppURL = plugins_url("", __FILE__) . "/";
        self::updateMetaData("options_meta", "ezppURL", "ezppURL", $ezppURL);
        return $ezppURL;
      }
      else if (!empty(self::$options['ezppURL'])) {
        return self::$options['ezppURL'];
      }
      else {
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT']);
        $ezppRoot = __DIR__;
        $self = str_replace($docRoot, '', $ezppRoot);
        $url = self::getBaseUrl() . $self . '/';
        return $url;
      }
    }

    static function handleImageTagsHtml($html) {
      $matches = array();
      $pattern = '/{img:([^ ]*?)}/';
      preg_match_all($pattern, $html, $matches);
      $tags = $matches[0];
      $images = $matches[1];
      $img = array();
      $imgSrc = "assets/";
      if (!empty($images) && is_array($images)) {
        foreach ($images as $i) {
          $img[] = "<img src='$imgSrc/$i' alt='$i' />";
        }
      }
      $html = str_replace($tags, $img, $html);

      return $html;
    }

    static function sendMail($subject, $message, $to) {
      $options = self::getOptions();
      $from = $options['support_email'];
      $headers = sprintf('From: "%s" <%s>' . "\r\n" .
              "Reply-To: %s\r\n" .
              "X-Mailer: %s\r\n" .
              "X-Sender-IP: %s\r\n" .
              "Bcc: %s\r\n", $options['support_name'], $from, $from, self::ezppURL(), $_SERVER['REMOTE_ADDR'], $from);
      $params = "-$from";
      $result = false;
      if (function_exists('wp_mail')) {
        $result = wp_mail($to, $subject, $message, $headers);
      }
      if (!$result) { // fall back to php mail
        $result = mail($to, $subject, $message, $headers, $params);
      }
      if (!$result) {
        throw new Exception(__("Error sending PHP email", self::$slug));
      }
      return true;
    }

    static function urlExists($url) {
      $c = curl_init();
      curl_setopt($c, CURLOPT_URL, $url);
      curl_setopt($c, CURLOPT_HEADER, 1); //get the header
      curl_setopt($c, CURLOPT_NOBODY, 1); //and *only* get the header
      curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); //get the response as a string from curl_exec(), rather than echoing it
      curl_setopt($c, CURLOPT_FRESH_CONNECT, 1); //don't use a cached version of the url
      if (!curl_exec($c)) {
        return false;
      }
      else {
        return true;
      }
    }

    static function validate_url($url) {
      $format = "Use the format http[s]://[www].site.com[/file[?p=v]]";
      if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $text = "$format";
        return $text;
      }
      $pattern = '#^(http(?:s)?\:\/\/[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*\.[a-zA-Z]{2,6}(?:\/?|(?:\/[\w\-]+)*)(?:\/?|\/\w+\.[a-zA-Z]{2,4}(?:\?[\w]+\=[\w\-]+)?)?(?:\&[\w]+\=[\w\-]+)*)$#';
      if (!preg_match($pattern, $url)) {
        $text = "$format";
        return $text;
      }
      if (!self::urlExists($url)) {
        $text = "URL not accessible";
        return $text;
      }
      return true;
    }

    static function validate_email($s) {
      if (!filter_var($s, FILTER_VALIDATE_EMAIL)) {
        return "Bad email address";
      }
      return true;
    }

    static function validate_notNull($s) {
      $s = trim($s);
      if (empty($s)) {
        return "Null value not allowed";
      }
      return true;
    }

    static function validate_number($s) {
      if (!is_numeric($s)) {
        return "Need a number here";
      }
      return true;
    }

    static function validate_alnum($s) {
      $aValid = array('_', '-');
      $s = str_replace($aValid, '', $s);
      if (!ctype_alnum($s)) {
        return "Please use only letters, numbers, - and _";
      }
      return true;
    }

    static function updateMetaData($table, $pk, $name, $value) {
      global $db;
      $row = array();
      switch ($table) {
        case 'options_meta':
          $row[$pk] = $value;
          $status = $db->putMetaData($table, $row);
          break;
        case 'subscribe_meta': // fake table name
          $table = 'product_meta';
          if (in_array($name, array('pt1', 'pt2', 'pt3'))) {
            $multiRow = array();
            $n = substr($name, -1);
            list($p, $t) = self::decodePT1($value, $n);
            $multiRow[] = array("name" => "p$n", "value" => $p, 'product_id' => $pk);
            $multiRow[] = array("name" => "t$n", "value" => $t, 'product_id' => $pk);
            $status = $db->putData($table, $multiRow);
          }
          else {
            $row['name'] = $name;
            $row['value'] = $value;
            $row['product_id'] = $pk;
            $status = $db->putMetaData($table, $row);
          }
          break;
        case 'product_meta': // Special because both name and value are editable
          $row['id'] = $pk;
          $row[$name] = $value;
          $status = $db->putRowData($table, $row);
          break;
        case 'templates':
          $row['name'] = $name;
          $row['value'] = $value;
          $row['category_id'] = $pk;
          $status = $db->putMetaData($table, $row);
          break;
        default:
          http_response_code(400);
          die("Unknown table accessed: $table");
      }
      return $status;
    }

    // AJAX CRUD implementation.
    // create() and update() are application specific.
    static function read() {
      // not implemented because $db->getData() does a decent job of it
    }

    // AJAX CRUD implementation. Delete.
    static function delete($table) {
      if (!self::isLoggedIn()) {
        http_response_code(400);
        die("Please login before deleting anything from $table!");
      }
      global $db;
      if (!$db->tableExists($table)) {
        http_response_code(400);
        die("Wrong table name: $table!");
      }
      extract($_POST, EXTR_PREFIX_ALL, 'posted');
      if (empty($posted_pk)) {
        http_response_code(400);
        die("Empty primary key to delete!");
      }
      $table = $db->prefix($table);
      $sql = "DELETE FROM $table WHERE `id` = $posted_pk";
      $db->query($sql);
      http_response_code(200);
    }

    static function mkCatNames($showInactive = false) {
      global $db;
      $catNames = array();
      $categories = $db->getData('categories', '*');
      foreach ($categories as $cat) {
        extract($cat);
        if ($active || $showInactive) {
          $catNames[$id] = $name;
        }
        else {
          $catNames[$id] = 'Inactive';
        }
      }
      return $catNames;
    }

    static function mkCatSource($showInactive = false) {
      global $db;
      $catSource = "[";
      $categories = $db->getData('categories', '*');
      foreach ($categories as $cat) {
        extract($cat);
        if ($active || $showInactive) {
          $catSource .= "{value: '$id', text: '$name'},";
        }
      }
      $catSource .= "]";
      return $catSource;
    }

    static function mkSelectSource($options) {
      $source = "[";
      foreach ($options as $o) {
        $source .= "{value: '$o', text: '$o'},";
      }
      $source .= "]";
      return $source;
    }

    static function getId($table, $when) {
      global $db;
      $row = $db->getData($table, 'id', $when);
      return $row[0]['id'];
    }

    static function getOptions() {
      if (!empty(self::$options)) {
        return self::$options;
      }
      global $db;
      if ($db->tableExists('options_meta')) {
        self::$options = $db->getMetaData('options_meta');
      }
      return self::$options;
    }

    static function putDefaultOptions($options) {
      global $db;
      $row = array();
      foreach ($options as $k => $o) {
        $row[$k] = $o['value'];
      }
      $rowDB = $db->getMetaData('options_meta');
      $row = array_merge($row, $rowDB);
      $db->putMetaData('options_meta', $row);
    }

    static function renderOption($pk, $option) {
      self::rmTransient('options');
      $optionsDB = self::getOptions();
      if (isset($optionsDB[$pk])) {
        $value = $optionsDB[$pk];
        $option['value'] = $value;
      }
      return self::renderRow($pk, $option);
    }

    static function renderRow($pk, $option) {
      $value = "";
      $type = 'text';
      $help = $more_help = "";
      $dataValue = "";
      $dataTpl = "";
      $dataMode = "data-mode='inline'";
      $dataSource = "";
      $trClass = $reload = $class = "";
      $name = "";
      $validator = "";
      $options = array();
      extract($option);
      if ($reload) {
        $class .= "xeditReload ";
      }
      if ($type == 'hidden') {
        $tr = '';
        return $tr;
      }
      if (!empty($trClass)) {
        $trClass = " class='$trClass'";
      }
      $dataType = "data-type='$type'";
      if (!empty($more_help)) {
        $clickHelp = "class='btn-help'";
      }
      else {
        $clickHelp = '';
      }
      $tr = "<tr$trClass><td>$name</td>";
      switch ($type) {
        case 'no-edit':
          $class .= "black";
          break;
        case 'checkbox' :
          $class .= "xedit-checkbox";
          $dataType = "data-type='checklist'";
          $dataValue = "data-value='$value'";
          if ($value) {
            $class .= ' btn-sm btn-success';
            $value = "";
          }
          else {
            $class .= ' btn-sm btn-danger';
            $value = "";
          }
          break;
        case 'category':
          $class .= "xedit";
          $dataType = "data-type='select'";
          $dataValue = "data-value='$value'";
          if (!empty($value)) {
            $value = self::getCatName($value);
          }
          $dataSource = 'data-source="' . self::mkCatSource() . '"';
          break;
        case 'select':
          $class .= "xedit";
          $dataType = "data-type='select'";
          $dataValue = "data-value='$value'";
          $dataSource = 'data-source="' . self::mkSelectSource($options) . '"';
          break;
        case 'file': // special case, return from here
          $type = '';
          $dataTpl = '';
          $class .= 'red';
          $value = "<input data-pk='$pk' id='fileinput' type='file' class='file' data-show-preview='false' data-show-upload='false'>";
          break;
        case 'submit':
        case 'button':
          $class .= "btn btn-primary btn-ez btn-$class";
          break;
        case 'dbselect':
        case 'dbeditableselect':
        case 'editableselect':
        case 'text':
        case 'textarea':
        default :
          $class .= "xedit";
          if ($dataTpl == 'none') {
            $dataTpl = '';
          }
          else {
            $dataTpl = "data-tpl='<input type=\"text\" style=\"width:450px\">'";
          }
          break;
      }
      if (!empty($validator)) {
        $valid = "data-validator='$validator'";
      }
      else {
        $valid = "";
      }
      if (empty($slug)) {
        $slug = "$pk-value";
      }
      if (!empty($button)) {
        $fun = "proc_$reveal";
        if (empty($url)) {
          $url = '#';
        }
        $options = self::getOptions();
        if (!empty($options[$reveal])) {
          $revealOption = $options[$reveal];
        }
        else {
          $revealOption = '';
        }
        if (method_exists("EZ", $fun)) {
          $dataReveal = @EZ::$fun($revealOption);
        }
        else {
          $dataReveal = "data-value='$revealOption' class='btn-sm btn-success reveal'";
        }
        $reveal = "</a><a href='$url' style='float:right' $dataReveal>$button";
      }
      else {
        $reveal = '';
      }
      $tr .= "\n<td style='width:70%'><a id='$slug' class='$class' data-name='$slug' data-pk='$pk' data-action='update' $dataType $dataTpl $dataMode $dataValue $dataSource $valid>$value $reveal</a></td>\n<td class='center-text'><a style='font-size:1.5em' data-content='$help' data-help='$more_help' data-toggle='popover' data-placement='left' data-trigger='hover' title='$name' $clickHelp><i class='glyphicon glyphicon-question-sign blue'></i></a></td></tr>\n";
      if ($type == 'hidden') {
        $tr = '';
      }
      return $tr;
    }

    static function randString($len = 32) {
      $chars = 'abcdefghijklmnopqrstuvwxyz';
      $chars .= strtoupper($chars) . '0123456789';
      $charLen = strlen($chars) - 1;
      $string = '';
      for ($i = 0; $i < $len; $i++) {
        $pos = rand(0, $charLen);
        $string .= $chars[$pos];
      }
      return $string;
    }

    static function flashMsg($msg, $class, $noflash = false, $noPre = false) {
      if ($noflash) {
        $fun = "show";
      }
      else {
        $fun = "flash";
      }
      $cleaned = str_replace(array("\n"), array('\n'), $msg);
      if (!empty($cleaned)) {
        $msg = htmlspecialchars($cleaned);
        if (!$noPre) {
          $msg = "<pre>$msg</pre>";
        }
        echo '<script>$(document).ready(function() {' .
        $fun . $class . '("' . $msg . '");
        });
        </script>';
      }
    }

    static function flashError($msg, $noPre = false) {
      self::flashMsg($msg, 'Error', false, $noPre);
    }

    static function showError($msg, $noPre = false) {
      self::flashMsg($msg, 'Error', true, $noPre);
    }

    static function flashWarning($msg, $noPre = false) {
      self::flashMsg($msg, 'Warning', false, $noPre);
    }

    static function showWarning($msg, $noPre = false) {
      self::flashMsg($msg, 'Warning', true, $noPre);
    }

    static function flashSuccess($msg, $noPre = false) {
      self::flashMsg($msg, 'Success', false, $noPre);
    }

    static function showSuccess($msg, $noPre = false) {
      self::flashMsg($msg, 'Success', true, $noPre);
    }

    static function flashInfo($msg, $noPre = false) {
      self::flashMsg($msg, 'Info', false, $noPre);
    }

    static function showInfo($msg, $noPre = false) {
      self::flashMsg($msg, 'Info', true, $noPre);
    }

    static function toggleMenu($header) {
      $options = self::getOptions();
      if (!empty($options['menu_placement'])) {
        $menuPlacement = $options['menu_placement'];
      }
      else {
        $menuPlacement = 'Auto';
      }
      if (self::$isInWP) { // standalone?
        $standAlone = !isset($_REQUEST['inframe']) && (@strpos($_SERVER["HTTP_REFERER"], 'wp-admin/options-general.php') === false);
      }
      else {
        $standAlone = true;
      }
      $topMenu = $menuPlacement == 'Top' || ($menuPlacement == 'Auto' && !$standAlone);
      if ($topMenu) {
        $search = array('<div class="col-sm-2 col-lg-2">',
            '<div class="sidebar-nav">',
            '<div class="nav-canvas">',
            '<ul class="nav nav-pills nav-stacked main-menu">',
            '<li class="accordion">',
            '<ul class="nav nav-pills nav-stacked">',
            '<a href="#">',
            '<div id="content" class="col-lg-10 col-sm-10">');
        $replace = array('<div>',
            '<div>',
            '<div>',
            '<ul class="nav nav-pills main-menu">',
            '<li class="dropdown">',
            '<ul class="dropdown-menu">',
            '<a href="#" data-toggle="dropdown">',
            '<div id="content" class="col-lg-12 col-sm-12">');
        $header = str_replace($search, $replace, $header);
      }
      return $header;
    }

    static function showService() {
      $select = rand(0, 4);
      echo "<div class='pull-right' style='margin-left:10px;'><a href='http://www.thulasidas.com/professional-php-services/' target='_blank' class='popup-long' title='Professional Services' data-content='The author of this plugin may be able to help you with your WordPress or plugin customization needs and other PHP related development. Find a plugin that almost, but not quite, does what you are looking for? Need any other professional PHP/jQuery dev services? Click here!' data-toggle='popover' data-trigger='hover' data-placement='left'><img src='img/svcs/300x250-0$select.jpg' style='border:0' alt='Professional Services from the Plugin Author' /></a></div>";
    }

    static function setTransient($key, $val, $timeout = 0) {
      $key = self::$slug . '-' . $key;
      if (empty($timeout)) {
        $timeout = self::$cacheTimeout;
      }
      if (function_exists('set_transient')) {
        return set_transient($key, $val, $timeout);
      }
      else {
        global $cache;
        return $cache->set($key, $val, $timeout);
      }
    }

    static function getTransient($key) {
      $key = self::$slug . '-' . $key;
      if (function_exists('get_transient')) {
        return get_transient($key);
      }
      else {
        global $cache;
        return $cache->get($key);
      }
    }

    static function rmTransient($key) {
      $key = self::$slug . '-' . $key;
      global $cache;
      return $cache->delete($key);
    }

    static function isUpdateAvailable() { // not ready yet
      return false;
    }

  }

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
