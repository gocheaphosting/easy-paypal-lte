<?php

require_once('../EZ.php');

$posted_validator = '';

extract($_REQUEST, EXTR_PREFIX_ALL, 'posted');
if ($posted_validator) { // a server-side validator is specified
  $fun = "validate_$posted_validator";
  if (method_exists('EZ', $fun)) {
    $valid = EZ::$fun($posted_value);
  }
  else {
    header('HTTP 400 Bad Request', true, 400);
    die("Unknown validator ($posted_validator) specified");
  }
  if ($valid !== true) {
    header('HTTP 400 Bad Request', true, 400);
    die("$valid");
  }
}

header('HTTP 200 Done', true, 200);
