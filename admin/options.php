<?php
require 'header.php';
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
require_once 'OptionTable.php';
require 'options-default.php';
$help = "<p>The tables below listing your options are editable. You can click on the option values and enter new values.  Hover over the <i class='glyphicon glyphicon-question-sign blue'></i> <b>Help</b> button on the row for quick info on what that option does. The options are grouped into PayPal, Other Options and Translations.</p><p>Translations are the strings that will be used in the frontend (customer-facing) pages of EZ PayPal. For instance, the download button has the default text <strong>Download it</strong>. If you would like to change it to <strong>Get it Now</strong> or <strong>T&eacute;l&eacute;charger</strong>, you can enter it in the Translations table.</p>";
$sections = array("PayPal Options" => $paypal, "Other Options" => $options, "Translations" => $i18n);
foreach ($sections as $section => $options) {
  openBox($section, "th-list", 11, $help);
  $help = "";
  $optionTable = new OptionTable($options);
  $optionTable->render();
  closeBox();
}
require 'footer.php';
