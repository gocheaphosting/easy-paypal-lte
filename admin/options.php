<?php
require('header.php');
?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Configuration</a>
    </li>
  </ul>
</div>

<?php
require 'options-default.php';
openBox("PayPal Options", "th-list", 11, "The table below is editable. You can click on the option values and enter new values.  Hover over the <i class='glyphicon glyphicon-question-sign blue'></i> <b>Help</b> button on the row for quick info on what that option does.");
?>
<table class="table table-striped table-bordered responsive">
  <thead>
    <tr>
      <th style='width:20%;min-width:200px'>Option</th>
      <th style='width:70%'>Value</th>
      <th class="center-text" style='width:10%;min-width:50px'>Help</th>
    </tr>
  </thead>
  <tbody>

    <?php
    foreach ($paypal as $pk => $option) {
      echo EZ::renderOption($pk, $option);
    }
    ?>
  </tbody>
</table>

<?php
closeBox();
openBox("Other Options", "th-list", 11, "The table below is editable. You can click on the option values and enter new values.  Hover over the <i class='glyphicon glyphicon-question-sign blue'></i> <b>Help</b> button on the row for quick info on what that option does.");
?>
<table class="table table-striped table-bordered responsive">
  <thead>
    <tr>
      <th style='width:20%;min-width:200px'>Option</th>
      <th style='width:70%'>Value</th>
      <th class="center-text" style='width:10%;min-width:50px'>Help</th>
    </tr>
  </thead>
  <tbody>

    <?php
    foreach ($options as $pk => $option) {
      echo EZ::renderOption($pk, $option);
    }
    ?>
  </tbody>
</table>

<?php
closeBox();
?>
<script>
  var xeditHandler = 'ajax/options.php';
  $(".reveal").click(function () {
    bootbox.alert($(this).attr('data-value'));
  });
</script>
<?php
require('footer.php');
