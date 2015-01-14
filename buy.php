<?php
if (file_exists('EzShopPro.php')) {
  require_once 'EzShopPro.php';
  $shop = new EzShopPro();
}
else {
  require_once 'EzShop.php';
  $shop = new EzShop();
}

$shop->verifyRequest();

require_once 'header.php';
printLogo();

insertAlerts(10);
openBox("Thank you for your purchase!", "shopping-cart", 10);
$shop->renderForm();
closeBox();
EZ::flashError($shop->error);
EZ::flashWarning($shop->warning);
EZ::flashSuccess($shop->success);
require_once 'footer.php';
