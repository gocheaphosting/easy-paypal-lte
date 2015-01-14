<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Database Tools</a>
    </li>
  </ul>
</div>

<?php
openBox("DB Import Export Tools", "hdd", 12);
?>
<p>This feature is available in the <a href="#" class="goPro">Pro version</a> of this program, which allows you to backup and restore your database in a variety of ways.</p>
<p>In this lite version, you will have to use your favorite database tool, such as phpMyAdmin.</p>
<hr>
<h4>Screenshot of the DB Tools from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(19);
?>
<div class="clearfix"></div>
<?php
closeBox();
include 'promo.php';
require 'footer.php';