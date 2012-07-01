<?php
/*
  Plugin Name: Easy PayPal
  Plugin URI: http://www.thulasidas.com/plugins/ezpaypal
  Description: <em>Lite Version</em>: Easiest way to start selling your digital goods online. Go to <a href="options-general.php?page=easy-paypal-lite.php">Settings &rarr; Easy PayPal</a> to set it up, or use the "Settings" link on the right.
  Version: 3.21
  Author: Manoj Thulasidas
  Author URI: http://www.thulasidas.com
*/

/*
  License: GPL2 or later
  Copyright (C) 2008 www.thulasidas.com
*/

if (class_exists("ezPayPal")) {
  // Another version is probably installed. Ask the user to deactivate it.
  die (__("<strong><em>Easy PayPal:</em></strong> Another version of this plugin is active.<br />Please deactivate it before activating <strong><em>Easy PayPal</em></strong>.", "easy-adsenser"));
}
else {
  class ezPayPal {
    var $plgDir, $plgURL ;
    function ezPayPal() { //constructor
      $this->plgURL = '/' . PLUGINDIR . '/' . 'easy-paypal-lite' ;
      $this->plgDir = dirname (__FILE__) ;
      wp_register_style('ezPayPalCSS1', "{$this->plgURL}/ezpp.css") ;
      wp_register_style('ezPayPalCSS2', "{$this->plgURL}/editableSelect.css") ;
    }
    function ezppStyles() {
      wp_enqueue_style('ezPayPalCSS1') ;
      wp_enqueue_style('ezPayPalCSS2') ;
    }
    function displayShop($atts, $content='') {
      $this->ezppStyles() ;
      // Don't cache the shop or the backend listener
      define('DONOTCACHEPAGE', true);
      chdir($this->plgDir) ;
      $office = $delivery = false ;
      if (isset($_GET['office']))
        $office = true ;
      else if (isset($_GET['delivery']))
        $delivery = true ;
      else if (isset($_GET['show']))
        $show = $_GET['show'] ;
      else
        extract(shortcode_atts(array("buy" => "",
              "show" => "",
              "link" => ""), $atts)) ;
      if ($office) {
        ob_start() ;
        include("ez-office.php") ;
        $shop = ob_get_clean() ;
        return $shop ;
      }
      if ($delivery) {
        ob_start() ;
        include("ez-delivery.php") ;
        $shop = ob_get_clean() ;
        return $shop ;
      }
      if (!empty($link) && strtolower($link) != "no") {
        if (!empty($buy)) $getParam="buy=$buy" ;
        if (!empty($show)) $getParam="show=$show" ;
        if (empty($content)) $content = "Buy Now!" ;
        $siteURL = home_url() ;
        echo "<a href='$siteURL/ez-shop?$getParam'>$content</a>" ;
      }
      else {
        if (!empty($buy)) $_GET["buy"] = $buy ;
        if (!empty($show)) $_GET["show"] = $show ;
        ob_start() ;
        include("ez-shop.php") ;
        $shop = ob_get_clean() ;
        return $shop ;
      }
    }
    static function createShop() {
      global $user_ID;
      $page['post_type'] = 'page';
      $page['post_content'] = '[ezshop]';
      $page['post_parent'] = 0;
      $page['post_author'] = $user_ID;
      $page['post_status'] = 'publish';
      $page['post_title'] = 'ezPayPal Shop';
      $page['post_name'] = 'ez-shop';
      $pageid = wp_insert_post ($page);
      return $pageid ;
    }
    static function install() {
      require_once('dbHelper.php');
      $ezDB = new dbHelper(); // this will check for valid config
      $GLOBALS['ezDB'] = $ezDB ;
      // create the necessary tables
      include_once('createTables.php') ;
      createTables() ;
      $ezppOptions = get_option($mOptions) ;
      if (empty($ezppOptions['shopPage'])) // don't create duplicate shop pages
        $shopPage = ezPayPal::createShop() ;
      else
        $shopPage = $ezppOptions['shopPage'] ;
      $ezppOptions = array('isSetup' => true, 'shopPage' => $shopPage) ;
      $mOptions = "ezPayPal" ;
      update_option($mOptions, $ezppOptions);
    }
    function printAdminPage() {
      chdir($this->plgDir) ;
      $mOptions = "ezPayPal" ;
      $ezppOptions = get_option($mOptions);
      if (!$ezppOptions['isSetup']) {
        ezPayPal::install() ;
        $ezppOptions = get_option($mOptions) ;
      }
      if ($ezppOptions['isSetup'] == true) {
        $toInclude = "admin.php" ;
        if (!empty($_GET['action'])) $toInclude = $_GET['action'] ;
        if ($toInclude == "setup.php") {
          ezPayPal::install() ;
          $toInclude = "admin.php" ;
        }
        $GLOBALS['toInclude'] = $toInclude ;
        include($toInclude) ;
      }
      else { // the installation failed
        echo "Trouble installing ezPayPal!" ;
      }
    }
  }
} //End Class ezPayPal

if (class_exists("ezPayPal")) {
  register_activation_hook(__FILE__, array("ezPayPal", 'install')) ;
  $ezPayPal = new ezPayPal() ;
  if (isset($ezPayPal)) {
    if (isset($_GET['delivery']) && !empty($_GET['dl'])) {
      include("ez-delivery.php") ;
      exit(0) ;
    }
    add_action('admin_menu', 'ezPayPal_admin_menu') ;
    add_shortcode('ezshop', array(&$ezPayPal, 'displayShop')) ;
    function ezPayPal_admin_menu() {
      global $ezPayPal ;
      $mName = 'Easy PayPal' ;
      $page = add_options_page($mName, $mName, 9, basename(__FILE__),
              array(&$ezPayPal, 'printAdminPage'));
      add_action( 'admin_print_styles-' . $page, array(&$ezPayPal, 'ezppStyles') );
    }
  }
}
