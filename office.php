<?php

require_once 'EzOfficePro.php';
require_once 'debug.php';
$office = new EzOfficePro();
$office->setDebug(false);

require_once 'header.php';
printLogo("Welcome to EZ PayPal Back Office");

insertAlerts(10);
openBox("Back Office", "shopping-cart", 10);
$log->setTag("Office");
$office->processTxn();
echo "<pre style='text-align:left'>\n" . $log->get() . "</pre>";
closeBox();

EZ::showError($log->getError());
EZ::showWarning($log->getWarn());
EZ::showSuccess($log->getInfo());

require_once 'footer.php';
