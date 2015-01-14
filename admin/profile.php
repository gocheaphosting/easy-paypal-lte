<?php
require_once('header.php');

$error = -1;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if (isset($_POST['login'])) {
    $row = EZ::authenticate();
  }
  if (is_array($row)) {
    if ($_POST['newpassword0'] == $_POST['newpassword1']) {
      if (empty($_POST['newpassword0']) && empty($_POST['email'])) {
        $error = 3;
      }
      else {
        $error = 0;
        $data = array();
        $data['id'] = 1;
        $data['username'] = $_POST['newusername'];
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
  else {
    $error = $row;
  }
}
switch ($error) {
  case 0:
    $error_message = '<div class="alert alert-info">User authenticated and Profile updated.</div>';
    break;
  case 1:
    $error_message = '<div class="alert alert-danger">Your current password is incorrect!</div>';
    break;
  case 3:
    $error_message = '<div class="alert alert-danger">Nothing to update! New password and new email are empty.</div>';
    break;
  case 4:
    $error_message = '<div class="alert alert-warning">Password not updated because it is empty. Email is updated.</div>';
    break;
  case 5:
    $error_message = '<div class="alert alert-warning">Email not updated because it is empty. Password is updated.</div>';
    break;
  case 6:
    $error_message = '<div class="alert alert-danger">New passwords do not match.</div>';
    break;
  default:
    $error_message = '<div class="alert alert-info">For your security, verify your current password<br/> again before updating your profile.</div>';
    break;
}
if ($error == "1") {

}
elseif ($error == "2") {

}
openBox("Edit Your Profile", "lock", 11, "<p>
   <i class='glyphicon glyphicon-lock red'></i> <b>Current Password</b>: For your security, this application requires you to authenticate yourself before you can modify the admin profile. Please enter your existing password for authentication.
   </p>
   <i class='glyphicon glyphicon-user blue'></i> <b>Username</b>: Enter a new admin user name. A name like <code>admin</code> is fine, but something less obvious would be more secure.
   </p>
   <p>
   <i class='glyphicon glyphicon-lock blue'></i> <b>New Password</b>: Please type in a new strong password (at least six characters long), and verify it.
   </p>
   <p>
   <i class='glyphicon glyphicon-envelope blue'></i> <b>Email</b>: <i>Optional</i>: Please provide an email address where you can receive password retrieval information, in case you forget your password.
   </p>");
$current = $db->getRowData('administrator');
?>
<div class="well col-md-5 center login-box">
  <?php echo $error_message; ?>
  <form class="form-horizontal" action="" method="post" id="defaultForm">
    <fieldset>
      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
          <input name="myusername" type="hidden" value="<?php echo $current['username']; ?>">
          <input name="mypassword" type="password" class="form-control" placeholder="Current Password">
        </div>
      </div>
      <div class="clearfix"></div><br>

      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
          <input name="newusername" type="text" class="form-control" placeholder="New Username"  value="<?php echo $current['username']; ?>">
        </div>
      </div>
      <div class="clearfix"></div><br>

      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
          <input name="newpassword0" type="password" class="form-control" placeholder="New Password">
        </div>
      </div>
      <div class="clearfix"></div><br>

      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
          <input name="newpassword1" type="password" class="form-control" placeholder="Confirm New Password">
        </div>
      </div>
      <div class="clearfix"></div><br>

      <div class="control-group">
        <div class="input-group input-group-lg">
          <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
          <input name="email" type="text" class="form-control" placeholder="New Email" value="<?php echo $current['email']; ?>">
        </div>
      </div>
      <div class="clearfix"></div>

      <p class="center col-md-5">
        <button type="submit" name="login" class="btn btn-primary">Update</button>
      </p>
    </fieldset>
  </form>
</div>

<?php
closeBox();
?>
<script>
  $(document).ready(function () {
    $('#defaultForm').bootstrapValidator({
      message: 'This value is not valid',
      group: '.control-group',
      fields: {
        myusername: {
          message: 'Username is not valid',
          validators: {
            notEmpty: {
              message: 'Username is required'
            }
          }
        },
        mypassword: {
          message: 'Current password is not valid',
          validators: {
            notEmpty: {
              message: 'Current password is required'
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
