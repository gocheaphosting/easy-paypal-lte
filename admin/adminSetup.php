<?php
if ($_SERVER['REQUEST_METHOD'] != "POST") {
  require_once 'mock-EZ.php';
}
$no_visible_elements = true;
require_once('../DbHelper.php');
$error = -1;
$indexBtnHidden = "hidden";
$adminSetupHidden = "";

if (!DbHelper::cfgIsValid()) {
  header('location: dbSetup.php?error=1');
  exit;
}

// clear previous logins
session_start();
session_unset();
session_destroy();
session_write_close();
setcookie(session_name(), '', 0, '/');
session_regenerate_id(true);

$helpBtn = "<a class='setup-help' style='font-size:1.5em;float:right' href='#' title='Click for help' data-toggle='tooltip' data-content=''><i class='glyphicon glyphicon-question-sign blue'></i></a>";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if (isset($_POST['login'])) {
    if ($_POST['newpassword0'] == $_POST['newpassword1']) {
      if (empty($_POST['newpassword0']) && empty($_POST['email'])) {
        $error = 3;
      }
      else {
        $error = 0;
        $data = array();
        $data['id'] = 1;
        $data['username'] = $_POST['myusername'];
        require_once('../EZ.php');
        if (!empty($_POST['newpassword0'])) {
          $data['password'] = EZ::md5($_POST['newpassword0']);
        }
        else {
          $error = 4;
        }
        if (!empty($_POST['email'])) {
          $data['email'] = $_POST['email'];
        }
        else {
          $error = 5;
        }
        $db->putRowData('administrator', $data);
      }
    }
    else {
      $error = 6;
    }
  }
}
$db = new DbHelper();
$table = 'administrator';
if ($db->tableExists($table)) {
  $row = $db->getData($table);
}
else {
  header('location: dbSetup.php');
  exit;
}
if (!empty($row)) {
  // already set up.
  $error = 7;
}
switch ($error) {
  case 0:
    $error_message = "<div class='alert alert-info'>$helpBtn User authenticated and Profile created.</div>";
    break;
  case 1:
    $error_message = "<div class='alert alert-danger'>$helpBtn Your username and password are incorrect!</div>";
    break;
  case 3:
    $error_message = "<div class='alert alert-danger'>$helpBtn Nothing to update! New password and new email are empty.</div>";
    break;
  case 4:
    $error_message = "<div class='alert alert-warning'>$helpBtn Password not updated because it is empty. Email is updated.</div>";
    break;
  case 5:
    $error_message = "<div class='alert alert-warning'>$helpBtn Email not updated because it is empty. Password is updated.</div>";
    break;
  case 6:
    $error_message = "<div class='alert alert-danger'>$helpBtn New passwords do not match.</div>";
    break;
  case 7:
    $error_message = "<div class='alert alert-info'>$helpBtn Congratulations! You have fully configured your application.<br /> Please go to the admin interface.</div>"
            . "<script>$(document).ready(function(){setTimeout(function(){ window.location = 'index.php'; }, 2000);});</script>";
    $adminSetupHidden = "hidden";
    $indexBtnHidden = "";
    break;
  default:
    $error_message = "<div class='alert alert-info'>$helpBtn Please create an admin account.</div>";
    break;
}

require_once('header.php');
?>
<div class="row">
  <div class="col-md5 center">
    <h2 class="col-md5"><img alt="EZ PayPal Logo" src="img/ezpaypal-brand.png" style="max-width: 250px;"/><br /><br />
      Welcome to EZ PayPal Admin Setup</h2><br /><br />
  </div>
  <!--/span-->
</div><!--/row-->
<div class="well col-md-5 center login-box">
  <?php echo $error_message; ?>
  <form class="form-horizontal <?php echo $adminSetupHidden; ?>" action="" method="post" id="defaultForm">
    <fieldset>
      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
          <input name="myusername" type="text" class="form-control" placeholder="Username">
        </div>
      </div>
      <div class="clearfix"></div><br>

      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
          <input name="newpassword0" type="password" class="form-control" placeholder="Password">
        </div>
      </div>
      <div class="clearfix"></div><br>

      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
          <input name="newpassword1" type="password" class="form-control" placeholder="Password Again">
        </div>
      </div>
      <div class="clearfix"></div><br>

      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
          <input name="email" type="text" class="form-control" placeholder="Email">
        </div>
      </div>
      <div class="clearfix"></div>

      <p class="center col-md-5">
        <button type="submit" name="login" class="btn btn-primary">Create Admin</button>
      </p>
    </fieldset>
  </form>
  <p class="center col-md-5">
    <a class="btn btn-primary <?php echo $indexBtnHidden; ?>" href='index.php'>Admin Interface</a>
  </p>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 id="myModalTitle">Admin Setup Help</h3>
      </div>
      <div class="modal-body">
        <div id="myModalText">
          <h4>Your application has been installed, but needs an admin account for secure access.</h4><br />
          <p>
            <i class="glyphicon glyphicon-user blue"></i> <b>Username</b>: Select an admin user name. A name like <code>admin</code> is fine, but something less obvious would be more secure.
          </p>
          <p>
            <i class="glyphicon glyphicon-lock blue"></i> <b>Password</b>: Please type in a strong password (at least six characters long), and verify it.
          </p>
          <p>
            <i class="glyphicon glyphicon-envelope blue"></i> <b>Email</b>: <i>Optional</i>: Please provide an email address where you can receive password retrieval information, in case you forget your password.
          </p>
          <p>Once the admin account is set up, you are ready to use the application. This page will not be operational after you set up your admin account, which is a precaution against possible hacker attacks. To further improve your security, you may want to delete this file (<code><?php echo __FILE__; ?></code>) from your server.</p>
          <p>Note that this application allows only one admin account, because one is all that is needed. If you would like to modify the admin profile (password and email), you can do so from the admin interface.</p>
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
    $('#defaultForm').bootstrapValidator({
      message: 'This value is not valid',
      group: '.control-group',
      fields: {
        myusername: {
          message: 'Username is not valid',
          validators: {
            notEmpty: {
              message: 'Username is required'
            },
            stringLength: {
              min: 5,
              max: 15,
              message: 'Username must be at least 5 and no more than 15 characters long'
            },
            regexp: {
              regexp: /^[a-zA-Z0-9_\.]+$/,
              message: 'Username can only consist of alphabetical, number, dot and underscore'
            }
          }
        },
        email: {
          validators: {
            notEmpty: {
              message: 'Email address is required'
            },
            emailAddress: {
              message: 'Not a valid email address'
            }
          }
        },
        newpassword0: {
          validators: {
            notEmpty: {
              message: 'Password is required'
            },
            stringLength: {
              min: 6,
              max: 15,
              message: 'Password must be at least 6 characters long'
            },
            different: {
              field: 'myusername',
              message: 'Password should not be the same as username'
            }
          }
        },
        newpassword1: {
          validators: {
            notEmpty: {
              message: 'Password confirmation is required'
            },
            identical: {
              field: 'newpassword0',
              message: 'Password mismatch'
            }
          }
        }
      }
    });
  });
</script>
<?php
require('footer.php');
