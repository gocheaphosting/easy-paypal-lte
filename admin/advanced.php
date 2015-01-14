<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Advanced Tools</a>
    </li>
  </ul>
</div>

<?php
openBox("Advanced Tools", "cog", 12);
?>
<h3>Advanced Tools and Options</h3>
<p>This page is a collection of advanced options to further tweak your EZ PayPal installation just the way you like it.</p>
<p> The following options are available in this advanced section of the <a href="#" class="goPro">Pro version</a> of this program. </p>

<ul>
  <li><b>Locale</b>: You can have EZ PayPal in your language. </li>
  <li><b>SMTP Email Support</b>: SMTP mail seems to pass spam filters better than the default PHP PEAR mail.</li>
  <li><b>Update Section</b>: You can show a product update section on your e-shop, and set the update price.</li>
  <li><b>E-Shop Options</b>: The <a href="#" class="goPro">Pro version</a> features an enhanced e-shop with the following extra features, and more:
    <ul>
      <li>You can display your e-shop as a table or tiles.</li>
      <li>You can also customize the columns displayed in the tables or tiles.</li>
      <li>You can add pictures to each product that can be displayed along with the product.</li>
      <li>You can also set up a detailed description to be shown to your customer upon request.</li>
    </ul>
  </li>
  <li><b>Window Mode</b>: When your customer clicks on a Buy Now link, the PayPal transaction can happen in a popup window, new tab/window, or in the same window, as you prefer.</li>
  <li><b>Branding on PayPal Checkout</b>: You can specify your own images to be used as banners on the PayPal checkout window, to drive home your brand awareness.</li>
</ul>
<?php
closeBox();
include('promo.php');
require('footer.php');
