<?php
session_start() ;
include_once('htmlHelper.php') ;
include_once('dbHelper.php');
include_once('formHelper.php') ;
include_once('ezpp.php') ;

$html = new htmlHelper() ;
$ezDB = new dbHelper();
$form = new formHelper(&$ezDB, &$html) ; // will die if DB connection fails
$ezpp = new ezpp(&$ezDB) ;

if (!$ezpp->isLoggedIn()) {
  if (file_exists("ez-manoj.php")) header("location:ez-manoj.php") ;
  else header("location:ez-shop.php") ;
}
$actions = array() ;
$actions['setup'] =
  array('name' => 'Initial Setup',
    'warning' => 'Initial Setup and Configuration.',
    'help' => 'You can change only a limited number of them here for security reasons.',
    'value' => 'Setup ezPayPal',
    'type' => 'submit') ;
$actions['admin'] =
  array('name' => 'Admin Control',
    'warning' => 'Configure your PayPal account, products etc.',
    'help' => 'You can set advanced options like SandBox mode, HTML emailing etc. on this panel.',
    'value' => 'Admin Control Panel',
    'type' => 'submit') ;
$actions['pro'] =
  array('name' => 'Pro Tools',
    'warning' => 'Access your Pro tools',
    'help' => 'like Batch Upload, Template/Sales Editors, E-Mail/Data Migration/Backup/Restore Tools, Affiliate and Reporting Engines etc.',
    'value' => 'Pro Tools Panel',
    'type' => 'submit') ;
$actions['pro']['disabled'] = !file_exists('pro/pro.php') ;
$actions['shop'] =
  array('name' => 'Go to Shop',
    'warning' => 'View your auto-generatred shopfront.',
    'help' => 'This button takes you to the auto-generated e-shop while keeping you logged in so that you can return to this screen if needed.',
    'value' => 'Go to E-Shop',
    'type' => 'submit') ;
foreach ($actions as $k => $a) {
  $actions[$k]['help'] = $a['warning'] . ' ' . $a['help'] ;
}
if (!empty($_POST['setup'])) $html->redirect("setup.php") ;
if (!empty($_POST['pro'])) $html->redirect("pro/pro.php") ;
if (!empty($_POST['admin'])) $html->redirect("admin.php") ;
if (!empty($_POST['shop'])) $html->redirect("ez-shop.php") ;
$html->ezppHeader('Mission Control', 'Welcome to ezPayPal!<br />Please choose and action') ;
$form->renderForm('setup', $actions, 'Choose an Action', '', '') ;
$html->ezppFooter() ;
?>
