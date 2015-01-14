var timeBased = {
  weekly: "#weeklyGet",
  monthly: "#monthlyGet",
  yearly: "#yearlyGet",
  daily: "#dailyGet"
};
function timeBasedClick(base, btn) {
  var plot;
  var _base = $("#" + base);
  var options = {
    lines: {
      show: true
    },
    bars: {
      show: false, barWidth: 10000000000, align: "center"
    },
    points: {
      show: true
    },
    selection: {
      mode: "x"
    },
    grid: {
      hoverable: true,
      clickable: true,
      backgroundColor: {colors: ["#fff", "#eee"]}
    },
    xaxis: {
      mode: "time"
    }
  };
  if (base === 'yearly') {
    options.lines.show = false;
    options.bars.show = true;
  }
  $.ajax({
    url: 'ajax/stats.php',
    data: {
      action: 'fetch',
      data: base
    },
    type: "GET",
    dataType: "json",
    success: function (series) {
      plot = $.plot(_base, [series], options);
    }
  });
  function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css({
      position: 'absolute',
      display: 'none',
      top: y + 5,
      left: x + 5,
      border: '1px solid #fdd',
      padding: '2px',
      'background-color': '#dfeffc',
      opacity: 0.80
    }).appendTo("body").fadeIn(200);
  }

  var previousPoint = null;
  $(_base).bind("plothover", function (event, pos, item) {
    $("#x").text(pos.x.toFixed(2));
    $("#y").text(pos.y.toFixed(2));

    if (item) {
      if (previousPoint != item.dataIndex) {
        previousPoint = item.dataIndex;

        $("#tooltip").remove();
        var x = item.datapoint[0].toFixed(0),
                y = item.datapoint[1].toFixed(0);

        var d = new Date(parseInt(x));
        var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"]
        var prefix = '';
        if (item.series.label === 'daily')
          prefix = d.getFullYear() + '-' + months[d.getMonth()] + '-' + d.getDate();
        if (item.series.label === 'weekly')
          prefix = 'Week of ' + d.getFullYear() + '-' + months[d.getMonth()] + '-' + d.getDate();
        if (item.series.label === 'monthly')
          prefix = d.getFullYear() + '-' + months[d.getMonth()];
        if (item.series.label === 'yearly')
          prefix = d.getFullYear();
        showTooltip(item.pageX, item.pageY, prefix + " : $" + y);
      }
    }
    else {
      $("#tooltip").remove();
      previousPoint = null;
    }
  });
  $(_base).bind("plotselected", function (event, ranges) {
    // do the zooming
    $.each(plot.getXAxes(), function (_, axis) {
      var opts = axis.options;
      opts.min = ranges.xaxis.from;
      opts.max = ranges.xaxis.to;
    });
    plot.setupGrid();
    plot.draw();
    plot.clearSelection();
    $(btn).fadeIn();
  });
}
$.each(timeBased, function (base, btn) {
  $(btn).click(function () {
    timeBasedClick(base, btn);
    $(this).fadeOut();
  });
});
$(document).ready(function () {
  $.each(timeBased, function (base, btn) {
    timeBasedClick(base, btn);
    $(btn).fadeOut();
  });
});

//pie charts
var volumeBased = {
  productSales: "productSales",
  productRevenue: "productRevenue",
  country: "country"
};

function volumeBasedClick(base) {
  var options = {
    series: {
      pie: {
        show: true,
        radius: 1,
        label: {
          show: true,
          radius: 2 / 3,
          threshold: 0.1
        }
      }
    },
    grid: {
      hoverable: true,
      clickable: true
    },
    legend: {
      show: false
    }
  };
  $.ajax({
    url: 'ajax/stats.php',
    data: {
      action: 'fetch',
      data: base
    },
    type: "GET",
    dataType: "json",
    success: function (series) {
      $.plot("#" + volumeBased[base], series.data, options);
    }
  });
  function pieHover(event, pos, obj) {
    if (!obj)
      return;
    var percent = parseFloat(obj.series.percent).toFixed(2);
    var count = parseFloat(obj.series.data[0][1]);
    if (base === "productRevenue") {
      count = "$" + count.toFixed(2);
    }
    var label = obj.series.label;
    if (!label) label = "Unknown";
    $("#hover-" + base).html('<span style="font-weight: bold; color: ' + obj.series.color + '">' + label + ': ' + count + ' (' + percent + '%)</span>');
  }
  $("#" + base).bind("plothover", pieHover);
}

$(document).ready(function () {
  $.each(volumeBased, function (base, target) {
    volumeBasedClick(base);
  });
});
