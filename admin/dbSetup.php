<?php

require_once('Installer.php');

$installer = new Installer();

$installer->configure();
if ($installer->verifyCfg()) {
  $installer->install();
}
$installer->printCfgForm();
