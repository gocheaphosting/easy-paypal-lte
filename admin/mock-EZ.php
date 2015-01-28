<?php

// fake class EZ
class EZ {

  static $isInWP = false;
  static $isInstallingWP = false;
  static $options = array();
  static $isUpdating = false;

  static function isLoggedInWP() {
    return false;
  }

  static function isInWP() {
    return false;
  }

}
