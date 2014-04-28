<?php

if (empty($_POST)) {
  return;
}
if (empty($_POST['receiver_email']) && empty($_POST['business'])) {
  return;
}

include_once('htmlHelper.php');
include_once('formHelper.php');
include_once('dbHelper.php');
include_once('ezpp.php');

$html = new HtmlHelper();
$ezDB = new DbHelper();
$ezpp = new EzPP($ezDB);

function verifyTxn($saleDetails, $paypalEmail, $product) {
  $ret = array();
  if (empty($saleDetails['quantity'])) {
    $saleDetails['quantity'] = 1;
  }
  $check = ($saleDetails['receiver_email'] == $paypalEmail ||
          $saleDetails['business'] == $paypalEmail) &&
          ($saleDetails['item_name'] == $product['product_name']) &&
          ($saleDetails['item_number'] == $product['product_code']) &&
          ($saleDetails['mc_currency'] == $product['mc_currency']) &&
          (!empty($saleDetails['txn_id']));
  $msg = sprintf("    receiver_email (or business) =? paypalEmail =>
      {$saleDetails['receiver_email']} (or {$saleDetails['business']}) =? {$paypalEmail} \n
    item_name =? product['product_name']) =>
      {$saleDetails['item_name']} =? {$product['product_name']} \n
    item_number =? product['product_code'] =>
      {$saleDetails['item_number']} =? {$product['product_code']} \n
    mc_currency =? product['mc_currency'] =>
      {$saleDetails['mc_currency']} =? {$product['mc_currency']} \n
    [Either Purchase] mc_gross =? product['product_price']*saleDetails['quantity'] =>
      {$saleDetails['mc_gross']} =? {$product['product_price']}*{$saleDetails['quantity']} \n
    [Or Update] mc_gross =? product['update_price']*saleDetails['quantity'] =>
      {$saleDetails['mc_gross']} =? {$product['product_price']}*{$saleDetails['quantity']} \n
    txn_id =? empty => {$saleDetails['txn_id']}");
  $purchaseCheck = $saleDetails['mc_gross'] == ($product['product_price'] * $saleDetails['quantity']);
  if (empty($product['update_price'])) {
    $product['update_price'] = 0.95;
  }
  $updateCheck = $saleDetails['mc_gross'] == ($product['update_price'] * $saleDetails['quantity']);
  if ($check) {
    if ($purchaseCheck) {
      $msg .= "\n" . __("Product details check PASSES [Purchase]", 'easy-paypal') . "\n";
      $check = $check && $purchaseCheck;
    }
    else if ($updateCheck) {
      $msg .= "\n" . __("Product details check PASSES [Update]", 'easy-paypal') . "\n";
      $check = $check && $updateCheck;
    }
    else {
      $msg .= "\n" . sprintf(__("Product details check FAILS [Price confusion: %s]", 'easy-paypal'), $saleDetails['mc_gross']) . "\n";
      $check = false;
    }
  }
  else {
    $msg .= "\n" . __("Product details check *FAILS*", 'easy-paypal') . "\n";
  }
  $ret['check'] = $check;
  $ret['purchaseCheck'] = $purchaseCheck;
  $ret['updateCheck'] = $updateCheck;
  $ret['msg'] = $msg;
  return $ret;
}

function alertMail(&$ezpp, $from, $to, $subject, $body) {
  $mailHeader = "From: $from\r\n";
  $mailHeader .= "X-Mailer: " . $ezpp->ezppURL() . "\r\n";
  $mailHeader .= "X-Sender-IP: {$_SERVER['REMOTE_ADDR']}\r\n";
  $mailParams = "-f$from";
  mail($to, $subject, $body, $mailHeader, $mailParams);
}

$ezTest = false;
if (!empty($_POST['ezTest'])) {
  $ezTest = $_POST['ezTest'];
  if (isset($_POST['random_txn'])) {
    $_POST['txn_id'] = FormHelper::randString(19);
  }
}

$ezppLog = '';

$dbStatus = $ezDB->link(false);
$ezppLog .= "$dbStatus\n";
if (!empty($dbStatus)) {
  $to = $ezDB->mailTo();
  if (!empty($to)) {
    alertMail($ezpp, $to, $to, __("ezPayPal: DB Error", 'easy-paypal'), $dbStatus);
  }
  exit();
}

$paypal = $ezDB->getRowData('paypal');

if ($paypal['sandbox_mode']) {
  $paypalURL = "www.sandbox.paypal.com/cgi-bin/webscr";
  $paypalIPN = "www.sandbox.paypal.com";
  $paypalEmail = $paypal['sandbox_email'];
  $sandboxMode = true;
}
else {
  $paypalURL = "www.paypal.com/cgi-bin/webscr";
  $paypalIPN = "www.paypal.com";
  $paypalEmail = $paypal['paypal_email'];
  $sandboxMode = false;
}

$mailLogs = !empty($paypal['mail_logs']);

$req = 'cmd=_notify-validate';
foreach ($_POST as $k => $v) {
  $v = urlencode(stripslashes($v));
  $req .= "&$k=$v";
}
$saleDetails = $_POST;
$ezppLog .= "\n" . __("POST DATA [Sale Details Table Entry]", 'easy-paypal') . "\n";
$ezppLog .= $ezpp->putSaleDetails($saleDetails);
$options = $ezDB->getRowData("options");

$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n";
$header .= "Host: $paypalIPN\r\n";
$header .= "Connection: close\r\n\r\n";

$fp = fsockopen('ssl://' . $paypalIPN, 443, $errno, $errstr, 30);
if (!$fp) {
  $dbStatus = __("Failed POST Back to PayPal.", 'easy-paypal') . " [$errno: $errstr]";
  $ezppLog .= "$dbStatus\n";
}
else {
  $dbStatus = __("Opened POST Back Socket to PayPal.", 'easy-paypal');
  $ezppLog .= "$dbStatus\n";
  $written = fwrite($fp, $header . $req);
  if ($written) {
    if (empty($ezTest)) {
      $res = stream_get_contents($fp);
      fclose($fp);
    }
    else {
      $res = $ezTest;
    }
    $pos = strpos($res, "VERIFIED");
    if ($pos !== false) {
      $dbStatus = __("PayPal IPN VERIFIED.", 'easy-paypal');
      $ezppLog .= "$dbStatus\n";
      $product = $ezpp->getProduct($saleDetails["item_number"]);
      $ezppLog .= "\n" . __("PRODUCT DETAILS CHECK", 'easy-paypal') . "\n";
      $verifyTxn = @verifyTxn($saleDetails, $paypalEmail, $product);
      if ($verifyTxn['check'] && ($verifyTxn['purchaseCheck'] || $verifyTxn['updateCheck']) && $saleDetails['payment_status'] == 'Completed') {
        $dbStatus = __("Transaction Verification done.", 'easy-paypal') . " " . __("[Either purchase or update]", 'easy-paypal');
        $ezppLog .= "$dbStatus\n";
        if ($verifyTxn['purchaseCheck']) {
          $ezppLog .= "\n" . __("IPN INFO [Sales Table Entry]", 'easy-paypal') . "\n";
          if (!empty($product['expire_hours'])) {
            $expire_hours = $product['expire_hours'];
          }
          else {
            $expire_hours = $options['expire_hours'];
          }
          $saleInfo = $ezpp->mkSaleInfo($saleDetails, $sandboxMode, $product['version'], $expire_hours);
          $ezppLog .= $ezpp->putSale($saleInfo) . "\n";
          if (!empty($options['html_email']) && $options['html_email']) {
            $emailResults = $ezpp->emailCustomerHtml($saleInfo, $product, $options) . "\n";
          }
          else {
            $emailResults = $ezpp->emailCustomer($saleInfo, $product, $options) . "\n";
          }
          if (!empty($emailResults)) {
            $ezppLog .= "Email sent.";
          }
          else {
            $ezppLog .= $emailResults;
            $mailLogs = true;
          }
        }
        if ($verifyTxn['updateCheck']) {
          $ezppLog .= "\nHandling Update: \n";
          $ezppLog .= "Error: Update Request with no update handler (lite edition)\n";
          $mailLogs = true;
        }
      }
      else {
        $dbStatus = "Purchase details ERRORS!";
        $ezppLog .= "$dbStatus\n";
        $ezppLog .= $verifyTxn['msg'];
        $ezppLog .= "Purchase does not match product details or TXN_ID missing\n";
        $mailLogs = true;
      }
    }
    else {
      $pos = strpos($res, "INVALID");
      $mailLogs = true;
      if ($pos !== false) {
        $dbStatus = __("PayPal Post Back returns INVALID.", 'easy-paypal');
        $ezppLog .= "$dbStatus\n";
      }
      else {
        $dbStatus = sprintf(__("PayPal Post Back returns %x: not handled.", 'easy-paypal'), $res);
        $ezppLog .= "$dbStatus\n";
      }
    }
  }
}
$saleDetails['dbStatus'] = $dbStatus;
$ezDebug = !empty($GLOBALS['ezDebug']) && $GLOBALS['ezDebug'];
if ($ezDebug || $mailLogs) {
  alertMail($ezpp, $options['support_email'], $options['support_email'], $dbStatus, $ezppLog);
  echo "Move on...! Nothing to see here.";
  $fpx = @fopen('ezpplog.txt', 'r+');
  if ($fpx) {
    fwrite($fpx, $ezppLog);
    fclose($fpx);
  }
}
if ($ezTest) {
  $html->ezppHeader(__('ezPayPal Back Office', 'easy-paypal'), __('Set Debug/Testing off in Production!', 'easy-paypal'));
  echo "<h4>" . __("Debug Mode. Here are IPN data and transaction result.", 'easy-paypal') . "</h4>";
  echo "<h4>$dbStatus</h4>\n";
  echo "<pre>\n";
  echo $ezppLog;
  echo "</pre>\n";
  $html->ezppFooter();
}
