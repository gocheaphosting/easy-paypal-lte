<?php
include('ezKillLite.php');

if (!class_exists("EzPayPal6")) {

  class EzPayPal6 {

    var $isPro, $strPro, $plgDir, $plgURL;
    var $ezTran, $domain;

    function EzPayPal6() { //constructor
      $this->plgDir = dirname(__FILE__);
      $this->plgURL = plugin_dir_url(__FILE__);
      $this->isPro = file_exists("{$this->plgDir}/admin/options-advanced.php");
      if ($this->isPro) {
        $this->strPro = ' Pro';
      }
      else {
        $this->strPro = ' Lite';
      }
      if (is_admin()) {
        require_once($this->plgDir . '/EzTran.php');
        $this->domain = 'easy-paypal';
        $this->ezTran = new EzTran(__FILE__, "EZ PayPal{$this->strPro}", $this->domain);
        $this->ezTran->setLang();
      }
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

    static function install() {
      $ezppOptions = array(); // not sure if I need this initialization
      $mOptions = "ezPayPal-V6";
      $ezppOptions = get_option($mOptions);
      if (empty($ezppOptions)) {
        // create the necessary tables
        $isInstallingWP = true;
        chdir(dirname(__FILE__) . '/admin');
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

    static function uninstall() {
      $mOptions = "ezPayPal-V6";
      $ezppOptions = get_option($mOptions);
      $shopPage = $ezppOptions['shopPage'];
      if (!empty($shopPage)) {
        wp_delete_post($shopPage, true);
      }
      delete_option($mOptions);
    }

    function printAdminPage() {
      if (!empty($_POST['ezpaypal_force_admin'])) {
        update_option('ezpaypal_force_admin', true);
      }
      $forceAdmin = get_option('ezpaypal_force_admin');
      if (!empty($_POST['ezpaypal_force_admin_again'])) {
        update_option('ezpaypal_force_admin_again', true);
      }
      $forceAdminAgain = get_option('ezpaypal_force_admin_again');
      $testFile = plugins_url("admin/promo.php", __FILE__);
      if (!$forceAdmin && !@file_get_contents($testFile)) { // index cannot be used for testing
        ?>
        <div class='error' style='padding:10px;margin:10px;font-size:1.3em;color:red;font-weight:500'>
          <p>This plugin needs direct access to its files so that they can be loaded in an iFrame. Looks like you have some security setting denying the required access. If you have an <code>.htaccess</code> file in your <code>wp-content</code> or <code>wp-content/plugins</code>folder, please remove it or modify it to allow access to the php files in <code><?php echo $this->plgDir; ?>/</code>.
          </p>
          <p>
            If you would like the plugin to try to open the admin page, please set the option here:
          </p>
          <form method="post">
            <input type="submit" value="Force Admin Page" name="ezpaypal_force_admin">
          </form>
          <p>
            <strong>
              Note that if the plugin still cannot load the admin page after forcing it, you may see a blank or error page here upon reload. If that happens, please deactivate and delete the plugin. It is not compatible with your blog setup.
            </strong>
          </p>
        </div>
        <?php
        return;
      }
      if ($forceAdmin && !$forceAdminAgain) {
        ?>
        <script>
          var errorTimeout = setTimeout(function () {
            jQuery('#the_iframe').replaceWith("<div class='error' style='padding:10px;margin:10px;font-size:1.3em;color:red;font-weight:500'><p>This plugin needs direct access to its files so that they can be loaded in an iFrame. Looks like you have some security setting denying the required access. If you have an <code>.htaccess</code> file in your <code>wp-content</code> or <code>wp-content/plugins</code>folder, please remove it or modify it to allow access to the php files in <code><?php echo $this->plgDir; ?>/</code>.</p><p><strong>If EZ PayPal still cannot load the admin page after forcing it, please deactivate and delete the plugin. It is not compatible with your blog setup.</strong></p><p><b>You can try forcing the admin page again, which will kill this message and try to load the admin page. <form method='post'><input type='submit' value='Force Admin Page Again' name='ezpaypal_force_admin_again'></form><br><br>If you still have errors on the admin page or if you get a blank admin page, this plugin really is not compatible with your blog setup.</b></p></div>");
          }, 1000);
        </script>
        <?php
      }
      $src = plugins_url("admin/index.php?inframe", __FILE__);
      ?>
      <script>
        function calcHeight() {
          var w = window,
                  d = document,
                  e = d.documentElement,
                  g = d.getElementsByTagName('body')[0],
                  y = w.innerHeight || e.clientHeight || g.clientHeight;
          document.getElementById('the_iframe').height = y - 70;
        }
        if (window.addEventListener) {
          window.addEventListener('resize', calcHeight, false);
        }
        else if (window.attachEvent) {
          window.attachEvent('onresize', calcHeight);
        }
      </script>
      <?php
      echo "<iframe src='$src' frameborder='0' style='width:100%;position:absolute;top:5px;left:-10px;right:0px;bottom:0px' width='100%' height='900px' id='the_iframe' onLoad='calcHeight();'></iframe>";
    }

  }

} //End Class EzPayPal

if (class_exists("EzPayPal6")) {
  $ezPayPal = new EzPayPal6();
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

    $file = dirname(__FILE__) . '/easy-paypal.php';
    register_activation_hook($file, array("EzPayPal6", 'install'));
    register_deactivation_hook($file, array("EzPayPal6", 'uninstall'));
  }
}
