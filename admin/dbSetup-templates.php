<?php

if (!function_exists('insertTemplate')) {

  function insertTemplate($db, $name, $value) {
    if (isset($_REQUEST['update'])) {
      $updating = true;
    }
    else {
      $updating = false;
    }
    $table = $db->prefix("templates");
    if ($updating) { // do not overwrite template values
      $onDuplicate = "UPDATE name='$name'";
    }
    else {
      $onDuplicate = "UPDATE name='$name', value='$value', active=true";
    }
    $sql = "INSERT INTO $table SET name='$name', value='$value', active=true
  ON DUPLICATE KEY $onDuplicate";
    $db->query($sql);
  }

}

if (!function_exists('mkHeaderAndFooter')) {

  function mkHeaderAndFooter() {
    $header = '<table cellpadding=4 style="width:500px; margin:5px auto 10px auto; border:1px solid #ccc; color:#224; font-family:arial, helvetica, sans-serif; font-size:10pt;" align="center">
  <tr>
    <td colspan="2" style="text-align:center">
      {img:ezpaypal-brand.png}
    </td>
  </tr>
';

    $footer = '
  <tr><td colspan="2" style="padding:5px; border-top:1px solid #666; background:#ececec; text-align:center;">
    <small>Powered by <a href="http://buy.thulasidas.com/ezpaypal">
    <em><span style="color:#f37">EZ</span> <span style="color:#25d">Pay</span><span style="color:#1ad">Pal</span></em></a>.
    </small>
  </td>
</tr>
</table>';
    $ret = compact('header', 'footer');
    return $ret;
  }

}

function mkBasicTemplates($db, $header, $footer) {
  $name = "download_page";
  $value = '<h4>' . __('Download Page for {product_name}', 'easy-paypal') . '</h4>
<br /><br />
<p>' . __('Dear {customer_name},', 'easy-paypal') . '</p>
<p>' . __('Thank you for purchasing {product_name}. Here are your purchase details:', 'easy-paypal') . '</p>
    <table align="center">
        <tr><td><strong>' . __('Product Name', 'easy-paypal') . ':</strong> </td><td> {product_name}</td></tr>
        <tr><td><strong>' . __('Purchased Price', 'easy-paypal') . ':</strong> </td><td> ${purchase_amount}</td></tr>
        <tr><td><strong>' . __('Transaction ID', 'easy-paypal') . ':</strong> </td><td> {txn_id}</td></tr>
        <tr><td><strong>' . __('Your email', 'easy-paypal') . ':</strong> </td><td> {customer_email}</td></tr>
        <tr><td><strong>' . __('Purchase Date', 'easy-paypal') . ':</strong> </td><td> {purchase_date}</td></tr>
        <tr><td><strong>' . __('Download Time Limit', 'easy-paypal') . ':</strong> </td><td> {expire_hours} hours</td></tr>
        <tr><td><strong>' . __('Download Expiry', 'easy-paypal') . ':</strong> </td><td> {expire_date}</td></tr>
      </table><br />
<p>' . __('Please find the download link to the product below.', 'easy-paypal') . '</p>
{download_button}';
  insertTemplate($db, $name, $value);
  $name = "download_page_query";
  $value = "<h4>" . __("Thank you for your purchase", 'easy-paypal') . "</h4><p>" . __("Unfortunately, there is a technical problem with your purchase. Most likely, your purchase details have not been posted by PayPal yet. Or our automated email with the download link ended up in your Junk/Spam folder. But fear not, you can retrieve the download/service link below.", 'easy-paypal') . "</p><p>"
          . __("Please enter your <b>PayPal email address</b> below:", 'easy-paypal') . '</p><form method="post">'
          . '<div class="input-group col-lg-8 col-md-12 col-sm-12" style="max-width:450px;padding:10px;"><span class="input-group-addon"><i class="glyphicon glyphicon-envelope blue"></i></span>'
          . '<input class="form-control" placeholder="Your PayPal Email" name="email">'
          . '<span class="input-group-btn"><input class="btn btn-primary" type="submit" id="download" value="' . __("Retrieve Product", "easy-paypal") . '"></span> </div></form>'
          . '<p><span style="color:red">' . sprintf(__("Plese be sure to use the <b>PayPal email address</b>, where you got the mail with the subject <strong>Receipt for Your Payment</strong> to us.", 'easy-paypal')) . "</span></p><p>" . __("If you are yet to receive the PayPal message, please wait.", 'easy-paypal') . " "
          . sprintf(__("You will soon receive it and an email from us with the download/service/support link.", 'easy-paypal')) . " " . __("If you do not find either in your Inbox in the next five minutes or so, please be sure to check your Junk/Spam folders as well.", 'easy-paypal') . "</p>";
  insertTemplate($db, $name, $value);

  $name = "email_subject";
  $value = __("Your Purchase: {product_name} ({product_code})", 'easy-paypal');
  insertTemplate($db, $name, $value);

  $name = "email_body_html";
  $value = $header . '  <tr>
  <td colspan=2 style="margin-bottom: 1.5em; padding: 0.3em; text-align: left; font-weight: bold; border-top: 1px solid; border-bottom: 1px solid; background-color: #390; border-color: #390; color: #390; background: #CFC;">Thank you for your purchase!
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <h2 style="font-size: 1.3em; margin: 0.6em 0 0 0; letter-spacing: -1px; color: #25d;">
      Download Page for {product_name}
      </h2>
      <br />
      <br />
      <p>Dear {customer_name},</p>
      <p>Thank you for purchasing {product_name}. Here are your purchase details:</p>
     <table align="center">
        <tr><td><strong>Product Name:</strong> </td><td> {product_name}</td></tr>
        <tr><td><strong>Purchased Price:</strong> </td><td> ${purchase_amount}</td></tr>
        <tr><td><strong>Transaction ID:</strong> </td><td> {txn_id}</td></tr>
        <tr><td><strong>Your email:</strong> </td><td> {customer_email}</td></tr>
        <tr><td><strong>Purchase Date:</strong> </td><td> {purchase_date}</td></tr>
        <tr><td><strong>Download Time Limit:</strong> </td><td> {expire_hours} hours</td></tr>
        <tr><td><strong>Download Expiry:</strong> </td><td> {expire_date}</td></tr>
      </table><br />
      <p>Please find the download link to the product below.</p>
      {download_button}
     <p>Should you need any assistance with the download, please reply to this email.</p>
     <br />
     Sincerely,
     <br />
     {support_name}
     <br />
     <br />
    </td>
  </tr>
' . $footer;
  insertTemplate($db, $name, $value);

  $name = "email_body";
  $value = __("Dear {customer_name},

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
{support_name}", 'easy-paypal');
  insertTemplate($db, $name, $value);
}

function mkDefaultTemplates($db) {
  $styles = mkHeaderAndFooter();
  extract($styles);
  mkBasicTemplates($db, $header, $footer);
}
