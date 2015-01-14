<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Subscription Products</a>
    </li>
  </ul>
</div>

<?php
openBox("Your Subscription Products", "retweet", 12);
?>
<p>Subscription products are digital goods that you sell on a recurring basis. A blog membership, support contract or a paid link placement is an example of a subscription product. This feature is available in the <a href="#" class="goPro">Pro version</a> of this program. </p>
<p>In this lite version, you can still set a product as recurring by editing it, but the subscription settings are not exposed to editing.</p>
<?php
closeBox();
include 'promo.php';
require 'footer.php';