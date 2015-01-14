<?php

// fake class EZ
class EZ {

  static $isInWP = false;
  static $isInstallingWP = false;
  static $options = array();

  static function isLoggedInWP() {
    return false;
  }

}
