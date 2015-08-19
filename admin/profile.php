<?php

require_once('header.php');
require_once '../EZ.php';
require_once 'Installer.php';

$installer = new Installer();

$installer->configure();

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
$installer->verifyAdmin($current);
$installer->printAdminForm($current);

closeBox();

require_once 'footer.php';
