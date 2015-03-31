<?php
require 'header.php';
require_once '../lib/Ftp.php';
$ftp = new Ftp();
require_once 'Updater.php';
$updater = new Updater('ezpaypal');
?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Update EZ PayPal</a>
    </li>
  </ul>
</div>
<style type="text/css">
  label{width:100px;}
</style>
<?php
insertAlerts(11);
openBox("Update or Upgrade Your Product", "plus", 11, "<p>It is easy to update your application. Once you have downloaded an update package, please use the Browse button in the <b>Upload Your Zip File</b> section. When you have selected the zip file to upload, the updater will take care of the rest.</p>"
        . "<p>If you have purchased a <a href='#' class='goPro'>Pro upgrade</a>, the process is identical. Just browse and upload the zip file."
        . "<p>In some installations, you may need to provide FTP details for the updater to work. If needed, you will be prompted for the credentials. Contact your hosting provider or system admin for details.</p>");
$updateBox = '';
?>
<div class="clearfix">&nbsp;</div>
<?php
if (EZ::$isPro || !empty(EZ::$options['allow_updates'])) {
  $localVersion = $updater->getLocalVersion();
  $remoteVersion = $updater->getRemoteVersion();
  $toolTip = $updater->getUpdateText();
  if ($updater->isOld()) {
    ?>
    <div class="col-md-3 col-sm-3 col-xs-6 update">
      <a data-toggle="tooltip" title="<?php echo $toolTip; ?>" class="well top-block update" href="#">
        <i class="glyphicon glyphicon-hand-up red"></i>
        <div>EZ PayPal V<?php echo $localVersion; ?></div>
        <div>Update to V<?php echo $remoteVersion; ?></div>
        <span class="notification red"><?php echo "V$remoteVersion"; ?></span>
      </a>
    </div>
    <?php
  }
  else {
    ?>
    <div class="col-md-3 col-sm-3 col-xs-6">
      <a data-toggle="tooltip" title="<?php echo $toolTip; ?>" class="well top-block" href="#">
        <i class="glyphicon glyphicon-thumbs-up red"></i>
        <div>EZ PayPal V<?php echo $localVersion; ?></div>
        <div>Your version is up-to-date</div>
      </a>
    </div>
    <?php
  }
}
else {
  $allow_updates = array('name' => 'Allow Update Check',
      'value' => 0,
      'help' => __("Enable this option to allow automatic update checks. Note that checking for updates requires your server to connect to that of the author. No data is collected from your server during update check; it is a read-only process. If you are okay with connecting to an extenral server, please enable this option to opt in.<b>Click on the Updates button again to reload the page</b>", 'easy-common'),
      'type' => 'checkbox');
  $updateBox = '<div id="updateBox" class="col-md-3 col-sm-3 col-xs-6" style="display:none"><table class="table table-striped table-bordered responsive">
      <thead>
        <tr>
          <th style="width:50%;min-width:180px">Option</th>
          <th style="width:25">Value</th>
          <th class="center-text" style="width:25%;min-width:50px">Help</th>
        </tr>
      </thead>' .
          EZ::renderOption('allow_updates', $allow_updates) .
          '</tbody>
    </table>
  </div>';
  ?>
  <div class="col-md-3 col-sm-3 col-xs-6">
    <a data-toggle="tooltip" title="Click to enable update check so that you can connect to the author server to get the current version." class="well top-block" href="#" id='allowUpdates'>
      <i class="glyphicon glyphicon-exclamation-sign red"></i>
      <div>Update Check is Disabled</div>
      <div>Enable it</div>
    </a>
  </div>
  <?php
}
if (EZ::$isPro) {
  ?>
  <div class="col-md-3 col-sm-3 col-xs-6">
    <a data-toggle="tooltip" title="See other premium WordPress plugins and PHP programs by the same author." class="well top-block" href="http://www.thulasidas.com/render" target="_blank">
      <i class="glyphicon glyphicon-star green"></i>
      <div>Other Plugins and Programs</div>
      <div>From the author</div>
    </a>
  </div>
  <?php
}
else {
  ?>
  <div class="col-md-3 col-sm-3 col-xs-6 goPro">
    <a data-toggle="tooltip" title="Get the Pro version for only $19.95. Tons of extra features. Instant download." class="well top-block goPro" href="http://buy.thulasidas.com/ezpaypal">
      <i class="glyphicon glyphicon-shopping-cart red"></i>
      <div>Get EZ PayPal Pro</div>
      <div>$19.95. Instant Download</div>
      <span class="notification red">Pro</span>
    </a>
  </div>
  <?php
}
?>
<div class="col-md-3 col-sm-3 col-xs-6">
  <a data-toggle="tooltip" title="Check out the author's blog for more information about the author. It has links to his credentials as well." class="well top-block" href="http://www.thulasidas.com/" target="_blank">
    <i class="glyphicon glyphicon-user blue"></i>
    <div>Author Profile</div>
    <div>CV from Author's blog</div>
  </a>
</div>
<div class="col-md-3 col-sm-3 col-xs-6">
  <a data-toggle="tooltip" title="If you need support with this application, please visit our support portal and raise a ticket for $0.95. The Pro version (or any purchase from us) comes with free support for a short period." class="well top-block" href="http://support.thulasidas.com/" target="_blank">
    <i class="glyphicon glyphicon-envelope yellow"></i>
    <div>Contact</div>
    <div>Enquiries and Support</div>
  </a>
</div>
<div class="clearfix"></div>
<?php
echo $updateBox;
?>
<div class="clearfix"></div>
<hr>
<div id="updateDiv">
  <h4>Upload Your Upgrade/Update</h4>
  <table class="table table-striped table-bordered responsive">
    <tbody>
      <tr>
        <td>Zip File</td>
        <td style='width:70%'><a id='file' class='red' data-name='file' data-type='file'  data-mode='inline' data-validator='notNull'><input id='fileinput' type='file' class='file' multiple=true data-show-preview='false' data-show-upload='false'> </a></td>
        <td class='center-text'><a style='font-size:1.5em' data-content='Browse to the zip file you have downloaded either for update, or Pro upgrade. Once selected, you will be asked to confirm the update, and the rest will be taken care of.' data-help='' data-toggle='popover' data-placement='left' data-trigger='hover' title='Your Digital Product' ><i class='glyphicon glyphicon-question-sign blue'></i></a></td>
      </tr>
    </tbody>
  </table>
</div>
<div class="clearfix"></div>
<div class="center red" id="loading" style="display:none;font-size:1.3em;width:100%"><i class="fa fa-spinner fa-spin"></i> Working! Please wait...</div>
<hr>
<?php
echo $ftp->printForm();
closeBox();
if (EZ::$isInWP) {
  $dbSetup = "dbSetup.php?wp&update";
}
else {
  $dbSetup = "dbSetup.php?update";
}
?>
<script>
  var xeditHandler = 'ajax/options.php';
  $(document).ready(function () {
    var file;
    function ajaxUpload(_file) {
      var data = new FormData();
      data.append('file', _file);
      $.ajax({
        url: 'ajax/update.php',
        type: 'POST',
        dataType: 'json',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
          $("#loading").hide();
          showSuccess(response.success);
          flashWarning(response.warning);
          setTimeout(function () {
            bootbox.confirm("<p>The database needs to be <a href='<?php echo $dbSetup; ?>'>setup again</a> to complete this update/upgrade, in order to create any new tables needed or to alter existing ones. If you are only reinstalling the app, you can skip this step.</p>", function (result) {
              if (result) {
                window.location.href = '<?php echo $dbSetup; ?>';
              }
              else {
                flashWarning("Database not updated. Please run the <a href='<?php echo $dbSetup; ?>'>setup again</a> now or your installation may be in an unpredictable state.");
              }
            });
          }, 5000);
        },
        error: function (a) {
          $("#loading").hide();
          $("#setupDiv").fadeIn();
          showError(a.responseText);
        }
      });
    }
    $("#fileinput").on('change', function (event) {
      file = event.target.files[0];
      if (file) {
        bootbox.confirm("<p>Are you sure you want to upload <code>" + file.name + "</code> to update/upgrade your <b>EZ PayPal</b> installaion? The update process is designed to be safe, but it will replace your existing files and may modify your database tables.</p><p class='red'> <em>Keeping a backup of your files and database is highly recommended.</em></p><p>Before updating, consider backing up:<ul><li><a href='ajax/update.php?backup'>Download a full backup</a> of your current app folder.</li><li><a href='ajax/db-tools.php?action=sqldump&gzip=true'>Download a compressed dump</a> of your database.</li></ul>Note that these backups may take a couple of minites to complete. Please be patient. Once done, be sure to check the downloaded files to verify that they are usable and complete.</p>", function (result) {
          if (result) {
            $("#updateDiv").hide();
            $("#loading").fadeIn();
            ajaxUpload(file);
          }
          else {
            flashWarning("File not uploaded. Browse again to upload a new file to update or upgrade your <b>EZ PayPal</b> installation.");
            $("#loading").hide();
            $("#updateDiv").fadeIn();
          }
        });
      }
    });
    $('.update').click(function (e) {
      e.preventDefault();
      var url = 'http://buy.thulasidas.com/update.php';
      var title = "Check for Updates";
      var w = 1024;
      var h = 728;
      return ezPopUp(url, title, w, h);
    });
    $('#allowUpdates').click(function (e) {
      e.preventDefault();
      $("#updateBox").show();
    });
  });
</script>

<?php
require('footer.php');
