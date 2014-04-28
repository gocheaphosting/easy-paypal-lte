<?php

include_once('htmlHelper.php');
include_once('dbHelper.php');
include_once('ezpp.php');

$html = new HtmlHelper();
$ezDB = new DbHelper();
$ezDB->doOrDie($html);
$ezpp = new EzPP($ezDB);

if (!empty($GLOBALS['ezDebug'])) {
  $ezDebug = $GLOBALS['ezDebug'];
}
else {
  $ezDebug = false;
}

if (!empty($GLOBALS["ezPayPal"]) && is_a($GLOBALS["ezPayPal"], "ezPayPal")) {
  $adminText = __("Admin", 'easy-paypal');
  $admin = "wp-admin/options-general.php?page=easy-paypal-lite.php";
  $shop = "ez-shop";
  $ezppURL = get_option('siteurl') . '/';
  $returnPage = "$ezppURL$shop?delivery";
  $ipnListener = "$ezppURL$shop?office";
  $showUpdateSection = false; // TODO: Make this an option (in option_meta)
}
else {
  $err = __("Easy PayPal is not configured correctly.", 'easy-paypal') . " " . __("Error in global variable.", 'easy-paypal');
}

$paypal = $ezDB->getRowData('paypal');
if (empty($paypal)) {
  $err = __("Easy PayPal is not configured correctly.", 'easy-paypal') . " " . __("No PayPal info!", 'easy-paypal') . " <a href='$admin'><input type='button' value='" . __("Go to Admin Page", 'easy-paypal') . "' name='setup'></a>\n";
}
if (!empty($paypal['sandbox_mode']) && $paypal['sandbox_mode']) {
  $paypalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
  $paypalIPN = "https://www.sandbox.paypal.com";
  $paypalEmail = $paypal['sandbox_email'];
}
else {
  $paypalURL = "https://www.paypal.com/cgi-bin/webscr";
  $paypalIPN = "https://www.paypal.com";
  if (!empty($paypal['paypal_email'])) {
    $paypalEmail = $paypal['paypal_email'];
  }
}

if (!empty($err)) {
  $html->ezDie($err);
}

function renderPrice($product) {
  $price = $product['product_price'];
  $currency = $product['mc_currency'];
  $lookup = array('USD' => '&#36;',
      'AUD' => '&#36;',
      'BRL' => '&#82;&#36;',
      'CAD' => '&#36;',
      'CZK' => '&#75;&#269;',
      'DKK' => '&#107;&#114;',
      'EUR' => '&euro;',
      'HKD' => '&#20803;',
      'HUF' => '&#70;&#116;',
      'ILS' => '&#8362;',
      'JPY' => '&yen;',
      'MYR' => '&#82;&#77;',
      'MXN' => '&#36;',
      'NOK' => '&#107;&#114;',
      'NZD' => '&#36;',
      'PHP' => '&#80;&#104;&#11;',
      'PLN' => '&#122;&#322;',
      'GBP' => '&#163;',
      'SGD' => '&#36;',
      'SEK' => '&#107;&#114;',
      'CHF' => '&#67;&#72;&#70;',
      'TWD' => '&#36;',
      'THB' => '&#3647;',
      'TRY' => '&#89;&#84;&#76;');
  $ret = $lookup[$currency] . trim($price);
  return $ret;
}

function renderProduct($product, $showDetails = false) {
  $name = $product['product_name'];
  $displayName = '<em><strong>' . $product['product_name'] . '</strong></em>';
  $code = $product['product_code'];
  $codeQty = 'a' . $code . '_qty';
  $link = "<button onclick='document.form1.buy.value=\"$code\";document.form1.quantity.value=document.form1.$codeQty.value; document.form1.submit();'>" . __("Buy now!", 'easy-paypal') . "</button>";
  $renderedPrice = renderPrice($product);
  if ($showDetails) {
    echo "<center><p>" . sprintf(__("Thank you for considering %s", 'easy-paypal'), $displayName) . "</p></center>";
  }
  else {
    echo "<tr><td>$name</td><td align='center'>$renderedPrice</td><td align='center'><input name='$codeQty' size='3' align='center' value='1'></td><td align='center'>$link</td></tr>\n";
  }
}

if (!empty($_GET['ref'])) {
  $affiliate_id = $_GET['ref'];
  $customLine = "<input type='hidden' name='custom' value='$affiliate_id'>\n";
  $getPar = "&ref=$affiliate_id";
}
else {
  $customLine = '';
  $getPar = '';
}

if (empty($_POST) && empty($_GET['show']) && empty($_GET['buy'])) { // list all items
  $html->ezppHeader(__('ezPayPal Shop', 'easy-paypal'), __("Enter Quantity and Click Buy", 'easy-paypal') . "&nbsp;|&nbsp;<a href='$admin'>" . $adminText . "</a>");
  $products = $ezDB->getData("products");
  sort($products);
  echo '<form name="form1" method="post"><input type="hidden" name="buy"><input type="hidden" name="quantity"><table width="90%" cellpadding="5px" align="center" class="setup">';
  echo "<tr class='title'><td width='60%'>" . __("Product", 'easy-paypal') . "</td><td align='center' width='10%'>" . __("Price", 'easy-paypal') . "</td><td width='10%'>" . __("Quantity", 'easy-paypal') . "</td><td align='center' width='20%'>" . __("Buy?", 'easy-paypal') . "</td></tr>";
  foreach ($products as $p) {
    if ($p['active']) {
      renderProduct($p);
    }
  }
  if ($showUpdateSection && file_exists('pro/ez-update.php')) {
    echo "<tr class='subtitle'><td colspan=4>&nbsp;</td></tr>";
    echo "<tr class='title'><td colspan=4><center><b>" . __("Check for Updates", 'easy-paypal') . "<b></center></td></tr>";
    echo "<tr class='subtitle'><td colspan=4><center>" . __("To retrive the list of updates available to you, please use the button below", 'easy-paypal') . "<center></td></tr>";
    echo "<tr><td colspan=4><p>" . __("The first update to each of the products you purchase is provided free of cost to you. Subsequent product updates can be purchased at \$0.95.", 'easy-paypal') . "</p>
<p><center><a href='pro/ez-update.php'><input type='button' value='" . __("Find Updates", 'easy-paypal') . "'></a></p></td></tr>";
  }
  echo '</table></form>';
  $html->ezppFooter(true);
  return;
}

if (!empty($_POST['update'])) {
  $txnID = $_POST['update'];
}

if (!empty($_POST['buy'])) {
  $productCode = $_POST['buy'];
}
else if (!empty($_GET['buy'])) {
  $productCode = $_GET['buy'];
}

if (!empty($productCode)) { // process a buy or update request
  $product = $ezpp->getProduct($productCode);
  if (empty($product)) {
    $product = array();
    $active = false;
    $product_name = "This product";
  }
  foreach ($product as $k => $v) {
    $$k = $v;
  }
  if ($active) {
    $onload = "";
    if (!$ezDebug) {
      $onload = " onload='ezppBuyForm.submit();'";
    }
    echo "<body$onload>\n";
    if (!empty($_POST['quantity'])) {
      $quantity = $_POST['quantity'];
    }
    else if (!empty($_GET['quantity'])) {
      $quantity = $_GET['quantity'];
    }
    else {
      $quantity = 1;
    }
    if (!empty($txnID)) { // verify an update request
      $sale = $ezDB->getSaleRow("sales", $txnID);
      if (!empty($sale)) {
        $amount = "0.95";
      }
      $update = "Update";
    }
    else {
      $amount = $product_price;
      $update = "&nbsp;$quantity license(s)";
    }
    $form = "<form name='ezppBuyForm' id='ezppBuyForm' action='$paypalURL' method='post'>
<input type='hidden' name='cmd' value='_xclick'>
<input type='hidden' name='business' value='$paypalEmail'>
<input type='hidden' name='item_name' value='$product_name'>
<input type='hidden' name='item_number' value='$product_code'>
<input type='hidden' name='amount' value='$amount'>
<input type='hidden' name='quantity' value='$quantity'>
<input type='hidden' name='no_shipping' value='$no_shipping'>
<input type='hidden' name='return' value='$returnPage'>
<input type='hidden' name='notify_url' value='$ipnListener'>
<input type='hidden' name='no_note' value='1'>
<input type='hidden' name='currency_code' value='$mc_currency'>
<input type='hidden' name='rm' value='2'>
$customLine";
    echo $form;
    $renderedPrice = renderPrice($product);
    echo "<h3 align='center'>$product_name</h3>\n";
    echo "<h4 align='center'>$renderedPrice</h4>\n";
    echo '<hr /><h4 align="center">' . __('Please wait while we transfer you to Paypal.', 'easy-paypal') . '</h4>';
    if (EzPP::isWP()) {
      echo "<div align='center'>
    <p>" . __("If you are not transfered in ten seconds, please click the button below", 'easy-paypal') . "</p>
    <input type='submit' value='" . __("Proceed to PayPal", 'easy-paypal') . "'></div>
    </div>";
    }
    if ($ezDebug) {
      $formSlashed = htmlspecialchars($form);
      echo "<h4 align='center'>" . __("Debug Mode", 'easy-paypal') . "</h4>
    <br />
    <div align='center'>
    <input type='button' value='" . __("View PayPal From", 'easy-paypal') . "' onclick='document.getElementById(\"formHidden\").style.display=\"block\";'>
    <input type='submit' value='" . __("Proceed to PayPal", 'easy-paypal') . "'></div>
    <div id='formHidden' style='display:none'><pre>$formSlashed
    </pre>
    </div>";
    }
    echo "</form>";
    echo "</body>\n";
    exit();
  }
  else {
    $html->setErr("Product not found.");
    header("refresh:5;url=$shop");
    $html->ezppHeader($product_name, sprintf(__("Thank You for Considering %s", 'easy-paypal'), $product_name), '.', true);
    echo __("This product is not ready for sale right now. Please come back later.", 'easy-paypal');
    printf('<center><input type="button" id="stop" value="' . __('Browse Products', 'easy-paypal') . '" onclick="window.location=\'$shop\';"></center>');
    $html->ezppFooter();
    return;
  }
}

if (!empty($_POST['show'])) {
  $productCode = $_POST['show'];
}
else if (!empty($_GET['show'])) {
  $productCode = $_GET['show'];
}

if (!empty($productCode)) { // dispaly the requested prod and post a buy on a timeout
  $product = $ezpp->getProduct($productCode);
  $active = false;
  $product_name = __("This Product", 'easy-paypal');
  if (is_array($product)) {
    foreach ($product as $k => $v) {
      $$k = $v;
    }
    $meta = $ezDB->getMetaData("product_meta", $product);
    $pp = array_merge($product, $meta);
  }
  else {
    $pp = $product;
  }
  $html->ezppHeader($product_name, sprintf(__("Thank You for Considering %s", 'easy-paypal'), $product_name), '.', true);
  if ($pp['active']) {
    renderProduct($pp, true);
    $renderedPrice = renderPrice($product);
    $s1 = sprintf(__("You will be redirected to PayPal to buy %s (for %s) in a few seconds.", 'easy-paypal'), "<i>$product_name</i>", $renderedPrice);
    $s2 = __("Time remaining:", 'easy-paypal');
    $s3 = __("Stop Countdown", 'easy-paypal');
    echo <<<ENDJS
    <form name="redirect">
    <center>
    <font face="Arial"><b>$s1<br><br>
    $s2&nbsp;<input type="text" size="3" name="redirect2" style="text-align:center">
    </form>
    seconds</b></font>
    <br />
    <br />
    <input type="button" id="stop" value="$s3" onclick="stopIt()">
    </center>

    <script>
    <!--
    var targetURL="{$shop}?buy=$productCode$getPar"
    var countdownfrom=15
    var increment=1

    var currentsecond=document.redirect.redirect2.value=countdownfrom+1
    function countredirect(){
       if (currentsecond!=1){
         currentsecond-=increment
         document.redirect.redirect2.value=currentsecond
       }
       else{
         window.location=targetURL
         return
       }
       setTimeout("countredirect()",1000)
    }
    function stopIt() {
      if (increment == 0) {
         window.location=targetURL
      }
      else {
        increment=0
        stopBtn = document.getElementById("stop")
        stopBtn.value = "Go to PayPal to buy it!"
        stopBtn.style.color="red"
      }
    }

    countredirect()
    //-->
    </script>
ENDJS;
  }
  else {
    echo __("This product is not on sale right now.<br /> Please come back and visit us later to look for it.", 'easy-paypal');
    printf('<center><input type="button" id="stop" value="' . __('Browse Products', 'easy-paypal') . '" onclick="window.location=\'$shop\';"></center>');
  }
  $html->ezppFooter();
  return;
}
else {
  $html->setErr(__("Product not found.", 'easy-paypal'));
  header("refresh:5;url=$shop");
  $html->ezppHeader(__('ezPayPal Shop', 'easy-paypal'), __('Please come back later', 'easy-paypal'));
  if (!empty($productCode)) {
    $seeking = sprintf(__("(under the name/key %s)", 'easy-paypal'), "<b>$productCode</b>");
  }
  else {
    $seeking = "";
  }
  echo sprintf(__("The product you are looking for %s is not ready for sale yet.<br /> Please come back and visit us later to look for it.", 'easy-paypal'), $seeking);
  printf('<center><input type="button" id="stop" value="' . __('Browse Products', 'easy-paypal') . '" onclick="window.location=\'$shop\';"></center>');
  $html->ezppFooter();
  return;
}
