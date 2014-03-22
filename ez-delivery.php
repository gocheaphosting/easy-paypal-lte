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

function deliverFile($saleInfo, $product, &$ezpp) {
  if ($ezpp->mkDateInt($saleInfo['expire_date']) < time()){
    return  "Sorry, your purchase has expired." ;
  }
  if (substr($product['file'], 0, 1) == '/' ||
      substr($product['file'], 1, 2) == ':') {
    $file = $product['file'] ;
  }
  else {
    $file = dirname(__FILE__) . '/' . $product['file'] ;
  }
  if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$product['filename']);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    @ob_clean(); // Don't let it throw any notices, which will mess up the zip file
    flush();
    readfile($file);
  }
  else return "Error: File not found." ;
}

if (!empty($GLOBALS["ezPayPal"]) && is_a($GLOBALS["ezPayPal"], "ezPayPal")) {
  $shop = "ez-shop" ;
  $ezppURL = get_option('siteurl') . '/' ;
  $returnPage = "$ezppURL$shop?delivery" ;
  $targetURL = "$returnPage&txn_id=" ;
}
else {
  $ezppURL = $ezpp->ezppURL() ;
  $returnPage = $ezppURL . 'ez-delivery.php' ;
  $targetURL = "$returnPage?txn_id=" ;
}

$options = $ezDB->getRowData("options") ;
$pageHtml = "<h4>Thank you for your purchase</h4><br />
Unfortunately, there is a technical problem with your purchase. Most likely, your purchase details haven't been posted by PayPal yet. But fear not, you can retrieve the download/service link below.<br /><br />" ;
$pageHtml .= "<font color='red'><p>Please enter your <b>PayPal email address</b> below:<p><center><form action='$returnPage' method='get'><input type='hidden' name='delivery'><input type='text' size='20' name='txn_id'>&nbsp;<input type='submit' id='download' value='Retrieve Download/Service Link'></form>
    </center></p><p>Plese be sure to use the <b>PayPal email address</b>, where you got the mail with the subject 'Receipt for Your Payment to {$options['support_name']}'.<p></font>";
$pageHtml .= "<p>If you haven't received the PayPal message, please wait. You will soon receive it and an email from me ({$options['support_email']}) with the download/service/support link. If you don't find either in your Inbox in the next five minutes or so, please be sure to check your Junk/Spam folders as well.</p><p>You can also <a href='mailto:{$options['support_email']}'>contact me</a> for the download link.</p>" ;
$justDoIt = false ;

if (!empty($_GET['dl'])) {
  $txn_id = $_GET['dl'] ;
  $saleInfo = $ezpp->getSale($txn_id) ;
  if (empty($saleInfo)) {
    $html->setErr("Purchase Details Not Found. <br /><a href='mailto:{$options['support_email']}'>Contact Support</a>.");
  }
  else {
    $product = $ezpp->getProduct($saleInfo['product_code']) ;
    $deliveryStatus = deliverFile($saleInfo, $product, $ezpp) ;
    if ($deliveryStatus) $html->setErr($deliveryStatus) ;
    else $html->setInfo("Thank you for your purhcase.") ;
  }
}
else {
  if (!empty($_POST['txn_id'])) { // PayPal PDT. Verify against IPN in DB
    $txn_id = $_POST['txn_id'] ;
    $saleDetails = $_POST ;
    $saleInfo = $ezpp->mkSaleInfo($saleDetails,
                false, // $paypal['sandbox_mode']: don't care
                1.0,   // $product['version']: don't care
                $options['expire_hours']) ;
    $saleInfoDB = $ezpp->getSale($txn_id) ;
    $justDoIt = $ezpp->validateSale($saleInfo, $saleInfoDB) ;
  }
  else if (!empty($_GET['txn_id'])) {
    $txn_id = $_GET['txn_id'] ;
    $saleInfoDB = $ezpp->getSale($txn_id) ;
    if (empty($saleInfoDB) || !$ezpp->validateEmail($txn_id)) {
      $html->setErr("Your email doesn't look right. <a href='$returnPage'>Please try again</a>!") ;
      $justDoIt = false ;
    }
    else {
      $justDoIt = true ;
    }
  }
  if ($justDoIt)
    if (strtolower($txn_id) != strtolower($saleInfoDB['txn_id']) &&
      strtolower($txn_id) != strtolower($saleInfoDB['customer_email'])) {
      // last line of defense
      $html->setErr("Looks like SQL injection. Rejected!") ;
      $justDoIt = false ;
    }
  if ($justDoIt) {
    $product = $ezpp->getProduct($saleInfoDB['product_code']) ;
    $pageHtml = $ezpp->procTemplate('download_page', $saleInfoDB, $product, $options) ;
  }
}

$html->ezppHeader('Thank you for your purchase', 'Please download your purchase', '.', true) ;
printf("<p>%s</p>", $pageHtml) ;
$html->ezppFooter(true) ;
