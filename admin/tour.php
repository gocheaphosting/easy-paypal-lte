<?php
$shopPage = EZ::ezppURL() . "shop.php";
if (EZ::$isInWP) {
  $shopPage .= "?wp";
}
?>
<div class="col-lg-8 col-sm-12">
  <h4>Quick Start</h4>
  <ul>
    <li>Create a <strong><a href='products-edit.php'>new product</a></strong>. You can <a href="products.php"><strong>edit</strong></a> your products and <a href="products-batch.php"><strong>upload</strong></a> new files for them whenever you like.</li>
    <li>Visit your <strong><a href="<?php echo $shopPage; ?>">e-shop</a></strong> to see it. In the <a href="#" class="goPro">Pro Version</a>, you can <a href="advanced.php">customize</a> your shop and other public pages with your own logo, branding and color scheme in the <strong>Advanced Shop Options</strong> section.</li>
    <li>Take a <strong><a class="restart" href="#">tour</a></strong> any time you would like to go through the application features.</li>
  </ul>
  <h4>WordPress and Shortcodes</h4>
  <p>If you are using the EZ PayPal as a WordPress plugin, you can use <a href='http://codex.wordpress.org/Shortcode' target='_blank' class='popup-long'>shortcodes</a> to place your product buy links on your posts and pages. Use the shortcode <code>[ezshop]</code>.</p>
  <p>The supported parameters are <code>id</code> (which is a product id) and <code>qty</code> (quantity). For example, each product can be displayed as a <strong>Buy Now</strong> using the shortcode format <code>[ezshop id=3 qty=2]Buy Now[/ezshop]</code>. This will insert a link, which when clicked, will take your reader to a PayPal page to buy two licences of the product with id 3.</p>
  <p>You can easily generate the appropriate short codes by visiting your <a href="../shop.php">E-Shop</a> and clicking on the Id column in any product row.</p>

  <h4>Context-Aware Help</h4>
  <p>Most of the admin pages of this application have a blue help button near the right hand side top corner. Clicking on it will give instructions and help specific to the task you are working on. All configuration options have a help button associated with it, which gives you a popover help bubble when you hover over it. Finally, almost every button in the admin interface has popover help associated with it. If you need further assistance, please see the <a href='#' id='showSupportChannels'>support channels</a> available.</p>
</div>
<div class="col-lg-4 col-sm-12">
  <h4>Play with a Demo</h4>
  <ul>
    <li>If you would like to play with the admin interface without messing up your installation, <a href="http://demo.thulasidas.com/ezpaypal" title='Visit the demo site to play with the admin interface' data-toggle='tooltip' target="_blank">please visit EZ PayPal demo site</a>.</li>
  </ul>
  <div id='supportChannels'>
    <h4>Need Support?</h4>
    <ul>
      <li>Please check the carefully prepared <a href="http://www.thulasidas.com/plugins/ezpaypal#faq" class="popup-long" title='Your question or issue may be already answered or resolved in the FAQ' data-toggle='tooltip'> Plugin FAQ</a> for answers.</li>
      <li>For the lite version, you may be able to get support from the <a href='https://wordpress.org/support/plugin/easy-paypal-lte/' class='popup-long' title='WordPress forums have community support for this plugin' data-toggle='tooltip'>WordPress support forum</a>.</li>
      <li>For preferential support and free updates, you can purchase a <a href='http://buy.thulasidas.com/support' class='popup btn-xs btn-info' title='Support contract costs only $4.95 a month, and you can cancel anytime. Free updates upon request, and support for all the products from the author.' data-toggle='tooltip'>Support Contract</a>.</li>
      <li>For one-off support issues, you can raise a one-time paid <a href='http://buy.thulasidas.com/ezsupport' class='popup btn-xs btn-primary' title='Support ticket costs $0.95 and lasts for 72 hours' data-toggle='tooltip'>Support Ticket</a> for prompt support.</li>
      <li>Please include a link to your blog when you contact the plugin author for support.</li>
    </ul>
  </div>
  <h4>Happy with this plugin?</h4>
  <ul>
    <li>Please leave a short review and rate it at <a href="https://wordpress.org/plugins/easy-paypal-lte/" class="popup-long" title='Please help the author and other users by leaving a short review for this plugin and by rating it' data-toggle='tooltip'>WordPress</a>. Thanks!</li>
  </ul>
</div>
<div class="clearfix"></div>
<hr />
<p class="center-text">
  <a class="btn btn-primary center-text restart" href="#" data-toggle='tooltip' title='Start or restart the tour any time' id='restart'><i class="glyphicon glyphicon-globe icon-white"></i>&nbsp; Start Tour</a>
  <a class="btn btn-primary center-text showFeatures" href="#" data-toggle='tooltip' title='Show the features of this plugin and its Pro version'><i class="glyphicon glyphicon-thumbs-up icon-white"></i>&nbsp; Show Features</a>
</p>
<?php
if (isset($_REQUEST['inframe'])) {
  ?>
  <style type="text/css">
    .tour-step-background {
      background: transparent;
      border: 2px solid blue;
    }
    .tour-backdrop {
      opacity:0.2;
    }
  </style>
  <?php
}
?>
<script>
  $(document).ready(function () {
    if (!$('.tour').length && typeof (tour) === 'undefined') {
      var tour = new Tour({backdrop: true, //backdropPadding: 20,
        onShow: function (t) {
          var current = t._current;
          var toShow = t._steps[current].element;
          var dad = $(toShow).parent('ul');
          var gdad = dad.parent();
          dad.slideDown();
          if (dad.hasClass('accordion')) {
            gdad.siblings('.accordion').find('ul').slideUp();
          }
          else if (dad.hasClass('dropdown-menu')) {
            gdad.siblings('.dropdown').find('ul').hide();
          }
        }
      });
      tour.addStep({
        element: "#dashboard",
        placement: "right",
        title: "Dashboard",
        content: "Welcome to EZ PayPal! When you login to your EZ PayPal Admin interface, you will find yourself in the Dashboard. Depending on the version of our app, you may see informational messages, statistics etc on this page."
      });
      tour.addStep({
        element: "#account",
        placement: "left",
        title: "Quick Access to Your Account",
        content: "Click here if you would like to logout or modify your profile (your password and email Id)."
      });
      tour.addStep({
        element: "#update",
        placement: "left",
        title: "Updates and Upgrades",
        content: "If you would like to check for regular updates, or install a purchased module or Pro upgrade, visit the update page by clicking this button."
      });
      tour.addStep({
        element: "#shop",
        placement: "left",
        title: "Open your EZ PayPal Shop in a new window. The shop page will contain all the products you have defined so far."
      });
      tour.addStep({
        element: "#standAloneMode",
        placement: "left",
        title: "Standalone Mode",
        content: "Open EZ PayPal Admin in a new window independent of WordPress admin interface. The standalone mode still uses WP authentication, and cannot be accessed unless logged in."
      });
      tour.addStep({
        element: "#tour",
        placement: "right",
        title: "Tour",
        content: "This page is the starting point of your tour. You can always come here to relaunch the tour, if you wish."
      });
      tour.addStep({
        element: "#goPro",
        placement: "right",
        title: "Upgrade Your App to Pro",
        content: "To unlock the full potential of this app, you may want to purchase the Pro version. You will get an link to download it instantly. It costs only $19.95 and adds tons of features. These Pro features are highlighted by a red icon on this menu bar."
      });
      tour.addStep({// The first on ul unroll is ignored. Bug in BootstrapTour?
        element: "#products",
        placement: "right",
        title: "Manage Your Products",
        content: "In this section, you can manage your products."
      });
      tour.addStep({
        element: "#products",
        placement: "right",
        title: "View and Modify Your Products",
        content: "Click here to see all your products in a neat table format. By clicking on the entries in the table, you can modify the product data, such as its name, description, price etc."
      });
      tour.addStep({
        element: "#products-edit",
        placement: "right",
        title: "Detailed Editing of Your Products",
        content: "To get finer control over the product details, you may want to use this page. In the Pro version, it will contain even more details like product upgrade price etc."
      });
      tour.addStep({
        element: "#products-new",
        placement: "right",
        title: "Create a New Product",
        content: "<p>It allows you to upload multiple banners and edit their meta data in a neat interface. In this lite version, you can upload the banner to your <code>banners</code> folder or any subfolder below it and run the <b>Batch Process</b> to enter the meta data.</p>"
      });
      tour.addStep({
        element: "#products-batch",
        placement: "right",
        title: "Batch Upload Product Files",
        content: "<p class='red'>This is a Pro feature.</p>After you define your products, you can upload your product files en-masse. If you are selling software, for instance, and if you have updated a large number of your products, you can name them appropriately and upload them all by dragging and droppping on this page."
      });
      tour.addStep({
        element: "#subscriptions",
        placement: "right",
        title: "Subscription Products",
        content: "<p class='red'>This is a Pro feature.</p><p>The Pro version allows you to define subscription products, such as memberships or advertising contracts, where you would like to bill your customer on a regular basis. On this page, you will be able to see all your subscription products and edit them.</p>"
      });
      tour.addStep({
        element: "#subscriptions-new",
        placement: "right",
        title: "New Subscription Product",
        content: "<p class='red'>This is a Pro feature.</p><p>The Pro version allows you to define subscription products, such as memberships or advertising contracts, where you would like to bill your customer on a regular basis. On this page, you can create a new subscription product.</p>"
      });
      tour.addStep({// The first on ul unroll is ignored. Bug in BootstrapTour?
        element: "#product-meta",
        placement: "right",
        title: "Meta Data",
        content: "<p>In this section, you can manage the meta data associated with your products.</p>"
      });
      tour.addStep({
        element: "#product-meta",
        placement: "right",
        title: "Product Meta Editor",
        content: "<p class='red'>This is a Pro feature.</p><p>In addition to the quantities you define when you create it, a product can also hold an unlimited number of free-form information. On this page, you can define the so-called meta data for any of your products. You can later use them in your product download page, email templates etc.</p>"
      });
      tour.addStep({
        element: "#categories",
        placement: "right",
        title: "View and Modify Your Categories",
        content: "Click here to see all your ad categories in a neat table format. By clicking on the entries in the table, you can modify the category data, such as its name and comment."
      });
      tour.addStep({
        element: "#categories-new",
        placement: "right",
        title: "Create a New Category",
        content: "<p class='red'>This is a Pro feature.</p><p>It allows you to create new product categories. In the lite version, you have three default categories that you can modify according to your preference. If you need modify the number of categories, you will have to do direct database manipulation to add or delete categories.</p>"
      });
      tour.addStep({// The first on ul unroll is ignored. Bug in BootstrapTour?
        element: "#sales",
        placement: "right",
        title: "Manage Sales",
        content: "<p>In this section, you can see and manage all your sales.</p>"
      });
      tour.addStep({
        element: "#sales",
        placement: "right",
        title: "All Your Sales",
        content: "<p>Here you can see all your sales in a neat table, or each on of them in a form.</p>"
      });
      tour.addStep({
        element: "#sales-edit",
        placement: "right",
        title: "Sales Editor",
        content: "<p class='red'>This is a Pro feature.</p><p>In the Pro version, you can modify some of the sales attributes (such as the expiry date of the download link) and resend the automated email to your buyers.</p>"
      });
      tour.addStep({
        element: "#stats",
        placement: "right",
        title: "Sales Statistics",
        content: "<p class='red'>This is a Pro feature.</p><p>Here you can see how your sales, including geographical data and browser information."
      });
      tour.addStep({// The first on ul unroll is ignored. Bug in BootstrapTour?
        element: "#buyers",
        placement: "right",
        title: "Buyers",
        content: "<p>In this section, you can see all your buyers.</p>"
      });
      tour.addStep({
        element: "#buyers",
        placement: "right",
        title: "All Your Buyers",
        content: "<p>Here you can see all your buyers in a neat table.</p>"
      });
      tour.addStep({
        element: "#subscribers",
        placement: "right",
        title: "All Your Subscribers",
        content: "<p class='red'>This is a Pro feature.</p><p>On this page, you will see all your subscribers in a neat table.</p>"
      });
      tour.addStep({
        element: "#email",
        placement: "right",
        title: "E-Mail Tool",
        content: "<p class='red'>This is a Pro feature.</p><p>On this page, you have a tool to contact your customers and subscribers based on a variety of selection criteria.</p>"
      });
      tour.addStep({// The first on ul unroll is ignored. Bug in BootstrapTour?
        element: "#options",
        placement: "right",
        title: "Configuration",
        content: "Set and edit your EZ PayPal configuration"
      });
      tour.addStep({
        element: "#options",
        placement: "right",
        title: "Configuration Options",
        content: "On this page, you will set up your EZ PayPal program by providing the configuration options."
      });
      tour.addStep({
        element: "#advanced",
        placement: "right",
        title: "Advanced Tools and Options",
        content: "<p class='red'>This is a Pro feature.</p><p>On this page, you will find advanced options to customize your EZ PayPal site.</p>"
      });
      tour.addStep({
        element: "#db-tools",
        placement: "right",
        title: "Configure Your Shop Page",
        content: "<p class='red'>This is a Pro feature.</p><p>These database tools will help you backup and restore your data, as well as migrate from existing ezPayPal installations.</p>"
      });
      tour.addStep({
        element: "#template-download",
        placement: "right",
        title: "Edit Download Page",
        content: "<p class='red'>This is a Pro feature.</p><p>The download page is where your customers will get redirected to, after they complete their payment. This menu item directs you to a download page editor.</p>"
      });
      tour.addStep({
        element: "#template-email",
        placement: "right",
        title: "Configure Your E-Mails",
        content: "<p class='red'>This is a Pro feature.</p><p>EZ PayPal uses templates to send emails when a customer needs to be contacted, with their product download link, for instance. On this page, you can customize the email templates.</p>"
      });
      tour.addStep({// The first on ul unroll is ignored. Bug in BootstrapTour?
        element: "#ezAffiliates",
        placement: "right",
        title: "Optional Modules",
        content: "<p class='red'>These are optional modules.</p><p>Extend the functionality of EZ PayPal with optional modules.</p>"
      });
      tour.addStep({
        element: "#ezAffiliates",
        placement: "right",
        title: "Start Your Affiliates Network",
        content: "<p class='red'>This is an optional module.</p><p>Affiliates marketing is a powerful tool that will turn your statisfied clients into your sales representatives. With the help of <strong>ezAffiliates</strong>, you can extend EZ PayPal into an affiliate machine. It costs only $4.95.</p>"
      });
      tour.addStep({
        element: "#ezSupport",
        placement: "right",
        title: "Paid Support Tickets",
        content: "<p class='red'>This is an optional module.</p><p>If you find yourself bogged down with frivolous support requests, you may want to try a paid support ticket system, where your users can raise support issues for a nominal fee ($0.95 by default). This <strong>ezSupport</strong> module can cut down your support load by upto 90%. It costs only $4.95.</p>"
      });
      tour.addStep({
        element: "#easy-text-links",
        placement: "right",
        title: "Text Links Management",
        content: "<p class='red'>This is an optional module.</p><p>If you have a website or blog with a high page rank, and if the returns from contextual advertising are a disappointment, you may want to try direct text links sales. This module will help you manage it. It costs only $7.95.</p>"
      });
      tour.addStep({// The first on ul unroll is ignored. Bug in BootstrapTour?
        element: "#profile",
        placement: "right",
        title: "Manage Your Account",
        content: "Set your account parameters or log off."
      });
      tour.addStep({
        element: "#profile",
        placement: "right",
        title: "Manage Your Profile",
        content: "Click here if you would like to modify your profile (your password and email Id)."
      });
      tour.addStep({
        orphan: true,
        placement: "right",
        title: "Done",
        content: "<p>You now know everything about the EZ PayPal interface. Congratulations!</p>"
      });
    }
    $("#showSupportChannels").click(function (e) {
      e.preventDefault();
      var bg = $("#supportChannels").css("backgroundColor");
      var fg = $("#supportChannels").css("color");
      $("#supportChannels").css({backgroundColor: "yellow", color: "black"});
      setTimeout(function () {
        $("#supportChannels").css({backgroundColor: bg, color: fg});
      }, 500);
    });
    $(".restart").click(function (e) {
      e.preventDefault();
      tour.restart();
    });
    $(".showFeatures").click(function (e) {
      e.preventDefault();
      $("#features").toggle();
      if ($("#features").is(":visible")) {
        $(this).html('<i class="glyphicon glyphicon-thumbs-up icon-white"></i>&nbsp; Hide Features');
      }
      else {
        $(this).html('<i class="glyphicon glyphicon-thumbs-up icon-white"></i>&nbsp; Show Features');
      }
    });
  });
</script>
