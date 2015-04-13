<?php
if (empty($no_visible_elements)) {
  require_once 'lock.php';
}

include_once('../debug.php');

function insertAlerts($width = 10) {
  ?>
  <div style="display:none" class="alert alert-info col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertInfoText"></span>
  </div>
  <div style="display:none" class="alert alert-success col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertSuccessText"></span>
  </div>
  <div style="display:none" class="alert alert-warning col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertWarningText"></span>
  </div>
  <div style="display:none" class="alert alert-danger col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertErrorText"></span>
  </div>
  <?php
}

function openRow($help = "") {
  if (empty($help)) {
    $help = "You can roll-up or temporarily suppress this box. For more help, click on the friendly Help button near the top right corner of this page, if there is one.";
  }
  else {
    ?>
    <a href="#" class="btn btn-primary btn-help" style="float:right" data-content="<?php echo $help; ?>"><i class="glyphicon glyphicon-question-sign large"></i> Help</a>
  <?php }
  ?>
  <div class="row">
    <?php
    return $help;
  }

  function closeRow() {
    ?>
  </div><!-- row -->
  <?php
}

function openCell($title, $icon = "edit", $size = "12", $help = "") {
  if (empty($help)) {
    $help = "You can roll-up or temporarily suppress this box. For more help, click on the friendly Help button near the top right corner of this page, if there is one.";
  }
  ?>
  <div class="box col-md-<?php echo $size; ?>">
    <div class="box-inner">
      <div class="box-header well" data-original-title="">
        <h2>
          <i class="glyphicon glyphicon-<?php echo $icon; ?>"></i>
          <?php echo $title; ?>
        </h2>
        <div class="box-icon">
          <a href="#" class="btn btn-help btn-round btn-default"
             data-content="<?php echo $help; ?>">
            <i class="glyphicon glyphicon-question-sign"></i>
          </a>
          <a href="#" class="btn btn-minimize btn-round btn-default">
            <i class="glyphicon glyphicon-chevron-up"></i>
          </a>
          <a href="#" class="btn btn-close btn-round btn-default">
            <i class="glyphicon glyphicon-remove"></i>
          </a>
        </div>
      </div>
      <div class="box-content">
        <?php
      }

      function closeCell() {
        ?>
      </div>
    </div>
  </div><!-- box -->
  <?php
}

function openBox($title, $icon = "edit", $size = "12", $help = "") {
  $help = openRow($help);
  openCell($title, $icon, $size, $help);
}

function closeBox() {
  closeCell();
  closeRow();
}

function showScreenshot($id) {
  $img = "../screenshot-$id.png";
  $iSize = getimagesize($img);
  $width = $iSize[0] . 'px';
  echo "<img src='$img' alt='screenshot' class='col-sm-12' style='max-width:$width'>";
}

function getHeader() {
  http_response_code(200);
  global $no_visible_elements;
  if (class_exists('EZ') && property_exists('EZ', 'isPro')) {
    $isPro = EZ::$isPro;
  }
  else {
    $isPro = false;
  }
  if (class_exists('EZ') && !empty(EZ::$options['theme'])) {
    $themeCSS = "css/bootstrap-" . strtolower(EZ::$options['theme']) . ".min.css";
  }
  else {
    $themeCSS = "css/bootstrap-cerulean.min.css";
  }
  ?>
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>EZ PayPal - Your Own E-Shop</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="EZ PayPal - Your Own E-Shop">
      <meta name="author" content="Manoj Thulasidas">

      <!-- The styles -->
      <link id="bs-css" href="<?php echo $themeCSS; ?>" rel="stylesheet">
      <link href="css/bootstrap-editable.css" rel="stylesheet">
      <link href="css/charisma-app.css" rel="stylesheet">
      <link href='css/bootstrap-tour.min.css' rel='stylesheet'>
      <link href='css/bootstrapValidator.css' rel='stylesheet'>
      <link href='css/dropzone.css' rel='stylesheet'>
      <link href="css/summernote.css" rel="stylesheet">
      <link href="css/font-awesome.min.css" rel="stylesheet">
      <link href="css/dataTables.bootstrap.css" rel="stylesheet">
      <link href="css/fileinput.min.css" rel="stylesheet">
      <style type="text/css">
        .popover{width:600px;}
        <?php
        if (class_exists('EZ') && empty(EZ::$options['breadcrumbs'])) {
          ?>
          .breadcrumb {display:none;}
          <?php
        }
        ?>
      </style>
      <!-- jQuery -->
      <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

      <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
      <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->

      <!-- The fav icon -->
      <link rel="shortcut icon" href="img/favicon.ico">

    </head>

    <body>
      <?php if (empty($no_visible_elements)) { ?>
        <!-- topbar starts -->
        <div class="navbar navbar-default" role="navigation">

          <div class="navbar-inner">
            <a id="index" class="navbar-brand" href="index.php"> <img alt="EZ PayPal Logo" src="img/ezpaypal-admin.png" class="hidden-xs"/>
              <span>Your Own E-Shop</span></a>
            <div class="btn-group pull-right">
              <?php
              if (!EZ::$isInWP) {
                $wpQs = '';
                ?>
                <a id="shop" href="<?php echo EZ::ezppURL() . "shop.php"; ?>" target="_blank" data-content="Open your EZ PayPal Shop in a new window. The shop page will contain all the products you have defined so far." data-toggle="popover" data-trigger="hover" data-placement="left" title='Visit Your Shop'><span class="btn btn-info"><i class="fa fa-paypal"></i> Visit Shop</span></a>
                <!-- user dropdown starts -->
                <button id="account" class="btn btn-default dropdown-toggle pull-right" data-toggle="dropdown">
                  <i class="glyphicon glyphicon-user"></i><span class="hidden-sm hidden-xs"> admin</span>
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  <li><a href="profile.php">Profile</a></li>
                  <li class="divider"></li>
                  <li><a href="login.php?logout">Logout</a></li>
                </ul>
                <!-- user dropdown ends -->
                <?php
              }
              else {
                $standaloneURL = plugins_url('index.php', __FILE__);
                $wpQs = '?wp';
                ?>
                <a id="shop" href="<?php echo EZ::ezppURL() . "shop.php$wpQs"; ?>" target="_blank" data-content="Open your EZ PayPal Shop in a new window. The shop page will contain all the products you have defined so far." data-toggle="popover" data-trigger="hover" data-placement="left" title='Visit Your E-Shop'><span class="btn btn-info"><i class="fa fa-paypal"></i> Visit Shop</span></a>
                <a id="standAloneMode" href="<?php echo $standaloneURL; ?>" target="_blank" data-content="Open EZ PayPal Admin in a new window independent of WordPress admin interface. The standalone mode still uses WP authentication, and cannot be accessed unless logged in." data-toggle="popover" data-trigger="hover" data-placement="left"  title='Standalone Admin Screen'><span class="btn btn-info"><i class="glyphicon glyphicon-resize-full"></i> Standalone Mode</span></a>
                <?php
              }
              ?>
              <a id="update" href="update.php" data-content="If you would like to check for regular updates, or install a purchased Pro upgrade, visit the Updates page." data-toggle="popover" data-trigger="hover" data-placement="left" title='Updates Page'><span class="btn btn-info"  ><i class="fa fa-cog fa-spin"></i> Updates
                  <?php
                  if (!$isPro) {
                    ?>
                    &nbsp;<span class="badge red">Pro</span>
                    <?php
                  }
                  ?>
                </span>
              </a>&nbsp;
            </div>
          </div>
        </div>
        <!-- topbar ends -->
      <?php } ?>
      <div class="ch-container">
        <div class="row">
          <?php
          if (empty($no_visible_elements)) {
            ob_start();
            ?>
            <!-- left menu starts -->
            <div class="col-sm-2 col-lg-2">
              <div class="sidebar-nav">
                <div class="nav-canvas">
                  <div class="nav-sm nav nav-stacked">

                  </div>
                  <ul class="nav nav-pills nav-stacked main-menu">
                    <li id="dashboard"><a href="index.php"><i class="glyphicon glyphicon-home"></i><span> Dashboard</span></a>
                    </li>
                    <?php
                    if (!$isPro) {
                      ?>
                      <li id='goPro'><a href="pro.php" class="red goPro" data-toggle="popover" data-trigger="hover" data-content="Get the Pro version of this app for <i>only</i> $19.95. Tons of extra features. Instant download." data-placement="right" title="Upgrade to Pro"><i class="glyphicon glyphicon-shopping-cart"></i><span><b> Go Pro!</b></span></a></li>
                      <?php
                    }
                    ?>
                    <li class="accordion">
                      <a href="#"><i class="glyphicon glyphicon-plus"></i><span> Products</span></a>
                      <ul class="nav nav-pills nav-stacked">
                        <li id='products'><a href="products.php"><i class="glyphicon glyphicon-th-list"></i><span> All Your Products</span></a></li>
                        <li id="products-batch"><a href="products-batch.php"><i class="glyphicon glyphicon-export red"></i><span> Batch Upload</span></a></li>
                        <li id="subscriptions"><a href="subscriptions.php"><i class="glyphicon glyphicon-retweet red"></i><span> Subscription Products</span></a></li>
                        <li id="categories"><a href="categories.php"><i class="glyphicon glyphicon-folder-open"></i><span> Categories</span></a></li>
                      </ul>
                    </li>
                    <li class="accordion">
                      <a href="#"><i class="glyphicon glyphicon-plus"></i><span> Sales</span></a>
                      <ul class="nav nav-pills nav-stacked">
                        <li id="sales"><a href="sales.php"><i class="glyphicon glyphicon-usd"></i><span> All Sales</span></a></li>
                        <li id="stats"><a href="stats.php"><i class="glyphicon glyphicon-stats red"></i><span> Statistics</span></a></li>
                      </ul>
                    </li>
                    <li class="accordion">
                      <a href="#"><i class="glyphicon glyphicon-plus red"></i><span> Customers</span></a>
                      <ul class="nav nav-pills nav-stacked">
                        <li id="buyers"><a href="buyers.php"><i class="fa fa-users red"></i><span> Your Customers</span></a></li>
                        <li id="subscribers"><a href="subscribers.php"><i class="fa fa-spin fa-refresh red"></i><span> Your Subscribers</span></a></li>
                        <li id="email"><a href="email.php"><i class="fa fa-envelope red"></i><span> Email Tool</span></a></li>
                      </ul>
                    </li>
                    <li class="accordion">
                      <a href="#"><i class="glyphicon glyphicon-plus"></i><span> Configuration</span></a>
                      <ul class="nav nav-pills nav-stacked">
                        <li id="options"><a href="options.php"><i class="glyphicon glyphicon-cog"></i><span> Options</span></a></li>
                        <li id="advanced"><a href="advanced.php"><i class="glyphicon glyphicon-cog red"></i><span> Advanced Options</span></a></li>
                        <li id="db-tools"><a href="db-tools.php"><i class="glyphicon glyphicon-import red"></i><span> Import and Export</span></a></li>
                      </ul>
                    </li>
                    <li class="accordion">
                      <a href="#"><i class="glyphicon glyphicon-plus"></i><span> Templates</span></a>
                      <ul class="nav nav-pills nav-stacked">
                        <li id="template-download"><a href="template-download.php"><i class="glyphicon glyphicon-download-alt red"></i><span> Download Page</span></a></li>
                        <li id="template-email"><a href="template-email.php"><i class="fa fa-envelope-o red"></i><span> Email Templates</span></a></li>
                        <li id="assets-upload"><a href="assets-upload.php"><i class="glyphicon glyphicon-camera red"></i><span> Upload Images</span></a></li>
                      </ul>
                    </li>
                    <li class="accordion">
                      <a href="#"><i class="glyphicon glyphicon-plus red"></i><span> Optional Modules</span></a>
                      <ul class="nav nav-pills nav-stacked">
                        <li id="ezSupport"><a href="ezSupport.php"><i class="glyphicon glyphicon-plus-sign red"></i><span> Paid Support</span></a></li>
                        <li id="easy-text-links"><a href="easy-text-links.php"><i class="glyphicon glyphicon-plus-sign red"></i><span> Text Link Ads</span></a></li>
                        <li id="express-checkout"><a href="express-checkout.php"><i class="glyphicon glyphicon-plus-sign red"></i><span> Express Checkout</span></a></li>
                        <li id="ezAffiliates"><a href="ezAffiliates.php"><i class="glyphicon glyphicon-plus-sign red"></i><span> Affiliates Program</span></a></li>
                      </ul>
                    </li>
                    <?php
                    if (!EZ::$isInWP) {
                      ?>
                      <li class="accordion">
                        <a href="#"><i class="glyphicon glyphicon-plus"></i><span> Your Account</span></a>
                        <ul class="nav nav-pills nav-stacked">
                          <li id="profile"><a href="profile.php"><i class="glyphicon glyphicon-lock"></i><span> Your Profile</span></a></li>
                          <li id="logout"><a href="login.php?logout"><i class="glyphicon glyphicon-ban-circle"></i><span> Logout</span></a></li>
                        </ul>
                      </li>
                      <?php
                    }
                    ?>
                  </ul>
                </div>
              </div>
            </div>
            <!--/span-->
            <!-- left menu ends -->

            <noscript>
            <div class="alert alert-block col-md-12">
              <h4 class="alert-heading">Warning!</h4>

              <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a>
                enabled to use this site.</p>
            </div>
            </noscript>

            <div id="content" class="col-lg-10 col-sm-10">
              <!-- content starts -->
              <?php
              if (EZ::isUpdateAvailable()) {
                ?>
                <div class="alert alert-info">
                  <a href="#" class="close" data-dismiss="alert">&times;</a>
                  <strong>Updates Available!</strong> Please update your EZ PayPal.
                </div>
                <?php
              }
            }
            $header = ob_get_clean();
            return $header;
          }

          $header = getHeader();
          if (method_exists('EZ', 'toggleMenu')) {
            $header = EZ::toggleMenu($header);
          }
          echo $header;
