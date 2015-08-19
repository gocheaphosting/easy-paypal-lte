<?php

include('ezKillLite.php');

if (!class_exists("EzPayPal6")) {

  require_once 'EzPlugin.php';

  class EzPayPal6 extends EzPlugin {

    public function __construct($name, $key) {
      $this->plgDir = __DIR__; // cannot be in the base class!
      $this->plgURL = plugin_dir_url(__FILE__); // cannot be in the base class!
      parent::__construct();
      $this->name = $name;
      $this->key = $key;
    }

    static function createShop() {
      global $user_ID;
      $page = get_page_by_path('ez-shop');
      if ($page == null) {
        $src = plugins_url("shop.php", __FILE__);
        $content = "<iframe src='$src?wp' frameborder='0' style='width:100%;' scrolling='no' id='the_iframe' onLoad='calcHeight();'></iframe>"
                . '<script type="text/javascript">
function calcHeight() {
  var the_iframe = document.getElementById("the_iframe");
  var the_height = the_iframe.contentWindow.document.body.scrollHeight;
  the_iframe.height = the_height;
}
</script>' .
                "<p>" . __("This is an auto-generated page by EZ PayPal Plugin. It displays the products you have defined in a neat table format, which allows your potential buyers to purchase them.", 'easy-paypal') . "</p>
<p>" . __("Note that you can create your own shop pages using the shortcodes. For example, each product can be displayed as a <strong>Buy Now</strong> using the shortcode format <code>[[ezshop id=3 qty=2]Buy Now[/ezshop]]</code>. This will insert a link, which when clicked, will take your reader to a PayPal page to buy two licences of the product with id 3.", 'easy-paypal') . "</p>
<p>" . __("This E-Shop page shows you the product listing with the ids and names to help you select products and generate shortcodes or links. Click on the Id to view short code or link for the product with the quantity as specified. You can use the shortcode [[ezshop]] or [[ezshop]]Link Text[[/ezshop]] to display a link to your e-shop.", 'easy-paypal') . "</p>";

        $page['post_type'] = 'page';
        $page['post_content'] = $content;
        $page['post_parent'] = 0;
        $page['post_author'] = $user_ID;
        $page['post_status'] = 'publish';
        $page['post_title'] = 'EZ PayPal Shop';
        $page['post_name'] = 'ez-shop';
        $page['comment_status'] = 'closed';
        $pageid = wp_insert_post($page);
      }
      else {
        $pageid = $page->ID;
      }
      return $pageid;
    }

    function displayShop($atts, $content = '') {
      $query = "";
      $vars = array("id" => "", "qty" => "");
      $vars = shortcode_atts($vars, $atts);
      foreach ($vars as $k => $v) {
        if (!empty($v)) {
          $query .= "&$k=$v";
        }
      }
      $getParam = "?wp";
      if (!empty($vars['qty'])) {
        $getParam .= "$query";
        if (empty($content)) {
          $content = "Buy Now!";
        }
        $buyLink = "<a class='ezpaypal-buy' href='{$this->plgURL}buy.php$getParam'>$content</a>";
      }
      else {
        if (empty($content)) {
          $content = "Visit Shop";
        }
        $buyLink = "<a class='ezpaypal-shop' href='{$this->plgURL}shop.php$getParam'>$content</a>";
      }
      return $buyLink;
    }

    static function install($dir = '', $mOptions = "ezPayPal-V6") {
      $ezppOptions = array(); // not sure if I need this initialization
      $ezppOptions = get_option($mOptions);
      if (empty($ezppOptions)) {
        // create the necessary tables
        $GLOBALS['isInstallingWP'] = true;
        chdir(__DIR__ . '/admin');
        require_once('dbSetup.php');
        $ezppOptions['isSetup'] = true;
        $shopPage = EzPayPal6::createShop();
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
        $shopPage = EzPayPal6::createShop();
        $ezppOptions['shopPage'] = $shopPage;
      }
      else {
        $shopPage = $ezppOptions['shopPage'];
      }
      update_option($mOptions, $ezppOptions);
    }

    static function uninstall($mOptions = "ezPayPal-V6") {
      $ezppOptions = get_option($mOptions);
      $shopPage = $ezppOptions['shopPage'];
      if (!empty($shopPage)) {
        wp_delete_post($shopPage, true);
      }
      delete_option($mOptions);
    }

  }

} //End Class EzPayPal

if (class_exists("EzPayPal6")) {
  $ezPayPal = new EzPayPal6('EZ PayPal', 'ezpaypal');
  if (isset($ezPayPal)) {
    add_action('admin_menu', 'ezPayPal_admin_menu');
    add_shortcode('ezshop', array($ezPayPal, 'displayShop'));

    if (!function_exists('ezPayPal_admin_menu')) {

      function ezPayPal_admin_menu() {
        global $ezPayPal;
        $mName = 'EZ PayPal ' . $ezPayPal->strPro;
        add_options_page($mName, $mName, 'activate_plugins', basename(__FILE__), array($ezPayPal, 'printAdminPage'));
      }

    }

    $file = __DIR__ . '/easy-paypal.php';
    register_activation_hook($file, array("EzPayPal6", 'install'));
    register_deactivation_hook($file, array("EzPayPal6", 'uninstall'));
  }
}
