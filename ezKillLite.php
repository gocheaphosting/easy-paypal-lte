<?php

/*
  Copyright (C) 2008 www.ads-ez.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!function_exists("ezKillLiteEZPP")) {
  if (!function_exists('is_plugin_active')) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  function ezKillLiteEZPP($pro, $liteEZPP) {
    $proActive = is_plugin_active($pro);
    $liteActive = is_plugin_active($liteEZPP);
    if ($proActive && $liteActive) {
      add_action('init', function() {
        global $liteEZPP;
        deactivate_plugins($liteEZPP);
      });
      printf('<div class="updated"><p>');
      printf(__("%s cannot be active now. Deactivating it so that you can use the Pro version %s If you really want to use the %s version, please deactivate the %s version first.", "easy-common"), "<strong><em>Easy PayPal Lite</em></strong>", "<strong><em>Easy PayPal Pro</em></strong>.<br />", "<strong><em>Lite</em></strong>", "<strong><em>Pro</em></strong>");
      printf("<br /><strong>" . __("Please reload this page to remove stale links.", 'easy-common') . " <input type='button' value='Reload Page' onClick='window.location.href=window.location.href.replace(\"activate=true&\",\"\")'></strong>");
      printf('</p></div>');
      add_action('admin_footer-plugins.php', function() {
        printf('<script>document.getElementById("message").innerHTML="' . "<span style='font-weight:bold;font-size:1.1em;color:red'>Easy PayPal: " . __("Pro Plugin is activated. Lite version is deactivated.", "easy-common") . "</span>" . '";</script>');
      });
    }
  }

}

$pro = "$plgSlug/easy-paypal.php";
$liteEZPP = "easy-paypal-lte/easy-paypal-lite.php"; // to put it global scope
if (class_exists("EzPayPal")) {
  $liteEZPP = "easy-paypal-lte/easy-paypal-lite.php";
  ezKillLiteEZPP($pro, $liteEZPP);
}
if (class_exists("EzPayPalPro")) {
  $liteEZPP = "easy-paypal/easy-paypal.php";
  ezKillLiteEZPP($pro, $liteEZPP);
}
// This works only because of the happy accident that p(ro) comes after l(ite)
// So EZ PayPal appears before EZ PayPal Pro in the listing (if installed under wp-content/plugins/ezpaypal).
if (class_exists("EzPayPal6")) {
  $liteEZPP = "ezpaypal/easy-paypal.php";
  ezKillLiteEZPP($pro, $liteEZPP);
}

//  && $plgSlug != 'ezpaypal-pro'