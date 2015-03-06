<?php

ob_start();

require_once('../../EZ.php');
require_once('../../lib/Ftp.php');

if (!EZ::isLoggedIn()) {
  http_response_code(400);
  die("Please login before uploading files!");
}

if (!class_exists('ZipArchive')) {
  $error = "Seems like zip is not fully enabled in the PHP installation on your server. (<code>class ZipArchive</code> not found.) This updater cannot proceed without it.<br />You might be able to add zip support via your cPanel/WHM interface. Look for Module Installers, and try installing zip via PHP Pecl installer.";
  http_response_code(400);
  die($error);
}

if (isset($_REQUEST['backup'])) {
  require_once '../Updater.php';
  $updater = new Updater('ezpaypal');
  $updater->backup();
  exit();
}

if (empty($_FILES)) {
  http_response_code(400);
  die("File upload error. No files reached the server!");
}

$ds = DIRECTORY_SEPARATOR;
$target = realpath("..$ds..") . $ds;
$ftp = new Ftp();
if (Ftp::isNeeded($target)) {
  if (!$ftp->isReady) {
    $error = "Cannot overwrite the EZ PayPal files! Here are your options to proceed."
            . "<ol><li>Enter or edit the FTP credentials below, if available. Contact your server admin for details.</li>"
            . "<li>Unpack the downloaded archive, remove the file <code>dbCfg.php</code>, and upload the rest to your server, overwriting the existing files.</li>"
            . "<li>Make your installation updatable by using this Unix command or equivalent:<pre><code>chmod -R 777 $target</code></pre></li></ol>";
    http_response_code(400);
    die($error);
  }
}

$warning = '';
$dirCount = 0;
$zip = new ZipArchive;
$tmpName = $_FILES['file']['tmp_name'];
if ($zip->open($tmpName) !== TRUE) {
  $error = "Cannot open the uploaded zip file.";
  http_response_code(400);
  die($error);
}

// ensure that it is an EZ PayPal archive
$stat = $zip->statIndex(0);
$root = $stat['name'];
$isEZPP = strpos($root, 'ezpaypal/') === 0;
if (!$isEZPP) {
  http_response_code(400);
  $error .= "The uploaded archive does not look like an EZ PayPal update. Root folder is <code>$root</code>.";
  die($error);
}
$toVerify = array('easy-paypal.php', 'wp-ezpaypal.php', 'shop.php', 'EzShop.php', 'office.php', 'EzOffice.php');
foreach ($toVerify as $d) {
  $idx = $zip->locateName($d, ZipArchive::FL_NODIR);
  if ($idx === false) {
    $idx = $zip->locateName($d);
  }
  if ($idx === false) {
    $error = "Cannot locate a critical file (<code>$d</code>) in the uploaded zip file.";
    http_response_code(400);
    die($error);
  }
}
// files to remove from the archive -- not to overwrite on the user's DB server details
$toDelete = array('dbCfg.php');
foreach ($toDelete as $d) {
  $idx = $zip->locateName($d, ZipArchive::FL_NODIR);
  if ($idx === false) {
    $idx = $zip->locateName($d);
  }
  if ($idx === false) {
    $error = "Cannot locate a critical file (<code>$d</code>) in the uploaded zip file.";
    http_response_code(400);
    die($error);
  }
  if (!$zip->deleteIndex($idx)) {
    $error = "Cannot delete the empty file (<code>$d</code>) from the archive.";
    http_response_code(400);
    die($error);
  }
}
$zip->close();

if ($zip->open($tmpName) !== TRUE) {
  $error = "Cannot reopen the uploaded zip file (after removing config).";
  http_response_code(400);
  die($error);
}

$zipRoot = $zip->getNameIndex(0);
for ($i = 1; $i < $zip->numFiles; $i++) {
  $filename = $zip->getNameIndex($i);
  $sourceFile = "zip://$tmpName#$filename";
  $targetFile = str_replace($zipRoot, $target, $filename);
  if (is_dir($targetFile)) {
    ++$dirCount;
    continue;
  }
  $lastChar = substr($sourceFile, -1);
  if ($lastChar == $ds || $lastChar == '/') {
    if (!$ftp->mkdir($targetFile)) {
      $error = "Error creating the directory $targetFile";
      http_response_code(400);
      die($error);
    }
    continue;
  }
  $targetDir = dirname($targetFile);
  if (!is_dir($targetDir) && !@$ftp->mkdir($targetDir)) {
    $error = "Error creating the new folder $targetDir";
    http_response_code(400);
    die($error);
  }
  if (!$ftp->copy($sourceFile, $targetFile)) {
    $error = "Error copying $filename to $targetFile";
    http_response_code(400);
    die($error);
  }
}
if ($dirCount > 0) {
  $warning = "Ignoring $dirCount folders, which already exist on your server.<br />";
}
$zip->close();
$success = "Congratulations, you have successfully updated EZ PayPal.";

ob_end_clean();
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(array('success' => $success, 'warning' => $warning));
exit();
