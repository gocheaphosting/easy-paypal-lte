<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Edit Your Sales</a>
    </li>
  </ul>
</div>

<?php
if (!empty($_REQUEST['pk'])) {
  $pk = $db->escape($_REQUEST['pk']);
  $sales = $db->getData('sales', '*', array('id' => $pk));
  if (!empty($sales)) {
    $sale = $sales[0];
  }
}
else {
  $sale = array();
  EZ::showError("No Sale ID specified!");
}

function mkAttr($s) {
  return ucwords(str_replace("_", " ", $s));
}

$saleAttr = array_map("mkAttr", array_keys($sale));

insertAlerts(10);
openBox("Edit Your Sales", "barcode", 10, "<p>In the Pro version, you can load a single sale or a bunch of sales on to a friendly interface and change their data. For instance, it will let you change the download expiry date and resend a download notification message -- one of the frequent support requests from the buyers.</p>
  <p>The Pro version will also link useful tools to the sales details table, such as the product details, customer purchase history, the raw IPN data from PayPal etc.</p>
<p>In this lite version, you will have to do direct database manipulation to change the sales data.</p>");
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
<p>
  <a class='btn btn-primary' title='List Products' data-trigger='hover' data-placement='top' data-toggle="popover" data-content='View all your products in a searchable/sortable table form.' href="sales.php"><i class="glyphicon glyphicon-list-alt icon-white"></i> Back to Sales List</a>
</p>
<?php
closeBox();
require 'footer.php';
