<?php require 'header.php'; ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Batch Upload</a>
    </li>
  </ul>
</div>

<?php
openBox("Batch Upload Product Files", "export", 12);
?>
<p>This feature is available in the <a href="#" class="goPro">Pro version</a> of this program, which allows you to upload multiple product files and get them copied to your product definitions automatically. </p>
<p>In this lite version, you can upload your product file while defining your product, or while editing it individually.</p>
<?php
closeBox();
include 'promo.php';
require 'footer.php';