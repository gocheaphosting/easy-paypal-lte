<?php

require_once 'EzOfficePro.php';
$office = new EzOfficePro();

$office->debug = false;
if (!empty($_REQUEST['email'])) {
  $email = $_REQUEST['email'];
  $result = EZ::getSaleHistory($email);
  if (!empty($result['pageHtml'])) {
    $pageHtml = $result['pageHtml'];
    $pageHtml = "<div id='updateDiv'>$pageHtml</div>"
            . "<input style='display:none' data-email='$email' class='update' type='button'>";
  }
  else if (empty($result['error'])) {
    $result['error'] = "No sale history found!";
  }
}

if (empty($pageHtml)) {
  $html = $office->getReturnPage();
}
else {
  $html = $pageHtml;
}

require_once 'header.php';
printLogo("EZ PayPal Delivery Portal");

insertAlerts(10);
openBox("Delivery Portal", "download-alt", 10);
echo $html;
closeBox();

if (file_exists('EzShopPro.php')) {
  require_once 'EzShopPro.php';
  EzShopPro::renderScript();
}

if (!empty($result['error'])) {
  EZ::flashError(strip_tags($result['error']), true);
}
else {
  EZ::flashError($log->getError());
}
EZ::flashWarning($log->getWarn());
EZ::flashSuccess($log->getInfo());

require_once 'footer.php';
