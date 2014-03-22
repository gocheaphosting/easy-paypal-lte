<?php
function createTables() {
  $db = $GLOBALS['ezDB'] ;
  if (empty($GLOBALS['innoDB'])) $innoDB = '' ;
  else $innoDB = $GLOBALS['innoDB'] ;
  if (file_exists('pro/useInnoDB.php')) {
    include ('pro/useInnoDB.php') ;
  }

  $t_products = $db->prefix("products") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_products (
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
created TIMESTAMP DEFAULT NOW(),
active BOOLEAN NOT NULL DEFAULT TRUE,
expire_hours INT(3) NOT NULL,
product_name VARCHAR(128) NOT NULL,
product_code VARCHAR(128) NOT NULL,
product_category VARCHAR(128) NOT NULL,
product_grouping VARCHAR(128) NOT NULL,
product_price DECIMAL(6,2) NOT NULL,
no_shipping SMALLINT NOT NULL DEFAULT 0,
mc_currency VARCHAR(3),
version DECIMAL(6,2) NOT NULL,
filename VARCHAR(128) NOT NULL,
file VARCHAR(256) NOT NULL,
UNIQUE KEY (product_code),
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  $t_productmeta = $db->prefix("product_meta") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_productmeta (
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
created TIMESTAMP DEFAULT NOW(),
product_code VARCHAR(128) NOT NULL,
name VARCHAR(128),
value LONGTEXT,
UNIQUE KEY (product_code, name),
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  $t_sales = $db->prefix("sales") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_sales (
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
created TIMESTAMP DEFAULT NOW(),
txn_id VARCHAR(20) NOT NULL,
customer_name VARCHAR(128) NOT NULL,
customer_email VARCHAR(128) NOT NULL,
business_name VARCHAR(128) NOT NULL,
purchase_amount DECIMAL(6,2) NOT NULL,
purchase_status VARCHAR(16) NOT NULL,
purchase_mode VARCHAR(16),
purchase_date DATETIME NOT NULL,
expire_hours INT(3) NOT NULL,
expire_date DATETIME NOT NULL,
product_name VARCHAR(128) NOT NULL,
product_code VARCHAR(128) NOT NULL,
quantity INT UNSIGNED DEFAULT 1,
sold_version VARCHAR(32),
updated_version VARCHAR(32),
lite_version BOOL DEFAULT TRUE,
affiliate_id VARCHAR(32),
UNIQUE KEY (txn_id),
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  $t_salesdetails = $db->prefix("sale_details") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_salesdetails (
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
created TIMESTAMP DEFAULT NOW(),
business VARCHAR(128),
charset VARCHAR(32),
custom VARCHAR(255),
first_name VARCHAR(64),
handling_amount DECIMAL(6,2),
ipn_track_id VARCHAR(32),
item_name VARCHAR(128) NOT NULL,
item_number VARCHAR(128) NOT NULL,
last_name VARCHAR(64) NOT NULL,
mc_currency VARCHAR(3),
mc_fee  DECIMAL(10,2),
mc_gross DECIMAL(10,2),
notify_version VARCHAR(32),
parent_txn_id VARCHAR(20) NOT NULL,
payer_email VARCHAR(128) NOT NULL,
payer_business_name VARCHAR(128) NOT NULL,
payer_id VARCHAR(20),
payer_status VARCHAR(20),
payment_date DATETIME NOT NULL,
payment_fee DECIMAL(6,2),
payment_gross DECIMAL(6,2) NOT NULL,
payment_status VARCHAR(32) NOT NULL,
payment_type VARCHAR(128),
protection_eligibility VARCHAR(128),
quantity INT UNSIGNED DEFAULT 1,
receiver_email VARCHAR(128) NOT NULL,
receiver_id VARCHAR(20),
residence_country VARCHAR(2),
shipping DECIMAL(6,2),
tax VARCHAR(20),
transaction_subject VARCHAR(128),
txn_id VARCHAR(20) NOT NULL,
txn_type VARCHAR(48),
verify_sign VARCHAR(128),
dbStatus VARCHAR(128),
UNIQUE KEY (txn_id),
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  $t_paypal = $db->prefix("paypal") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_paypal (
id INT UNSIGNED NOT NULL auto_increment,
created TIMESTAMP DEFAULT NOW(),
active BOOLEAN NOT NULL DEFAULT TRUE,
paypal_email VARCHAR(128) NOT NULL,
paypal_name VARCHAR(128) NOT NULL,
sandbox_email VARCHAR(128),
sandbox_mode BOOLEAN NOT NULL DEFAULT 0,
mail_logs BOOLEAN NOT NULL DEFAULT 0,
UNIQUE KEY (paypal_email),
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  $t_options = $db->prefix("options") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_options (
id INT UNSIGNED NOT NULL auto_increment,
created TIMESTAMP DEFAULT NOW(),
support_email VARCHAR(128) NOT NULL,
support_name VARCHAR(128) NOT NULL,
expire_hours INT(3) NOT NULL,
random_storage BOOLEAN NOT NULL DEFAULT 1,
random_file BOOLEAN NOT NULL DEFAULT 1,
html_email BOOLEAN NOT NULL DEFAULT 0,
storage_location VARCHAR(128) NOT NULL,
lite_location VARCHAR(128) NOT NULL,
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  $t_optionmeta = $db->prefix("option_meta") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_optionmeta (
id INT UNSIGNED NOT NULL auto_increment,
created TIMESTAMP DEFAULT NOW(),
form_name VARCHAR(128) NOT NULL,
name VARCHAR(32) NOT NULL,
value LONGTEXT,
display_name VARCHAR(64) NOT NULL,
type VARCHAR(32) NOT NULL,
help LONGTEXT,
validator VARCHAR(32) NOT NULL,
options LONGTEXT,
associated_name VARCHAR(32) NOT NULL,
UNIQUE KEY (form_name, name),
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  $t_templates = $db->prefix("templates") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_templates (
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
created TIMESTAMP DEFAULT NOW(),
active BOOLEAN NOT NULL DEFAULT TRUE,
name VARCHAR(64),
value LONGTEXT,
product_category VARCHAR(128) NOT NULL,
product_grouping VARCHAR(128) NOT NULL,
UNIQUE KEY (product_category, product_grouping, name),
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  $t_ezaffiliates = $db->prefix("ezaffiliates") ;
  $sql = "CREATE TABLE IF NOT EXISTS $t_ezaffiliates (
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
created TIMESTAMP DEFAULT NOW(),
active BOOLEAN NOT NULL DEFAULT TRUE,
name VARCHAR(128),
value LONGTEXT,
UNIQUE KEY (name),
PRIMARY KEY (id))
$innoDB";
  $db->query($sql);

  if (function_exists('addFK')) addFK() ;
  mkDefaultTemplates() ;
  if (function_exists('mkProTemplates')) mkProTemplates() ;
}

function createLoginTable($username, $password) {
  $db = $GLOBALS['ezDB'] ;
  $return = array() ;
  $table = $db->prefix(md5($username)) ;
  if ($db->tableExists($table)) {
    include_once("ezpp.php") ;
    $ezpp = new ezpp($db) ;
    $msg =  "Admin user ($username) already exists. " ;
    if ($ezpp->login($username, $password))
      $return['warning'] = $msg . "Temporarily logged in." ;
    else {
      $return['error'] = $msg . "Enter your password (twice)." ;
      return $return ;
    }
  }
  $key = md5($password) ;
  $sql = "CREATE TABLE IF NOT EXISTS $table (
id INT UNSIGNED NOT NULL AUTO_INCREMENT,
created TIMESTAMP DEFAULT NOW(),
keyval VARCHAR(32),
value VARCHAR(32),
PRIMARY KEY (id));" ;
  $db->query($sql) ;
  $row = array('keyval' => $key, 'value' => $key) ;
  $db->putRowData($table, $row) ;
  return $return ;
}

function mkDefaultTemplates() {
  $name = "download_page" ;
  $value = '<h4>Download Page for {product_name}</h4>
<br /><br />
<p>Dear {customer_name},</p>
<p>Thank you for purchasing {product_name}. Here are your purchase details:</p>
<blockquote>
  <p><strong>Product Name:</strong>&nbsp;&nbsp;&nbsp; {product_name}</p>
  <p><strong>Purchased Price:</strong>&nbsp;&nbsp;&nbsp; {purchase_amount} {mc_currency}</p>
  <p><strong>Transaction ID:</strong>&nbsp;&nbsp;&nbsp; {txn_id}</p>
  <p><strong>Purchase Date:</strong>&nbsp;&nbsp;&nbsp; {purchase_date}</p>
  <p><strong>Download Time Limit:</strong>&nbsp;&nbsp;&nbsp; {expire_hours} hours</p>
  <p><strong>Download Expiry:</strong>&nbsp;&nbsp;&nbsp; {expire_date}</p>
</blockquote>
<p>Please find the download link to the product below.</p>
{download_button}' ;
  insertTemplate($name, $value) ;

  $name = "email_body" ;
  $value = "Dear {customer_name},

Thank you for purchasing {product_name}.

Below is the URL to your download page. You have
approximately {expire_hours} hours to download your purchase.
After that period the download page will expire.

Download for {product_name} ({product_code})
{download_url}

If the URL above is not clickable, please copy and paste the
URL into your browser.

Should you need any assistance with the download, please reply
to this email.

(Ref: This email is sent to {customer_email}).

Sincerely,
{support_name}" ;
  insertTemplate($name, $value) ;

  $name = "email_subject" ;
  $value = "Your Purchase: {product_name} ({product_code})" ;
  insertTemplate($name, $value) ;
}

function insertTemplate($name, $value) {
  $db = $GLOBALS['ezDB'] ;
  $table = $db->prefix("templates") ;
  $sql = "INSERT INTO $table SET name='$name', value='$value', active=true
  ON DUPLICATE KEY UPDATE name='$name', value='$value', active=true" ;
  $db->query($sql) ;
}

if (file_exists('pro/extraTemplates.php')) include_once('pro/extraTemplates.php') ;
