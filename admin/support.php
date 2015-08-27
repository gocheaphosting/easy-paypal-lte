<div class="col-lg-4 col-sm-12">
  <h4>Play with a Demo</h4>
  <ul>
    <li>If you would like to play with the admin interface without messing up your installation, <a href="http://demo.thulasidas.com/<?php echo EZ::$slug; ?>" title='Visit the demo site to play with the admin interface' data-toggle='tooltip' target="_blank">please visit <?php echo EZ::$name; ?> demo site</a>.</li>
  </ul>
  <div id='supportChannels'>
    <h4>Need Support?</h4>
    <ul>
      <li>Please check the carefully prepared <a href="http://www.thulasidas.com/plugins/<?php echo EZ::$slug; ?>#faq" class="popup-long" title='Your question or issue may be already answered or resolved in the FAQ' data-toggle='tooltip'> Plugin FAQ</a> for answers.</li>
    <?php
    if (EZ::$isPro) {
      ?>
      <li>The Pro version comes with a short <a href='http://support.thulasidas.com/open.php' class='popup btn-xs btn-success' title='Open a support ticket if you have trouble with your Pro version. It is free during the download link expiry time.' data-toggle='tooltip'>Free Support</a>.</li>
      <?php
    }
    else {
      ?>
      <li>For the lite version, you may be able to get support from the <a href='https://wordpress.org/support/plugin/<?php echo EZ::$wpslug; ?>' class='popup' title='WordPress forums have community support for this plugin' data-toggle='tooltip'>WordPress support forum</a>.</li>
      <li class="text-success bg-success">Visit the <a href='http://buy.thulasidas.com/update.php' class='popup btn-xs btn-success' title='If you purchased the Pro version of this plugin, but did not get an automated email or a download page, , please click here to find it.' data-toggle='tooltip'>Product Delivery Portal</a> to download the Pro version you have purchased.</li>
      <?php
    }
    ?>
      <li>For preferential support and free updates, you can purchase a <a href='http://buy.thulasidas.com/support' class='popup btn-xs btn-info' title='Support contract costs only $4.95 a month, and you can cancel anytime. Free updates upon request, and support for all the products from the author.' data-toggle='tooltip'>Support Contract</a>.</li>
      <li>For one-off support issues, you can raise a one-time paid <a href='http://buy.thulasidas.com/ezsupport' class='popup btn-xs btn-primary' title='Support ticket costs $0.95 and lasts for 72 hours' data-toggle='tooltip'>Support Ticket</a> for prompt support.</li>
      <li>Please include a link to your blog when you contact the plugin author for support.</li>
    </ul>
  </div>
  <h4>Happy with this plugin?</h4>
  <ul>
    <li>Please leave a short review and rate it at <a href="https://wordpress.org/plugins/<?php echo EZ::$wpslug; ?>/" class="popup-long" title='Please help the author and other users by leaving a short review for this plugin and by rating it' data-toggle='tooltip'>WordPress</a>. Thanks!</li>
  </ul>
</div>
<div class="clearfix"></div>
<script>
  $(document).ready(function () {
    $("#showSupportChannels").click(function (e) {
      e.preventDefault();
      var bg = $("#supportChannels").css("backgroundColor");
      var fg = $("#supportChannels").css("color");
      $("#supportChannels").css({backgroundColor: "yellow", color: "black"});
      setTimeout(function () {
        $("#supportChannels").css({backgroundColor: bg, color: fg});
      }, 500);
    });
  });
</script>
