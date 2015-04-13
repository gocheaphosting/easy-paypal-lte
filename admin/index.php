<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Dashboard</a>
    </li>
  </ul>
</div>

<?php
openBox("Quick Start and Tour", "home", 12);
require 'tour.php';
closeBox();
?>
<div id="features" style="display: none">
  <?php
  openBox("Features and Benefits", "thumbs-up", 12);
  include('intro.php');
  closeBox();
  ?>
</div>
<?php
include('promo.php');
require('footer.php');
