<?php

/*
  Plugin Name: Easy PayPal
  Plugin URI: http://www.thulasidas.com/plugins/ezpaypal
  Description: <em>Lite Version</em>: Easiest way to start selling your digital goods online. Go to <a href="options-general.php?page=easy-paypal-lite.php">Settings &rarr; Easy PayPal</a> to set it up, or use the "Settings" link on the right.
  Version: 5.24
  Author: Manoj Thulasidas
  Author URI: http://www.thulasidas.com
 */

/*
  Copyright (C) 2008 www.ads-ez.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (class_exists("EzPayPalPro")) {
  $plg = "Easy PayPal Lite";
  $lite = plugin_basename(__FILE__);
  include_once('ezDenyLite.php');
  ezDenyLite($plg, $lite);
}
else {

  class EzPayPal {

    var $plgDir, $plgURL;
    var $ezTran, $ezAdmin, $slug, $domain, $myPlugins;

    function EzPayPal() { //constructor
      $this->plgDir = dirname(__FILE__);
      $this->plgURL = plugin_dir_url(__FILE__);
      if (is_admin()) {
        require_once($this->plgDir . '/EzTran.php');
        $this->domain = $this->slug = 'easy-paypal';
        $this->ezTran = new EzTran(__FILE__, "Easy PayPal", $this->domain);
        $this->ezTran->setLang();
      }
    }

    function ezppStyles() {
      wp_register_style('ezPayPalCSS1', "{$this->plgURL}/ezpp.css");
      wp_register_style('ezPayPalCSS2', "{$this->plgURL}/editableSelect.css");
      wp_enqueue_style('ezPayPalCSS1');
      wp_enqueue_style('ezPayPalCSS2');
    }

    function ezppScripts() {
      wp_register_script('ezPayPalJS1', "{$this->plgURL}/editableSelect.js");
      wp_enqueue_script('ezPayPalJS1');
    }

    function displayShop($atts, $content = '') {
      $this->ezppStyles();
      $link = '';
      chdir($this->plgDir);
      if (isset($_GET['show'])) {
        $show = $_GET['show'];
      }
      else if (isset($_GET['buy'])) {
        $buy = $_GET['buy'];
      }
      else {
        extract(shortcode_atts(array("buy" => "",
            "show" => "",
            "link" => "yes"), $atts));
      }
      if (!empty($buy)) {
        $getParam = "buy=$buy";
      }
      if (!empty($show)) {
        $getParam = "show=$show";
      }
      if (!empty($getParam) && !empty($link) && strtolower($link) != "no") {
        if (empty($content)) {
          $content = "Buy Now!";
        }
        $siteURL = home_url();
        return "<a href='$siteURL/ez-shop?$getParam'>$content</a>";
      }
      else {
        $toInclude = 'ez-shop.php';
        $handlers = array('office' => 'ez-office.php',
            'delivery' => 'ez-delivery.php');
        foreach ($handlers as $k => $v) {
          if (isset($_GET[$k])) {
            $toInclude = $v;
          }
        }
        $GLOBALS['toInclude'] = $toInclude;
        ob_start();
        include_once($toInclude);
        $shop = ob_get_clean();
        return $shop;
      }
    }

    static function createShop() {
      global $user_ID;
      global $shortcode_tags;
      $shop = get_option('siteurl') . '/ez-shop';
      $permalink = admin_url('options-permalink.php');
      $content = "<p>" . __("This is an auto-generated page by Easy PayPal Plugin. It displays the products you have defined in a neat table format, which allows your potential buyers to purchase them.", 'easy-paypal') . "</p>
<p>" . sprintf(__("Whether you display this page or not, you should not delete it. This page is the target to which PayPal will send the information about your purchases. It is the <em>PayPal Listener</em> and the plugin will not work if it is not reachabe at %s. Please click to verify.", 'easy-paypal'), "<a href='$shop'>$shop</a>") . "</p>
<p>" . sprintf(__("If the shop page (%s) is not reachable, please %senable a permalink structure%s for your blog. <em>Any structure (other than the ugly default structure using %s) will do.", 'easy-paypal'), $shop, "<a href='$permalink'>", "</a>", "<code>?p=</code>") . "</p>
<p>" . __("Note that you can create your own shop pages using the shortcodes. For example, each product can be displayed as a <strong>Buy Now</strong> using the shortcode format <code>[[ezshop buy='product_code']Buy Now[/ezshop]]</code>. This will insert a link, which when clicked, will take your reader to a PayPal page to buy the product.", 'easy-paypal') . "</p>
<p>" . __("The ez-shop page is not meant to be a public page. It is a page needed for the plugin to receive messages from PayPal and handle them. It is also a quick page to show you that the plugin is working. Please create a pretty page with links using short codes.</p><p color='red'>If you decide to edit this page, please do not delete the <code>[[ezshop]]</code> line below this line.", 'easy-paypal') . "</p>\n";
      $page['post_type'] = 'page';
      $useRawTag = true; // TODO: Make this an option (in option_meta)
      if ($useRawTag && class_exists("Mysitemyway") && array_key_exists("raw", $shortcode_tags)) {
        $page['post_content'] = $content . '[raw][ezshop][/raw]';
      }
      else {
        $page['post_content'] = $content . '[ezshop]';
      }
      $page['post_parent'] = 0;
      $page['post_author'] = $user_ID;
      $page['post_status'] = 'publish';
      $page['post_title'] = 'ezPayPal Shop';
      $page['post_name'] = 'ez-shop';
      $page['comment_status'] = 'closed';
      $pageid = wp_insert_post($page);
      return $pageid;
    }

    static function install() {
      require_once('dbHelper.php');
      $ezDB = new DbHelper(); // this will check for valid config
      $GLOBALS['ezDB'] = $ezDB;
      $ezppOptions = array(); // not sure if I need this initialization
      $mOptions = "ezPayPal";
      $ezppOptions = get_option($mOptions);
      if (empty($ezppOptions)) {
        // create the necessary tables
        include_once('createTables.php');
        createTables();
        $ezppOptions['isSetup'] = true;
        $shopPage = EzPayPal::createShop();
        $ezppOptions['shopPage'] = $shopPage;
      }
      $shopPage = $ezppOptions['shopPage'];
      if (!empty($shopPage)) {
        $shopObj = get_post($shopPage);
      }
      else {
        $shopObj = false;
      }
      if (empty($shopPage) || empty($shopObj) || $shopObj->post_status == 'trash') {
        $shopPage = EzPayPal::createShop();
        $ezppOptions['shopPage'] = $shopPage;
      }
      else {
        $shopPage = $ezppOptions['shopPage'];
      }
      update_option($mOptions, $ezppOptions);
    }

    static function uninstall() {
      $mOptions = "ezPayPal";
      $ezppOptions = get_option($mOptions);
      $shopPage = $ezppOptions['shopPage'];
      if (!empty($shopPage)) {
        wp_trash_post($shopPage);
      }
      delete_option($mOptions);
    }

    static function session_start() {
      if (!session_id()) {
        @session_start();
      }
    }

    function printAdminPage() {
      // if translating, print translation interface
      if ($this->ezTran->printAdminPage()) {
        return;
      }
      $slug = $this->slug;
      $plgURL = $this->plgURL;
      $plg = array('value' => 'Easy PayPal');
      $this->myPlugins = array($slug => $plg);
      require_once($this->plgDir . '/EzAdmin.php');
      $this->ezAdmin = new EzAdmin($plg, $slug, $plgURL);
      $this->ezAdmin->domain = $this->domain;
      $ez = $this->ezAdmin;
      self::session_start();
      $_SESSION['loginMessage'] = '';
      chdir($this->plgDir);
      EzPayPal::install();
      $mOptions = "ezPayPal";
      $ezppOptions = get_option($mOptions);
      $perma = trim(get_option('permalink_structure'));
      if (empty($perma)) {
        echo '<div class="error"><p><b><em>' . __('Permalinks Error', 'easy-paypal') . '</em></b>: ' . sprintf(__('You need to set your %s to something other than the default for <em>Easy PayPal</em> shop to work properly. You can set it at %s', 'easy-paypal'), '<a href="http://codex.wordpress.org/Using_Permalinks" target="_blank">permalinks</a>', '<a href="options-permalink.php">Settings &rarr; Permalinks</a>.</p></div><br />');
      }
      if ($ezppOptions['isSetup'] == true) {
        $toInclude = "./admin.php";
        if (!empty($_GET['action'])) {
          $toInclude = $_GET['action'];
        }
        if ($toInclude == "setup.php") {
          EzPayPal::install();
          $toInclude = "admin.php";
        }
        $GLOBALS['toInclude'] = $toInclude;
        include($toInclude);
      }
      else { // the installation failed
        echo "Trouble installing ezPayPal!";
      }
      $help = __("Need help?<br />Click me!", 'easy-paypal');
      echo "<div class='updated' style='font-weight:bold;padding:5px;width:686px;margin:0px auto;'><span onclick='popUp(\"{$this->plgURL}/docs/index.php?wordpress\");return false;' onmouseover=\"Tip('$help', WIDTH, 70, ABOVE, true)\" onmouseout=\"UnTip()\" style='color:green;cursor:pointer;'>";
      echo __("Quickstart Guide: Click here for quick help on how to use this plugin.", 'easy-paypal');
      echo "&nbsp;&nbsp;&nbsp;<img title='$help' style='cursor:pointer;vertical-align:bottom' alt='(?)' src='{$this->plgURL}/help.png' />";
      echo "</span>";
      $plgKey = 'easy-paypal-lte';
      $promoClick = "onclick=\"popupwindow('http://www.thulasidas.com/promo.php?key=$plgKey','Get Pro', 1024, 768);return false;\"";
      $promoTip = htmlspecialchars("<a style=\"color:#009;font-weight:bold\" href=\"http://www.thulasidas.com/promo.php?key=$plgKey\" target=_blank >" . __("Limited Time Offer. Get the Pro version for less than a dollar!", 'easy-common') . "</a>");
      echo "<a href='http://www.thulasidas.com/promo.php?key=$plgKey' $promoClick onmouseover=\"Tip('$promoTip', WIDTH, 200, FIX, [this, 5, 5])\" style='float:right' onmouseout=\"UnTip()\">Go <strong>Pro</strong> for a dollar!</a>";
      echo "</div>";
      echo "<form method='post' style='width:800px;margin-left:auto;margin-right:auto'>";
      $this->ezTran->renderTranslator();
      echo "</form><br />";
      echo "<div style='width:800px;margin:0px auto;'>";
      $ez->renderSupport();
      echo "</div>";
    }

  }

} //End Class ezPayPal

if (class_exists("ezPayPal")) {
  $ezPayPal = new EzPayPal();
  if (isset($ezPayPal)) {
    if (isset($_GET['delivery']) && !empty($_GET['dl'])) {
      include("ez-delivery.php");
      exit(0);
    }
    add_action('admin_menu', 'ezPayPal_admin_menu');
    add_shortcode('ezshop', array($ezPayPal, 'displayShop'));

    function ezPayPal_admin_menu() {
      global $ezPayPal;
      $mName = 'Easy PayPal';
      $page = add_options_page($mName, $mName, 'activate_plugins', basename(__FILE__), array($ezPayPal, 'printAdminPage'));
      add_action('admin_print_styles-' . $page, array($ezPayPal, 'ezppStyles'));
      add_action('admin_print_scripts-' . $page, array($ezPayPal, 'ezppScripts'));
    }

    register_activation_hook(__FILE__, array("ezPayPal", 'install'));
    register_deactivation_hook(__FILE__, array("ezPayPal", 'uninstall'));
    add_action('init', array("EzPayPal", 'session_start'));
  }
}
