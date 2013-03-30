<?php
include_once('htmlHelper.php') ;
include_once('formHelper.php') ;
include_once('dbHelper.php');
include_once('ezpp.php') ;

$isPro = file_exists("pro/pro.php") ;
if ($isPro) {
  if (file_exists('pro/dispute.php')) include_once('pro/dispute.php') ;
  if (file_exists('pro/ezaffiliates/ezaffiliates.php')) @include_once('pro/ezaffiliates/ezaffiliates.php') ;
}

$html = new htmlHelper() ;
$ezDB = new dbHelper();
$ezpp = new ezpp($ezDB) ;

function verifyTxn($saleDetails, $paypalEmail, $product) {
  $ret = array() ;
  if (empty($saleDetails['quantity'])) $saleDetails['quantity'] = 1 ;
  $check = ($saleDetails['receiver_email'] == $paypalEmail ||
          $saleDetails['business'] == $paypalEmail) &&
    ($saleDetails['item_name'] == $product['product_name']) &&
    ($saleDetails['item_number'] == $product['product_code']) &&
    ($saleDetails['mc_currency'] == $product['mc_currency']) &&
    (!empty($saleDetails['txn_id'])) ;
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
    txn_id =? empty => {$saleDetails['txn_id']}") ;
  $purchaseCheck =
    $saleDetails['mc_gross'] == ($product['product_price']*$saleDetails['quantity']) ;
  if (empty($product['update_price'])) $product['update_price'] = 0.95 ;
  $updateCheck =
    $saleDetails['mc_gross'] == ($product['update_price']*$saleDetails['quantity']) ;
  if ($check) {
    if ($purchaseCheck) {
      $msg .= "\nProduct details check PASSES [Purchase]\n" ;
      $check = $check && $purchaseCheck ;
    }
    else if ($updateCheck) {
      $msg .= "\nProduct details check PASSES [Update]\n" ;
      $check = $check && $updateCheck ;
    }
    else  {
      $msg .= "\nProduct details check FAILS [Price confusion: {$saleDetails['mc_gross']}]\n" ;
      $check = false ;
    }
  }
  else  $msg .= "\nProduct details check *FAILS*\n" ;
  $ret['check'] = $check ;
  $ret['purchaseCheck'] = $purchaseCheck ;
  $ret['updateCheck'] = $updateCheck ;
  $ret['msg'] = $msg ;
  return $ret ;
}

function alertMail(&$ezpp, $from, $to, $subject, $body) {
  $mailHeader  = "From: $from\r\n";
  $mailHeader .= "X-Mailer: ".$ezpp->ezppURL()."\r\n";
  $mailHeader .= "X-Sender-IP: {$_SERVER['REMOTE_ADDR']}\r\n";
  $mailParams = "-f$from";
  mail($to, $subject, $body, $mailHeader, $mailParams) ;
}
$ezTest = false ;
if (!empty($_POST['ezTest'])) {
  $ezTest = $_POST['ezTest'] ;
  if (isset($_POST['random_txn'])) $_POST['txn_id'] = formHelper::randString(19) ;
}

$ezppLog = '' ;

$dbStatus = $ezDB->link(false) ;
$ezppLog .= "$dbStatus\n";
if (!empty($dbStatus)) {
  $to = $ezDB->mailTo() ;
  if (!empty($to)) alertMail($ezpp, $to, $to, "ezPayPal: DB Error", $dbStatus) ;
  exit() ;
}

$paypal = $ezDB->getRowData('paypal') ;

if ($isPro && $paypal['sandbox_mode']) {
  $paypalURL = "www.sandbox.paypal.com/cgi-bin/webscr";
  $paypalIPN = "www.sandbox.paypal.com";
  $paypalEmail = $paypal['sandbox_email'] ;
  $sandboxMode = true ;
}
else {
  $paypalURL = "www.paypal.com/cgi-bin/webscr";
  $paypalIPN = "www.paypal.com";
  $paypalEmail = $paypal['paypal_email'] ;
  $sandboxMode = false ;
}

$mailLogs = $isPro && !empty($paypal['mail_logs']) && $paypal['mail_logs'] ;

$req = 'cmd=_notify-validate';
foreach ($_POST as $k => $v) {
  $v = urlencode(stripslashes($v));
  $req .= "&$k=$v";
}
$saleDetails = $_POST ;
$ezppLog .= "\nPOST DATA [Sale Details Table Entry]\n" ;
$ezppLog .= $ezpp->putSaleDetails($saleDetails) ;
$options = $ezDB->getRowData("options") ;

$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

$fp = fsockopen ('ssl://'.$paypalIPN, 443, $errno, $errstr, 30);
if (!$fp) {
  $dbStatus = "Failed POST Back to PayPal. [Socket not open]" ;
  $ezppLog .= "$dbStatus\n";
}
else {
  $dbStatus = "Opened POST Back Socket to PayPal." ;
  $ezppLog .= "$dbStatus\n";
  $written = fwrite($fp, $header . $req);
  if ($written) {
    if (empty($ezTest)) {
      $res = stream_get_contents($fp) ;
      fclose ($fp);
    }
    else $res = $ezTest ;
    $pos = strpos($res, "VERIFIED") ;
    if ($pos !== false) {
      $dbStatus = "PayPal IPN VERIFIED." ;
      $ezppLog .= "$dbStatus\n";
      $product = $ezpp->getProduct($saleDetails["item_number"]) ;
      $ezppLog .= "\nPRODUCT DETAILS CHECK\n";
      $verifyTxn = @verifyTxn($saleDetails, $paypalEmail, $product) ;
      if ($verifyTxn['check']
        && ($verifyTxn['purchaseCheck'] || $verifyTxn['updateCheck'])
        && $saleDetails['payment_status'] == 'Completed') {
        $dbStatus = "Transaction Verification done. [Either purchase or update]" ;
        $ezppLog .= "$dbStatus\n";
        if ($verifyTxn['purchaseCheck']) {
          $ezppLog .= "\nIPN INFO [Sales Table Entry]\n";
          if (!empty($product['expire_hours'])) $expire_hours = $product['expire_hours'] ;
          else $expire_hours = $options['expire_hours'] ;
          $saleInfo = $ezpp->mkSaleInfo($saleDetails, $sandboxMode,
                      $product['version'], $expire_hours) ;
          if (function_exists('mkStatus'))
            $saleInfo['purchase_status'] = mkStatus($saleDetails) ;
          $ezppLog .= $ezpp->putSale($saleInfo) . "\n" ;
          if (!empty($options['html_email']) && $options['html_email'])
            $emailResults = $ezpp->emailCustomerHtml($saleInfo, $product, $options) . "\n" ;
          else
            $emailResults = $ezpp->emailCustomer($saleInfo, $product, $options) . "\n" ;
          if (!empty($emailResults)) $ezppLog .= "Email sent.";
          else {
            $ezppLog .= $emailResults ;
            $mailLogs = true ;
          }
          if (function_exists('putAffiliateSale')) {
            $ezppLog .= "\nAffiliate Sale Insert: \n" ;
            $ezppLog .= @putAffiliateSale($saleInfo, $saleDetails) ;
          }
        }
        if ($verifyTxn['updateCheck']) {
            $ezppLog .= "\nHandling Update: \n" ;
          if (function_exists('handleUpdate')) $ezppLog .= handleUpdate($saleDetails) ;
          else  {
            $ezppLog .= "Error: Update Request with no update handler (lite edition)\n"  ;
            $mailLogs = true ;
          }
        }
      }
      else {
        if (function_exists('handleDispute')) {
          $ezppLog .= "Txn Verification Status = {$verifyTxn['check']}\n" ;
          $ezppLog .= "Txn Verification Details = {$verifyTxn['msg']}\n" ;
          $ezppLog .= "Pro Dispute Handler Launched.\n";
          $dbStatus = handleDispute($saleDetails) ;
          $ezppLog .= "Dispute Handler says: $dbStatus\n";
        }
        else {
          $dbStatus = "Purchase details ERRORS!" ;
          $ezppLog .= "$dbStatus\n";
          $ezppLog .= $verifyTxn['msg'] ;
          $ezppLog .= "Purchase does not match product details or TXN_ID missing\n";
        }
        $mailLogs = true ;
      }
    }
    else {
      $pos = strpos($res, "INVALID") ;
      if ($pos !== false) {
        $dbStatus = "PayPal Post Back returns INVALID." ;
        $ezppLog .= "$dbStatus\n";
        $mailLogs = true ;
      }
      else {
        $dbStatus = "PayPal Post Back returns $res: not handled." ;
        $ezppLog .= "$dbStatus\n";
        $mailLogs = true ;
      }
    }
  }
}
$saleDetails['dbStatus'] = $dbStatus ;
$ezDebug = !empty($GLOBALS['ezDebug']) && $GLOBALS['ezDebug'] ;
if ($ezDebug || $mailLogs) {
  alertMail($ezpp, $options['support_email'], $options['support_email'], $dbStatus, $ezppLog) ;
  $fpx = @fopen('ezpplog.txt', 'r+');
  if ($fpx) {
    fwrite($fpx, $ezppLog);
    fclose($fpx);
  }
}
if ($ezTest) {
  $html->ezppHeader('ezPayPal Back Office', 'Set Debug/Testing off in Production!') ;
  echo "<h4>Debug Mode. Here are IPN data and transaction result.</h4>";
  echo "<h4>$dbStatus</h4>\n";
  echo "<pre>\n";
  echo $ezppLog;
  echo "</pre>\n";
  $html->ezppFooter() ;
}
?>