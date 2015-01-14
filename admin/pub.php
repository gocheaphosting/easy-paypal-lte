<?php
$no_visible_elements = true;
require_once('header.php');
?>
<div class="row">
  <div class="col-md5 center">
    <h2 class="col-md5"><img alt="EZ PayPal Logo" src="img/ezpaypal-brand.png" style="max-width: 250px;"/><br /><br />
      Welcome to EZ PayPal</h2><br /><br />
  </div>
  <!--/span-->
</div><!--/row-->
<?php
openBox("Welcome to EZ PayPal", "glass", 12);
include('intro.php');
closeBox();
openBox("<a href='http://buy.thulasidas.com/ezpaypal' class='goPro'>Start Your Own E-Shop Now!</a>", "shopping-cart", 12);
if (!empty($no_visible_elements)) {
  ?>
  <a href="index.php" class="btn btn-success launch" style="float:right" data-toggle="tooltip" title="Launch the installer now"> <i class="glyphicon glyphicon-cog"></i> Launch Installer</a>
  <?php
}
?>
<h3>Installation</h3>
<h4>Installing the package is simple</h4>
<ol>
  <li>First, upload the contents of the zip archive you <a href='http://buy.thulasidas.com/lite/ezpaypal-lite.zip'>downloaded</a> or <a href='http://buy.thulasidas.com/ezpayal'>purchased</a> to your server. (Given that you are reading this page, you have probably already completed this step.)</li>
  <li><a href="index.php">Launch the installer</a> by visiting the admin interface using your web browser.
  </li>
  <li>Enter the DB details and set up and Admin account in a couple of minutes and you are done with the installation!</li>
</ol>

<p>Note that in the second step, your web server will try to create a configuration file where you uploaded the <code>ezpaypal</code> package. If it cannot do that because of permission issues, you will have to create an empty file <code>dbCfg.php</code> and make it writeable. Don't worry, the setup will prompt you for it with detailed instructions.</p>

<h4>To get started with your e-shop</h4>

<ol>
  <li><a href='products-edit.php'>Create</a> a product or two.</li>
  <li>Visit the <a href='../shop.php'>shop page</a> to see everything shows up okay.</li>
  <li>Optionally, <a href='http://ads-ez.com'>publicize</a> your shop and wait for your customers to make purchases.</li>
</ol>

<h4>Upgrading to Pro</h4>
<p>If you would like to have the Pro features, purchase the <a class="goPro" href='http://buy.thulasidas.com/ezpaypal'>Pro version</a> for $19.95. You will get an instant download link, and painless upgrade path with all your products, categories and sale data saved, including your admin credentials.</p>

<p class="red">Remember to take a quick <b><a href="tour.php">tour</a></b> to know what this application can do for you.</p>


<?php
closeBox();
include('promo.php');
require('footer.php');
