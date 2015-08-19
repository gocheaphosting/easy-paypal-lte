<?php

require_once 'DbTable.php';

class OptionTable extends DbTable {

  function __construct($options) {
    $this->columns = array();

    $col = new DbColumn('name');
    $col->width = "20%";
    $col->minWidth = "200px";
    $col->noEdit = false;
    $col->heading = "Option";
    $this->columns[$col->dbCol] = $col;

    $col = new DbColumn('value');
    $col->width = "70%";
    $col->noEdit = false;
    $col->heading = "Value";
    $this->columns[$col->dbCol] = $col;

    $col = new DbColumn('help');
    $col->width = "10%";
    $col->minWidth = "50px";
    $col->noEdit = true;
    $col->heading = "Help";
    $this->columns[$col->dbCol] = $col;

    $this->table = 'options_meta';
    global $db;
    $optionsDB = $db->getMetaData($this->table);
    foreach ($options as $pk => $o) {
      $options[$pk]['pk'] = $pk;
      if (isset($optionsDB[$pk])) {
        $options[$pk]['value'] = $optionsDB[$pk];
      }
    }
    $this->rows = $options;
    $this->ajaxHandler = 'ajax/options.php';
    $this->needCreate = false;
    $this->needEdit = false;
    $this->useDataTable = false;
    $this->showActions = false;
  }

  function __destruct() {

  }

  function OptionTable($options) {
    if (version_compare(PHP_VERSION, "5.0.0", "<")) {
      $this->__construct($options);
      register_shutdown_function(array($this, "__destruct"));
    }
  }

  // It cannot be private because when DbTable::rennder() calls it, the scope
  // would be DbTable, not OptionTable
  function _printRow($row, $create = false) {
    echo EZ::renderRow($row['pk'], $row);
  }

  function _printJS() {
    if (self::$renderedJS) {
      return;
    }
    self::$renderedJS = true;
    ?>
    <script>
      var xeditHandler = 'ajax/options.php';
      $(document).ready(function () {
        setTimeout(function () {
          $(".xeditReload").editable('option', 'success', function () {
            window.location.reload(true);
          });
        }, 1500);
      });
      $("#switchTheme").click(function () {
        window.location.reload(true);
      });
      var clicked;
      $('body').on('click', '.imageList', function (e) {
        e.preventDefault();
        clicked = $(this);
        $.ajax({url: 'ajax/assets-upload.php',
          data: {
            action: 'show'
          },
          success: function (images) {
            bootbox.alert({title: "Click on an image to insert it", message: images});
          },
          error: function (a) {
            flashError(a.responseText);
          }
        });
      });
      $('body').on('click', ".insertImage", function (e) {
        e.preventDefault();
        var url = $(this).children('img').attr('src');
        var xedit = clicked.siblings('a');
        xedit.editable('setValue', url);
        xedit.editable('submit');
        $('.bootbox').modal('hide');
      });
      $('body').on('click', ".reveal", function (e) {
        e.preventDefault();
        bootbox.alert($(this).attr('data-value'));
      });
    </script>
    <?php

  }

  // AJAX handler
  static function handle($table, $column = "", $title = "", $columns = array()) {
    if (!empty($_REQUEST['action'])) {
      $action = $_REQUEST['action'];
    }
    else {
      $action = '';
    }
    unset($_REQUEST['file']);
    switch ($action) {
      case 'update':
        static::update($table, true);
        break;
      default:
        break;
    }
    exit;
  }

}
