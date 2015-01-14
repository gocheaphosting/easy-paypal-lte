<?php

// allow urls of the kind host://root/[dir/]<product id><code><key>
$uri = explode('/', $_SERVER['REQUEST_URI']);
$code = strtolower(end($uri));
if (empty($code)) {
  $code = '';
}
switch ($code) {
  case '':
  case 'index.php':
    header('location: admin/pub.php');
    break;
  case 'ez-shop.php':
  case 'ez-manoj.php':
    header('location: shop.php');
    break;
  case 'ez-delivery.php':
    header('location: return.php');
    break;
  case 'ez-update.php':
    header('location: update.php');
    break;
  default:
    $parts = explode(".", $code);
    $ext = end($parts);
    if (in_array($ext, array("js", "php", "css", "jpg", "png", "gif"))) {
      header('HTTP 404 File not found', true, 404);
      die("File $code not found!");
    }
    header("location: buy.php?id=$code");
}