<?php
include_once('htmlHelper.php') ;
include_once('dbHelper.php');
include_once('ezpp.php') ;

$html = new htmlHelper() ;
$ezDB = new dbHelper();
$ezDB->doOrDie($html) ;
$ezpp = new ezpp($ezDB) ;

if (!empty($GLOBALS['ezDebug'])) $ezDebug = $GLOBALS['ezDebug'] ;
else $ezDebug = false ;

if (!empty($GLOBALS["ezPayPal"]) && is_a($GLOBALS["ezPayPal"], "ezPayPal")) {
  $shop = "ez-shop" ;
  $ezppURL = get_option('siteurl') . '/' ;
  $admin = $ezppURL."wp-admin/options-general.php?page=easy-paypal-lite.php" ;
  $returnPage = "$ezppURL$shop?delivery" ;
  $ipnListener = "$ezppURL$shop?office" ;
}
else {
  $admin = 'admin.php' ;
  $shop = 'ez-shop.php' ;
  $ezppURL = $ezpp->ezppURL() ;
  $returnPage = $ezppURL . 'ez-delivery.php' ;
  $ipnListener = $ezppURL . 'ez-office.php' ;
}

$paypal = $ezDB->getRowData('paypal') ;
if (empty($paypal)) $err = "ezPayPal is not configured correctly. No PayPal info! &nbsp; <a href='$admin'><input type='button' value='Go to Admin Page' name='setup'></a>\n" ;
if (!empty($paypal['sandbox_mode']) && $paypal['sandbox_mode']) {
  $paypalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
  $paypalIPN = "https://www.sandbox.paypal.com";
  $paypalEmail = $paypal['sandbox_email'] ;
}
else {
  $paypalURL = "https://www.paypal.com/cgi-bin/webscr";
  $paypalIPN = "https://www.paypal.com";
  if (!empty($paypal['paypal_email'])) $paypalEmail = $paypal['paypal_email'] ;
}

if (!empty($err)) $html->ezDie($err) ;

function renderProduct($product, $showDetails=false) {
  $name = $product['product_name'] ;
  $displayName = '<em><strong>'.$product['product_name'].'</strong></em>';
  $code = $product['product_code'] ;
  $codeQty = 'a' . $code. '_qty' ;
  $link = "<button onclick='document.form1.buy.value=\"$code\";document.form1.quantity.value=document.form1.$codeQty.value; document.form1.submit();'>Buy now!</button>" ;
  $price = $product['product_price'] ;
  if ($showDetails) {
    echo "<center><p>Thank you for considering $displayName</p></center>" ;
  }
  else {
    echo "<tr><td>$name</td><td align='center'>$price".$product['mc_currency']."</td><td align='center'><input name='$codeQty' size='3' align='center' value='1'></td><td align='center'>$link</td></tr>\n" ;
  }
}

$browser = $_SERVER["HTTP_USER_AGENT"];

if (!empty($_GET['ref'])) {
  $affiliate_id = $_GET['ref'] ;
  $customLine = "<input type='hidden' name='custom' value='$affiliate_id'>\n" ;
  $getPar = "&ref=$affiliate_id" ;
}
else {
  $customLine = '' ;
  $getPar = '' ;
}

if (empty($_POST) && empty($_GET['show']) && empty($_GET['buy'])) { // list all items
  $html->ezppHeader('ezPayPal Shop', "Enter Quantity and Click Buy&nbsp;|&nbsp;<a href='$admin'>Admin</a>") ;
  $products = $ezDB->getData("products") ;
  sort($products) ;
  echo '<form name="form1"><input type="hidden" name="buy"><input type="hidden" name="quantity"><table width="90%" cellpadding="5px" align="center" class="setup">' ;
    echo "<tr class='title'><td width='55%'>Product</td><td align='center' width='15%'>Price</td><td width='10%'>Quantity</td><td align='center' width='20%'>Buy?</td></tr>" ;
  foreach ($products as $p) {
    if ($p['active']) renderProduct($p) ;
  }
  if (file_exists('pro/ez-update.php')) {
    echo "<tr class='subtitle'><td colspan=4>&nbsp;</td></tr>" ;
    echo "<tr class='title'><td colspan=4><center><b>Check for Updates<b></center></td></tr>" ;
   echo "<tr class='subtitle'><td colspan=4><center>To retrive the list of updates available to you, please use the button below<center></td></tr>" ;
    echo "<tr><td colspan=4><p>The first update to each of the products you purchase is provided free of cost to you. Subsequent product updates can be purchased at \$0.95.</p>
<p><center><a href='pro/ez-update.php'><input type='button' value='Find Updates'></a></p></td></tr>" ;
  }
  echo '</table></form>' ;
  $html->ezppFooter(true) ;
  return ;
}

if (!empty($_POST['update'])) $txnID = $_POST['update'] ;

if (!empty($_POST['buy'])) $productCode = $_POST['buy'] ;
else if (!empty($_GET['buy'])) $productCode = $_GET['buy'] ;

if (!empty($productCode)) { // process a buy or update request
  $product = $ezpp->getProduct($productCode) ;
  if (empty($product)) {
    $product = array() ;
    $active = false ;
    $product_name = "This product" ;
  }
  foreach ($product as $k => $v) $$k = $v ;
  if ($active) {
    $onload = "" ;
    if (!$ezDebug) $onload = " onload='ezppBuyForm.submit();'" ;
    echo "<body$onload>\n" ;
    if (!empty($_POST['quantity'])) $quantity = $_POST['quantity'] ;
    else if (!empty($_GET['quantity'])) $quantity = $_GET['quantity'] ;
    else $quantity = 1 ;
    if (!empty($txnID)) { // verify an update request
      $sale = $ezDB->getSaleRow("sales", $txnID) ;
      if (!empty($sale)) $amount = "0.95" ;
      $update = "Update" ;
    }
    else {
      $amount = $product_price ;
      $update = "&nbsp;$quantity license(s)" ;
    }
    $form = "<form name='ezppBuyForm' action='$paypalURL' method='post'>
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
$customLine" ;
    echo $form ;
    echo "<h3 align='center'>$product_name</h3>\n";
    echo "<h4 align='center'>\$$product_price</h4>\n";
    echo '<hr /><h4 align="center">Please wait while we transfer you to Paypal.</h4>' ;
    if (function_exists('plugins_url')) echo "<div align='center'>
    <p>If you are not transfered in ten seconds, please click the button below</p>
    <input type='submit' value='Proceed to PayPal'></div>
    </div>" ;
    if ($ezDebug) {
      $formSlashed = htmlspecialchars($form) ;
      echo "<h4 align='center'>Debug Mode</h4>
    <br />
    <div align='center'>
    <input type='button' value='View PayPal From' onclick='document.getElementById(\"formHidden\").style.display=\"block\";'>
    <input type='submit' value='Proceed to PayPal'></div>
    <div id='formHidden' style='display:none'><pre>$formSlashed
    </pre>
    </div>" ;
    }
    echo "</form>" ;
    echo "</body>\n" ;
    exit() ;
  }
  else {
    $html->setErr("Product not found.") ;
    header("refresh:5;url=$shop") ;
    $html->ezppHeader($product_name, "Thank You for Considering $product_name", '.', true) ;
    echo "This product is not ready for sale right now. Please come back later." ;
    printf('<center><input type="button" id="stop" value="Browse Products" onclick="window.location=\'$shop\';"></center>') ;
    $html->ezppFooter() ;
    return ;
  }
}

if (!empty($_POST['show'])) $productCode = $_POST['show'] ;
else if (!empty($_GET['show'])) $productCode = $_GET['show'] ;

if (!empty($productCode)) { // dispaly the requested prod and post a buy on a timeout
  $product = $ezpp->getProduct($productCode) ;
  $active = false ;
  $product_name = "This Product" ;
  if (is_array($product)) {
    foreach ($product as $k => $v) $$k = $v ;
    $meta = $ezDB->getMetaData("product_meta", $product) ;
    $pp = array_merge($product, $meta) ;
  }
  else {
    $pp = $product ;
  }
  $html->ezppHeader($product_name, "Thank You for Considering $product_name", '.', true) ;
  if ($pp['active']) {
    renderProduct($pp, true) ;
    echo <<<ENDJS
    <form name="redirect">
    <center>
    <font face="Arial"><b>You will be redirected to PayPal to buy <i>$product_name</i> (for \$$product_price) in<br><br>
    <input type="text" size="3" name="redirect2" style="text-align:center">
    </form>
    seconds</b></font>
    <br />
    <br />
    <input type="button" id="stop" value="Stop Countdown" onclick="stopIt()">
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
    echo "This product is not on sale right now.<br /> Please come back and visit us later to look for it." ;
    printf('<center><input type="button" id="stop" value="Browse Products" onclick="window.location=\'$shop\';"></center>') ;
  }
  $html->ezppFooter() ;
  return ;
}
else {
  $html->setErr("Product not found.") ;
  header("refresh:5;url=$shop") ;
  $html->ezppHeader('ezPayPal Shop', 'Please come back later') ;
  if (!empty($productCode)) $seeking = "(under the name/key <b>$productCode</b>)" ;
  else $seeking = "" ;
  echo "The product you are looking for $seeking is not ready for sale yet.<br /> Please come back and visit us later to look for it." ;
  printf('<center><input type="button" id="stop" value="Browse Products" onclick="window.location=\'$shop\';"></center>') ;
  $html->ezppFooter() ;
  return ;
}
