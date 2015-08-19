<?php

require_once('../../EZ.php');
require_once '../Updater.php';

$updater = new Updater('ezpaypal');
$updater->name = "EZ PayPal";
$updater->toVerify = array('easy-paypal.php', 'wp-ezpaypal.php', 'shop.php', 'EzShop.php', 'office.php', 'EzOffice.php');

$updater->handle();
