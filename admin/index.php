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
openBox("Introduction", "home",12);
include('intro.php');
closeBox();
include('promo.php');
require('footer.php');
