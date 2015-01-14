<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Shop Template</a>
    </li>
  </ul>
</div>

<?php
openBox("Shop Template", "credit-card", 12);
?>
<h3>Your Shop Template</h3>
<p>This page is available in the <a href="#" class="goPro">Pro version</a> of this program, and will help you customize the shop page the way you like it.</p>
<?php
closeBox();
include('promo.php');
require('footer.php');
