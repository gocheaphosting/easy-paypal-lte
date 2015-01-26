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
    http_response_code(400);
    die("Unknown validator ($posted_validator) specified");
  }
  if ($valid !== true) {
    http_response_code(400);
    die("$valid");
  }
}

http_response_code(200);
