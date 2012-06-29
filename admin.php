<?php
@session_start() ;
require_once('htmlHelper.php') ;
require_once('dbHelper.php');
require_once('formHelper.php') ;
require_once('ezpp.php') ;

$html = new htmlHelper() ;
$ezDB = new dbHelper();
$form = new formHelper($ezDB, $html) ; // will die if DB connection fails
$ezpp = new ezpp($ezDB) ;

if (!$ezpp->isLoggedIn()) $html->ezDie("Unauthorized Access") ;  // will do start_session()

$paypal = array() ;
$paypal['paypal_name'] =
  array('name' => 'PayPal username',
    'help' => 'Enter your name (or your company name). This is used for display purposes.',
    'validator' => 'notNull',
    'value' => '',
    'unique' => true) ;
$paypal['paypal_email'] =
  array('name' => 'PayPal email ID',
    'help' => 'Enter your PayPal email ID, which will receive your payments.',
    'value' => '',
    'validator' => 'email') ;

$options = array() ;
$options['support_email'] =
  array('name' => 'Support email ID',
    'help' => 'An email ID where your buyers can contat you for support.',
    'value' => '',
    'validator' => 'email') ;
$options['support_name'] =
  array('name' => 'Support Name',
    'value' => '',
    'help' => 'A friendly name to go with your support email ID.') ;
$options['expire_hours'] =
  array('name' => 'Expire Time (in hours)',
    'value' => 72,
    'help' => 'The number of hours after which the download links will expire. When a purchase is made, an auto-generated email is sent to the buyer with a download link. The link expires after the number of hours you specify here. (I use 72 hours).') ;
$options['random_storage'] =
  array('name' => 'Random Storage Location',
    'help' => 'Randomize the storage location of your digital goods so that potential hackers cannot easily guess it? Recommended. Note that you will have to set the permission to the storage directory so that files can be uploaded there. Click on the <b>Show</b> button to see the random location.',
    'type' => 'checkbox',
    'value' => true,
    'hidden' => 'storage_location',
    'reveal' => 'button') ;
$options['random_file'] =
  array('name' => 'Random File Name',
    'help' => 'Randomize the file name of your digital goods on the server as you upload it so that potential hackers cannot easily guess it? Recommended.',
    'type' => 'checkbox',
    'value' => true) ;
$options['lite_location'] =
  array('name' => 'Location for Lite Versions',
    'help' => 'Optional. If you plan to provide <b>Lite</b> versions of your products, here is where they will reside. Give the full pathname on the server, or a relative pathname under the <code>ezPayPal</code> folder.',
    'value' => 'lite',
    'type' => 'text') ;
$options['storage_location'] =
  array('name' => 'Location for Pro Versions',
    'help' => 'If you plan to use non-random location for the <b>Pro</b> versions of your products, here is where they will reside. Note that non-random location is not recommended because it can be easily guessed and hacked into.<br />Give the full pathname on the server, or a relative pathname under the <code>ezPayPal</code> folder.',
    'value' => 'storage',
    'type' => 'hidden') ;

$products = array() ;
$products['product_code'] =
  array('name' => 'Product Code',
    'help' => 'A short code for the digital product you want to sell. e.g., ezPro. This key field needs to be unique.',
    'type' => 'dbEditableSelect',
    'form' => 'products',
    'table' => 'products',
    'column' => 'product_code',
    'validator' => 'notNull',
    'value' => '',
    'unique' => TRUE) ;
$products['product_name'] =
  array('name' => 'Product Name',
    'help' => 'A descriptive name of the digital product you want to sell. e.g., Easy AdSense Pro - Premium AdSense Plugin for WordPress bloggers',
    'value' => '',
    'validator' => 'notNull') ;
$products['active'] =
  array('name' => 'Activate this Product?',
    'help' => 'Leave this option checked to list the product in your shop. Unchecking it is equivalent to deleting the product. It will not be listed, nor can it be bought.',
    'type' => 'checkbox',
    'value' => true) ;
$products['product_category'] =
  array('name' => 'Product Category',
    'help' => 'An optional category for the product, which you can use for grouping similar products together. You can have categories like Apps, Plugins, eBooks, Photos, Songs etc.',
    'type' => 'dbEditableSelect',
    'form' => 'products',
    'table' => 'products',
    'value' => '',
    'column' => 'product_category') ;
$products['product_grouping'] =
  array('name' => 'Product Grouping',
    'help' => 'An optional grouping for your products, this field can contain higher level groupins, like Software (for Apps, Plugins, for instance), Creative (e.g. for eBooks, Photos) and so on. This field can help you organize or autogenerate your e-shop pages as your business grows.',
    'type' => 'dbEditableSelect',
    'form' => 'products',
    'table' => 'products',
    'value' => '',
    'column' => 'product_grouping') ;
$products['product_price'] =
  array('name' => 'Product Price',
    'help' => 'Enter only numbers. e.g. 4.95',
    'value' => '',
    'validator' => 'number') ;
$products['mc_currency'] =
  array('name' => 'Product Currency',
    'help' => 'Specify the currency in which the product price is give. e.g. USD.',
    'type' => 'select',
    'value' => 'USD',
    'options' => array('USD', 'AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY')) ;
$products['no_shipping'] =
  array('name' => 'Shipping Address Needed',
    'help' => 'Specify whether PayPal will ask for a shipping address for this product: <ol start=0><li> Prompt for (but not require) an address</li><li> Do not prompt for an address</li><li> Prompt for an address, and require one</li></ol>',
    'type' => 'select',
    'value' => '1',
    'options' => array('0','1','2')) ;
$products['filename'] =
  array('name' => 'Product File Name',
    'help' => 'When your customers buy your digital product, this is the file they will download.',
    'value' => '') ;
$products['file'] =
  array('name' => 'Your Digital Product',
    'help' => 'This is the actual digital product that you want to put up for sale using ezPayPal. It can be a zip archive, photos, PDF files etc. If you have multiple files making up the product, please make a zip archive of it first. Note that the file you upload does not need to have the same name you specify as <b>Product File Name</b>. The real file will be saved on your server under a random file name in a random location so that it will not be easily discovered by potential hackers.',
    'type' => 'file',
    'value' => '',
    'validator' => 'notNull') ;

if (file_exists('pro/proControls.php')) include('pro/proControls.php') ;

$products['submitText'] = "Insert Product" ;

if (!empty($_POST)) {
  $form->handleSubmit('paypal', $paypal) ;
  $form->handleSubmit('options', $options) ;
  $form->handleSubmit('products', $products) ;
}

$form->loadRowSetFromDB('options', $options) ;
$form->loadRowSetFromDB('paypal', $paypal) ;
$form->loadRowSetFromDB('products', $products) ;

$html->ezppHeader('Admin Control Panel') ;
$form->renderForm('paypal', $paypal, 'PayPal Account Details',
  'Setup your PayPal Account and provide details here',
  'Update PayPal Details') ;
$sqlErr = '' ;
if (!$ezDB->valid) $sqlErr = "Error connecting to the database" ;
$form->renderForm('options', $options, 'Options',
  'General Options regarding the behavior of ezPayPal.',
  'Update Options', $sqlErr) ;
$form->renderForm('products', $products, 'Products',
  'Setup your Products. Enter your digital goods inventory one by one.',
  $products['submitText']) ;
$html->ezppFooter() ;
?>