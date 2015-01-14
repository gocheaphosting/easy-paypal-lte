<?php

require_once('../../EZ.php');

if (!empty($_REQUEST['action'])) {
  $action = $_REQUEST['action'];
}
else {
  $action = '';
}
unset($_REQUEST['file']);
switch ($action) {
  case 'create':
    echo EZ::create('products');
    break;
  case 'update':
  default:
    EZ::update('products');
    break;
}
