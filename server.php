<?php

include('EZ.php');

if (empty(EZ::$options['enable_server'])) {
  die("This feature is currently not active.");
}

$listingAll = false;

if ($_SERVER['REQUEST_METHOD'] !== "POST" && empty($_GET)) {
  ?>
  <p>To get subscription details, enter the subscriber details.</p>
  <p>Or, leave it empty and hit submit for all subscriptions.</p>
  <form>
    Email: <input name='payer_email'><br />
    ID: <input name='subscr_id'><br />
    Submit: <input name='subscriptions' type='submit' value='Submit'>
  </form>
  <p>To get product sale stats, enter the product key or id.</p>
  <p>Or, leave it empty and hit submit for all products.</p>
  <form>
    ID: <input name='product'><br />
    Submit: <input name='products' type='submit' value='Submit'>
  </form>
  <?php

  exit;
}

function getOneProductInfo($db, $prodKey) {
  $product = EZ::getProduct($prodKey);
  $price = $product['product_price'];
  $item_number = $product['product_code'];

  $sold = $db->getCount("sale_details", "payment_status='Completed' and item_number='$item_number'");
  $refunded = $db->getCount("sale_details", "(payment_status='Reversed' or payment_status='Refunded') and item_number='$item_number'");

  if ($sold > 0) {
    $rating = 1 - $refunded / $sold;
  }
  else {
    $rating = 1;
  }
  $rating *= 100;
  $num_ratings = $sold;
  return "$rating $num_ratings $price";
}

function getAllProductInfo($db) {
  if (!isset($_REQUEST['products'])) { // not coming from a form, disregard
    return;
  }
  global $listingAll;
  $listingAll = true;
  $products = $db->getData('products', '*', 'active=1');
  $data = array();
  foreach ($products as $p) {
    $prodKey = $p['id'];
    $data[$p['product_name']] = getOneProductInfo($db, $prodKey);
  }
  return $data;
}

function getProductInfo($db) {
  if (!empty($_REQUEST['product'])) {
    $prodKey = $db->escape($_REQUEST['product']);
    if ($prodKey != $_REQUEST['product']) {
      //  die("SQL Injection trap.") ;
      die("0 0 2");
    }
  }
  if (empty($prodKey)) {
    return getAllProductInfo($db);
  }
  else {
    return getOneProductInfo($db, $prodKey);
  }
}

function sendData($data, $raw = false) {
  if (empty($data)) {
    return;
  }
  if ($raw) {
    $str = $data;
  }
  else {
    $serial = serialize($data);
    $b64 = base64_encode(gzdeflate($serial, 9));
    $str = chunk_split($b64);
  }
  echo $str;
}

function getSubscriptionData($db) {
  if (!empty($_REQUEST['payer_email']) && !empty($_REQUEST['subscr_id'])) {
    $payer_email = $db->escape($_REQUEST['payer_email']);
    $subscr_id = $db->escape($_REQUEST['subscr_id']);
    if ($payer_email != $_REQUEST['payer_email'] ||
            $subscr_id != $_REQUEST['subscr_id']) {
      //  die("SQL Injection trap.") ;
      die("0 0 2");
    }
  }
  if (empty($payer_email) || empty($subscr_id)) {
    return getAllSubscriptions($db);
  }
  else {
    return getOneSubscription($db, $payer_email, $subscr_id);
  }
}

function getOneSubscription($db, $payer_email, $subscr_id) {
  $when = compact("subscr_id", "payer_email");
  $data = $db->getData("sale_details", "subscr_id, payer_email, item_number, item_name, created, txn_id, txn_type, payment_gross, subscr_date, period1, amount1, period3, amount3", $when);
  return $data;
}

function getAllSubscriptions($db) {
  if (!isset($_REQUEST['subscriptions'])) { // not coming from a form, disregard
    return;
  }
  global $listingAll;
  $listingAll = true;
  $data = array();
  $subscribers = $db->getData("sale_details", "subscr_id, payer_email", "subscr_id != '' and payer_email != ''");
  foreach ($subscribers as $s) {
    $d = getOneSubscription($db, $s['payer_email'], $s['subscr_id']);
    if (empty($data[$s['subscr_id']])) {
      $data[$s['subscr_id']] = array($d);
    }
    else {
      $data[$s['subscr_id']][] = $d;
    }
  }
  return $data;
}

$data = getSubscriptionData($db);
if (!empty($data)) {
  if ($listingAll) {
    include_once 'debug.php';
    d($data);
  }
  else {
    sendData($data);
  }
}

$data = getProductInfo($db);
if (!empty($data)) {
  if ($listingAll) {
    include_once 'debug.php';
    d($data);
  }
  else {
    sendData($data, true);
  }
}
