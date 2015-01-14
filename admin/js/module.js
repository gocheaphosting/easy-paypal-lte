$(document).ready(function () {
  var file;
  function ajaxUpload(_file) {
    var data = new FormData();
    data.append('file', _file);
    data.append('module', currentModule);
    $.ajax({
      url: 'ajax/module.php',
      type: 'POST',
      dataType: 'json',
      data: data,
      processData: false,
      contentType: false,
      success: function (response) {
        $("#loading").hide();
        showSuccess(response.success);
        flashWarning(response.warning);
        $("#installDiv").html(response.info).fadeIn();
        setTimeout(function () {
          bootbox.alert("<p>The module needs to be <a href='../modules/" + currentModule + "/ez-configure.php' target='_blank'>configured</a> to complete the " + currentModule + " module installation and update, in order to populate or alter options.</p>");
        }, 5000);
      },
      error: function (a) {
        $("#loading").hide();
        $("#installDiv").fadeIn();
        showError(a.responseText);
      }
    });
  }
  $("#reinstall").click(function () {
    $("#loading").fadeIn();
    $.ajax({
      url: 'ajax/module.php',
      type: 'POST',
      dataType: 'json',
      data: {action: 'install', module: currentModule},
      success: function (response) {
        $("#loading").hide();
        if (response.success)
          showSuccess(response.success);
        if (response.warning)
          flashWarning(response.warning);
        if (response.info)
          $("#installDiv").html(response.info).fadeIn();
      },
      error: function (a) {
        $("#loading").hide();
        $("#installDiv").fadeIn();
        showError(a.responseText);
      }
    });
  });
  $("#resetStatus").click(function () {
    $.ajax({
      url: 'ajax/module.php',
      type: 'POST',
      dataType: 'json',
      data: {action: 'reset', module: currentModule},
      success: function () {
        window.location.reload(true);
      },
      error: function (a) {
        showError(a.responseText);
      }
    });
  });
  $("#markConfigured").click(function () {
    $.ajax({
      url: 'ajax/module.php',
      type: 'POST',
      dataType: 'json',
      data: {action: 'mark', module: currentModule},
      success: function () {
        window.location.reload(true);
      },
      error: function (a) {
        showError(a.responseText);
      }
    });
  });
  $("#fileinput").on('change', function (event) {
    file = event.target.files[0];
    if (file) {
      bootbox.confirm("<p>Are you sure you want to upload <code>" + file.name + "</code> to add " + currentModule + " to your <b>EZ PayPal</b> installaion? The module installation process is designed to be safe, but it may add files and create new database tables.</p><p class='red'> <em>Keeping a backup of your files and database is highly recommended.</em></p><p>Before updating, consider backing up:<ul><li><a href='ajax/update.php?backup'>Download a full backup</a> of your current app folder.</li><li><a href='ajax/db-tools.php?action=sqldump&gzip=true'>Download a compressed dump</a> of your database.</li></ul>Note that these backups may take a couple of minites to complete. Please be patient. Once done, be sure to check the downloaded files to verify that they are usable and complete.</p>", function (result) {
        if (result) {
          $("#installDiv").hide();
          $("#loading").fadeIn();
          ajaxUpload(file);
        }
        else {
          flashWarning("File not uploaded. Browse again to upload the " + currentModule + " file to your <b>Ads EZ</b> installation.");
        }
      });
    }
  });
  $('#install').click(function (e) {
    e.preventDefault();
    $("#installButtons").hide();
    $("#installDiv").fadeIn();
  });

});
