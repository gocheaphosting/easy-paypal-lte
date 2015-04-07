<?php

require_once 'lang.php';

if (!function_exists('putDefaultOptions')) {

  function putDefaultOptions($db, $options) {
    $row = array();
    foreach ($options as $k => $o) {
      $row[$k] = $o['value'];
    }
    $rowDB = $db->getMetaData('options_meta');
    $row = array_merge($row, $rowDB);
    $db->putMetaData('options_meta', $row);
  }

}

if (!function_exists('randString')) {

  function randString($len = 32) {
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $chars .= strtoupper($chars) . '0123456789';
    $charLen = strlen($chars) - 1;
    $string = '';
    for ($i = 0; $i < $len; $i++) {
      $pos = rand(0, $charLen);
      $string .= $chars[$pos];
    }
    return $string;
  }

}

$paypal = array();
$paypal['paypal_name'] = array('name' => __('PayPal username', 'easy-paypal'),
    'help' => __('Enter your name (or your company name). This is used for display purposes.', 'easy-paypal'),
    'validator' => 'notNull',
    'value' => 'EZ PayPal',
    'unique' => true);
$paypal['paypal_email'] = array('name' => __('PayPal email ID', 'easy-paypal'),
    'help' => __('Enter your PayPal email ID, which will receive your payments.', 'easy-paypal'),
    'value' => '',
    'validator' => 'email');
$paypal['sandbox_email'] = array('name' => 'PayPal SandBox email ID',
    'value' => '',
    'help' => 'PayPal SandBox email ID, which you can use for testing your setup. This entry is optional.');
$paypal['sandbox_mode'] = array('name' => 'Use SandBox [test] mode?',
    'type' => 'checkbox',
    'value' => false,
    'help' => 'If checked, PayPal SandBox will be used.');


$options = array();
$options['salt'] = array('name' => __('DB Security Salt', 'ads-ez'),
    'type' => 'hidden',
    'value' => randString(),
    'help' => __('Not visible to the end user', 'ads-ez'));
$options['support_email'] = array('name' => __('Support email ID', 'easy-paypal'),
    'help' => __('An email ID where your buyers can contact you for support.', 'easy-paypal'),
    'value' => '',
    'validator' => 'email');
$options['support_name'] = array('name' => __('Support Name', 'easy-paypal'),
    'value' => '',
    'help' => __('A friendly name to go with your support email ID.', 'easy-paypal'));
$options['expire_hours'] = array('name' => __('Expire Time (in hours)', 'easy-paypal'),
    'value' => 72,
    'validator' => 'number',
    'help' => __('The number of hours after which the download links will expire. When a purchase is made, an auto-generated email is sent to the buyer with a download link. The link expires after the number of hours you specify here. (Default is 72 hours).', 'easy-paypal'));
$options['random_storage'] = array('name' => __('Use Random Storage Location?', 'easy-paypal'),
    'help' => __('Randomize the storage location of your digital goods so that potential hackers cannot easily guess it? Recommended. Note that you will have to set the permission to the storage directory so that files can be uploaded there. Click on the <b>Show</b> button to see the random location.', 'easy-paypal'),
    'type' => 'checkbox',
    'value' => true,
    'button' => 'Show Location',
    'reveal' => 'storage_location');
$options['storage_location'] = array('name' => __('Randomized Storage Location', 'ads-ez'),
    'type' => 'hidden',
    'value' => randString(),
    'help' => __('Not visible to the end user', 'ads-ez'));
$options['random_file'] = array('name' => __('Use Random File Name?', 'easy-paypal'),
    'help' => __('Randomize the file name of your digital goods on the server as you upload it so that potential hackers cannot easily guess it? Recommended.', 'easy-paypal'),
    'type' => 'checkbox',
    'value' => true);
$options['show_product_info'] = array('name' => __('Show Product Info?', 'easy-paypal'),
    'help' => __('If you would like to show product image and description on the page that comes up when your buyer clicks on the <strong>Buy Now</strong> buttons, check this option.', 'easy-paypal'),
    'value' => 0,
    'type' => 'checkbox');
