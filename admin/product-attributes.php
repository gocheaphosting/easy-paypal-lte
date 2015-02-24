<?php

$prodAttr = array();
$prodAttr['pk'] = array(
    'name' => 'ID',
    'help' => 'This is the primary ID of the product you are editing. It will be populated automatically.',
    'type' => 'hidden');
$prodAttr['product_code'] = array('name' => __('Product Code', 'easy-paypal'),
    'help' => __('A short code for the digital product you want to sell. e.g., ezPro. This key field needs to be unique.', 'easy-paypal'),
    'validator' => 'alnum',
    'value' => '',
    'unique' => TRUE);
$prodAttr['product_name'] = array('name' => __('Product Name', 'easy-paypal'),
    'help' => __('A descriptive name of the digital product you want to sell. e.g., Easy AdSense Pro - Premium AdSense Plugin for WordPress bloggers.', 'easy-paypal'),
    'value' => '',
    'validator' => 'notNull');
$prodAttr['active'] = array('name' => __('Activate this Product?', 'easy-paypal'),
    'help' => __('Leave this option checked to list the product in your shop. Unchecking it is equivalent to deleting the product. It will not be listed, nor can it be bought.', 'easy-paypal'),
    'type' => 'checkbox',
    'value' => true);
$prodAttr['category_id'] = array('name' => __('Product Category', 'easy-paypal'),
    'help' => __('An optional category for the product, which you can use for grouping similar products together. You can have categories like Apps, Plugins, eBooks, Photos, Songs etc. The drop-down menu is populated from the categories you have defined. To add a new category, please visit the Category section using the admin menu.', 'easy-paypal'),
    'type' => 'category',
    'value' => '');
$prodAttr['product_grouping'] = array('name' => __('Product Grouping', 'easy-paypal'),
    'help' => __('An optional grouping for your products, this field can contain higher level groupins, like Software (for Apps, Plugins, for instance), Creative (e.g. for eBooks, Photos) and so on. This field can help you organize or autogenerate your e-shop pages as your business grows.', 'easy-paypal'),
    'value' => '');
$prodAttr['product_price'] = array('name' => __('Product Price', 'easy-paypal'),
    'help' => __('Enter only numbers. e.g. 4.95', 'easy-paypal'),
    'value' => '',
    'validator' => 'number');
$prodAttr['expire_hours'] = array('name' => __('Download Link Expiry', 'easy-paypal'),
    'help' => __('The number of hours the download link will be active. Default is 72 hours. Enter only an integer numbers. e.g. 72. A week is about 170, a month is about 750.', 'easy-paypal'),
    'value' => '',
    'validator' => 'number');
$prodAttr['mc_currency'] = array('name' => __('Product Currency', 'easy-paypal'),
    'help' => __('Specify the currency in which the product price is give. e.g. USD.', 'easy-paypal'),
    'type' => 'select',
    'value' => 'USD',
    'options' => array('USD', 'AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY'));
$prodAttr['no_shipping'] = array('name' => __('Shipping Address Needed', 'easy-paypal'),
    'help' => __('Specify whether PayPal will ask for a shipping address for this product:', 'easy-paypal') . ' <ul><li>0 &rarr;  ' . __('Prompt for an address', 'easy-paypal') . '</li><li>1 &rarr;  ' . __('No prompt for address', 'easy-paypal') . '</li><li>2 &rarr;  ' . __('Prompt and require a shipping address', 'easy-paypal') . '</li></ul>',
    'type' => 'select',
    'value' => '1',
    'options' => array('0', '1', '2'));
$prodAttr['filename'] = array('name' => __('Product File Name', 'easy-paypal'),
    'help' => __('When your customers buy your digital product, this is the file they will download.', 'easy-paypal'),
    'value' => '');
$prodAttr['file'] = array('name' => __('Your Digital Product', 'easy-paypal'),
    'help' => __('This is the actual digital product that you want to put up for sale using EZ PayPal. It can be a zip archive, photos, PDF files etc. If you have multiple files making up the product, please make a zip archive of it first. Note that the file you upload does not need to have the same name you specify as <b>Product File Name</b>. The real file will be saved on your server under a random file name in a random location so that it will not be easily discovered by potential hackers.', 'easy-paypal'),
    'type' => 'file',
    'value' => '',
    'validator' => 'notNull');
$prodAttr['version'] = array('name' => 'Product Version',
    'needsPro' => true,
    'value' => '0.00',
    'help' => 'Your sales will keep track of the product versions so that you can, for example, send upgrade notifications to your buyers. You would use the Email Tools (found under Pro Tools) for this purpose. Use a numeric version with two decimals (like 3.12 to indicate major version 3, minor version 1 and update 2, for instance). String versions (like V3.1.2) are not supported.',
    'validator' => 'number');
if (EZ::$isPro) {
  $prodAttr['recurring'] = array('name' => 'Subscription Product?',
      'needsPro' => true,
      'type' => 'checkbox',
      'value' => false,
      'help' => 'If you want to make this product a subscription-based one, please tick here. Be sure to enter the subscription details. You may have to edit the product again to see the button to enter subscription details.');
}
