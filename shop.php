<?php

if (!session_id()) {
  session_start();
}

require_once 'EzShopPro.php';
$shop = new EzShopPro();

if (!empty($_REQUEST['error'])) {
  $shop->error = urldecode($_REQUEST['error']);
}

require_once 'header.php';
printLogo("Welcome to EZ PayPal Shop");

insertAlerts(10);
openBox("Please edit the quantity and hit Buy Now", "shopping-cart", 10, "<p>The table below lists all the products available at this e-shop. Please click on the <strong>Quantity</strong> to change it, and hit the <button class='btn-sm btn-success'>Buy Now</button> button to proceed to pay for it.</p><p>The products table is searchable and sortable. Enter any text in search box to look for an item. Click on the column header to sort the column, and use the pagination bar below to see other pages.</p>");
$shop->renderProductTable();
closeBox();

if (!empty(EZ::$options['show_update_section'])) {
  openBox("Check for Updates", "hand-up", 10);
  $shop->renderUpdateSection();
  closeBox();
}

$shop->renderScript();

EZ::flashError($shop->error, true);
EZ::flashWarning($shop->warning, true);
EZ::flashSuccess($shop->success, true);

require_once 'footer.php';
