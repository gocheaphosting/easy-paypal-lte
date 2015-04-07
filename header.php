<?php

function insertAlerts($width = 10) {
  ?>
  <div style="display:none; text-align:left" class="center alert alert-info col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertInfoText"></span>
  </div>
  <div style="display:none; text-align:left" class="center alert alert-success col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertSuccessText"></span>
  </div>
  <div style="display:none; text-align:left" class="center alert alert-warning col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertWarningText"></span>
  </div>
  <div style="display:none; text-align:left" class="center alert alert-danger col-lg-<?php echo $width; ?>" role="alert">
    <button type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <span id="alertErrorText"></span>
  </div>
  <?php
}

function openRow($help = "") {
  if (empty($help)) {
    $help = "You can roll-up or temporarily suppress this box. For more help, click on the friendly Help button near the top right corner of this page, if there is one.";
  }
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
  $admin = EZ::ezppURL() . "/admin/index.php";
  if (empty($help)) {
    $help = "You can roll-up or temporarily suppress this box. For more help, click on the friendly Help button near the top right corner of this page, if there is one.";
  }
  ?>
  <div class="box col-md-<?php echo $size; ?> center">
    <div class="box-inner">
      <div class="box-header well" data-original-title="">
        <h2>
          <i class="glyphicon glyphicon-<?php echo $icon; ?>"></i>
          <?php echo $title; ?>
        </h2>
        <div class="box-icon">
          <?php
          if (!empty(EZ::$options['show_admin'])) {
            ?>
            <a href="<?php echo $admin; ?>" class="btn btn-admin btn-round btn-default"
               title="Open Admin Interface" data-toggle='tooltip'>
              <i class="glyphicon glyphicon-cog red"></i>
            </a>
            <?php
          }
          ?>
          <a href="#" class="btn btn-help btn-round btn-default"
             data-content="<?php echo $help; ?>"
             title="Help" data-toggle='tooltip'>
            <i class="glyphicon glyphicon-question-sign"></i>
          </a>
          <a href="#" class="btn btn-minimize btn-round btn-default"
             title="Rollup this panel" data-toggle='tooltip'>
            <i class="glyphicon glyphicon-chevron-up"></i>
          </a>
          <a href="#" class="btn btn-close btn-round btn-default"
             title="Hide this panel temporarily until reload" data-toggle='tooltip'>
            <i class="glyphicon glyphicon-remove"></i>
          </a>
        </div>
      </div>
      <div class="box-content" style='text-align:left'>
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

function printLogo($h2 = '') {
  if (!empty(EZ::$options['shop_logo'])) {
    $shopLogo = EZ::$options['shop_logo'];
  }
  else {
    $shopLogo = "assets/ezpaypal-brand.png";
  }
  ?>

  <div class="row">
    <div class="col-lg-12 center">
      <h2 class="col-lg-12"><img alt="EZ PayPal Logo" src="<?php echo $shopLogo; ?>" style="max-width:100%;"/>
        <?php
        if (!empty($h2)) {
          echo "<small><br />$h2<br /></small>";
        }
        ?>
      </h2>
    </div>
  </div>

  <?php
}

if (!empty(EZ::$options['shop_theme'])) {
  $themeCSS = "admin/css/bootstrap-" . strtolower(EZ::$options['shop_theme']) . ".min.css";
}
else {
  $themeCSS = "admin/css/bootstrap-cerulean.min.css";
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
    <link href="admin/css/bootstrap-editable.css" rel="stylesheet">
    <link href="admin/css/charisma-app.css" rel="stylesheet">
    <link href="admin/css/font-awesome.min.css" rel="stylesheet">
    <link href="admin/css/dataTables.bootstrap.css" rel="stylesheet">
    <link href="admin/css/list-grid.css" rel="stylesheet">
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
    <div class="ch-container">
      <div class="row">
        <noscript>
        <div class="alert alert-block col-md-12">
          <h4 class="alert-heading">Warning!</h4>
          <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
        </div>
        </noscript>

        <div id="content" class="col-lg-12 col-md-12 col-sm-12">
