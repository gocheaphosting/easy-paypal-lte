<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Statistics</a>
    </li>
  </ul>
</div>

<?php
openBox("Statistics and Charts", "stats", 12);
?>
<p>This feature is available in the Pro version of this program, which allows you to view your ad serving statistics to pinpoint opportunities to optimize your revenue. </p>
<hr>
<h4>Screenshot of Statistics Page from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(13);
?>
<div class="clearfix"></div>
<?php
closeBox();
include('promo.php');
require('footer.php');
