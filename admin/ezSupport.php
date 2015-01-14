<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Optional Module - EZ Support</a>
    </li>
  </ul>
</div>

<?php
openBox("EZ Support", "plus-sign", 12);
?>
<h4>Paid Support Ticket System</h4>
<p>The <a href="#" class="goPro">Pro version</a> of EZ PayPal can be enhanced with optional modules. Note that these modules will not work with the lite version of the program.</p>
<p>EZ Support is paid support ticket module for EZ PayPal based on the excellent <a href="http://osticket.com" target="_blank">osTicket</a> Support Ticket System. Using it in conjunction with EZ PayPal, you can request that a fee be paid before a support ticket is raised.</p>
<p>
<h5>
  Why would you want to charge your users for support?
</h5>

<p>
  If you find your users sending you emails with questions that are clearly answered in the FAQ, documentation etc., this package may help you. In my case, when I got tired of cutting and pasting the FAQ answers, I decided to charge them a small amount ($0.95) per question. In fact, I didn't really charge them; I just told them that I would. And, to my surprise, my support load went down by over 90%! I wanted to use a support ticket system, but was worried that it would swamp me with frivolous tickets and spam. I needed a paid support model, which I couldn't find. So I created EZ Support based on OSTickets.  It certainly eliminated spam and frivolous support calls.
</p>
<p>
  EZ Support can be purchased and added on to your EZ PayPal system for $7.95.
</p>
<hr>
<div>
  <a class="btn btn-primary" href='http://www.thulasidas.com/packages/ezsupport/' target='_blank' title="More Information" data-toggle="popover" data-content="Visit the webpage for EZ Support to learn more about it and buy it." data-trigger="hover" data-placement='top'>Learn More</a>
  <a class="btn btn-success goPro" href='#' title="Buy It Now!" data-toggle="popover" data-content="If you need the EZ Support module, please get the Pro version of EZ PayPal first, and then purchase the EZ Support module." data-trigger="hover" data-placement='top'>Go Pro</a>
  <a class="btn btn-success goPro" href='#' title="Buy It Now!" data-toggle="popover" data-content="If you need the EZ Support module, please get the Pro version of EZ PayPal first, and then purchase the EZ Support module" data-trigger="hover" data-placement='top' data-product='ezsupport-module'>Buy Now</a>
</div>

<?php
closeBox();
include('promo.php');
require('footer.php');
