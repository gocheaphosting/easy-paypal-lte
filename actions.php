<?php
$actions = array() ;
$actions['admin'] =
  array('name' => 'Admin',
    'help' => 'Go to the Admin Control Panel where you can change your PayPal credentials, set options and define new products/items to sell.',
    'needPro' => false);
$actions['pro'] =
  array('name' => 'Pro Features & Tools',
    'help' => '<b>Pro Features and Tools:</b> The following extra features and tools are available in the <b>Pro</b> version.<br />' .
    '<b>Data Security</b>: The <em>Pro</em> version takes special measures to set up data verification links to ensure your sales data is safe and not susceptible to corruption. In technical terms, it checks for the existence of InnoDB in your MySQL installation, and uses it if found, setting up foreign keys to ensure referential integrity, and indices to guarantee performance. The Lite version uses the default MyISAM engine, fast and simple, but not exactly secure.<br />' .
   '<b>Sandbox Mode</b>: In the <em>Pro</em> version, you have the option to choose PayPal sandbox mode so that you can check your setup before going live.<br />' .
   '<b>HTML Emails</b>: In the <em>Pro</em> version, you can send impressive HTML email to your customers rather than the boring plain text messages.<br />' .
   '<b>Template Editor</b>: The email body, thank you page and download display are all editable in the <em>Pro</em> version.<br />' .
'<b>Automatic handling of refunds and disputes</b>: When you issue a refund on the PayPal website, the corresponding sale in your database will be set to inactive. And if a buyer registers a dispute, he (and you) will get a friendly email message stating that the dispute is being reviewed and handled.<br />' .
'<b>E-Check handling</b>: The <em>Pro</em> version recognizes e-check payments and sends a mail to the buyer regarding the delay for the check clearance.<br />' .
   '<b>Sales Editor</b>: You can load a single sale or a bunch of sales on to a friendly interface and change their data. For instance, it will let you change the download expiry date and resend a download notification message -- one of the frequent support requests from the buyers.<br />' .
   '<b>Email Tools</b>: You can select a number of your buyers to notify, for example, of a critical update of your products, or of a free upgrade opportunity.<br />' .
   '<b>Product Version Support</b>: The <em>Pro</em> version supports versioning of your products. It will keep track of the version sold to your buyers and your current versions. So, if you want to send a product and version specific upgrade notice, you can do it with <em>Pro</em> version.<br />' .
   '<b>Batch Upload</b>: The <em>Pro</em> version gives an easy way to upload your product files (when you release new versions, for instance), and keeps track of their versions.<br />' .
   '<b>Additional Tools</b>: The <em>Pro</em> version also gives you a bunch of tools (php example files) that can help you migrate your existing sales data or product definitions.<br />' .
   '<b>Data Migration</b>: Using this <em>Pro</em> tool, your database tables can be automatically upgraded to the later version without losing your sales info and other settings.<br />' .
   '<b>DB Backup</b>: The <em>Pro</em> version has an option to generate a backup of your sales info to download to a safe location.<br />' .
   '<b>DB Restore</b>: It also provides a means to restore (of course) a previously backed up data file, overwriting (or appending to, as you wish) the existing sales info.<br />' .
   '<b>Security Audit</b>: The <em>Pro</em> version provides you with a tool to check your settings and installation for possible security issues. (WIP)<br />' .
   '<b>Upgradeable Products</b>: You can define products that are upgradeable. For instance, you can sell a short eBook at an introductory price. If your buyer likes it, he has the option of buying the full book by paying the difference. (WIP)<br />',
    'needPro' => true);
$actions['affiliate'] =
  array('name' => 'Affiliates',
    'help' => '<b>Optional Package: Affiliate Marketing</b> Turn your satisfied customers into your viral sales force!',
    'needPro' => true);
/* $actions['reports'] = */
/*   array('name' => 'Reports', */
/*     'help' => '<b>Optional Package: Reporting</b> Get detailed reports about your sales and trends.', */
/*     'needPro' => true); */

function mkActionURL($k, $a) {
  $isPro = strpos(getcwd(),"pro") || file_exists("pro/pro.php") ;
  $name = addslashes(htmlspecialchars($a['name'])) ;
  if ($k == 'pro') {
    $width = 400 ;
    if (function_exists('plugin_dir_url')) {
      $ezname = 'easy-paypal' ;
      $confirm = "Easy PayPal Pro is a premium plugin. Would you like to purchase it for \$6.95?" ;
    }
    else {
      $ezname = 'ezpaypal-pro' ;
      $confirm = "EZ PayPal Pro is a paid upgrade. Would you like to purchase it for \$9.95?" ;
    }
  }
  else {
    $ezname = 'ezpaypal-pro' ;
    $price = 9.95 ;
    $width = 300 ;
    $confirm = "$name is an optional feature of the Pro Standalone version of this plugin. Would you like to purchase it for \$$price? ($name can be added on later for \$4.95.)" ;
  }
  $help = sprintf( ' onmouseover="Tip(\'%s\', WIDTH, '. $width . ', TITLE, \'%s\', ' .
          'FIX, [this, 5, 2])" onmouseout="UnTip()" ', htmlspecialchars($a['help']), $name);
  if ($a['needPro'])
    if ($isPro) $ret = sprintf("'%spro/%s.php' %s",
                       $_SESSION['ezppURL'], $k, $help) ;
    else  $ret = "'http://buy.thulasidas.com/$ezname' onclick='return confirm(\"$confirm\");' target='_blank' $help" ;
  else $ret = sprintf("'%s%s.php' %s", $_SESSION['ezppURL'], $k, $help) ;
  return $ret ;
}
function showActions($actions, $button=false) {
  $ret = '' ;
  foreach ($actions as $k => $a) {
    if (!empty($ret)) $ret .= "|" ;
    $name = $a['name'] ;
    if ($button)
      $nameButton = "<input type='button' value='$name' name='$name'>" ;
    else
      $nameButton = $name ;
    $url = mkActionURL($k, $a) ;
    $ret .= "    <a href=$url>$nameButton</a> " ;
  }
  return $ret ;
}
?>