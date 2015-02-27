<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Sales</a>
    </li>
  </ul>
</div>
<h3>All Your Sales</h3>
<?php
$sales = $db->getData('sales');

openBox("Sales", "th-list", 12, "<p>The table below listing your sales is searchable and sortable.</p>"
        . "<p>If you want to view the details of a sale, please click on the <a href='#' class='btn-sm btn-info'><i class='glyphicon glyphicon-zoom-in icon-white'></i></a> button.<p>");
?>
<table class="table table-striped table-bordered responsive data-table">
  <thead>
    <tr>
      <th class="center-text" style='width:15%;min-width:150px;'>Date</th>
      <th style='width:20%'>Buyer Name</th>
      <th style='width:15%'>Buyer E-Mail</th>
      <th style='width:30%'>Product</th>
      <th class="right" style='width:15%;min-width:150px;'>Expiry</th>
      <th class="center-text" style='width:5%;min-width:65px;'></th>
    </tr>
  </thead>
  <tbody>

    <?php
    foreach ($sales as $s) {
      extract($s);
      echo <<<EOF
    <tr>
      <td>$purchase_date</td>
      <td>$customer_name</td>
      <td>$customer_email</td>
      <td>$product_name</td>
      <td class="center-text">$expire_date</td>
      <td class="center-text">
        <a class="btn-sm btn-info saleInfo" href="#" title="View Details" data-toggle="tooltip" data-pk="$id"><i class="glyphicon glyphicon-zoom-in icon-white action"></i> </a>&nbsp; <a class="btn-sm btn-warning saleDetails" href="#" title="View IPN Message Details (Raw data)" data-toggle="tooltip" data-pk="$id"><i class="glyphicon glyphicon-zoom-in icon-white action"></i>&nbsp;</a>
      </td>
    </tr>
EOF;
    }
    ?>
  </tbody>
</table>
<?php
closeBox();
?>
<script>
  function getSalesData(pk, table, title) {
    $.ajax({url: 'ajax/sales-table.php',
      data: {
        pk: pk,
        table: table
      },
      success: function (sale) {
        $("#myModalText").html(sale);
        $('#myModalTitle').text(title);
        $('#myModalClose').hide();
        $('#myModalSave').text('Done');
        $('#myModal').modal('show');
      },
      error: function (a) {
        flashError(a.responseText);
      }
    });
  }
  $("body").on('click', '.saleInfo', function (e) {
    e.preventDefault();
    var pk = $(this).attr('data-pk');
    getSalesData(pk, 'sales', 'Sale Info');
  });
  $("body").on('click', '.saleDetails', function (e) {
    e.preventDefault();
    var pk = $(this).attr('data-pk');
    getSalesData(pk, 'sale_details', 'Sale Details: IPN Raw Data');
  });
</script>
<?php
require 'footer.php';
