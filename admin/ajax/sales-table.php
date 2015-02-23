<?php
require_once('../../EZ.php');

if (!EZ::isLoggedIn()) {
  http_response_code(400);
  die("Please login before accessing sales info!");
}

$row = $_REQUEST;
if (empty($row['pk'])) {
  http_response_code(400);
  die("No primary key supplied for sales record");
}
$pk = $row['pk'];
if (!empty($row['table'])) {
  $table = $row['table'];
}
if (!$db->tableExists($table)) {
  http_response_code(400);
  die("Wrong table name: $table!");
}
if ($table == 'sales') {
  $id = 'id';
}
else if ($table == 'sale_details') {
  $id = 'sale_id';
}
else {
  http_response_code(400);
  die("Wrong table name: $table!");
}
$sales = $db->getData($table, '*', array($id => "$pk"));
if (!empty($sales)) {
  $sale = $sales[0];
}

function mkAttr($s) {
  return ucwords(str_replace("_", " ", $s));
}

$saleAttr = array_map("mkAttr", array_keys($sale));
http_response_code(200);
?>
<table class="table table-striped table-bordered responsive">
  <tbody>
    <?php
    foreach ($sale as $key => $val) {
      $attr = mkAttr($key);
      echo "<tr><td>$attr</td><td>$val</td></tr>";
    }
    ?>
  </tbody>
</table>
