<?php

function ezImport($db, $dbBak, $tableSet) {
  $tables = $dbBak->getTableNames(true);
  foreach ($tables as $table) {
    $tableStub = str_replace($dbBak->dbPrefix, '', $table);
    if (in_array($tableStub, $tableSet) && $db->tableExists($tableStub)) {
      $data = $dbBak->getData($table);
      foreach ($data as $row) {
        $db->putRowData($tableStub, $row);
      }
    }
  }
}

function ezMigrate($db, $dbBak, $tables) {
  // See if migration is needed
  $colNames = $dbBak->getColNames('products');
  if (!in_array('product_category', $colNames)) {
    return;
  }
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
