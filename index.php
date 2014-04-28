<?php

session_start();
include_once('htmlHelper.php');
include_once('dbHelper.php');
include_once('formHelper.php');
include_once('ezpp.php');

$html = new HtmlHelper();
$ezDB = new DbHelper();
$form = new FormHelper($ezDB, $html); // will die if DB connection fails
$ezpp = new EzPP($ezDB);

if (!$ezpp->isLoggedIn()) {
  if (file_exists("ez-manoj.php")) {
    header("location:ez-manoj.php");
  }
  else {
    header("location:ez-shop.php");
  }
}
$actions = array();
$actions['setup'] = array('name' => __('Initial Setup', 'easy-paypal'),
    'warning' => __('Initial Setup and Configuration.', 'easy-paypal'),
    'help' => __('You can change only a limited number of them here for security reasons.', 'easy-paypal'),
    'value' => __('Setup ezPayPal', 'easy-paypal'),
    'type' => 'submit');
$actions['admin'] = array('name' => __('Admin Control', 'easy-paypal'),
    'warning' => __('Configure your PayPal account, products etc.', 'easy-paypal'),
    'help' => __('You can set advanced options like SandBox mode, HTML emailing etc. on this panel.', 'easy-paypal'),
    'value' => __('Admin Control Panel', 'easy-paypal'),
    'type' => 'submit');
$actions['pro'] = array('name' => __('Pro Tools', 'easy-paypal'),
    'warning' => __('Access your Pro tools', 'easy-paypal'),
    'help' => 'like Batch Upload, Template/Sales Editors, E-Mail/Data Migration/Backup/Restore Tools, Affiliate and Reporting Engines etc.',
    'value' => __('Pro Tools Panel', 'easy-paypal'),
    'type' => 'submit');
$actions['pro']['disabled'] = !file_exists('pro/pro.php');
$actions['shop'] = array('name' => __('Go to Shop', 'easy-paypal'),
    'warning' => __('View your auto-generatred shopfront.', 'easy-paypal'),
    'help' => __('This button takes you to the auto-generated e-shop while keeping you logged in so that you can return to this screen if needed.', 'easy-paypal'),
    'value' => __('Go to E-Shop', 'easy-paypal'),
    'type' => 'submit');
foreach ($actions as $k => $a) {
  $actions[$k]['help'] = $a['warning'] . ' ' . $a['help'];
}
if (!empty($_POST['setup'])) {
  $html->redirect("setup.php");
}
if (!empty($_POST['pro'])) {
  $html->redirect("pro/pro.php");
}
if (!empty($_POST['admin'])) {
  $html->redirect("admin.php");
}
if (!empty($_POST['shop'])) {
  $html->redirect("ez-shop.php");
}
$html->ezppHeader(__('Mission Control', 'easy-paypal'), __('Welcome to ezPayPal!<br />Please choose and action', 'easy-paypal'));
$form->renderForm('setup', $actions, __('Choose an Action', 'easy-paypal'), '', '');
$html->ezppFooter();
