<?php
require_once 'EZ.php';

class EzShop {

  var $options, $return, $cancel_return, $notify_url, $paypalURL, $business;
  var $error = '', $warning = '', $success = '';
  var $product; // Single product, populated while checking inputs in verifyRequest

  function __construct() {
    $this->options = EZ::getOptions();
    if (EZ::$isInWP) {
      $qs = "?wp";
    }
    else {
      $qs = '';
    }
    $ezppURL = EZ::ezppURL();
    $this->return = "{$ezppURL}return.php$qs";
    $this->cancel_return = "{$ezppURL}shop.php$qs";
    $this->notify_url = "{$ezppURL}office.php$qs";
    if (!empty($this->options['sandbox_mode']) && $this->options['sandbox_mode']) {
      $this->paypalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
      $this->business = $this->options['sandbox_email'];
    }
    else {
      $this->paypalURL = "https://www.paypal.com/cgi-bin/webscr";
      if (!empty($this->options['paypal_email'])) {
        $this->business = $this->options['paypal_email'];
      }
    }
  }

  function __destruct() {

  }

  function EzShop() {
    if (version_compare(PHP_VERSION, "5.0.0", "<")) {
      $this->__construct();
      register_shutdown_function(array($this, "__destruct"));
    }
  }

  function renderProductTable() {
    global $db;
    $products = $db->getData("products");
    sort($products);
    ?>
    <table class="table table-striped table-bordered responsive data-table">
      <thead>
        <tr>
          <?php
          if (EZ::isLoggedIn()) {
            ?>
            <th style='width:4%'> <?php _e("Id", 'easy-paypal'); ?> </th>
            <?php
          }
          ?>
          <th style='width:40%'> <?php _e("Product", 'easy-paypal'); ?> </th>
          <th style='width:30%'><?php _e("Category", 'easy-paypal'); ?> </th>
          <th class='center-text' style='width:8%'><?php _e("Price", 'easy-paypal'); ?> </th>
          <th class='center-text' style='width:7%'><?php _e("Quantity", 'easy-paypal'); ?> </th>
          <th class='center-text' style='width:11%;min-width:80px'><?php _e("Buy?", 'easy-paypal'); ?> </th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($products as $p) {
          if ($p['active']) {
            $product = EZ::getProduct($p['id']);
            if (empty($product['hidden'])) {
              $this->renderProductRow($product);
            }
          }
        }
        ?>
      </tbody>
    </table>
    <?php
  }

  static function renderScript() {
    ?>
    <script>
      $('.buyNow').click(function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var qty = $("#qty_" + id).text();
        var wp = '';
        if (isInWP()) {
          wp = 'wp&';
        }
        var href = 'buy.php?' + wp + 'id=' + id + '&qty=' + qty;
        if ($(this).hasClass('popup_paypal')) {
          var title = "Go to PayPal";
          var w = 1024;
          var h = 728;
          return ezPopUp(href, title, w, h);
        }
        else {
          var url = 'buy.php?' + wp + 'id=' + id + '&qty=' + qty;
          if ($(this).hasClass('blank_paypal')) {
            var win = window.open(url, '_blank');
            win.focus();
          }
          else {
            window.location.href = url;
          }
        }
        return false;
      });
      $('.productInfo').click(function () {
        var info = $(this).attr('data-info');
        bootbox.alert(info);
      });
      $('.shortCode').click(function () {
        var id = $(this).text();
        var qty = $("#qty_" + id).text();
        var name = $(this).attr('data-name');
        var wp = '';
        var shortCode = '';
        var link = '';
        if (isInWP()) {
          wp = 'wp&';
          shortCode = "Shortcode: <code>[ezshop id=" + id + " qty=" + qty +
                  "]Buy " + name + " Now![/ezshop]</code><br>";
        }
        link = "Link: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; " +
                "<code>&lt;a href='<?php echo EZ::ezppURL(); ?>buy.php?" + wp
                + "id=" + id + "&qty=" + qty +
                "'>Buy " + name + " Now!&lt;/a&gt;</code><br><br>" +
                "<b>Cut and paste as needed.<b>";
        bootbox.dialog({
          message: shortCode + link,
          title: "Shortcode and Link",
          buttons: {
            success: {
              label: "OK"
            }
          }
        });
      });
    </script>
    <?php
  }

  function mkProductInfo($product) {
    extract($product);
    $renderedPrice = $this->renderPrice($product_price, $mc_currency);
    $category = EZ::getCatName($category_id);
    $info = "<h4>$product_name</h4>";
    if (!empty($image)) {
      $info .= "<img class=\"center-block\" style=\"max-width:300px;max-height:200px;\" src=\"$image\" alt=\"$product_name\" />";
    }
    $info .= "<p>Category: $category</p>";
    $info .= "<p>Price: $renderedPrice</p>";
    if (!empty($desc)) {
      $info .= "<p>$desc</p>";
    }
    $info = htmlspecialchars($info);
    return $info;
  }

  function renderProductRow($product) {
    extract($product);
    $renderedPrice = $this->renderPrice($product_price, $mc_currency);
    $category = EZ::getCatName($category_id);
    $idColumn = "";
    if (EZ::isLoggedIn()) {
      if (empty(EZ::$options['hide_shortcode'])) {
        $idColumn = "<td class='center-text'><a href='#' class='shortCode' title='Click to view shortcodes and links for $product_name' data-toggle='tooltip' data-name='$product_name'>$id</a></td>";
      }
      else {
        $idColumn = "<td class='center-text'>$id</td>";
      }
    }
    if (!empty(EZ::$options['popup_paypal'])) {
      $popup = 'popup_paypal';
    }
    else {
      $popup = '';
    }
    $info = $this->mkProductInfo($product);
    $buyButton = self::mkBuyButton($id);
    echo "<tr>"
    . $idColumn
    . "<td><a href='#' class='productInfo' title='Click to view $product_name' data-toggle='tooltip' data-info='$info'>$product_name</a></td>"
    . "<td>$category</td>"
    . "<td class='center-text'>$renderedPrice</td>"
    . "<td class='center-text'><a id='qty_$id' href='#' class='xedit-new' data-validator='number'>1</a></td>"
    . "<td class='center-text'>$buyButton</td></tr>\n";
  }

  static function mkBuyButton($id) {
    $popup = "";
    if (!empty(EZ::$options['popup_paypal'])) {
      if (EZ::$options['popup_paypal'] == 'New Browser Window/Tab') {
        $popup = "blank_paypal";
      }
      else if (EZ::$options['popup_paypal'] == 'Popup Window') {
        $popup = "popup_paypal";
      }
    }
    else {

    }
    $button = "<a data-id='$id' class='btn-sm btn-success right buyNow $popup' href='#' style='white-space:nowrap;'><i class='glyphicon glyphicon-shopping-cart icon-white action'></i> Buy Now</a>";
    return $button;
  }

  function renderPrice($price, $currency) {
    $ret = trim($price) . $currency;
    return $ret;
  }

  function mkReturnPage() {
    return $this->return;
  }

  function mkAmountLine() {
    $amountLine = "<input type='hidden' name='amount' value='{$this->product['product_price']}'>";
    return $amountLine;
  }

  function mkShippingLine() {
    $shippingLine = "<input type='hidden' name='no_shipping' value='{$this->product['no_shipping']}'>";
    return $shippingLine;
  }

  function mkXClickLine() {
    $xclickLine = "<input type='hidden' name='cmd' value='_xclick'>";
    return $xclickLine;
  }

  function mkLogoLine() {
    if (!empty(EZ::$options['checkout_logo'])) {
      $checkoutLogo = EZ::$options['checkout_logo'];
    }
    else {
      $checkoutLogo = EZ::ezppURL() . "assets/checkout-header.png";
    }
    $logoLine = "<input type='hidden' name='cpp_header_image' value='$checkoutLogo'>";
    return $logoLine;
  }

  function mkProductImage($product) {
    extract($product);
    $category = EZ::getCatName($category_id);
    $info = "<div class='center-text'>";
    if (!empty($image)) {
      $info .= "<img class=\"center-block\" style=\"max-width:300px;max-height:200px;\" src=\"$image\" alt=\"$product_name\" />";
    }
    $info .= "<p>Category: $category</p>";
    if (!empty($desc)) {
      $info .= "<p>$desc</p>";
    }
    $info .= "</div>";
    return $info;
  }

  function renderForm() {
    $inputs = $this->getInputs();
    $header = $this->makeHeader($inputs);
    $form = $this->makeForm($inputs);
    if (!empty(EZ::$options['show_image'])) {
      $info = $this->mkProductImage($this->product);
    }
    else {
      $info = '';
    }
    $footer = $this->makeFooter($inputs, $form);
    $html = $header . $form . $info . $footer;
    echo $html;
  }

  function makeForm($inputs) {
    $qty = $inputs['qty'];
    $autoSubmit = empty($inputs['debug']);
    $product = $this->product;
    extract($product);
    $returnPage = $this->mkReturnPage();
    $amountLine = $this->mkAmountLine();
    $xclickLine = $this->mkXClickLine();
    $shippingLine = $this->mkShippingLine();
    $logoLine = $this->mkLogoLine();
    $form = "<form id='ezppBuyForm' action='$this->paypalURL' method='post'>
$xclickLine
<input type='hidden' name='business' value='$this->business'>
<input type='hidden' name='item_name' value='$product_name'>
<input type='hidden' name='item_number' value='$product_code'>
$amountLine
<input type='hidden' name='quantity' value='$qty'>
<input type='hidden' name='return' value='$returnPage'>
<input type='hidden' name='cancel_return' value='$this->cancel_return'>
<input type='hidden' name='notify_url' value='$this->notify_url'>
<input type='hidden' name='no_note' value='1'>
<input type='hidden' name='currency_code' value='$mc_currency'>
<input type='hidden' name='rm' value='2'>
<input type='hidden' name='bn' value='AdsEz_SP'>
$logoLine
$shippingLine";
    if ($autoSubmit) {
      $form .= "\n</form>";
    }
    else {
      $form .= "<input class='btn btn-success' type='submit' value='Go To PayPal' />\n$form\n</form>";
    }
    return $form;
  }

  function mkPrice() {
    $product_price = number_format($this->product['product_price'], 2, ".", "");
    return $product_price;
  }

  function mkPriceHeading() {
    $price = $this->mkPrice();
    return "<h4 align='center'> \$$price each</h4>\n";
  }

  function makeHeader($inputs) {
    $qty = $inputs['qty'];
    $product = $this->product;
    extract($product);
    $html = "<div class='center-text'><h3>$product_name, Quantity: $qty</h3>\n";
    $html .= $this->mkPriceHeading();
    if (!empty(EZ::$options['show_product_info'])) {
      if (!empty($image)) {
        $html .= "<img class='center-block' style='max-width:300px;max-height:250px;' src='$image' alt='$product_name' />";
      }
      $category = EZ::getCatName($category_id);
      $html .= "<h5>$category</h5>";
      if (!empty($desc)) {
        $html .= "<p>$desc</p>";
      }
    }
    $html .= '</div>';
    return $html;
  }

  function makeFooter($inputs, $form = '') {
    $autoSubmit = empty($inputs['debug']);
    if ($autoSubmit) {
      $html = <<<EOF
      <hr />
      <h4 align="center">Please wait while we transfer you to PayPal.</h4>
      <div style='width:16px;margin:0 auto;'>
        <img src='admin/img/loading.gif' alt='[Please wait]' />
      </div>
      <script>
        $(document).ready(function () {
          $("#ezppBuyForm").submit();
        });
      </script>
EOF;
    }
    else {
      $html = <<<EOF
      <h4 align="center">Please go to Paypal to complete your purchase.</h4>
      <hr />
      <div id='formRendered' style="display:none;text-align:left">
        <pre><?php echo htmlspecialchars($form); ?></pre>
        <a class='btn btn-success' href='#' id="viewForm">View Form</a>
      </div>
      <script>
        $("#viewForm").click(function () {
          $("#formRendered").fadeIn();
          $(this).fadeOut();
        });
      </script>
EOF;
    }
    return $html;
  }

  function renderUpdateSection() {
    echo "This is a <a href='#' class='goPro'>Pro</a> feature.";
  }

  function verifyRequest() {
    if (isset($_REQUEST['wp'])) {
      $wpQs = '&wp';
    }
    else {
      $wpQs = '';
    }

    $inputs = $this->getInputs();
    if (empty($inputs['id'])) {
      $error = urlencode("The product you are looking for is not ready for sale yet. Please select another product from the table below. You can search and sort the table to find the right product.");
      header("location: shop.php?error=$error$wpQs");
      exit();
    }
    else if ($inputs['id'] == -1) {
      $error = urlencode("No Product specified. Please select a product from the table below. You can  search and sort the table to find the right product.");
      header("location: shop.php?error=$error$wpQs");
      exit();
    }
  }

  function getInputs() {
    $needed = array('id', 'qty');
    $request = array_map('htmlspecialchars', $_REQUEST);
    $inputs = array();
    foreach ($needed as $k) {
      if ($request[$k] != $_REQUEST[$k]) { // invalid input
        return $inputs;
      }
    }
    if (empty($request['qty'])) {
      $inputs['qty'] = 1;
    }
    else {
      $inputs['qty'] = $request['qty'];
    }
    if (empty($request['id'])) {
      $inputs['id'] = -1;
      return $inputs;
    }
    $this->product = EZ::getProduct($request['id'], true);
    if (empty($this->product)) {
      $error = urlencode("The product you are looking for is not ready for sale yet. Please select another product from the table below. You can search and sort the table to find the right product.");
      header("location: shop.php?error=$error");
      exit();
    }
    $inputs['id'] = $this->product['id'];
    return $inputs;
  }

}
