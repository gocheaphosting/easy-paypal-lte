<?php
if (!class_exists("EzPlugin")) {

  class EzPlugin {

    var $name, $key;
    var $isPro, $strPro, $plgDir, $plgURL;

    function __construct() { //constructor
      $this->isPro = file_exists("{$this->plgDir}/admin/options-advanced.php");
      if ($this->isPro) {
        $this->strPro = 'Pro';
      }
      else {
        $this->strPro = 'Lite';
      }
    }

    static function install($dir, $mOptions) {
      $ezOptions = get_option($mOptions);
      if (empty($ezOptions)) {
        // create the necessary tables
        $GLOBALS['isInstallingWP'] = true;
        chdir($dir . '/admin');
        require_once('dbSetup.php');
        $ezOptions['isSetup'] = true;
      }
      update_option($mOptions, $ezOptions);
    }

    static function uninstall($mOptions) {
      delete_option($mOptions);
    }

    function printAdminPage() {
      $forceAdminKey = $this->key . '_force_admin';
      $forceAgainKey = $forceAdminKey . '_again';
      if (!empty($_POST[$forceAdminKey])) {
        update_option($forceAdminKey, true);
      }
      $forceAdmin = get_option($forceAdminKey);
      if (!empty($_POST[$forceAgainKey])) {
        update_option($forceAgainKey, true);
      }
      $forceAgain = get_option($forceAgainKey);
      // index cannot be used for testing, because it may forward to some other file
      $testFile = "{$this->plgURL}admin/promo.php";
      $divTop = "<p><strong>{$this->name}</strong> loads its admin pages in an iFrame, which may look like an ad to some client side ad blockers. If you are running AdBlock or similar extensions, please disable it for your blog domain, and see if the admin page appears.</p><p>The plugin also needs direct access to its files so that they can be loaded in an iFrame. If you have some security setting or plugin denying the required access, you will see this message. Please disable such security settings (temporarily) to see if the admin page appears.</p><p>If you have an <code>.htaccess</code> file in your <code>wp-content</code> or <code>wp-content/plugins</code>folder, please remove it or modify it to allow access to the php files in <code>{$this->plgDir}/</code>.</p>";
      if (!$forceAdmin && !@file_get_contents($testFile)) {
        ?>
        <div class='error' style='padding:10px;margin:10px;font-size:1.3em;color:red;font-weight:500'>
          <?php echo $divTop; ?>
          <p>If you would like the plugin to try to open the admin page despite the restriction, please set the option here:</p>
          <form method="post">
            <input type="submit" value="Force Admin Page" name="<?php echo $forceAdminKey; ?>">
          </form>
          <p>
            <strong>
              Note that if the plugin still cannot load the admin page after forcing it, you may see a blank or error page here upon reload. If that happens, please deactivate and delete the plugin. It is not compatible with your blog setup.
            </strong>
          </p>
        </div>
        <div style='padding:5px;margin:0;background-color:#fdd;display:none' id="adBlocked">
          This plugin loads its admin pages in an iFrame, which may look like an ad to some browser-side ad blockers. Looks like your browser is preventing the admin pages from being displayed. If you are running AdBlock or similar extensions, please disable it for your blog domain, and see if the admin page appears.
        </div>
        <?php
        return;
      }
      if ($forceAdmin && !$forceAgain) {
        ?>
        <script>
          var errorTimeout = setTimeout(function () {
            jQuery('#the_iframe').replaceWith("<div class='error' style='padding:10px;margin:10px;font-size:1.3em;color:red;font-weight:500'><?php echo $divTop; ?><p><strong>If <?php echo $this->name; ?> still cannot load the admin page after forcing it, please deactivate and delete the plugin. It is not compatible with your blog setup.</strong></p><p><b>You can try forcing the admin page again (after taking a screenshot of this page, or otherwise saving the information), which will kill this message and try to load the admin page. <form method='post'><input type='submit' value='Force Admin Page Again' name='<?php echo $forceAgainKey; ?>'></form><br><br>If you still have errors on the admin page or if you get a blank admin page, this plugin really is not compatible with your blog setup.</b></p></div>");
          }, 1000);
        </script>
        <?php
      }
      $src = "{$this->plgURL}/admin/index.php?inframe";
      ?>
      <script>
        function calcHeight() {
          var w = window,
                  d = document,
                  e = d.documentElement,
                  g = d.getElementsByTagName('body')[0],
                  y = w.innerHeight || e.clientHeight || g.clientHeight;
          document.getElementById('the_iframe').height = y - 70;
        }
        if (window.addEventListener) {
          window.addEventListener('resize', calcHeight, false);
        }
        else if (window.attachEvent) {
          window.attachEvent('onresize', calcHeight);
        }
        jQuery(document).ready(function () {
          setTimeout(function () {
            jQuery("#adBlocked").show();
          }, 2000);
        });
      </script>
      <?php
      echo "<iframe src='$src' frameborder='0' style='width:100%;position:absolute;top:5px;left:-10px;right:0px;bottom:0px' width='100%' height='900px' id='the_iframe' onLoad='calcHeight();'></iframe>";
    }

  } //End Class EzPlugin

}
