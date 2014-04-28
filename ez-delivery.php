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

function deliverFile($saleInfo, $product, &$ezpp) {
  if ($ezpp->mkDateInt($saleInfo['expire_date']) < time()) {
    return "Sorry, your purchase has expired.";
  }
  if (substr($product['file'], 0, 1) == '/' ||
          substr($product['file'], 1, 2) == ':') {
    $file = $product['file'];
  }
  else {
    $file = dirname(__FILE__) . '/' . $product['file'];
  }
  if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $product['filename']);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    @ob_clean(); // Don't let it throw any notices, which will mess up the zip file
    flush();
    readfile($file);
  }
  else {
    return __("Error: File not found.", 'easy-paypal');
  }
}

if (!empty($GLOBALS["ezPayPal"]) && is_a($GLOBALS["ezPayPal"], "ezPayPal")) {
  $shop = "ez-shop";
  $ezppURL = get_option('siteurl') . '/';
  $returnPage = "$ezppURL$shop?delivery";
  $targetURL = "$returnPage&txn_id=";
}
else {
  $ezppURL = $ezpp->ezppURL();
  $returnPage = $ezppURL . 'ez-delivery.php';
  $targetURL = "$returnPage?txn_id=";
}

$options = $ezDB->getRowData("options");
$pageHtml = "<h4>" . __("Thank you for your purchase", 'easy-paypal') . "</h4><br />" . __("Unfortunately, there is a technical problem with your purchase. Most likely, your purchase details haven't been posted by PayPal yet. But fear not, you can retrieve the download/service link below.", 'easy-paypal') . "<br /><br />";
$pageHtml .= "<font color='red'><p>" . __("Please enter your <b>PayPal email address</b> below:", 'easy-paypal') . "<p><center><form action='$returnPage' method='get'><input type='hidden' name='delivery'><input type='text' size='20' name='txn_id'>&nbsp;<input type='submit' id='download' value='" . __("Retrieve Download/Service Link", 'easy-paypal') . "'></form></center></p><p>" . sprintf(__("Plese be sure to use the <b>PayPal email address</b>, where you got the mail with the subject 'Receipt for Your Payment to %s'.", 'easy-paypal'), $options['support_name']) . "<p></font><p>" . __("If you haven't received the PayPal message, please wait.", 'easy-paypal') . " " . sprintf(__("You will soon receive it and an email from me (%s) with the download/service/support link.", 'easy-paypal'), $options['support_name']) . " " . __("If you don't find either in your Inbox in the next five minutes or so, please be sure to check your Junk/Spam folders as well.", 'easy-paypal') . "</p><p>" . sprintf(__("You can also %s contact me%s for the download link.", 'easy-paypal'), "<a href='mailto:{$options['support_email']}'>", "</a>") . "</p>";
$justDoIt = false;
if (!empty($_GET['dl'])) {
  $txn_id = $_GET['dl'];
  $saleInfo = $ezpp->getSale($txn_id);
  if (empty($saleInfo)) {
    $html->setErr(__("Purchase Details Not Found.", 'easy-paypal') . " <br /><a href='mailto:{$options['support_email']}'>" . __("Contact Support", 'easy-paypal') . "</a>.");
  }
  else {
    $product = $ezpp->getProduct($saleInfo['product_code']);
    $deliveryStatus = deliverFile($saleInfo, $product, $ezpp);
    if ($deliveryStatus) {
      $html->setErr($deliveryStatus);
    }
    else {
      $html->setInfo(__("Thank you for your purhcase.", 'easy-paypal'));
    }
  }
}
else {
  if (!empty($_POST['txn_id'])) { // PayPal PDT. Verify against IPN in DB
    $txn_id = $_POST['txn_id'];
    $saleDetails = $_POST;
    $saleInfo = $ezpp->mkSaleInfo($saleDetails, false, // $paypal['sandbox_mode']: don't care
            1.0, // $product['version']: don't care
            $options['expire_hours']);
    $saleInfoDB = $ezpp->getSale($txn_id);
    $justDoIt = $ezpp->validateSale($saleInfo, $saleInfoDB);
  }
  else if (!empty($_GET['txn_id'])) {
    $txn_id = $_GET['txn_id'];
    $saleInfoDB = $ezpp->getSale($txn_id);
    if (empty($saleInfoDB) || !$ezpp->validateEmail($txn_id)) {
      $html->setErr(__("Your email doesn't look right.", 'easy-paypal') . " <a href='$returnPage'>" . __("Please try again.", 'easy-paypal') . "</a>");
      $justDoIt = false;
    }
    else {
      $justDoIt = true;
    }
  }
  if ($justDoIt) {
    if (strtolower($txn_id) != strtolower($saleInfoDB['txn_id']) &&
            strtolower($txn_id) != strtolower($saleInfoDB['customer_email'])) {
      // last line of defense
      $html->setErr(__("Looks like SQL injection. Rejected!", 'easy-paypal'));
      $justDoIt = false;
    }
  }
  if ($justDoIt) {
    $product = $ezpp->getProduct($saleInfoDB['product_code']);
    $pageHtml = $ezpp->procTemplate('download_page', $saleInfoDB, $product, $options);
  }
}

$html->ezppHeader(__('Thank you for your purchase', 'easy-paypal'), __('Please download your purchase', 'easy-paypal'), '.', true);
printf("<p>%s</p>", $pageHtml);
$html->ezppFooter(true);
