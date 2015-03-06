<?php
/*
 * This is the landing page of the EZ PayPal installation.
 * It redirects HTTP requests to product purchase pages, if possible.
 * These are permalink urls of the kind host://root/[dir/]<product id><code><key>
 * Also, forwards the targets in the old version (<V6.00) to the new targerts.
 * In order for this to work, you need an .htaccess file with the following content:
# BEGIN EZ PayPal
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php?id=1 [L]
</IfModule>
# END EZ PayPal
 */

$request = rtrim($_SERVER['REQUEST_URI'], '/');
if ($request != $_SERVER['REQUEST_URI']) { // ending slash stripped
  $pwd = "../";
}
else {
  $pwd = "";
}
$uri = explode('/', $request);
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
    header('location: ../update.php');
    break;
  default:
    $parts = explode(".", $code);
    $ext = end($parts);
    if (in_array($ext, array("js", "php", "css", "jpg", "png", "gif"))) {
      http_response_code(404);
      die("File $code not found!");
    }
    header("location: {$pwd}buy.php?id=$code");
}

// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code')) {

  function http_response_code($newcode = NULL) {
    static $code = 200;
    if ($newcode !== NULL) {
      header('X-PHP-Response-Code: ' . $newcode, true, $newcode);
      if (!headers_sent()) {
        $code = $newcode;
      }
    }
    return $code;
  }

}
