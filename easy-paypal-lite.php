<?php
/*
  Plugin Name: Easy PayPal
  Plugin URI: http://www.thulasidas.com/plugins/ezpaypal
  Description: <em>Lite Version</em>: Easiest way to start selling your digital goods online. Go to <a href="options-general.php?page=easy-paypal-lite.php">Settings &rarr; Easy PayPal</a> to set it up, or use the "Settings" link on the right.
  Version: 3.52
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
      $this->plgURL = plugins_url(basename(dirname(__FILE__))) ;
      $this->plgDir = dirname (__FILE__) ;
    }
    function ezppStyles() {
      wp_register_style('ezPayPalCSS1', "{$this->plgURL}/ezpp.css") ;
      wp_register_style('ezPayPalCSS2', "{$this->plgURL}/editableSelect.css") ;
      wp_enqueue_style('ezPayPalCSS1') ;
      wp_enqueue_style('ezPayPalCSS2') ;
    }
    function ezppScripts() {
      wp_register_script('ezPayPalJS1', "{$this->plgURL}/editableSelect.js") ;
      wp_enqueue_script('ezPayPalJS1') ;
    }
    function displayShop($atts, $content='') {
      $this->ezppStyles() ;
      chdir($this->plgDir) ;
      if (isset($_GET['show']))
        $show = $_GET['show'] ;
      else if (isset($_GET['buy']))
        $buy = $_GET['buy'] ;
      else
        extract(shortcode_atts(array("buy" => "",
              "show" => "",
              "link" => ""), $atts)) ;
      if (!empty($link) && strtolower($link) != "no") {
        if (!empty($buy)) $getParam="buy=$buy" ;
        if (!empty($show)) $getParam="show=$show" ;
        if (empty($content)) $content = "Buy Now!" ;
        $siteURL = home_url() ;
        return "<a href='$siteURL/ez-shop?$getParam'>$content</a>" ;
      }
      else {
        $toInclude = 'ez-shop.php' ;
        $handlers = array('office' => 'ez-office.php',
                    'delivery' => 'ez-delivery.php') ;
        foreach ($handlers as $k => $v) if (isset($_GET[$k])) $toInclude = $v ;
        $GLOBALS['toInclude'] = $toInclude ;
        ob_start() ;
        include($toInclude) ;
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
      $pageid = wp_insert_post($page);
      return $pageid ;
    }
    static function install() {
      require_once('dbHelper.php');
      $ezDB = new dbHelper(); // this will check for valid config
      $GLOBALS['ezDB'] = $ezDB ;
      $ezppOptions = array() ; // not sure if I need this initialization
      $mOptions = "ezPayPal" ;
      $ezppOptions = get_option($mOptions) ;
      if (!$ezppOptions['isSetup']) {
        // create the necessary tables
        include_once('createTables.php') ;
        createTables() ;
        $ezppOptions['isSetup'] = true ;
      }
      $shopPage = $ezppOptions['shopPage'] ;
      if (!empty($shopPage)) $shopObj = get_post($shopPage) ;
      else $shopObj = false ;
      if (empty($shopPage) || empty($shopObj) || $shopObj->post_status == 'trash') {
        $shopPage = ezPayPal::createShop() ;
        $ezppOptions['shopPage'] = $shopPage ;
      }
      else
        $shopPage = $ezppOptions['shopPage'] ;
      update_option($mOptions, $ezppOptions);
    }
    static function uninstall(){
      $mOptions = "ezPayPal" ;
      $ezppOptions = get_option($mOptions) ;
      $shopPage = $ezppOptions['shopPage'] ;
      if (!empty($shopPage)) {
        wp_delete_post($shopPage) ;
      }
      delete_option($mOptions) ;
    }
    function printAdminPage() {
      @session_start() ;
      chdir($this->plgDir) ;
      ezPayPal::install() ;
      $mOptions = "ezPayPal" ;
      $ezppOptions = get_option($mOptions);
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
      $page = add_options_page($mName, $mName, 'activate_plugins', basename(__FILE__),
              array(&$ezPayPal, 'printAdminPage'));
      add_action( 'admin_print_styles-' . $page, array(&$ezPayPal, 'ezppStyles') );
      add_action( 'admin_print_scripts-' . $page, array(&$ezPayPal, 'ezppScripts') );
    }
    $me = basename($ezPayPal->plgDir) . '/' . basename(__FILE__) ;
    add_action("activate_$me", array("ezPayPal", 'install')) ;
    add_action("deactivate_$me", array("ezPayPal", 'uninstall')) ;
  }
}
