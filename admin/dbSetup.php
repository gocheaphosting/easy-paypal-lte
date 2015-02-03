<?php
if (!session_id()) {
  session_start();
}
require_once('../DbHelper.php');
require_once 'mock-EZ.php';
require_once 'dbSetup-functions.php';

EZ::$isUpdating = isset($_REQUEST['update']);
EZ::$isInWP = isset($_REQUEST['wp']);
EZ::$isInstallingWP = !empty($isInstallingWP);

if (EZ::$isInstallingWP || EZ::$isUpdating) {
  doInstall();
  if (EZ::$isUpdating) {
    if (EZ::$isInWP) {
      $wp = '?wp';
    }
    else {
      $wp = '';
    }
    header("Location: index.php$wp");
    exit();
  }
  return;
}

function doInstall() {
  $db = new DbHelper();
  $tables = array('categories', 'paypal', 'options', 'option_meta', 'templates',
      'products', 'product_meta', 'sales', 'sale_details', 'ipn', 'addresses');
  if (EZ::$isUpdating) {
    $needMigration = false;
  }
  else {
    $needMigration = $db->saveTables($tables);
  }
  $db->importSQL('setup.sql');
  $db->importSQL('setup-pro.sql');
  if ($needMigration) {
    $dbBak = new DbHelper();
    $dbBak->dbPrefix = "bak_" . $db->dbPrefix;
    ezMigrate($db, $dbBak, $tables);
  }
  require_once('options-default.php');
  putDefaultOptions($db, $options);
  if (file_exists("options-advanced.php")) {
    include_once('options-advanced.php');
    putDefaultOptions($db, $options);
  }
  require_once 'dbSetup-templates.php';
  mkDefaultTemplates($db);
  if (file_exists('dbSetup-pro-templates.php')) {
    require_once 'dbSetup-pro-templates.php';
    mkProTemplates($db);
  }
}

$no_visible_elements = true;
$dbHost = $dbName = $dbUsr = $dbPwd = $dbEmail = $dbPrefix = "";
$cfgIsValid = false;
$cfgDir = dirname(dirname(__FILE__));
$helpBtn = "<a class='setup-help' style='font-size:1.5em;float:right' href='#' title='Click for help' data-toggle='tooltip' data-content=''><i class='glyphicon glyphicon-question-sign blue'></i></a>";
$error_message = "<div class='alert alert-info'>$helpBtn Please enter your database details.</div>";
$counter = "<div id='counter' style='display:none' class='alert alert-warning'>Thank you! Verifying... This may take a while. <span class='counter' style='font-weight:bold'>0</span> seconds.</div>";
if (!empty($_GET['error'])) {
  if (!empty($_SESSION['posted'])) {
    $posted = $_SESSION['posted'];
    extract($posted);
  }
  if ($_GET['error'] == "1") {
    $error_message = "<div class='alert alert-danger'>$helpBtn Error connecting to the database. Check your DB details below.</div>";
  }
  if ($_GET['error'] == "2") {
    if (!empty($_GET['cfg'])) {
      $cfg = "<p>Or create the config file on your server and insert the following content in it.</p>Config file is <code>$cfgDir/dbCfg.php</code><pre>" . htmlspecialchars(urldecode($_GET['cfg'])) . "</pre>";
    }
    else {
      $cfg = '';
    }
    $error_message = "<div class='alert alert-danger'  style='text-align:left'>$helpBtn Error:  Permission denied! Unable to open config file (<code>dbCfg.php</code> in <code>$cfgDir</code>) for writing.<br />Try creating the file on your server and making it writable. On Unix, the commands are <br />&nbsp<code>cd $cfgDir </code><br />&nbsp<code>touch dbCfg.php</code> <br/>&nbsp;<code>chmod 777 dbCfg.php</code>$cfg</div>";
  }
  if ($_GET['error'] == "3") {
    $error_message = "<div class='alert alert-info' style='text-align:left'>$helpBtn Config file (<code>$cfgDir/dbCfg.php</code>) successfully written. <br />For your security, please write-protect it using commands equivalent to<br />&nbsp<code>cd $cfgDir </code><br />&nbsp<code>chmod 644 dbCfg.php</code><br />Please proceed to the <a href='index.php'>Admin Interface</a> to set up your products.</div>";
  }
  $cfgIsValid = $_GET['error'] == "4";
}
else {
  $cfgIsValid = DbHelper::cfgIsValid();
}
if ($cfgIsValid) { // valid config. don't display for security reasons
  $dbHost = $dbName = $dbUsr = $dbPwd = $dbEmail = $dbPrefix = "";
  $dbSetupHidden = "hidden";
  $adminSetupHidden = "";
  // Wait for the DB to accept config (Needed on Arvixe, CentOS)
  while (!DbHelper::cfgIsValid()) {
    sleep(5);
  }
  doInstall();
  $error_message = "<div class='alert alert-info'>$helpBtn Congratulations! You have configured your DB details.<br /> Please setup an admin account now.</div>"
          . "<script>$(document).ready(function(){setTimeout(function(){ window.location = 'adminSetup.php'; }, 2000);});</script>";
}
else {
  $dbSetupHidden = "";
  $adminSetupHidden = "hidden";
}

require_once('header.php');
?>

<div class="row">
  <div class="col-md5 center">
    <h2 class="col-md5"><img alt="EZ PayPal Logo" src="img/ezpaypal-brand.png" style="max-width: 250px;"/><br /><br />
      Welcome to EZ PayPal Setup</h2><br /><br />
  </div>
  <!--/span-->
</div><!--/row-->

<div class="row">
  <div class="well col-md-5 center setup-box">
    <?php echo $error_message . $counter; ?>

    <form class="form-horizontal <?php echo $dbSetupHidden; ?>" action="index.php" method="post">
      <fieldset>
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-cloud-upload blue"></i></span>
          <input name="dbHost" type="text" class="form-control" placeholder="Database Host" value="<?php echo $dbHost; ?>">
        </div>
        <div class="clearfix"></div><br>

        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-hdd blue"></i></span>
          <input name="dbName" class="form-control" placeholder="Database Name" value="<?php echo $dbName; ?>">
        </div>
        <div class="clearfix"></div><br>

        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-list blue"></i></span>
          <input name="dbPrefix" class="form-control" placeholder="Database Prefix" value="<?php echo $dbPrefix; ?>">
        </div>
        <div class="clearfix"></div><br>

        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-log-in blue"></i></span>
          <input name="dbUsr" class="form-control" placeholder="Database User Name" value="<?php echo $dbUsr; ?>">
        </div>
        <div class="clearfix"></div><br>

        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-lock blue"></i></span>
          <input name="dbPwd" class="form-control" placeholder="Database Pasword" value="<?php echo $dbPwd; ?>">
        </div>
        <div class="clearfix"></div><br>

        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope blue"></i></span>
          <input name="dbEmail" class="form-control" placeholder="Your Email" value="<?php echo $dbEmail; ?>">
        </div>
        <div class="clearfix"></div><br>

        <p class="center col-md-5">
          <button id="dbSetup" type="submit" name="dbSetup" class="btn btn-primary">Setup</button>
        </p>
      </fieldset>
    </form>
    <p class="center col-md-5">
      <a class="btn btn-primary <?php echo $adminSetupHidden; ?>" href='adminSetup.php'>Admin Setup</a>
    </p>
  </div>
  <!--/span-->
</div><!--/row-->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 id="myModalTitle">Setup Help</h3>
      </div>
      <div class="modal-body">
        <div id="myModalText">
          <h4>Your application hasn't been installed. Please provide the database details to set it up.</h4>
          <p>
            You can give the same details as your existing ezPayPal (version older than V6.00) installation by entering the values from its <code>ezppCfg.php</code> to update it to this new version. If you do so, please be warned that the database schema will be modified, and you will not be able to go back to the old version. It is wise to keep a full back of your database.
          </p>
          <p>
            If you have the <a href='#' class='goPro'>Pro version</a> of this application, you can migrate the database at a later stage using built-in database tools.
          </p>
          <p>
            <i class="glyphicon glyphicon-cloud-upload blue"></i> <b>Database Host</b>: If your database is hosted on a different server, please provide its name. Usually, MySQL databases are hosted at the same server as your Webserver, in which case, you can use <code>localhost</code> as your database server.
          </p>
          <p>
            <i class="glyphicon glyphicon-hdd blue"></i> <b>Database Name</b>: If you are using a dedicated database created using your CPanel or other hosting provider interface, please provide its name. It usually has the form <code>username_dbname</code>. If you have limits on the number of databases you can create on your server, you can reuse an existing database. If not, it is best to create a dedicated one for this application.
          </p>
          <p>
            <i class="glyphicon glyphicon-list blue"></i> <b>Database Prefix</b>: <i>Optional</i>: Use a prefix for all the database tables so that you can easily identify them. A prefix like <code>ez_</code> is a decent one, but to enhance your security, you may want to choose a different one.
          </p>
          <p>
            <i class="glyphicon glyphicon-log-in blue"></i> <b>Database User Name</b>: Your username to log on to the database server. If you created your database and db users on a cPanel, you'd know the user name. It is typically the same as the database name itself. Please contact your system admin if in doubt.
          </p>
          <p>
            <i class="glyphicon glyphicon-lock blue"></i> <b>Database Password</b>: Your database password. You can set it on your cPanel or equivalent. Please contact your system admin if in doubt.
          </p>
          <p>
            <i class="glyphicon glyphicon-envelope blue"></i> <b>Your Email</b>: <i>Optional</i>. This email ID will be used to send database error messages from this application. Later on, you will set up other email addresses where application or support messages may be directed. However, if the DB cannot be connected to, those email IDs cannot be accessed, and diagnostic messages cannot be sent. So, this is the only email address the program will have access to. If you don't want emails about DB errors, leave it empty or give a fake email ID like <code>nobody@nowhere.com</code>.
          </p>
          <p>Once all the required values are given, this application will try to generate a DB configuration file for you. If it fails to do so because of file permission errors, it will ask you to correct the issues with clear instructions.</p>
          <p>Note that if you already have a valid configuration file (possibly created by running this interface previously), it will not let you modify it. This is a design feature -- in order to prevent hacker attacks on your server via this interface or specially crafted forms. To further improve your security, you may want to delete this file (<code><?php echo __FILE__; ?></code>) from your server.</p>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" id="myModalSave" class="btn btn-primary" data-dismiss="modal">Done</a>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function () {
    $('.setup-help').click(function (e) {
      e.preventDefault();
      $('#myModal').modal('show');
    });
    $('#dbSetup').click(function () {
      var current = 0;
      $('#counter').show();
      setInterval(function () {
        ++current;
        $('.counter').text(current);
      }, 1000);
    });
  });
</script>
<?php
require_once('footer.php');
