<?php
if (class_exists("ezpp")) {
  echo "Problem, class ezpp exists! \nCannot safely continue.\n" ;
  exit ;
}
else {
  class ezpp {
    var $db ;

    function __construct(&$db) {
      $this->db = &$db ;
    }

    function __destruct(){
    }

    function ezpp(&$db) {
      if(version_compare(PHP_VERSION,"5.0.0","<")){
        $this->__construct($db);
        register_shutdown_function(array($this,"__destruct"));
      }
    }

    function validateSale($saleInfo, $saleInfoDB) {
      $verified = is_array($saleInfo) && is_array($saleInfoDB) ;
      if (!$verified) return $verified ;
      $verified = true ;
      $verifyFields = array('txn_id',
                        'customer_name',
                        'customer_email',
                        'purchase_amount',
                        'purchase_status',
                        'product_name',
                        'product_code') ;
      foreach ($verifyFields as $v) {
        $verified = $verified && ($saleInfo[$v] == $saleInfoDB[$v]) ;
        if (!$verified) return $verified ;
      }
      return $verified ;
    }

    function sanitizeTxnId($txn_id) {
      if (filter_var($txn_id, FILTER_VALIDATE_EMAIL)) return $txn_id ;
      $ret = preg_replace("/[^0-9a-zA-Z]/", "", $txn_id) ;
      return $ret ;
    }

    function mkDownloadUrl($saleInfo){
      $txn_id = $saleInfo['txn_id'] ;
      if ($this->mkDateInt($saleInfo['expire_date']) < time())
        $downloadUrl = "<font color='red'>Sorry, your purchase has expired.</font>" ;
      else {
        if (function_exists('get_option')) {
          $ezppURL = get_option('siteurl') . '/' ;
          $shop = 'ez-shop?delivery&' ;
        }
        else {
          $ezppURL =  $this->ezppURL() ;
          $shop = 'ez-delivery.php?' ;
        }
        $downloadUrl = sprintf("%s%sdl=%s", $ezppURL, $shop, $this->sanitizeTxnId($txn_id)) ;
      }
      return $downloadUrl ;
    }

    function mkDownloadButton($saleInfo){
      if ($this->mkDateInt($saleInfo['expire_date']) < time())
        $downloadButton = "<font color='red'>Sorry, your purchase has expired.</font>" ;
      else
        $downloadButton = sprintf("<a href='%s'><button>%s</button></a>",
                          $this->mkDownloadUrl($saleInfo), "Download it!") ;
      return $downloadButton ;
    }

    function mkDownloadLink($saleInfo){
      if ($this->mkDateInt($saleInfo['expire_date']) < time())
        $downloadLink = "Sorry, your purchase has expired." ;
      else
        $downloadLink = sprintf("<a href='%s'>%s</a>",
                        $this->mkDownloadUrl($saleInfo), "Download it!") ;
      return $downloadLink ;
    }

    function ezppURL() {
      if (function_exists('plugins_url')) {
        $url = plugins_url() . '/' . basename(dirname(__FILE__)) . '/' ;
      }
      else {
        $http =  "http";
        if(isset($_SERVER['HTTPS']) and ($_SERVER['HTTPS'] == "on")) {
          $http = "https" ;
        }
        $pwd = dirname($_SERVER['PHP_SELF']) ;
        $pos = strpos($pwd, '/pro') ;
        if ($pos !== false) $pwd = substr($pwd, 0, $pos) ;
        if ($pwd == '/') $pwd = '' ;
        $url = sprintf("%s://%s%s/", $http, $_SERVER['SERVER_NAME'], $pwd) ;
      }
      return $url ;
    }

    function validateEmail($email) {
      $s = trim(strtolower($email)) ;
      $s = $this->db->escape($s) ;
      if (filter_var($s, FILTER_VALIDATE_EMAIL)) return $s ;
      else return false ;
    }

    function mkDateString($intOrStr) {
      if (is_int($intOrStr))
        $dateStr = date('Y-m-d H:i:s', $intOrStr) ;
      else
        $dateStr = date('Y-m-d H:i:s', strtotime($intOrStr)) ;
      return $dateStr ;
    }

    function mkDateInt($intOrStr) {
      if (is_int($intOrStr))
        $dateInt = $intOrStr ;
      else
        $dateInt = strtotime($intOrStr) ;
      return $dateInt ;
    }

    function putSale($saleInfo) {
      $db = $this->db ;
      $saleInfo['purchase_date']  = $this->mkDateString($saleInfo['purchase_date']) ;
      $saleInfo['customer_email'] = strtolower($saleInfo['customer_email']) ;
      $db->putRowData("sales", $saleInfo) ;
      return print_r($saleInfo, true) ;
    }

    function putSaleDetails($saleDetails) {
      $db = $this->db ;
      $saleDetails['payment_date'] = $this->mkDateString($saleDetails['payment_date']) ;
      $saleDetails['payer_email'] = strtolower($saleDetails['payer_email']) ;
      $db->putRowData("sale_details", $saleDetails) ;
      return print_r($saleDetails, true) ;
    }

    function getSale($ID) {
      $db = $this->db ;
      $saleInfo = $db->getSaleRow("sales", $ID) ;
      if (!empty($saleInfo)) {
        $purchaseDate = $this->mkDateInt($saleInfo['purchase_date']) ;
        $expireHours = intval($saleInfo['expire_hours']) ;
        $dbExpireDate = $this->mkDateInt($saleInfo['expire_date']) ;
        if ($dbExpireDate < $purchaseDate)
          $expireDate = $purchaseDate + $expireHours * 60 * 60 ;
        else
          $expireDate = $dbExpireDate ;
        $timeLeft = ($expireDate - time()) / 60 / 60 ;
        $saleInfo['time_left'] = round($timeLeft, 2) ;
        $saleInfo['purchase_date'] = $this->mkDateString($saleInfo['purchase_date']) ;
        $saleInfo['expire_date'] = $this->mkDateString($saleInfo['expire_date']) ;
      }
      return $saleInfo ;
    }

    function getSaleMap() {
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
        'affiliate_id' => 'custom') ;
      return $detailsToInfo ;
    }

    function mkSaleDetails($saleInfo) {
      $detailsToInfo = $this->getSaleMap() ;
      foreach ($detailsToInfo as $info => $detail) {
        if (!empty($saleInfo[$info])) $saleDetails[$detail] = $saleInfo[$info] ;
      }
      $nameArray = explode(' ', $saleInfo['customer_name']) ;
      $saleDetails = array() ;
      $saleDetails['first_name'] = array_shift($nameArray) ;
      $saleDetails['last_name'] = implode($nameArray) ;
      if (empty($saleInfo['quantity'])) $saleDetails['quantity'] = 1 ;
      return $saleDetails ;
    }

    function mkSaleInfo($saleDetails, $sandbox, $version, $expireHours) {
      $detailsToInfo = $this->getSaleMap() ;
      $saleInfo = array() ;
      foreach ($detailsToInfo as $info => $detail) {
        if (!empty($saleDetails[$detail])) $saleInfo[$info] = $saleDetails[$detail] ;
      }
      $saleInfo['customer_name'] = $saleDetails['first_name'] . ' ' .
        $saleDetails['last_name'] ;

      if (empty($saleDetails['payment_date']) ||
        (!empty($saleDetails['payment_type']) && $saleDetails['payment_type'] == 'echeck'))
        $saleInfo['purchase_date'] = time() ;
      else
        $saleInfo['purchase_date'] = $this->mkDateInt($saleDetails['payment_date']);
      if (empty($saleDetails['expire_date']))
        $saleInfo['expire_date'] = $this->mkDateInt($saleInfo['purchase_date'])
          + ($expireHours * 60 * 60);
      else
        $saleInfo['expire_date'] = $this->mkDateInt($saleDetails['expire_date']) ;
      // always return dates as strings
      $saleInfo['purchase_date'] = $this->mkDateString($saleInfo['purchase_date']) ;
      $saleInfo['expire_date'] = $this->mkDateString($saleInfo['expire_date']) ;
      if (empty($saleDetails['expire_hours']))
        $saleInfo['expire_hours'] = $expireHours ;
      else
        $saleInfo['expire_hours'] = $saleDetails['expire_hours'] ;
      $saleInfo['sold_version'] = $version ;
      if ($sandbox) $saleInfo['purchase_mode'] = 'Test' ;
      else $saleInfo['purchase_mode'] = 'Live' ;
      return $saleInfo ;
    }

    function getProduct($productCode) {
      $db = $this->db ;
      $ret = $db->getData("products", '*', array('product_code'=>$productCode)) ;
      if (!empty($ret)) return $ret[0] ;
    }

    function getAllProducts() {
      $db = $this->db ;
      $ret = $db->getData("products", '*', 1, 'product_name') ;
      if (!empty($ret)) {
        $ret = array_reverse($ret) ;
        return $ret ;
      }
    }

    function isLoggedIn($pwd='.') {
      if (function_exists('current_user_can')) {
        @session_start() ;
        return current_user_can('administrator') ;
      }
    }

    function login($username, $password) {
      if (function_exists('current_user_can')) return current_user_can('administrator') ;
      $db = $this->db ;
      if (is_a($db, 'dbHelper')) {
        $table = $db->prefix(md5($username)) ;
        $key = md5($password) ;
        if ($db->tableExists($table)) {
          $sql = "SELECT value FROM $table WHERE keyval='$key'" ;
          $result = $db->query($sql) ;
          $row = mysql_fetch_assoc($result) ;
          return $key == $row['value'] ;
        }
      }
      return false ;
    }

    static function braceIt($s) {
      return '{' . $s . '}' ;
    }

    function procTemplate($template, $saleInfo, $product, $options) {
      $product_category = $product['product_category'] ;
      $product_grouping = $product['product_grouping'] ;
      if (!empty($saleInfo['notify_version'])) {
        // "saleInfo" is really saleDetails.
        $saleDetails = $saleInfo ;
        $saleInfo = $this->mkSaleInfo($saleDetails, false,
                    $product['version'], $options['expire_hours']) ;
        $saleInfo = array_merge($saleDetails, $saleInfo) ;
      }
      $downloadLink = $this->mkDownloadLink($saleInfo) ;
      $downloadUrl = $this->mkDownloadUrl($saleInfo) ;
      $downloadButton = $this->mkDownloadButton($saleInfo) ;
      $filter = array('name' => $template,
                'product_category' => $product_category,
                'product_grouping' => $product_grouping) ;
      $db = $this->db ;
      $templateData = $db->getMetaData('templates', $filter) ;
      if (!empty($templateData[$template]))
        $templateStr = $templateData[$template] ;
      else { // default template
        $filter = array('name' => $template) ;
        $templateData = $db->getMetaData('templates', $filter) ;
        if (!empty($templateData[$template]))
          $templateStr = $templateData[$template] ;
        else {
          $ret = "Sorry, no template, but the download link is $downloadLink" ;
        }
      }
      if (!empty($templateStr)) {
        // handle dates
        $saleInfo['purchase_date'] = $this->mkDateString($saleInfo['purchase_date']) ;
        $saleInfo['expire_date'] = $this->mkDateString($saleInfo['expire_date']) ;
        $ret = str_replace(array_map(array('ezpp', 'braceIt'),array_keys($saleInfo)),
               array_values($saleInfo), $templateStr) ;
        $ret = str_replace(array_map(array('ezpp', 'braceIt'),array_keys($product)),
               array_values($product), $ret) ;
        $ret = str_replace(array_map(array('ezpp', 'braceIt'),array_keys($options)),
               array_values($options), $ret) ;
        $ret = str_replace(array('{download_link}','{download_url}','{download_button}'),
               array($downloadLink,$downloadUrl,$downloadButton), $ret) ;
      }
      return $ret ;
    }

    function procTemplateDB($template, $txn_id) {
      $db = $this->db ;
      $saleInfo = $this->getSale($txn_id) ;
      $productCode = $saleInfo['product_code'] ;
      $product = $this->getProduct($productCode) ;
      $options = $db->getRowData("options") ;
      return $this->procTemplate($template, $saleInfo, $product, $options) ;
    }

    function emailCustomer($saleInfo, $product, $options, $template="email") {
      $emailSubject = $this->procTemplate($template.'_subject', $saleInfo, $product, $options) ;
      $emailBody = $this->procTemplate($template.'_body', $saleInfo, $product, $options) ;
      if (!empty($saleInfo['customer_email'])) $emailTo = $saleInfo['customer_email'] ;
      else if (!empty($saleInfo['payer_email'])) $emailTo = $saleInfo['payer_email'] ;
      else return "Error sending email: No customer/payer email in sale info" ;
      $emailFrom =  $options['support_email'] ;
      $emailHeader = sprintf('From: "%s" <%s>' . "\r\n" .
                     "Reply-To: %s\r\n" .
                     "X-Mailer: %s\r\n" .
                     "X-Sender-IP: %s\r\n" .
                     "Bcc: %s\r\n",
                     $options['support_name'], $emailFrom,
                     $emailFrom,
                     $this->ezppURL(),
                     $_SERVER['REMOTE_ADDR'],
                     $emailFrom) ;
      $emailParams = "-f$emailFrom";
      $emailResult = mail($emailTo, $emailSubject, $emailBody, $emailHeader, $emailParams) ;
      if ($emailResult) {
        return ;
      }
      else {
        return "Error sending email:\n
     mail($emailTo,$emailSubject,$emailBody,$emailHeader,$emailParams)";
      }
    }

    function includeFileExists($file) {
      $ps = explode(":", ini_get('include_path')) ;
      foreach ($ps as $path) {
        if (file_exists($path.'/'.$file)) return true ;
      }
      if(file_exists($file)) return true ;
      return false ;
    }

    function handleImageTags($output, &$mime='') {
      $matches = array() ;
      $pattern = '/{img:([^ ]*?)}/' ;
      preg_match_all($pattern, $output, $matches) ;
      $tags = $matches[0] ;
      $images = $matches[1] ;
      $cid = array() ;
      if (!empty($images) && is_array($images))
        foreach ($images as $i) {
          if (!empty($mime) && is_a($mime, 'Mail_mime')) {
            $ext = end(explode('.', $i)) ;
            $mime->addHTMLImage($i, "image/$ext", $i, true, $i) ;
            $cid[] = "cid:$i" ;
          }
          else
            $cid[] = "../$i" ;
        }
      $output = str_replace($tags, $cid, $output) ;
      return $output ;
    }

    function emailCustomerHtml($saleInfo, $product, $options, $template="email") {
      if ($this->includeFileExists("Mail.php") &&
        $this->includeFileExists("Mail/mime.php")) {
        @include_once('Mail.php');
        include_once "Mail/mime.php";
      }
      else {
        return "Cannot do HTML mail - PEAR Mail and mime missing. Fell back to Text mail.\n" .
          $this->emailCustomer($saleInfo, $product, $options, $template) ;
      }
      $crlf = PHP_EOL;
      $mime = new Mail_mime($crlf) ;

      $emailSubject = $this->procTemplate($template.'_subject', $saleInfo, $product, $options) ;
      $emailBody = $this->procTemplate($template.'_body', $saleInfo, $product, $options) ;
      $mime->setTXTBody($emailBody) ;

      $emailBodyHtml = $this->procTemplate($template.'_body_html', $saleInfo, $product, $options) ;
      $matches = array() ;
      $pattern = '/{img:([^ ]*?)}/' ;
      preg_match_all($pattern, $emailBodyHtml, $matches) ;
      $tags = $matches[0] ;
      $images = $matches[1] ;
      $cid = array() ;
      $imgLocation = dirname(__FILE__) ;
      if (!empty($images) && is_array($images))
        foreach ($images as $i) {
          $ext = end(explode('.', $i)) ;
          $mime->addHTMLImage("$imgLocation/$i", "image/$ext", $i, true, $i) ;
          $cid[] = "cid:$i" ;
        }
      $emailBodyHtml = str_replace($tags, $cid, $emailBodyHtml) ;
      $mime->setHTMLBody($emailBodyHtml) ;

      $emailFrom = $options['support_email'] ;
      if (!empty($saleInfo['customer_email'])) $emailTo = $saleInfo['customer_email'] ;
      else if (!empty($saleInfo['payer_email'])) $emailTo = $saleInfo['payer_email'] ;
      else return "Error sending email: No customer/payer email in sale info" ;
      $emailFromEx = sprintf('"%s" <%s>', $options['support_name'], $emailFrom) ;
      // $emailToEx = sprintf('"%s" <%s>', $saleInfo['customer_name'], $emailFrom) ;

      $mimeParams['text_encoding'] = "8bit" ;
      $mimeParams['text_charset'] = "UTF-8" ;
      $mimeParams['html_encoding'] = "base64" ;
      $mimeParams['html_charset'] = "UTF-8" ;
      $mimeParams['head_charset'] = "UTF-8" ;
      $body = $mime->get($mimeParams) ;

      $emailParams = "-f$emailFrom";
      $mail =& Mail::factory('mail', $emailParams) ;
      $toAndBcc = array('To' => $emailTo) ;
      $headers = array(
        'From' => $emailFromEx,
        'Subject' => $emailSubject,
        "Reply-To" => $emailFrom,
        "X-Mailer" => $this->ezppURL(),
        "X-Sender-IP" => $_SERVER['REMOTE_ADDR']) ;
      $headers = $mime->headers($headers) ;
      $emailResult = @$mail->send($toAndBcc, $headers, $body) ;

      $emailParams = "-f$emailTo";
      $mail =& Mail::factory('mail', $emailParams) ;
      $toAndBcc = array('To' => $emailFrom) ;
      $headers = array(
        'From' => $emailFromEx,
        'Subject' => $emailSubject,
        "Reply-To" => $emailTo,
        "X-Mailer" => $this->ezppURL(),
        "X-Sender-IP" => $_SERVER['REMOTE_ADDR']) ;
      $headers = $mime->headers($headers) ;
      $emailResult = @$mail->send($toAndBcc, $headers, $body) ;

      if ($emailResult === TRUE) {
        return ;
      }
      else {
        return "Error sending email:\n
     PEAR HTML mail->send($emailTo, headers, body)";
      }
    }
  }
}
?>