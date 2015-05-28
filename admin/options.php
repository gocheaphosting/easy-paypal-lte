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
$help = "<p>The tables below listing your options are editable. You can click on the option values and enter new values.  Hover over the <i class='glyphicon glyphicon-question-sign blue'></i> <b>Help</b> button on the row for quick info on what that option does. The options are grouped into PayPal, Other Options and Translations.</p><p>Translations are the strings that will be used in the frontend (customer-facing) pages of EZ PayPal. For instance, the download button has the default text <strong>Download it</strong>. If you would like to change it to <strong>Get it Now</strong> or <strong>T&eacute;l&eacute;charger</strong>, you can enter it in the Translations table.</p>";
$sections = array("PayPal Options" => $paypal, "Other Options" => $options, "Translations" => $i18n);
foreach ($sections as $section => $options) {
  openBox($section, "th-list", 11, $help);
  $help = "";
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
}
?>
<script>
  var xeditHandler = 'ajax/options.php';
  $(".reveal").click(function () {
    bootbox.alert($(this).attr('data-value'));
  });
</script>
<?php
require('footer.php');
