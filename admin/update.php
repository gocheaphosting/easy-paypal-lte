<?php
require 'header.php';
require_once 'Updater.php';
$updater = new Updater('ezpaypal');
$updater->name = "EZ PayPal";
$updater->price = "19.95";
$updater->render();
require 'footer.php';
