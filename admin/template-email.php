<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Email Templates</a>
    </li>
  </ul>
</div>

<?php
openBox("Email Templates", "envelope", 12);
?>
<h3>Email Templates</h3>
<p>EZ PayPal can use multiple email templates to inform your customers of their purchase status, updates etc. In this section, you will find the tools to manage the email templates.</p>
<p>This page is available in the <a href="#" class="goPro">Pro version</a> of this program. In the lite version, you get a set of default templates, which you can modify using your favorite database tool, if you need to.</p>
<hr>
<h4>Screenshot of the Email Template Editor from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(17);
?>
<div class="clearfix"></div>
<?php
closeBox();
include('promo.php');
require('footer.php');
