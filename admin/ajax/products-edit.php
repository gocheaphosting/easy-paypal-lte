<?php

// Non standard output handling. All the echoed outputs will be considered warnings
// to be handled by ajax complete() function

require_once('../../EZ.php');

if (!EZ::isLoggedIn()) {
  http_response_code(400);
  die("Please login before uploading files!");
}
if (empty($_FILES)) {
//   http_response_code(400);
  http_response_code(200);
  die("No files uploaded");
}

$pk = $_REQUEST['pk'];
$when = array('id' => $pk);
$prod = $db->getData('products', array('product_name', 'file', 'filename'), $when);
$prod = $prod[0];
if (empty($prod['filename'])) {
  http_response_code(400);
  die("No product file set for {$prod['product_name']}!");
}
$target = $prod['file'];
$options = EZ::getOptions();
$ds = DIRECTORY_SEPARATOR;
$installRoot = realpath("..$ds..") . $ds;
$storageLocation = $options['storage_location'];
if (empty($target)) { // No file set for this product. Set one.
  $ext = pathinfo($prod['filename'], PATHINFO_EXTENSION);
  $rand = EZ::randString(24);
  $target = "$storageLocation$ds$rand.$ext";
  $when['file'] = $target;
  $db->putRowData('products', $when);
}
$storageLocation = $installRoot . $storageLocation;
if (!is_dir($storageLocation)) { // storage location doesn't exist. Try creating it.
  if (!@mkdir($storageLocation)) { // cannot make it, try renaming storage to it
    if (!@rename($storageLocation . 'storage', $storageLocation)) { // cannot rename it either
      http_response_code(400);
      die("Storage location doesn't exist. Please create it and make it writable to your web server.<br>The unix commands are:<pre><code>mkdir $storageLocation\nchmod 777 $storageLocation</code></pre>");
    }
  }
}
if (!is_writable($storageLocation)) {
  http_response_code(400);
  die("Storage location is not writable. Please make it writable to your web server. The unix command is:<pre><code>chmod 777 $storageLocation</code></pre>");
}
$tempFile = $_FILES['file']['tmp_name'];

if (!@move_uploaded_file($tempFile, $installRoot . $target)) {
  http_response_code(400);
  die("File move error: {$_FILES['file']['name']} to {$prod['product_name']} target <code>{$prod['file']}</code>");
}
http_response_code(200);
die('All good');
