<?php
require 'header.php';
insertAlerts(11);
if (empty(EZ::$options['sales_ipn'])) {
  EZ::showError("This feature is currently not active.", true);
}
else {

  function mkAttr($s) {
    return ucwords(str_replace("_", " ", $s));
  }
  ?>
  <div>
    <ul class="breadcrumb">
      <li>
        <a href="#">Home</a>
      </li>
      <li>
        <a href="#">IPN Poster</a>
      </li>
    </ul>
  </div>

  <?php
  openBox("Post IPN", "cog", 11, "<p>If your IPN messages are not posted properly, you can try recovering from it using this tool. This tool will repost IPN messages as though they are coming from PayPal. Although accessible only when logged, it is still a security hole. Remove this file if you are concerned about it.<p>"
          . "<p>Retrieve the IPN messages from PayPal and post them using this tool. You can also decode the IPN messages stored in your database table <code>{$db->dbPrefix}ipn</code> and post them as well.</p>");
  EZ::showWarning("Reposting IPN messages may mess up your database and resend email messages to your buyers. Please be careful. Use it only if you absolutely need to.", true);
  ?>
  <div class="col-lg-6 col-sm-12">
    <p>Here is how to retrieve your IPN messages from your PayPal account and repost.</p>
    <ul>
      <li>Logon to your <strong><a href='https://www.paypal.com' target='_blank'>PayPal Account</a></strong>.</li>
      <li>Go to <strong><a href='https://www.paypal.com/us/cgi-bin/webscr?cmd=_display-ipns-history' class='popup'>History &rarr; IPN History</a></strong>. (In the classic interface, you can also see the <strong>History</strong> item on the menu bar. In the new interface, just click the link.)</li>
      <li>Click on a Message ID to see the IPN message.</li>
      <li>Cut and paste it in the text box below.</li>
      <li>Hit the <strong>Post it</strong> button.</li>
      <li>Please know that Posting the IPN message may affect the database and resend email messages.</li>
    </ul>
  </div>
  <div class="col-lg-6 col-sm-12">
    <p><strong>EZ PayPal</strong> also stores all the IPN messages that it receives. You can retrieve them and decode/repost them as follows.</p>
    <ul>
      <li>Use your favorite database client (such as phpMyAdmin) to access your database..</li>
      <li>Browse to the database table <code><?php echo "{$db->dbPrefix}ipn"; ?></code>.</li>
      <li>Find the IPN message by date/time.</li>
      <li>Cut and paste it in the text box.</li>
      <li>Hit the <strong>Decode IPN</strong> button.</li>
      <li>Optionally, <strong>Post It</strong> if needed.</li>
    </ul>
  </div>
  <div class="clearfix"></div>
  <?php
  if (!empty($_POST['ipn'])) {
    if (isset($_POST['postIpn'])) {
      $ipn = $_POST['ipn'];
      parse_str($ipn, $_POST);
      unset($_POST['ipn']);
      require_once '../EzOfficePro.php';
      $office = new EzOfficePro();
      $log->setTag("Office");
      $office->setDebug(true);
      $office->processTxn();
      echo "<pre style='text-align:left'>\n" . $log->get() . "</pre>";
      EZ::showError($log->getError());
      EZ::showWarning($log->getWarn());
      EZ::showSuccess($log->getInfo());
    }
    if (isset($_POST['decodeIpn'])) {
      $b64 = $_POST['ipn'];
      $serialized = base64_decode(str_replace("\r\n", "", $b64));
      $ipn = unserialize($serialized);
      $rowcount = 0;
      ?>
      <h3>Decoded IPN Message</h3>
      <table class="table table-striped table-bordered responsive">
        <tbody>
          <?php
          $nRows = count($ipn);
          $keys = array_keys($ipn);
          echo "<tr>\n";
          for ($i = 0; $i < $nRows; ++$i) {
            $key = $keys[$i];
            $val = $ipn[$key];
            $attr = mkAttr($key);
            $j = $i + 1;
            echo "<td style='width:24%;font-weight:bold'>$attr</td><td style='width:24%;'>$val</td>\n";
            if ($j % 2 == 0) {
              if ($j < $nRows) {
                echo "</tr>\n<tr>\n";
              }
              else {
                echo "</tr>\n";
              }
            }
            else {
              echo "<td style='width:4%;'></td>\n";
            }
          }
          if ($nRows + 1 % 4 != 0 && $nRows + 1 % 2 == 0) {
            echo "<td></td><td></td></tr>\n";
          }
          ?>
        </tbody>
      </table>
      <?php
      $req = '';
      foreach ($ipn as $k => $v) {
        $req .= "$k=$v&";
      }
      rtrim($req, "&");
    }
  }
  else {
    $req = "";
  }
  ?>
  <form role="form" method="post">
    <div class="form-group">
      <label for="ipn">IPN Message</label>
      <textarea class="form-control" name="ipn" placeholder="Cut and Paste IPN message" style="height:200px;white-space: normal" id="ipn"><?php echo $req; ?></textarea>
    </div>
    <button type="submit" class="btn btn-danger" name="postIpn">Post it</button>
    <button type="submit" class="btn btn-primary" name="decodeIpn">Decode</button>
  </form>
  <?php
  closeBox();
}
require 'footer.php';
