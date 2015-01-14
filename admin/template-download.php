<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Download Page Template</a>
    </li>
  </ul>
</div>

<?php
openBox("Download Page Template", "download-alt", 12);
?>
<h3>Download Page Template</h3>
<p>This page is available in the <a href="#" class="goPro">Pro version</a> of this program, and will help you customize the download page that your customers will be forwarded to after their purchase. In the lite version, you get a default page, which you can modify using your favorite database tool, if you need to.</p>
<hr>
<h4>Screenshot of the Template Editor from the <a href="#" class="goPro">Pro</a> Version</h4>
<?php
showScreenshot(16);
?>
<div class="clearfix"></div>
<?php
closeBox();
include('promo.php');
require('footer.php');
