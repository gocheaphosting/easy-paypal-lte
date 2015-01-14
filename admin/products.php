<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Products</a>
    </li>
  </ul>
</div>
<h3>All Your Products</h3>
<?php
$catSource = EZ::mkCatSource();
$catNames = EZ::mkCatNames();

$products = $db->getData('products');

openBox("Products", "th-list", 12, "<p>The table below listing your products is editable. You can click on the product code, name etc. and enter new values.  You also can set the product inactive (which means it won't be displayed) by clicking on the green <b>Active</b> button. An inactive button will have a red <b>Disabled</b> button.</p>"
        . "<p>The table is also searchable and sortable.</p>"
        . "<p> If you want to create a new product, please click on the <a href='products-edit.php'><b>Add New Product</b></a> button below the table.<p>");
?>
<table class="table table-striped table-bordered responsive data-table">
  <thead>
    <tr>
      <th class="center-text"style='width:3%'>ID</th>
      <th class="center-text"style='width:14%'>Code</th>
      <th style='width:24%'>Name</th>
      <th style='width:24%'>Category</th>
      <th style='width:15%'>File Name</th>
      <th class="right" style='width:8%'>Price</th>
      <th class="center-text" style='width:8%;min-width:90px;'>Active?</th>
      <th class="center-text" style='width:4%;min-width:20px;'></th>
    </tr>
  </thead>
  <tbody>

    <?php
    foreach ($products as $p) {
      extract($p);
      if ($active) {
        $class = 'success';
      }
      else {
        $class = 'danger';
      }
      $catName = @$catNames[$category_id];
      echo <<<EOF
    <tr>
      <td class="center-text">$id</td>
      <td><a href="#" class='xedit' data-name='product_code' data-pk='$id' data-validator='alnum'>$product_code</a></td>
      <td><a href="#" class='xedit' data-name='product_name' data-pk='$id' data-tpl='<input type="text" style="width:250px">' data-validator='notNull'>$product_name</a></td>
      <td><a href="#" class='xedit' data-name='category_id' data-type='select' data-pk='$id' data-title='Category' data-source="$catSource" data-value='$category_id'>$catName</a></td>
      <td><a href='#' class='xedit' data-name='filename' data-pk='$id' data-validator='notNull'>$filename</a></td>
      <td class="center-text"><a href='#' class='xedit' data-name='product_price' data-pk='$id' data-validator='number'>$product_price</a></td>
      <td class="center-text"><a class='xedit-checkbox btn-sm btn-$class' data-name='active' data-type='checklist' data-pk='$id' data-title='Status' data-value='$active'></a></td>
      <td class="center-text">
        <a class="btn-sm btn-info action" href="products-edit.php?pk=$id" title="Edit" data-toggle="tooltip"><i class="glyphicon glyphicon-pencil icon-white action"></i> </a>
      </td>
    </tr>
EOF;
    }
    ?>
  </tbody>
</table>
<a class="btn btn-success action" href="products-edit.php"><i class="glyphicon glyphicon-plus icon-white action"></i>&nbsp;Add New Product</a>
<?php
closeBox();
?>
<script>
  var xeditHanlder = 'ajax/products.php';
</script>
<?php
require 'footer.php';
