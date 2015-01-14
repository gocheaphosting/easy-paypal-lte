<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Optional Module - Express Checkout</a>
    </li>
  </ul>
</div>

<?php
openBox("EZ Affiliates", "plus-sign", 12);
?>
<p>The <a href="#" class="goPro">Pro version</a> of EZ PayPal can be enhanced with optional modules. Note that these modules will not work with the lite version of the program.</p>
<p>Express Checkout is a way to integrate PayPal even more seamlessly with your web site by letting your buyer complete the purchase process without leaving your product page. The table below from (<a href="https://www.paypal.com/sg/cgi-bin/webscr?cmd=_additional-payment-ref-impl1" target="_blank">PayPal</a>) summarizes its features. The Express Checkout module can be purchased for $9.95.</p>

<table class="table table-striped table-bordered responsive" width="100%">
  <tr bgcolor="#CCDDEE">
    <td></td>
    <td>PayPal Express Checkout</td>
    <td>Standard Checkout</td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">What technical skills are needed?</td>
    <td>You integrate PayPal using Name-Value Pair APIs.</td>
    <td>You integrate PayPal using HTML.</td>
  </tr>
  <tr>
    <td bgcolor="#EEEEEE">Do my customers pay on my website or at PayPal?</td>
    <td>Your customer makes their payment and completes their order on your website. This enables tighter integration with your website and order management processes.</td>
    <td>Your customer makes their payment and completes their order at PayPal.</td></tr><tr><td bgcolor="#EEEEEE">How does PayPal integrate with my existing systems?</td>
    <td>The notice of successful payment authorisation is provided to you real-time, before you submit the order to your database.</td>
    <td>The notice of successful payment authorisation is provided to you via either email or Instant Payment Notification.</td>
  </tr>
</table>

<p><a href='http://www.thulasidas.com/packages/express-checkout/' target='_blank'>Learn more </a> about it or <a href="" id='express-checkout' class="goPro">buy it</a>.</p>

<?php
closeBox();
include('promo.php');
require('footer.php');
