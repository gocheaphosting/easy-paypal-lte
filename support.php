<?php
function ezPluginInfo(){
  $me = basename(dirname(__FILE__)) ;
  $plugins = get_plugins() ;
  $ret = array('Version' => '', 'Info' => '') ;
  $break = '' ;
  foreach ($plugins as $k => $p) {
    $baseDir = dirname($k) ;
    if ($baseDir == $me) {
      $version = $p['Version'] ;
      $info = "$break{$p['Title']} V{$p['Version']} (Referer: {$_SERVER['HTTP_REFERER']})" ;
      $ret[] = array('Version' => $version, 'Info' => $info) ;
    }
  }
  return $ret ;
}
function renderSupport($name, $plg) {
  $plugindir = get_option('siteurl') . '/' . PLUGINDIR . '/' .  basename(dirname(__FILE__)) ;
  $value = $plg['value'];
  $desc = $plg['desc'] ;
  $support = $plg['support'] ;
  $url = 'http://www.thulasidas.com/plugins/' . $name . '#FAQ' ;
  $link = '<a href="' . $url . '" target="_blank">' . $value . '</a>' ;
  echo "&nbsp;<a href='http://support.thulasidas.com' onclick=\"popupwindow('http://support.thulasidas.com','ezSupport for $value', 1024, 768);return false;\" title='" ;
  _e('Ask a support question (in English or French only) via ezSupport @ $0.95', 'easy-adsenser') ;
  echo "'><img src='$plugindir/ezsupport.png' class='alignright' border='0' alt='ezSupport Portal'/></a>" ;
  printf(__("If you need help with %s, please read the FAQ section on the $link page. It may answer all your questions.", 'easy-adsenser'), $value, $link) ;
  echo "<br />" ;
  _e("Or, if you still need help, you can raise a support ticket.", 'easy-adsenser') ;
  echo "&nbsp;<a href='http://support.thulasidas.com' onclick=\"popupwindow('http://support.thulasidas.com','ezSupport for $value', 1024, 768);return false;\" title='" ;
  _e('Ask a support question (in English or French only) via ezSupport @ $0.95', 'easy-adsenser') ;
  echo "'>" ;
  _e("[Request Paid Support]", 'easy-adsenser') ;
  echo "</a>&nbsp;<small><em>[" ;
  _e('Implemented using our ezSupport Ticket System.', 'easy-adsenser') ;
  echo "]</em></small>" ;
  $info = ezPluginInfo() ;
  $_SESSION['ezSupport'] = $info[0]['Info'] ;
}

$myPlugins['easy-paypal'] =
  array('value' => 'Easy PayPal',
    'support' => 'YBB5HXSJ97C7E',
    'price' => '6.95',
    'share' => false,
    'long' => false,
    'blurb' => '<em><strong>Easy PayPal</strong></em> is the plugin version of ezPayPal, the simplest possible way to sell your digital goods online. This premium plugin ',
    'desc' => 'helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. [Easy PayPal is a Premium WordPress plugin.]',
    'title' => 'Do you have an application, PHP package, photograph, PDF book (or any other downloadable item) to sell from your blog? Find the set up of a shopping cart system too overwhelming? <em>ezPayPal</em> may be the right solution for you.',
    'pro' => 'The Pro version adds a whole slew of features: Data Security, Sandbox Mode, Template Editors, Automatic Handling of returns, refunds, e-chques etc, Sales Editor, Email Tools, Product Version support, Batch Product File Uploads, Data backup/restore/migration tools and so on. It can also be enhanced with optional modules like Affiliate Package, Reporting Tools etc. This powerful and professional package  provides you with a complete and robust solution for your online business.  <em><strong>ezPayPal Pro</strong></em> provides the most robust and feature-complete solution to sell your digital goods online. It helps you quickly set up an online store to sell any downloadable item, where your buyers can pay for it and get an automatic, expiring download link. The whole flow runs fully automated and designed to run unattended. <em><strong>ezPayPal</strong></em> manages all aspects of selling your digital goods.') ;

renderSupport($plgName, $myPlugins[$plgName]) ;

?>