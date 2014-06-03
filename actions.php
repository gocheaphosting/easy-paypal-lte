<?php

$actions = array();
$actions['admin'] = array('name' => 'Admin',
    'help' => __('Go to the Admin Control Panel where you can change your PayPal credentials, set options and define new products/items to sell.', 'easy-paypal'),
    'needPro' => false);
//$actions['setup'] = array('name' => 'Setup',
//    'help' => __('Go to the initial set up screen to specify your credentials and set up your database connections.', 'easy-paypal'),
//    'needPro' => false);
if (file_exists('pro/batch.php')) {
  $actions['batch'] = array('name' => __('Batch Upload', 'easy-paypal'),
      'help' => __('Copy your product files from a server storage location to ezPayPal storage area.', 'easy-paypal'),
      'needPro' => true);
}
$helpStrings = array(__('Pro Features and Tools', 'easy-paypal') => __('The following extra features and tools are available in the <b>Pro</b> version.', 'easy-paypal'),
    __('Data Security', 'easy-paypal') => __('The <em>Pro</em> version takes special measures to set up data verification links to ensure your sales data is safe and not susceptible to corruption. In technical terms, it checks for the existence of InnoDB in your MySQL installation, and uses it if found, setting up foreign keys to ensure referential integrity, and indices to guarantee performance. The Lite version uses the default MyISAM engine, fast and simple, but not exactly secure.', 'easy-paypal'),
    __('Sandbox Mode', 'easy-paypal') => __('In the <em>Pro</em> version, you have the option to choose PayPal sandbox mode so that you can check your setup before going live.', 'easy-paypal'),
    __('HTML Emails', 'easy-paypal') => __('In the <em>Pro</em> version, you can send impressive HTML email to your customers rather than the boring plain text messages.', 'easy-paypal'),
    __('Template Editor', 'easy-paypal') => __('The email body, thank you page and download display are all editable in the <em>Pro</em> version.', 'easy-paypal'),
    __('Automatic handling of refunds and disputes', 'easy-paypal') => __('When you issue a refund on the PayPal website, the corresponding sale in your database will be set to inactive. And if a buyer registers a dispute, he (and you) will get a friendly email message stating that the dispute is being reviewed and handled.', 'easy-paypal'),
    __('E-Check handling', 'easy-paypal') => __('The <em>Pro</em> version recognizes e-check payments and sends a mail to the buyer regarding the delay for the check clearance.', 'easy-paypal'),
    __('Sales Editor', 'easy-paypal') => __('You can load a single sale or a bunch of sales on to a friendly interface and change their data. For instance, it will let you change the download expiry date and resend a download notification message -- one of the frequent support requests from the buyers.', 'easy-paypal'),
    __('Email Tools', 'easy-paypal') => __('You can select a number of your buyers to notify, for example, of a critical update of your products, or of a free upgrade opportunity.', 'easy-paypal'),
    __('Product Version Support', 'easy-paypal') => __('The <em>Pro</em> version supports versioning of your products. It will keep track of the version sold to your buyers and your current versions. So, if you want to send a product and version specific upgrade notice, you can do it with <em>Pro</em> version.', 'easy-paypal'),
    __('Batch Upload', 'easy-paypal') => __('The <em>Pro</em> version gives an easy way to upload your product files (when you release new versions, for instance), and keeps track of their versions.', 'easy-paypal'),
    __('Additional Tools', 'easy-paypal') => __('The <em>Pro</em> version also gives you a bunch of tools (php example files) that can help you migrate your existing sales data or product definitions.', 'easy-paypal'),
    __('Data Migration', 'easy-paypal') => __('Using this <em>Pro</em> tool, your database tables can be automatically upgraded to the later version without losing your sales info and other settings.', 'easy-paypal'),
    __('DB Backup', 'easy-paypal') => __('The <em>Pro</em> version has an option to generate a backup of your sales info to download to a safe location.', 'easy-paypal'),
    __('DB Restore', 'easy-paypal') => __('It also provides a means to restore (of course) a previously backed up data file, overwriting (or appending to, as you wish) the existing sales info.', 'easy-paypal'),
    __('Security Audit', 'easy-paypal') => __('The <em>Pro</em> version provides you with a tool to check your settings and installation for possible security issues. (WIP)', 'easy-paypal'),
    __('Upgradeable Products', 'easy-paypal') => __('You can define products that are upgradeable. For instance, you can sell a short eBook at an introductory price. If your buyer likes it, he has the option of buying the full book by paying the difference. (WIP)', 'easy-paypal'));
$helpText = '';
foreach ($helpStrings as $k => $v) {
  $helpText .= "<b>$k</b>: $v<br/>";
}
$ezppURL = plugins_url('',__FILE__);
$actions['reports'] = array('name' => 'Reports',
    'help' => "<b>Optional Package: Reporting</b> Get detailed reports about your sales and visualize trends using beautiful charts.",
    'image' => "$ezppURL/screenshot-10.png",
    'needPro' => true);
$actions['subscribe'] = array('name' => 'Subscription',
    'image' => "$ezppURL/screenshot-11.png",
    'help' => '<b>Optional Package: Subscription</b> Easily set up and sell subscription products, such time-limited access to your sites, news letters etc.',
    'needPro' => true);
$actions['ultra'] = array('name' => __('Ultra Edition', 'easy-paypal'),
    'help' => "The Ultra Edition of the plugin is the Pro version with both the Reporting Engine and the Subscription Module pre-installed.",
    'needPro' => true);
$actions['pro'] = array('name' => __('Pro Functions', 'easy-paypal'),
    'help' => $helpText,
    'needPro' => true);

//$actions['affiliate'] = array('name' => 'Affiliates',
//    'help' => __('<b>Optional Package: Affiliate Marketing</b> Turn your satisfied customers into your viral sales force!', 'easy-paypal'),
//    'needPro' => true);

function mkActionURL($k, $a) {
  $name = addslashes(htmlspecialchars($a['name']));
  if ($k == 'pro') {
    $width = 400;
    if (function_exists('plugin_dir_url')) {
      $ezname = 'easy-paypal';
      $confirm = __("Easy PayPal Pro is a premium plugin. Would you like to purchase it for \$6.95?", 'easy-paypal');
    }
    else {
      $ezname = 'ezpaypal-pro';
      $confirm = __("EZ PayPal Pro is a paid upgrade. Would you like to purchase it for \$9.95?", 'easy-paypal');
    }
  }
  else if ($k == 'ultra') {
    $width = 400;
    if (function_exists('plugin_dir_url')) {
      $ezname = 'easy-paypal-ultra';
      $confirm = __("Easy PayPal Ultra does everything that Easy PayPal Pro does, plus it includes the reporting engine as well as the subscription module. Would you like to purchase it for \$13.95?", 'easy-paypal');
    }
    else {
      $ezname = 'ezpaypal-pro';
      $confirm = __("EZ PayPal Pro is a paid upgrade. Would you like to purchase it for \$9.95?", 'easy-paypal');
    }
  }
  else {
    $ezname = "easy-paypal-$k";
    $price = 9.95;
    $width = 300;
    $confirm = "$name is an optional module to the Pro version of this plugin. Would you like to purchase the Pro version and the module together for \$$price? (Limited time offer)";
  }
  if (!empty($a['image'])) {
    $image = "&nbsp;Example:<br /><img src=\"{$a['image']}\" width=\"100%\" \>";
  }
  else {
    $image = '';
  }
  $help = sprintf(' onmouseover="Tip(\'%s\', WIDTH, ' . $width . ', TITLE, \'%s\', ' .
          'FIX, [this, 5, 2])" onmouseout="UnTip()" ', htmlspecialchars($a['help'].$image), $name);
  if ($a['needPro']) {
    $ret = "'http://buy.thulasidas.com/$ezname' onclick='return confirm(\"$confirm\");' target='_blank' $help";
  }
  else {
    $ret = sprintf("'%s%s.php' %s", $_SESSION['ezppURL'], $k, $help);
  }
  return $ret;
}

function showActions($actions, $button = false) {
  $ret = '';
  foreach ($actions as $k => $a) {
    if (!empty($ret)) {
      $ret .= "|";
    }
    $name = $a['name'];
    if ($button) {
      $nameButton = "<input type='button' value='$name' name='$name'>";
    }
    else {
      $nameButton = $name;
    }
    $url = mkActionURL($k, $a);
    $ret .= "    <a href=$url>$nameButton</a> ";
  }
  return $ret;
}
