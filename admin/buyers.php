<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Your Customers</a>
    </li>
  </ul>
</div>

<?php
openBox("All Your Customers", "user", 12);
?>
<p>On this page, in the <a href="#" class="goPro">Pro version</a>, you will have see your buyer information such as purchase history, total amount paid, affiliate status etc.</p>
<?php
closeBox();
include('promo.php');
require('footer.php');
