<?php

function menuHidden() {
  global $no_visible_elements;
  return empty($no_visible_elements) && empty($GLOBALS['no_visible_elements']);
}

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
