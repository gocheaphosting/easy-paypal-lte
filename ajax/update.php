<?php

require_once('../EZ.php');

$posted_email = $posted_action = $posted_validator = '';

extract($_REQUEST, EXTR_PREFIX_ALL, 'posted');

$action = $posted_action;
switch ($action) {
  case 'show':
    $result = EZ::getSaleHistory($posted_email);
    if (!empty($result['error'])) {
      http_response_code(400);
      die($result['error']);
    }
    if (!empty($result['pageHtml'])) {
      $pageHtml = $result['pageHtml'];
    }
    else {
      http_response_code(400);
      die("Null return from getSaleHistory!");
    }
    break;
  case 'freeUpdate':
    $bootBox = '';
    $sale = $db->getRowData('sales', array('id' => $posted_pk));
    if (empty($sale)) {
      http_response_code(400);
      die("Technical issues: Cannot locate the sale!");
    }
    $expireHours = intval($sale['expire_hours']);
    $expireDate = EZ::mkDateString(time() + $expireHours * 60 * 60);
    $sale['expire_date'] = $expireDate;
    $product = EZ::getProduct($sale['product_id']);
    $sale['updated_version'] = $product['version'];
    $db->putRowData('sales', $sale);
    $updatePrice = EZ::getUpdatePrice($product);
    $bootBox .= "<p>Your copy of <em>{$product['product_name']}</em> has been marked as updated to V{$product['version']} in our system. You have $expireHours hours to download it. Please click on the <b>Download Update</b> button to download it now.</p></p>Note that future updates to this product are chargeable at $updatePrice.<p>";
    $bootBox .= "<p><a href='return.php?dl={$sale['txn_id']}' class='btn-sm btn-success'>Download Update</a></p>";
    http_response_code(200);
    echo $bootBox;
    exit();
    break;
  case 'paidUpdate':
    $bootBox = '';
    $sale = $db->getRowData('sales', array('id' => $posted_pk));
    if (empty($sale)) {
      http_response_code(400);
      die("Technical issues: Cannot locate the sale!");
    }
    $product = EZ::getProduct($sale['product_id']);
    if (empty($sale['updated_version'])) {
      $sale['updated_version'] = $sale['sold_version'];
    }
    $isMinor = floor($product['version']) == floor($sale['updated_version']);
    if ($isMinor) {
      $updatePrice = EZ::getUpdatePrice($product);
    }
    else {
      $updatePrice = EZ::getUpdatePriceMajor($product);
    }
    $bootBox .= "<p>You are running an old version (V{$sale['updated_version']}) of <em>{$product['product_name']}</em>. A new version (V{$product['version']}) with enhancements and bug fixes is available. You can purchase an update for only $updatePrice.<p>";
    $bootBox .= "<p><a href='buy.php?id={$sale['product_id']}&sale_id={$sale['id']}&qty=1&update=true&txn_id={$sale['txn_id']}' class='btn-sm btn-info'>Buy Update</a></p>";
    http_response_code(200);
    echo $bootBox;
    exit();
    break;
}
$pageHtml .= "<div class='btn-group'><a href='#' class='update'  data-email='$posted_email'><button class='btn btn-primary'>Refresh List</button></a> "
        . "<a href='#' class='reload'><button class='btn btn-primary'>Re-enter Email</button></a></div>";

http_response_code(200);
die($pageHtml);
