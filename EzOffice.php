<?php

require_once 'EZ.php';

class EzOffice {

  var $options, $paypalURL, $paypalHost, $business;
  var $saleDetails = array(), $saleInfo = array(), $product = array();
  private $_debug = false;

  function __construct($debug = false) {
    global $log;
    $this->options = EZ::getOptions();
    if (!empty($this->options['sandbox_mode'])) {
      $this->paypalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
      $this->paypalHost = "www.sandbox.paypal.com";
      $this->business = $this->options['sandbox_email'];
    }
    else {
      $this->paypalURL = "https://www.paypal.com/cgi-bin/webscr";
      $this->paypalHost = "www.paypal.com";
      if (!empty($this->options['paypal_email'])) {
        $this->business = $this->options['paypal_email'];
      }
    }
    $this->_debug = $debug;
    if ($debug) {
      $log->setLevel('DEBUG');
    }
    else {
      $log->setLevel('INFO');
    }
  }

  function __destruct() {

  }

  function EzOffice($debug = false) {
    if (version_compare(PHP_VERSION, "5.0.0", "<")) {
      $this->__construct($debug);
      register_shutdown_function(array($this, "__destruct"));
    }
  }

  function setDebug($debug) {
    $this->_debug = $debug;
  }

  function getSaleDetails() {
    global $log;
    if (empty($this->saleDetails)) { // run it only once
      if (!empty($_POST)) {
        $saleDetails = $_POST;
        if (!empty($saleDetails['payment_date'])) {
          $saleDetails['payment_date'] = EZ::mkDateString($saleDetails['payment_date']);
        }
        if (!empty($saleDetails['payer_email'])) {
          $saleDetails['payer_email'] = strtolower($saleDetails['payer_email']);
        }
        $this->saleDetails = $saleDetails;
      }
      else {
        $log->debug("GetSaleDetails: Empty Sale Deatils.");
        return array();
      }
    }
    return $this->saleDetails;
  }

  function verifyIPN() {
    global $log;
    $saleDetails = $_POST;
    if (empty($saleDetails)) {
      $log->debug("VerifyIPN: Empty sale details.");
      return;
    }
    $req = 'cmd=_notify-validate';
    if (function_exists('get_magic_quotes_gpc')) {
      $get_magic_quotes_exists = get_magic_quotes_gpc() == 1;
    }
    foreach ($saleDetails as $key => $value) {
      if ($get_magic_quotes_exists) {
        $value = urlencode(stripslashes($value));
      }
      else {
        $value = urlencode($value);
      }
      $req .= "&$key=$value";
    }
    $ch = curl_init("$this->paypalURL");
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    $headers = array("Host: $this->paypalHost", "Connection: Close");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($this->_debug) {
      $res = "VERIFIED";
    }
    else {
      $res = curl_exec($ch);
    }
    curl_close($ch);
    if (strcmp($res, "VERIFIED") == 0) {
      $log->info("VerifyIPN: Successfully verified IPN.");
      return true;
    }
    else {
      $log->error("VerifyIPN: IPN verification fails.");
      return false;
    }
  }

  function getProduct() {
    global $log;
    if (empty($this->product)) { // run this only once
      if (!empty($this->saleDetails['item_number'])) {
        $productCode = $this->saleDetails['item_number'];
        $product = EZ::getProduct($productCode);
      }
      else {
        $log->debug("GetProduct: Trying to get a product with no sale details!");
      }
      if (!empty($product)) {
        $this->product = $product;
      }
      else {
        $log->debug("GetProduct: Empty product.");
      }
    }
    return $this->product;
  }

  function txnIdExists() {
    global $log;
    $check = true;
    $saleDetails = $this->getSaleDetails();
    if (empty($saleDetails['txn_id'])) {
      $log->error("Empty transaction ID from PayPal.");
      $check = false;
    }
    return $check;
  }

  function verifyTxn() {
    global $log;
    $saleDetails = $this->getSaleDetails();
    $product = $this->getProduct();
    $check = true;
    if ($saleDetails['receiver_email'] != $this->business &&
            $saleDetails['business'] != $this->business &&
            $saleDetails['business'] != $this->options['second_paypal_email']) {
      $log->error("Receiver email ({$saleDetails['receiver_email']}) and Business ({$saleDetails['business']}) not equal to one of your emails ($this->business or {$this->options['second_paypal_email']})");
      $check = false;
    }
    if ($saleDetails['item_name'] != $product['product_name']) {
      $log->error("Product paid for ({$saleDetails['item_name']}) not the same as sold ({$product['product_name']})");
      $check = false;
    }
    if ($saleDetails['item_number'] != $product['product_code']) {
      $log->error("Product paid for ({$saleDetails['item_number']}) not the same as sold ({$product['product_code']})");
      $check = false;
    }
    if ($saleDetails['mc_currency'] != $product['mc_currency']) {
      $log->error("Purchase currency ({$saleDetails['mc_currency']}) differs from product currency ({$product['mc_currency']})");
      $check = false;
    }
    return $check;
  }

  function isPurchase() {
    $saleDetails = $this->getSaleDetails();
    $product = $this->getProduct();
    $isPurchase = !empty($saleDetails['mc_gross']) &&
            !empty($saleDetails['quantity']) &&
            $saleDetails['mc_gross'] == ($product['product_price'] * $saleDetails['quantity']);
    return $isPurchase;
  }

  function handlePurchase() {
    global $log;
    if (!$this->txnIdExists()) {
      $log->error("Txn Id is needed to handle a purchase. Quitting...");
      return;
    }
    $log->info("Saving Sale...");
    try {
      $this->saveSale();
    } catch (Exception $e) {
      $log->error($e->getMessage());
      return;
    }
    $log->info("Emailing customer...");
    try {
      $this->emailCustomer('email');
    } catch (Exception $e) {
      $log->error($e->getMessage());
    }
    $log->info("Done!");
  }

  function isTxnCompleted() {
    global $log;
    $saleDetails = $this->getSaleDetails();
    $check = true;
    if ($saleDetails['payment_status'] != 'Completed') {
      $log->warn("Payment status says {$saleDetails['payment_status']}. Should be 'Completed'. Possibly a Dispute, Refund, Subscription.");
      $check = false;
    }
    return $check;
  }

  private static function _getSaleMap() {
    $detailsToInfo = array(
        'txn_id' => 'txn_id',
        'business_name' => 'payer_business_name',
        'created' => 'created',
        'customer_email' => 'payer_email',
        'purchase_amount' => 'mc_gross',
        'purchase_status' => 'payment_status',
        'purchase_date' => 'payment_date',
        'product_name' => 'item_name',
        'product_code' => 'item_number',
        'quantity' => 'quantity',
        'affiliate_id' => 'custom');
    return $detailsToInfo;
  }

  function resetExpiry() {
    $product = $this->getProduct();
    if (!empty($product['expire_hours'])) {
      $expireHours = $product['expire_hours'];
    }
    else {
      $expireHours = $this->options['expire_hours'];
    }
    $this->saleInfo['expire_date'] = EZ::mkDateString(time() + $expireHours * 60 * 60);
  }

  function getSaleInfo() {
    global $log;
    if (empty($this->saleInfo)) {
      if (!empty($this->saleDetails['txn_id'])) {
        $saleInfo = EZ::getSaleRow("sales", $this->saleDetails['txn_id']);
      }
      else {
        $log->debug("GetSaleInfo: Trying to get sale info with no sale details!");
      }
      if (empty($saleInfo)) {
        $detailsToInfo = self::_getSaleMap();
        $saleDetails = $this->getSaleDetails();
        if (empty($saleDetails)) { // cannot generate saleInfo
          return $this->saleInfo;
        }
        $product = $this->getProduct();
        $saleInfo = array();
        foreach ($detailsToInfo as $info => $detail) {
          if (!empty($saleDetails[$detail])) {
            $saleInfo[$info] = $saleDetails[$detail];
          }
        }
        $saleInfo['customer_name'] = '';
        if (!empty($saleDetails['first_name'])) {
          $saleInfo['customer_name'] = $saleDetails['first_name'];
        }
        if (!empty($saleDetails['last_name'])) {
          $saleInfo['customer_name'] .= " {$saleDetails['last_name']}";
        }
        $saleInfo['customer_name'] = trim($saleInfo['customer_name']);
        if (!empty($product['id'])) {
          $saleInfo['product_id'] = $product['id'];
        }
        else {
          $log->debug("GetSaleInfo: Sale has no product!");
        }
        if (!empty($product['expire_hours'])) {
          $expireHours = $product['expire_hours'];
        }
        else {
          $expireHours = $this->options['expire_hours'];
        }
        if (empty($saleInfo['purchase_date'])) {
          $saleInfo['purchase_date'] = time();
        }
        $saleInfo['expire_date'] = EZ::mkDateInt($saleInfo['purchase_date']) + ($expireHours * 60 * 60);
        // always return dates as strings
        $saleInfo['expire_date'] = EZ::mkDateString($saleInfo['expire_date']);
        $saleInfo['expire_hours'] = $expireHours;
        if (!empty($product['version'])) {
          $saleInfo['sold_version'] = $product['version'];
        }
        else {
          $saleInfo['sold_version'] = 0.00;
        }
        if (!empty($this->options['sandbox_mode'])) {
          $saleInfo['purchase_mode'] = 'Test';
        }
        else {
          $saleInfo['purchase_mode'] = 'Live';
        }
      }
      $this->saleInfo = $saleInfo;
    }
    return $this->saleInfo;
  }

  function processTxn() {
    global $log;
    $log->info("Verifiying IPN...");
    if (!$this->verifyIPN()) {
      $log->error("IPN verification fails");
      return;
    }
    $log->info("Success!");
    $log->info("Verifying Txn...");
    if (!$this->verifyTxn()) {
      $log->error("Transaction verification fails");
      return;
    }
    if (!$this->isTxnCompleted()) {
      $log->error("Transaction is not completed");
      return;
    }
    $log->info("Success!");
    $this->handlePurchase();
  }

  function saveSaleInfo($saleInfo = false) {
    global $db;
    if (!$saleInfo) {
      $saleInfo = $this->getSaleInfo();
    }
    $db->putRowData("sales", $saleInfo);
    $this->saleInfo['id'] = $db->getInsertId();
  }

  function saveSaleDetails($saleDetails = false) {
    global $db;
    if (!$saleDetails) {
      $saleDetails = $this->getSaleDetails();
    }
    $db->putRowData("sale_details", $saleDetails);
    $this->saleDetails['id'] = $db->getInsertId();
  }

  function saveSale() {
    $this->saveSaleInfo();
    $this->saleDetails['sale_id'] = $this->saleInfo['id'];
    $this->saveSaleDetails();
  }

  private static function _sanitizeTxnId($txn_id) {
    if (filter_var($txn_id, FILTER_VALIDATE_EMAIL)) {
      return $txn_id;
    }
    $ret = preg_replace("/[^0-9a-zA-Z]/", "", $txn_id);
    return $ret;
  }

  function mkDownloadUrl() {
    $saleInfo = $this->getSaleInfo();
    if (empty($saleInfo['txn_id'])) {
      return false;
    }
    $txn_id = $saleInfo['txn_id'];
    if (EZ::mkDateInt($saleInfo['expire_date']) < time()) {
      $downloadUrl = "<span style='color:red'>" . __("Sorry, your purchase has expired.", 'easy-paypal') . "</span>";
    }
    else {
      $product = $this->getProduct();
      if (empty($product['filename'])) { // no file to be downloaded
        $downloadUrl = "mailto:{$this->options['support_email']}";
      }
      else {
        $ezppURL = EZ::ezppURL();
        if (EZ::isInWP()) {
          $shop = 'return.php?wp&';
        }
        else {
          $shop = 'return.php?';
        }
        $downloadUrl = sprintf("%s%sdl=%s", $ezppURL, $shop, self::_sanitizeTxnId($txn_id));
      }
    }
    return $downloadUrl;
  }

  function mkDownloadButton() {
    $saleInfo = $this->getSaleInfo();
    if (empty($saleInfo)) {
      return "<span style='color:red'>" . __("Sorry, no sales found.", 'easy-paypal') . "</span>";
    }
    $product = $this->getProduct();
    if (empty($product['filename'])) { // no file to be downloaded
      $buttonText = "Contact us";
    }
    else {
      $buttonText = "Download it";
    }
    if (EZ::mkDateInt($saleInfo['expire_date']) < time()) {
      $downloadButton = "<span style='color:red'>" . __("Sorry, your purchase has expired.", 'easy-paypal') . "</span>";
    }
    else {
      $downloadButton = sprintf("<a href='%s'><button class='btn btn-success'>%s</button></a>", $this->mkDownloadUrl(), $buttonText);
    }
    return $downloadButton;
  }

  function mkDownloadLink() {
    $downloadLink = strip_tags($this->mkDownloadButton(), '<span><a>');
    return $downloadLink;
  }

  private static function _braceIt($s) {
    return '{' . $s . '}';
  }

  function patchPeriods($saleDetails) {
    // Fix subscription period names from period to pt.
    // Over-ridden in the derived class.
    return $saleDetails;
  }

  function mkLogo() {
    $ezppURL = EZ::ezppURL();
    if (empty($this->options['shop_logo'])) {
      $src = $this->options['shop_logo'];
      if (strpos($src, 'http') === false) {
        $src = $ezppURL . $src;
      }
    }
    else {
      $src = $ezppURL . "assets/ezpaypal-brand.png";
    }
    $logo = "<img src='$src' alt='EZ PayPal'>";
    return $logo;
  }

  function processTemplate($name, $html = false) {
    global $log;
    $product = $this->getProduct();
    if (!empty($product['category_id'])) {
      $category_id = $product['category_id'];
    }
    else {
      $category_id = 0;
    }
    if (!empty($product['product_grouping'])) {
      $product_grouping = $product['product_grouping'];
    }
    else {
      $product_grouping = '';
    }
    $filter = array('name' => $name,
        'active' => 1,
        'category_id' => $category_id,
        'product_grouping' => $product_grouping);
    $template = EZ::getTemplate($name, $filter);
    if (!empty($template)) {
      $saleInfo = $this->getSaleInfo();
      $saleDetails = $this->getSaleDetails();
      $saleDetails = $this->patchPeriods($saleDetails);
      $downloadUrl = $this->mkDownloadUrl();
      $downloadLink = $this->mkDownloadLink();
      $downloadButton = $this->mkDownloadButton();
      $logo = $this->mkLogo();
      $searchAndReplace = array_merge($this->options, $saleDetails, $saleInfo, array('download_link' => $downloadLink, 'download_url' => $downloadUrl, 'download_button' => $downloadButton, 'url' => EZ::ezppURL(), 'logo' => $logo));
      $search = array_map(array($this, '_braceIt'), array_keys($searchAndReplace));
      $replace = array_values($searchAndReplace);
      $ret = str_replace($search, $replace, $template);
      if ($html) {
        $ret = EZ::handleImageTagsHtml($ret);
      }
    }
    else {
      $downloadLink = $this->mkDownloadLink();
      $ret = sprintf(__("Your download link is %s", 'easy-paypal'), $downloadLink);
      $log->warn("No template found for " . print_r($filter, true) . "");
    }
    return $ret;
  }

  function emailCustomer($template) {
    $subject = $this->processTemplate($template . '_subject');
    $message = $this->processTemplate($template . '_body');
    $saleInfo = $this->getSaleInfo();
    $saleDetails = $this->getSaleDetails();
    if (!empty($saleInfo['customer_email'])) {
      $to = $saleInfo['customer_email'];
    }
    else if (!empty($saleDetails['payer_email'])) {
      $to = $saleInfo['payer_email'];
    }
    else {
      throw new Exception((__("Error sending email: No customer/payer email in sale info", 'easy-paypal')));
    }
    if (EZ::sendMail($subject, $message, $to)) {
      return true;
    }
    else {
      throw new Exception((__("Error sending email: error return from sendMail", 'easy-paypal')));
    }
  }

  function getReturnPage() {
    global $log;
    $saleDetails = $this->getSaleDetails();
    if (!empty($saleDetails['txn_id'])) { // PDT posted. Get the corresponding IPN from DB.
      $txn_id = self::_sanitizeTxnId($saleDetails['txn_id']);
      $this->saleDetails = EZ::getSaleRow("sale_details", $txn_id);
      $html = $this->processTemplate('download_page', true);
    }
    else if (!empty($_REQUEST['dl'])) { // Download request
      $txn_id = self::_sanitizeTxnId($_REQUEST['dl']);
      $this->saleDetails = EZ::getSaleRow("sale_details", $txn_id);
      $alt = isset($_REQUEST['alt']);
      $this->deliverFile($alt);
      $html = $log->get();
    }
    else { // No PDT. Query the customer for txn_id/email
      $html = $this->processTemplate('download_page_query', true);
    }
    return $html;
  }

  function deliverFile($alt = false) {
    global $log;
    $product = $this->getProduct();
    if (empty($product)) {
      $log->error("DeliverFile: No product defined!");
      return false;
    }
    if ($alt) {
      if (!empty($product['alt_product'])) {
        $product = EZ::getProduct($product['alt_product']);
      }
      else {
        $log->error("DeliverFile: Alternate product requested, but none is defined!");
      }
    }
    $saleInfo = $this->getSaleInfo();
    if (empty($saleInfo)) {
      $log->error("DeliverFile: No sale found!");
      return false;
    }
    if (EZ::mkDateInt($saleInfo['expire_date']) < time()) {
      return "Sorry, your purchase has expired.";
    }
    if (substr($product['file'], 0, 1) == '/' || substr($product['file'], 1, 2) == ':') {
      // absolute pathname?
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
      exit;
    }
    else {
      $log->error("DeliverFile: No file found for this product!");
      return false;
    }
  }

}
