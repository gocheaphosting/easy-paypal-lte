<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Email Your Customers</a>
    </li>
  </ul>
</div>

<?php
openBox("Email Your Customers", "envelope", 12);
?>
<p>In the <a href="#" class="goPro">Pro version</a> of this program, you have an Email Tools. You can select a number of your buyers to notify, for example, of a critical update of your products, or of a free upgrade opportunity. </p>
<p>The Email tool lets you filter your customers in a variety of ways, based on sales, products, time, purchase total etc.</p>
<?php
closeBox();
include 'promo.php';
require 'footer.php';