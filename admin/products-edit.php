<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Create or Update Product</a>
    </li>
  </ul>
</div>
<style type="text/css">
  label{width:100px;}
</style>

<?php
$catSource = EZ::mkCatSource();
$catNames = EZ::mkCatNames();

$product = array();
if (!empty($_REQUEST['pk'])) {
  $pk = $_REQUEST['pk'];
  $product = EZ::getProduct($pk);
  if (!empty($product)) {
    $pk = $product['pk'] = $product['id'];
  }
}
else {
  $pk = 0;
}

insertAlerts(11);
openBox("Edit Product", "plus", 11, "<p>On this page, you can create a Product or edit an existing one. Please enter new values for the attributes by clicking on the editable values in the pane. If you are happy with it, you can save the product your database by clicking the corresponding button.</p>"
        . "<p>Even after saving, you can still edit and update the attributes like Product Name, Price etd. You can also list all your <b><a href='products.php'>Products</a></b> to edit any one of them later.</p>"
        . "<p>Note that if you try to create a new Product with the same Product Code as an existing one, your new values will overwrite the existing record in your database.</p>");

require_once 'product-attributes.php';
?>
<table class="table table-striped table-bordered responsive">
  <tbody>
    <?php
    foreach ($prodAttr as $slug => $attribute) {
      $attribute['slug'] = $slug;
      if (isset($product[$slug])) {
        $attribute['value'] = $product[$slug];
      }
      else if (!isset($attribute['value'])) {
        $attribute['value'] = '';
      }
      echo EZ::renderRow($pk, $attribute);
    }
    ?>
  </tbody>
</table>
<p>
  <?php
  $ajaxData = array_keys($prodAttr);
  if ($pk == 0) {
    unset($ajaxData[0]);
    $ajaxHandler = 'ajax/success.php';
    $ajaxAction = 'create';
    ?>
    <a id="createProd" class='btn btn-success' title='Save to Database' data-trigger='hover' data-placement='top' data-toggle="popover" data-content='Insert this category into your database. Please ensure that all the required parameters are specified.' href="#"><i class="glyphicon glyphicon-save icon-white"></i> Save New Product</a> &nbsp;
    <a class='btn btn-warning' title='List Products' data-trigger='hover' data-placement='top' data-toggle="popover" data-content='Go back to view all your products in an editable table form. This will discard any new product being defined unless you have saved it.' href="products.php"><i class="glyphicon glyphicon-list-alt icon-white"></i> Back to Products List</a>
    <?php
  }
  else {
    if (file_exists('products-meta.php')) {
      ?>
      <a class='btn btn-success' title='Edit Meta Data' data-trigger='hover' data-placement='top' data-toggle="popover" data-content='Edit Product Meta Data. Meta data is extra, free-form data you can attach to a product description. You can display it on your product description and download pages.' href="products-meta.php?pk=<?php echo $pk; ?>"><i class="glyphicon glyphicon-pencil icon-white"></i> Edit Meta Data</a>&nbsp;
      <?php
    }
    if (file_exists('subscriptions-edit.php')) {
      ?>
      <a id='subscriptionEdit' class='btn btn-success' style='display:none' title='Edit Subscription Data' data-trigger='hover' data-placement='top' data-toggle="popover" data-content='Since this is a subscription product, you can modify the subscripiton details by clicking here.' href="subscriptions-edit.php?pk=<?php echo $pk; ?>"><i class="glyphicon glyphicon-retweet icon-white"></i> Edit Subscription Details</a>&nbsp;
      <?php
    }
    $ajaxHandler = 'ajax/products.php';
    $ajaxAction = 'update';
    ?>
    <a class='btn btn-primary' title='List Products' data-trigger='hover' data-placement='top' data-toggle="popover" data-content='Go back to view all your products in an editable table form.' href="products.php"><i class="glyphicon glyphicon-list-alt icon-white"></i> Back to Products List</a>
  </p>
  <?php
}
$ajaxData = '["' . implode('", "', $ajaxData) . '"]';

closeBox();
?>
<script>
  var xeditHandler = '<?php echo $ajaxHandler; ?>';
  function showSubscriptionEdit() {
    if (xeditHandler === 'ajax/products.php') {
      var id = $("#recurring");
      if (id.hasClass('btn-danger')) {
        $("#subscriptionEdit").hide();
      }
      if (id.hasClass('btn-success')) {
        $("#subscriptionEdit").show();
      }
    }
  }

  $(document).ready(function () {
    showSubscriptionEdit();
    var pk;
    var file;
    $('#createProd').click(function () {
      var go = true;
      var id = $(this).attr('id');
      var attributes = <?php echo $ajaxData; ?>;
      var data = {action: '<?php echo $ajaxAction; ?>'};
      $.each(attributes, function (i, val) {
        var $id = $('#' + val);
        var newValue = $id.text();
        data[val] = newValue;
        var validator = $id.attr('data-validator');
        if (validator) {
          var validationMsg = validate[validator](newValue);
          if (validationMsg) {
            go = false;
            $id.html(newValue +
                    '<br><span style="font-weight:bold;font-size:0.8em;font-style:normal">' +
                    validationMsg + '</span>');
          }
        }
      });
      if (go) {
        $.ajax({url: 'ajax/products.php',
          type: 'POST',
          data: data,
          success: function (pkRet) {
            pk = pkRet;
            $("#pk").text(pk);
            $("#createProd").attr('disabled', 'disabled').
                    text('Already Saved').fadeOut(2000);
            xeditHandler = 'ajax/products.php';
            $('.xedit, .xedit-checkbox').editable('option', 'url', xeditHandler)
                    .editable('option', 'pk', pk)
                    .editable('option', 'params', function (params) {
                      params.action = 'update';
                      return params;
                    });
            setTimeout(function () {
              showSubscriptionEdit();
            }, 25);
            ajaxUpload(pk, file);
          },
          error: function (a) {
            flashError(a.responseText);
          }
        });
      }
    });
    if (xeditHandler === 'ajax/products.php') {
      setTimeout(function () {
        $("#recurring").editable('option', 'success', function (response, value) {
          window.location.reload(true);
        });
      }, 25);
    }
    function ajaxUpload(_pk, _file) {
      if (!_file) {
        flashWarning("No file uploaded.");
        return;
      }
      var data = new FormData();
      data.append('file', _file);
      data.append('pk', _pk);
      var td = $("#fileinput").closest('td');
      $(td).html('<span class="center red" style="font-size:1.1em;width:100%"><i class="fa fa-spinner fa-spin"></i> Working! Please wait...</span>');
      $.ajax({
        url: 'ajax/products-edit.php',
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        success: function () {
          var msg = "Uploaded <code>" + _file.name + "</code> and replaced the file of <b>" + $("#product_name").text() + "</b> with it.";
          flashSuccess(msg);
          $(td).html(msg + ' <small>(<a href="#" onclick="location.reload();return false;">Upload another file</a>)</small>');
        },
        error: function (a) {
          showError(a.responseText);
        },
        complete: function (a) {
          if (typeof a !== "object")
            flashWarning(a);
        }
      });
    }
    $("#fileinput").on('change', function (event) {
      file = event.target.files[0];
      if (file) {
        if (!pk) { // pk is not set by AJAX for new product. Set it from attribute
          pk = $(event.target).attr('data-pk');
        }
        if (pk && pk !== '0') {
          bootbox.confirm("Are you sure you want to upload <code>" + file.name + "</code> to <b>" + $("#product_name").text() + "</b>?", function (result) {
            if (result) {
              ajaxUpload(pk, file);
            }
            else {
              flashWarning("File not uploaded. Browse again to upload and associate a new file with the product <b>" + $("#product_name").text() + "</b>.");
            }
          });
        }
      }
    });
  });
</script>
<?php
require('footer.php');
