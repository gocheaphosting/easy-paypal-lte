<?php

if (class_exists("Installer")) {
  echo "Problem, class Installer exists! \nCannot safely continue.\n";
  exit;
}
else {
  require_once 'AbstractInstaller.php';

  class Installer extends AbstractInstaller {

    function configure() {
      // Set up name, logo, extra help and tables (to verify and backup).
      $this->name = "EZ PayPal";
      $this->logo = "img/ezpaypal-brand.png";
      $this->help = "<p>You can give the same details as your existing ezPayPal (version older than V6.00) installation by entering the values from its <code>ezppCfg.php</code> to update it to this new version. If you do so, please be warned that the database schema will be modified, and you will not be able to go back to the old version. It is wise to keep a full back of your database.</p><p>If you have the <a href='#' class='goPro'>Pro version</a> of this application, you can migrate the database at a later stage using built-in database tools.</p>";
      $this->tables = array('categories', 'paypal', 'options', 'option_meta', 'templates',
          'products', 'product_meta', 'sales', 'sale_details', 'ipn', 'addresses');
    }

    function migrate($dbBak) { // Data migration
      // See if migration is needed
      $colNames = $dbBak->getColNames('products');
      if (!in_array('product_category', $colNames)) {
        return;
      }
      $db = $this->mkDB();
      $tables = $this->tables;
      //  'categories': new table
      $data = $dbBak->getColData('products', 'product_category');
      $id = 0;
      foreach ($data as $d) {
        $row = array('name' => $d);
        ++$id;
        $row['id'] = $id;
        $row['comment'] = "Migrated from your old ezPayPal";
        $row['active'] = 1;
        $db->putRowData('categories', $row);
      }
      $catLookup = $db->getColData2('categories', 'name', 'id');
      $prodLookup = $dbBak->getColData2('products', 'product_code', 'id');
      $saleLookup = $dbBak->getColData2('sales', 'txn_id', 'id'); // approx
      foreach ($tables as $table) {
        if ($dbBak->tableExists($table)) {
          switch ($table) {
            case 'paypal':
            case 'options':
              $data = $dbBak->getRowData($table);
              foreach ($data as $name => $value) {
                $db->putMetaData('options_meta', array($name => $value));
              }
              break;
            case 'option_meta':
              $data = $dbBak->getColData2($table, 'name', 'value');
              foreach ($data as $name => $value) {
                $db->putMetaData('options_meta', array($name => $value));
              }
              break;
            case 'products':
            case 'templates':
              $data = $dbBak->getData($table);
              foreach ($data as $row) {
                if (!empty($catLookup[$row['product_category']])) {
                  $row['category_id'] = $catLookup[$row['product_category']];
                }
                else {
                  $row['category_id'] = 0;
                }
                $db->putRowData($table, $row);
              }
              break;
            case 'product_meta':
            case 'sales':
              $data = $dbBak->getData($table);
              foreach ($data as $row) {
                $row['product_id'] = $prodLookup[$row['product_code']];
                $db->putRowData($table, $row);
              }
              break;
            case 'sale_details':
              $data = $dbBak->getData($table);
              foreach ($data as $row) {
                if (!empty($saleLookup[$row['txn_id']])) {
                  $row['sale_id'] = $saleLookup[$row['txn_id']];
                }
                else {
                  $row['sale_id'] = 0;
                }
                $db->putRowData($table, $row);
              }
              break;
            default:
              $data = $dbBak->getData($table);
              if (!$db->tableExists($table)) {
                continue;
              }
              foreach ($data as $row) {
                $db->putRowData($table, $row);
              }
          }
        }
      }
    }

    function setup() { // Post install setup, templates definitions etc.
      require_once 'dbSetup-templates.php';
      $db = $this->mkDB();
      mkDefaultTemplates($db);
      if (file_exists('dbSetup-pro-templates.php')) {
        require_once 'dbSetup-pro-templates.php';
        mkProTemplates($db);
      }
    }

  }

}