<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Upload Image Files</a>
    </li>
  </ul>
</div>

<?php
openBox("Upload Image Files", "camera", 12);
?>
<p>This feature is available in the <a href="#" class="goPro">Pro version</a> of this program, which allows you to upload multiple image files to your assets location and use them as your logos or in your templates </p>
<p>In this lite version, you can upload your image files using FTP to your assets (<code><?php echo EZ::ezppURL() . "/assets/"?></code>) location and use them by editing the templates using your favorite database tool.</p>
<hr>
<h4>Screenshot of the Image Assets Manager from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(18);
?>
<div class="clearfix"></div>
<?php
closeBox();
include 'promo.php';
require 'footer.php';