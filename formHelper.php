<?php
if (class_exists("formHelper")) {
  echo "Problem, class formHelper exists! \nCannot safely continue.\n" ;
  exit ;
}
else {
  class formHelper {
    var $html, $db, $cwd ;
    var $rowSetName, $rowSet ;
    function __construct(&$db, &$html, $dieOnError=true) {
      $this->db = &$db ;
      $this->html = &$html ;
      if (function_exists('plugins_url'))
        $this->cwd = plugins_url(basename(dirname(__FILE__))) ;
      else
        $this->cwd = $html->cwd ;
      if ($dieOnError) {
        // Check DB connectivity
        $this->db->doOrDie($this->html) ;
      }
    }
    function __destruct(){
    }
    function formHelper(&$db, &$html, $dieOnError=true) {
      if(version_compare(PHP_VERSION,"5.0.0","<")){
        $this->__construct($db, $html, $dieOnError);
        register_shutdown_function(array($this,"__destruct"));
      }
    }
    function setDB(&$db) {
      $this->db = &$db ;
    }
    function setHtml(&$html) {
      $this->html = &$html ;
    }
    function isValid($control, $s) {
      $err = '' ;
      if (!empty($this->db)) $db = $this->db ;
      $type = 'text' ;
      if (!empty($control['type'])) $type = strtolower($control['type']) ;
      if ($db->valid && $type == 'text' ) {
        $escaped = $db->escape($s) ;
        if ($escaped != $s) $err = "Looks like SQL injection. Rejected.<br />" ;
      }
      $validator = 'validate_' ;
      if (!empty( $control['validator'])) $validator .= $control['validator'] ;
      if (method_exists($this, $validator)) $err .= $this->$validator($s) ;
      if (!empty($err)) return $err ;
      else return true ;
    }
    function validate_email($s) {
      if (!filter_var($s, FILTER_VALIDATE_EMAIL)) return "Bad email address" ;
    }
    function validate_notNull($s) {
      $s = trim($s) ;
      if (empty($s)) return "Null value not allowed." ;
    }
    function validate_number($s) {
      if (!is_numeric($s)) return "Need a number here" ;
    }
    function getValErr(&$control, $posted) {
      // get the posted value from a control (and generate a possible error string)
      $return = array() ;
      if (!is_array($control)) return $return ;
      $type = 'text' ;
      if (!empty($control['type'])) $type = strtolower($control['type']) ;
      switch ($type) {
      case 'button' :
        break ;
      case 'checkbox' :
        $return['val'] = isset($posted) ;
        break ;
      case 'file' :
        $return['val'] = 'File Accepted' ;
        break ;
      case 'button' :
        $return['val'] = $control['value'] ;
        break ;
      case 'dbselect' :
      case 'select' :
      case 'dbeditableselect' :
      case 'editableselect' :
      case 'text' :
      case 'textarea' :
      default :
        $return['val'] = $posted ;
        $err = $this->isValid($control, $posted) ;
        if ($err !== true) $return['err'] = $err ;
        if (!empty($control['unique']) and $control['unique']) {
          $db = $this->db ;
          if (!empty($control['table'])) $table = $control['table'] ;
          if (!empty($control['column'])) $column = $control['column'] ;
          if (empty($db) || empty($table) || empty($column))
            $err .= '<br />Unique column requires DB details. [Dev error]' ;
          else
            $return['update'] = $db->rowExists($table, $column, $posted) ;
        }
      }
      return $return ;
    }
    function validate_password($password) {
      $strength = array("blank","very weak","weak","not very strong","strong","very strong");
      $score = 1 ;
      if (strlen($password) < 1) $score = 0 ;
      if (strlen($password) < 4) $score = 1 ;
      if (strlen($password) >= 6) $score++;
      if (strlen($password) >= 8) $score++;
      if (strlen($password) >= 10) $score++;
      if (preg_match("/[a-z]/", $password) && preg_match("/[A-Z]/", $password))
        $score++;
      if (preg_match("/[0-9]/", $password))
        $score++;
      if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,£,(,)]/", $password))
        $score++;
      if ($score > 5) $score = 5 ;
      if ($score < 3)
        return "Your password [strength: $score] is " . $strength[$score];
    }
    function showOneRow($k, $v) {
      if (!is_array($v)) return ;
      if (!empty($v['needsPro']) && $v['needsPro']) {
        if (!file_exists("pro/pro.php")) return ;
      }
      $js = '' ;
      $error = '' ;
      if (!empty($v['error']))
        $error = '<font class="error">' . $v['error'] . '</font>' ;
      if (!empty($v['warning'])) {
        if (!empty($error)) $error .= '<br />' ;
        $error .= '<font class="warning">' . $v['warning'] . '</font>' ;
      }
      $type = 'text' ;
      if (!empty($v['type'])) $type = strtolower($v['type']) ;
      $selectLike = false ;
      $state = '' ;
      if (!empty($v['disabled']) && $v['disabled']) $state = ' disabled="dsiabled"' ;
      $o = '' ;
      $inline = '' ;
      if (!empty($v['inline'])) $inline = $v['inline'] ;
      $browse = '' ;
      switch ($type) {
      case 'separator':
        $this->printFormTableFooter('') ;
        $this->printFormTableHeader($v['title'], $v['subtitle'], $v['error'], 'clean', '') ;
        return ;
      case 'button' :
      case 'submit' :
        $state .= ' style="width:150px" ' ;
        break ;
      case 'checkbox' :
        if ($v['value']) $state .= ' checked="checked" ' ;
        if (!empty($v['ifFalseShow']) && is_array($v['ifFalseShow'])) {
          $state .= ' onchange="';
          foreach ($v['ifFalseShow'] as $toDisable) {
            $state .= 'document.getElementsByName(\''.$toDisable.'\').item(0).disabled=this.checked;' ;
          }
          $state .= '"' ;
        }
        break ;
      case 'textarea':
        $style = '' ;
        if (!empty($v['style'])) $style .= $v['style'] ;
        if (!empty($v['rows'])) $style .= " rows={$v['rows']}" ;
        else  $style .= " rows=10" ;
        if (!empty($v['cols'])) $style .= " cols={$v['cols']}" ;
        else  $style .= " cols=27" ;
        break ;
      case 'password' :
        break ;
      case 'hidden' :
        break ;
      case 'file' :
        break ;
      case 'dbselect' : // get the options with a db query
      case 'select' :
        if (!empty($v['options']) && is_array($v['options'])) {
          $style = '' ;
          if (!empty($v['style'])) $style = "style='{$v['style']}'" ;
          $selectLike = true ;
          break ; // only if options are already defined
        }
      case 'dbeditableselect' :
        if (!empty($v['table']) && !empty($v['column'])) {
          $db = $this->db ;
          $optArray = $db->getColData($v['table'], $v['column']) ;
          if (!empty($optArray)) {
            asort($optArray) ;
            $v['options'] = array_values($optArray) ;
          }
          else $v['options'] = array() ;
        }
        else break ; // only if table and column are not given
      case 'editableselect' :
        if (!empty($v['form']) && !empty($v['options'])) {
          // if we cannot parse it as a select, fall through to text.
          $o = ' selectBoxOptions="' ;
          $o .= implode(';', $v['options']) . '"' ;
          if (!empty($v['unique']) && $v['unique']) {
            $o .= sprintf(" onchange='document.%s.%s_autoload.value=1;document.%s.submit();'",
                  $v['form'],$v['form'],$v['form']);
            /* $browse = sprintf("<input class='button' type='submit' value='...' " .
                      "title='Load the information from the Database' name='%s_load'>",
                      $v['form']) ; */
            $browse = sprintf("<input type='hidden' name='%s_autoload'>",
                       $v['form']) ;
          }
          else {
            $o .= sprintf(" onchange='return ;'");
          }
          $type = 'text' ;
          $js = '<script type="text/javascript">createEditableSelect(document.forms["' .
            $v['form'] . '"].' . $k . ');</script>' ;
          break ;
        }
      case 'text' :
      default :
        $type = 'text' ;
      }
      $t = "   " ;

      if ($type == 'hidden')
        $hideRow = 'style="display:none;"' ;
      else
        $hideRow = '' ;

      $echo = sprintf("$t$t<tr $hideRow><td width=210>%s<img src='{$this->cwd}/help.png' " .
              "alt='[?]' style='float:right' " .
              'onmouseover="Tip(\'%s\', WIDTH, 230, TITLE, \'%s\', ' .
              'FIX, [this, 10, 5])" onmouseout="UnTip()">&nbsp;&nbsp;</td>' .
              "\n$t$t$t<td width='210px'>\n",
              $v['name'], htmlspecialchars($v['help']), strip_tags($v['name'])) ;
      if ($selectLike) {
        $echo .= sprintf("$t$t$t$t<select %s name='%s'>\n", $style, $k) ;
        foreach ($v['options'] as $o) {
          $state = '' ;
          if ($v['value'] == $o) $state = ' selected="selected"' ;
          $echo .= "$t$t$t$t$t<option $state value='$o'>$o</option>\n" ;
        }
        $echo .= "$t$t$t$t</select>\n" ;
      }
      else if ($type == 'textarea') {
        $echo .= sprintf("$t$t$t$t<textarea %s name='%s'>%s</textarea>\n" ,
                $style, $k, $v['value']) ;
      }
      else {
        $echo .= sprintf("$t$t$t$t<input %s type='%s' name='%s' size=35 value='%s' %s>%s\n",
                $state, $type, $k, $v['value'], $o, $browse.$inline) ;
      }
      $echo .= "$t$t$t</td>\n$t$t$t<td>$error $js</td></tr>\n" ;
      echo $echo ;
    }
    function showRows($rowSet) {
      echo "     <table border=0 cellspacing=0 cellpadding=2 class='clean'>\n" ;
      foreach ($rowSet as $k => $v) {
        $this->showOneRow($k, $rowSet[$k]) ;
      }
      echo "     </table>\n" ;
    }
    function printFormTableHeader($title, $subtitle,
      $errMsg='', $class="setup", $openRow="<tr><td colspan=3>") {
      $echo = sprintf("<!-- Print Form Table Header -->
      <table width='100%%' cellspacing='0' cellpadding='2' class='%s'>
      <tr class='title'><td colspan=3>%s</td></tr>
      <tr class='subtitle'><td colspan=3>%s</td></tr>%s\n",
              $class, $title, $subtitle, $openRow) ;
      echo $echo ;
      if (!empty($errMsg))
        printf('<span class="error"><b>%s</b></span>
    <tr><td colspan=2>' . "\n", $errMsg) ;
    }
    function printFormTableFooter($closeRow="</td></tr>") {
      printf("<!-- Print Form Table Footer --> $closeRow  </table>\n") ;
    }
    function renderForm($formName, $rowSet, $title, $subtitle, $buttonText,
            $errMsg='', $renderFormStart=true) {
      $script = $_SERVER['REQUEST_URI'] ;
      echo "<!-- Start of renderForm($formName) -->\n" ;
      $enc = '' ;
      if ($formName == 'products') $enc = 'enctype="multipart/form-data"' ;
      if ($renderFormStart && !empty($formName))
        printf('    <form action="%s" method="post" name="%s" %s>',
          $script, $formName, $enc) ;

      $this->printFormTableHeader($title, $subtitle, $errMsg) ;

      $this->showRows($rowSet) ;

      $this->printFormTableFooter();

      if (!empty($buttonText))
        printf('
    <div align="center">
    <input class="button" type="submit" value="%s" name="%s-submit">
    <input class="button" type="reset" name="%s-reset" value="Reset">
    </div>',  $buttonText, $formName, $formName);
      printf("</form><br />\n<!-- End of renderForm($formName) -->\n") ;
    }
    function renderDoubleForm($formName, $rowSet1, $title1, $subtitle1,
      $rowSet2, $title2, $subtitle2, $buttonText, $errMsg='') {
      $this->renderForm($formName, $rowSet1, $title1, $subtitle1, '', $errMsg) ;
      $this->renderForm($formName, $rowSet2, $title2, $subtitle2, $buttonText, $errMsg, false) ;
    }
    function loadMetaSetFromDB($rowSetName, &$rowSet) {
      $this->loadRowSetFromDB($rowSetName, $rowSet, $meta=true) ;
    }
    function loadRowSetFromDB($rowSetName, &$rowSet, $meta=false) {
      // don't load anything if already loaded
      if (!empty($_POST[$rowSetName.'_load'])) return ;
      if (!empty($_POST[$rowSetName.'_autoload'])) return ;
      $db = $this->db ;
      if ($meta) $rowSetVals = $db->getMetaData($rowSetName) ;
      else $rowSetVals = $db->getRowData($rowSetName) ;
      foreach ($rowSet as $k => $v) {
        if (!is_array($v)) break ;
        if (isset($rowSetVals[$k])) $rowSet[$k]['value'] = $rowSetVals[$k] ;
        $associatedKey = '' ;
        if (!empty($rowSet[$k]['hidden'])) $associatedKey = $rowSet[$k]['hidden'] ;
        if (!empty($associatedKey)) {
          $associatedVal = '' ;
          if (!empty($rowSetVals[$associatedKey]))
            $associatedVal = $rowSetVals[$associatedKey] ;
          else if (!empty($rowSet[$associatedKey]['value']))
            $associatedVal = $rowSet[$associatedKey]['value'] ;

          if (!$rowSet[$k]['value']) $type = 'text' ;
          else {
            if (empty($rowSet[$k]['reveal'])) $type = '' ;
            else $type = strtolower($rowSet[$k]['reveal']) ;
          }
          $help = $rowSet[$associatedKey]['help'] ;
          switch($type){
          case 'button':
            $rowSet[$k]['inline'] = sprintf("<input type='%s' value='Show' onmouseover=\"Tip('%s', WIDTH, 370, TITLE, '%s', FIX, [this, -10, 5])\" onmouseout=\"UnTip()\" onclick=\"window.prompt('Copy to clipboard: Ctrl/Cmd-C:', '%s')\">", $type, "Hidden value associated with this option is:<br /><code>$associatedKey => $associatedVal</code><br /><b><em>Click to reveal and copy to clipboard.</em></b><br />", "Associated Value", $associatedVal) ;
            break ;
          case 'text':
            $rowSet[$k]['inline'] = sprintf("<input type='%s' value='%s' onmouseover=\"Tip('%s', WIDTH, 250, TITLE, '%s', FIX, [this, -10, 5])\" onmouseout=\"UnTip()\" name='%s' id='%s'>", $type, $associatedVal, $help, "Associated Value", $associatedKey, $associatedKey) ;
          }
        }
      }
    }
    function uploadFile(&$rowSet, $fileName) {
      if (!empty($_FILES['file'])) {
        $randomName = '' ;
        $db = $this->db ;
        $optionsVals = $db->getRowData('options') ;
        $storage = $optionsVals['storage_location'] ;
        $name = $_FILES['file']['name'] ;
        if (!empty($name)) {
          $ext = end(explode('.', $name)) ;
          if ($optionsVals['random_file'])
            $randomName = $storage . '/' . formHelper::randString(24) . '.' . $ext ;
          else
            $randomName = $storage . '/' . $name ;
          $tmpName = $_FILES['file']['tmp_name'] ;
          $pwd = getcwd() ;
          $command = "cd $pwd && mkdir -p $storage && chmod 777 $storage" ;
          if (!is_dir($storage) || !is_writeable($storage)) {
            @exec($command) ;
          }
          $tip = sprintf("In order to move the file to the storage location, log on your server, and issue commands equivalent to:<br><code>$command.</code><br /><b>Click on [?] to copy the actual command.</b>");
          $title = "Error during file upload" ;
          if (!@move_uploaded_file($tmpName, $randomName))
            $rowSet['file']['warning'] = sprintf("<font color='red'>Error moving the file. Please ensure that the storage directory exists and is writeable. </font><span " . 'onmouseover="Tip(\'%s\', WIDTH, 335, TITLE, \'%s\', FIX, [this, -10, 5])" onmouseout="UnTip()" onclick="window.prompt(\'Copy to clipboard: Ctrl/Cmd-C:\', \'%s\')"' . ">[?]</span>", $tip, $title, $command) ;
          else {
            // ensure that nobody can run any php scripts in $storage
            $fpx = @fopen("$storage/.htaccess", 'w') ;
            if ($fpx) {
              fwrite($fpx, "Deny from All\n") ;
              fclose($fpx);
            }
            if (!empty($fileName)) @unlink($fileName) ; // remove the existing file
          }
        }
        else
          $rowSet['file']['warning'] = "Empty file to be uploaded?!" ;
        return $randomName ;
      }
    }
    static function randString($len, $chars = 'abcdefghijklmnopqrstuvwxyz0123456789') {
      $string = '';
      for ($i = 0; $i < $len; $i++) {
        $pos = rand(0, strlen($chars)-1);
        $string .= $chars{$pos};
      }
      return $string;
    }
    function isRowSetValid($rowSet) {
      $goodRows = true ;
      foreach ($rowSet as $k => $row) {
        if (is_array($row)) $goodRows = $goodRows && empty($row['error']) ;
      }
      return $goodRows ;
    }
    function handleMetaSubmit($formName, &$rowSet) {
      $this->handleSubmit($formName, $rowSet, $meta=true) ;
    }
    function handleSubmit($formName, &$rowSet, $meta=false) {
      // $formName == paypal, options or products
      $db = $this->db ;
      if (!empty($_POST[$formName.'-submit'])){
        $dbRow = array() ;
        foreach ($rowSet as $k => $row) {
          $valErr = array() ;
          if (isset($_POST[$k]))
            $valErr = $this->getValErr($rowSet[$k], $_POST[$k]) ;
          else if (!empty($row['type']) && $row['type'] == 'checkbox')
            $valErr['val'] = false ;
          if (isset($valErr['val'])) $dbRow[$k] = $valErr['val'] ;
          if (!empty($valErr['err'])) $rowSet[$k]['error'] = $valErr['err'] ;
        }
        if ($formName == 'options') {
          if ($dbRow['random_storage'])
            $dbRow['storage_location'] = formHelper::randString(32) ;
          else {
            if (empty($_POST['storage_location']))
              $dbRow['storage_location'] = 'storage' ;
            else
              $dbRow['storage_location'] = $_POST['storage_location'] ;
          }
        }
        if (!empty($rowSet['file'])) {
          // pull the current product filename from the DB
          $prod = $db->getData("products", "file",
                  array('product_code' => $dbRow['product_code'])) ;
          if (!empty($prod)) $fileName = $prod[0]['file'] ;
          else $fileName = '' ;
          $dbRow['file'] = $this->uploadFile($rowSet, $fileName) ;
          if (empty($dbRow['file'])) unset($dbRow['file']) ;
        }
        if ($this->isRowSetValid($rowSet)) {
          if ($meta) $db->putMetaData($formName, $dbRow) ;
          else $db->putRowData($formName, $dbRow) ;
          $this->html->setWarn("$formName info updated.") ;
        }
      }
      if (!empty($_POST[$formName.'_load']) || !empty($_POST[$formName.'_autoload'])){
        $toLoad = array() ;
        foreach ($rowSet as $k => $row) {
          if ($row['unique']) {
            $valErr = $this->getValErr($rowSet[$k], $_POST[$k]) ;
            if (!empty($valErr['err'])) $rowSet[$k]['error'] = $valErr['err'] ;
            $toLoad = $row ;
            $toLoad['value'] = $valErr['val'] ;
            $rowSet[$k]['warning'] = 'This product will be modified in your DB if you hit "Update Products" below' ;
            break ;
          }
        }
        if (!empty($toLoad)) {
          $table = $toLoad['table'] ;
          $column = $toLoad['column'] ;
          if (empty($db) || empty($table) || empty($column))
            $this->html->setErr('<br />Unique column requires DB details. [Dev error]') ;
          else
            if (empty($toLoad['value']))
              $this->html->setErr('<br />Need a value here to look up in the DB') ;
            else
              $dbRow = $db->getRowData($table, array($column => $toLoad['value'])) ;
          if (!empty($dbRow)) foreach ($rowSet as $k => $row) {
              if (is_array($row)) $rowSet[$k]['value'] = $dbRow[$k] ;
            }
          else { // new product code
            $rowSet[$k] = $toLoad ;
          }
        }
      }
      $formName = ucwords($formName) ;
      $rowSet['submitText'] = "Update $formName" ;
    }
  }
}
?>