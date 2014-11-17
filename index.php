<?php

define('PYWWS_OUTPUT_DIR', '/home/robin/sites/info.riviera.org.uk/htdocs/pywws');

if ( isset($_GET['period']) && $_GET['period'] != "" ){
  $period = $_GET['period'];
}else{
  $period = "24h";
}

include_once "header.php";

$intervals = array( 
                    "24h" => "Last 24 hrs",
                    "1w"  => "Last Week",
                    "1m"  => "Last Month",
                 );

?>

<div class="row">
	<div class="col-md-2">
		<div class="well well-sm">
            <ul class="nav nav-pills nav-stacked">
<?php foreach ($intervals as $key => $value) { ?>
        <li <?php if ( $key == $period ) {?> class="active"<?php } ?>><a href="?period=<?php echo $key;?>"><?php echo $value; ?></a></li>
<?php } ?>
      	</ul>
      </div>
	</div>

	<div class="col-md-10">
		<h3><?php echo $intervals[$period];?></h3>
    
        <ul class="nav nav-tabs" data-tabs="tabs">
          <li><a href="#info" data-toggle="tab">Info</a></li>
          <li class="active"><a href="#temp" data-toggle="tab">Temp</a></li>
          <li><a href="#wind" data-toggle="tab">Wind</a></li>
          <li><a href="#rain" data-toggle="tab">Rain</a></li>
          <li><a href="#pressure" data-toggle="tab">Pressure</a></li>
          <li><a href="#windrose" data-toggle="tab">Rose</a></li>
          <li><a href="#pywws" data-toggle="tab">Pywws</a></li>
        </ul>
    
        <div id="my-tab-content" class="tab-content">
            <div class="tab-pane fade" id="info">
                <h4>Data from the <?php echo $intervals[$period];?></h4>
                <?php include "pywws/$period.html"; ?>
            </div>
            <div class="tab-pane active fade in" id="temp"><div id="temp-container"></div></div>
            <div class="tab-pane fade" id="wind"><div id="wind-container"></div></div>
            <div class="tab-pane fade" id="rain"><div id="rain-container"></div></div>
            <div class="tab-pane fade" id="pressure"><div id="pressure-container"></div></div>
            <div class="tab-pane fade" id="windrose"><img src="pywws/rose_<?php echo $period;?>.png" class="img-responsive"></div>
            <div class="tab-pane fade" id="pywws"><img src="pywws/<?php echo $period;?>.png" class="img img-responsive"></div>
        </div>
	</div><!-- ./ col-md-10 -->

<script type="text/javascript">
jQuery(document).ready( 

function ($) {
  
  $('#tabs').tab();

  // This is a little hacky but...
  // 
  // So when the non active tabs are rendered they seem to have 0 width, then
  // highcharts comes along and tries to draw a graph in the 0 width div which 
  // it refuses to do, so it picks a minimum size (600px iirc). As a hack, I 
  // take the width of the active tab, save that and then force all graphs to 
  // be that width. And yes I know attaching a var to the window object isn't 
  // very nice.
  window.chartWidth = document.getElementById("temp-container").clientWidth;

  // fetchData();

  showTempChart();
  showWindChart();
  showRainChart();
  showPressureChart();

});
</script>


<script type="text/javascript">

function showTempChart(){

  var options = {
    chart: {
      renderTo: 'temp-container',
      type: 'spline',
      zoomType: 'x',
      width: window.chartWidth
    },
    title: { text: '' },
    xAxis: {
      type: "datetime",
      dateTimeLabelFormats: { hour: "%H. %M" }
    },
    yAxis: [{ // Primary yAxis
      title: {
        useHTML: true,
        text: "&deg; C"
      },
      max: 35,
      min: -10
    },{ // Secondary yAxis
      title: {
        useHTML: true,
        text: "%"
      },
      max: 100,
      min: 0,
      opposite: true
    }],
    tooltip: {
      shared: true,
      useHTML: true,
      borderColor: '#888',
      backgroundColor: '#eee',
      formatter: function () {
        var s = '<b>' + Highcharts.dateFormat("%a %e %b %H:%M", this.x) + '</b><br/>';

        $.each(this.points, function(i, point) {
            if ( point.series.name == "Relative Humidity" ){
              s +=  point.series.name + ':  ' + point.y.toFixed(1) + ' %RH<br/>';
            }else{
              s +=  point.series.name + ':  ' + point.y.toFixed(1) + ' &deg;C<br/>';
            }
        });

        return s;
      }
    },
    plotOptions: { },
    series: []
  };

  $.getJSON('pywws/<?php echo $period;?>.json', function(json){

    var outTempdata  = new Array();
    var windChillTempdata  = new Array();
    var HumidityOutdata = new Array();
    var dewPointdata = new Array();


    $.each( json.data, function() {
      $.each ( this, function(tstmp, val) {
        if ( typeof tstmp != 'undefined' ){
          // console.log(val["TempOut"]);
          outTempdata.push( Array(Date.parse(tstmp), this["TempOut"]) );  
          windChillTempdata.push( Array(Date.parse(tstmp), this["WindChill"]) );  
          HumidityOutdata.push( Array(Date.parse(tstmp), this["HumidityOut"]) );  
          dewPointdata.push( Array(Date.parse(tstmp), this["DewPoint"]) );  
        }
      }); // .each this
    }); // .each json

    var outTempOptions = {
      name: "Outside Temp",
      data: outTempdata,
      lineWidth: 1,
      marker: { radius: 1 }, 
      yAxis: 0,
      color: '#FF0000',
    }

    var windChillTempOptions = {
      name: "Wind Chill",
      data: windChillTempdata,
      lineWidth: 1,
      marker: { radius: 1 }, 
      yAxis: 0,
      color: '#DF73FF',
      visible: false
    }

    var dewPointOptions = { 
      name: "Dew Point",
      data: dewPointdata,
      lineWidth: 1,
      marker: { radius: 1 }, 
      yAxis: 0,
      color: '#3399FF',
      visible: false
    };

    var HumidityOutOptions = { 
      name: "Relative Humidity",
      data: HumidityOutdata,
      lineWidth: 1,
      marker: { radius: 1 }, 
      yAxis: 1,
      color: '#00CC00',
    };

    options.series.push(outTempOptions);
    options.series.push(windChillTempOptions);
    options.series.push(dewPointOptions);
    options.series.push(HumidityOutOptions);

    // Prints a vertical yellow bar showing daylight hours
    options.xAxis.plotBands = []
    for (var i = 31; i >= 0; i--) {
        var d = new Date();
        d.setHours(0,0,0,0);
        d.setDate(d.getDate()-i);
        var sunrise = d.getTime()+computeSunrise(dayOfYear(), true);
        var sunset = d.getTime()+computeSunrise(dayOfYear(), false);
        options.xAxis.plotBands.push({
            from: sunrise,
            to: sunset,
            color: '#FCFFC5'
        });
    };

    var chart = new Highcharts.Chart(options);
  }); // .getJSON
}

function showWindChart(){

  var options = {
    chart: {
      renderTo: 'wind-container',
      type: 'column',
      zoomType: 'x',
      width: window.chartWidth
    },
    title: { text: '' },
    xAxis: {
      type: "datetime",
      dateTimeLabelFormats: { hour: "%H. %M" }
    },
    yAxis: {
      title: {
        useHTML: true,
        text: "mph"
      },
      min: 0
    },
    tooltip: {
      shared: true,
      useHTML: true,
      borderColor: '#888',
      backgroundColor: '#eee',
      formatter: function () {
        var s = '<b>' + Highcharts.dateFormat("%a %e %b %H:%M", this.x) + '</b><br/>';

        $.each(this.points, function(i, point) {
            s +=  point.series.name + ':  ' + point.y.toFixed(1) + ' mph<br/>';
        });

        return s;
      }
    },    
    plotOptions: {
      column: {
         stacking: null,
      },
      series: {
        grouping: false,
        groupPadding: 0
      }
    },
    series: []
  };

  $.getJSON('pywws/<?php echo $period;?>.json', function(json){

    var WindAvgdata = new Array();
    var WindGustdata = new Array();

    $.each( json.data, function() {
      $.each ( this, function(tstmp, val) {
        if ( typeof tstmp != 'undefined' ){
          // console.log(val["TempOut"]);
          WindAvgdata.push( Array(Date.parse(tstmp), this["WindAvg"]) ); 
          WindGustdata.push( Array(Date.parse(tstmp), this["WindGust"]) );
        }
      }); // .each this
    }); // .each json

    var WingAvgoptions = {
      name: "Average",
      data: WindAvgdata,
      lineWidth: 1,
      marker: { radius: 1 },
      color: '#3399FF',

    };
    var WingGustoptions = {
      name: "Gust",
      data: WindGustdata,
      lineWidth: 1,
      marker: { radius: 1 },
      color: '#CC33FF',
    };

    options.series.push(WingGustoptions);
    options.series.push(WingAvgoptions);

    // Prints a vertical yellow bar showing daylight hours
    options.xAxis.plotBands = []
    for (var i = 31; i >= 0; i--) {
        var d = new Date();
        d.setHours(0,0,0,0);
        d.setDate(d.getDate()-i);
        var sunrise = d.getTime()+computeSunrise(dayOfYear(), true);
        var sunset = d.getTime()+computeSunrise(dayOfYear(), false);
        options.xAxis.plotBands.push({
            from: sunrise,
            to: sunset,
            color: '#FCFFC5'
        });
    };

    var chart = new Highcharts.Chart(options);
  }); // .getJSON
}

function showRainChart(){

  var options = {
    chart: {
      renderTo: 'rain-container',
      zoomType: 'x',
      width: window.chartWidth
    },
    title: { text: '' },
    xAxis: {
      type: "datetime",
      dateTimeLabelFormats: { hour: "%H. %M" }
    },
    yAxis: [{
      title: {
        useHTML: true,
        text: "mm"
      },
      min: 0
    },{
      title: {
      useHTML: true,
      text: "mm"
      },
      min: 0,
      opposite: true
    }],
    tooltip: {
      useHTML: true,
      borderColor: '#888',
      backgroundColor: '#eee',

      formatter: function () {
        return "<b>" + this.series.name + "</b><br/>" + Highcharts.dateFormat("%a %e %b %H:%M", this.x) + ": " + this.y.toFixed(1) + " mm"
      }
    },
    plotOptions: {
      series: {
        groupPadding: 0
      }
    },
    series: []
  };

  $.getJSON('pywws/<?php echo $period;?>.json', function(json){

    var data = new Array();
    var Totaldata = new Array();
    var total = 0;

   $.each( json.data, function() {
      $.each ( this, function(tstmp, val) {
        if ( typeof tstmp != 'undefined' ){

          if ( this["Rain"] > 0 ){
            total += this["Rain"];
          }
          // console.log(total);
          data.push( Array(Date.parse(tstmp), this["Rain"]) ); 
          Totaldata.push( Array(Date.parse(tstmp), total ) ); 
        }
      }); // .each this
    }); // .each json



    var seriesOptions = {
      name: "Rainfall (mm)",
      data: data,
      lineWidth: 1,
      marker: { radius: 1 },
      type: 'column',
      yAxis: 0,
      color: '#66FFFF',
    };

    var TotalOptions = {
      name: "Total",
      data: Totaldata,
      lineWidth: 1,
      marker: { radius: 1 },
      type: 'spline',
      yAxis: 1,
      color: '#3399FF',
    };

    options.series.push(seriesOptions);
    options.series.push(TotalOptions);

    // Prints a vertical yellow bar showing daylight hours
    options.xAxis.plotBands = []
    for (var i = 31; i >= 0; i--) {
        var d = new Date();
        d.setHours(0,0,0,0);
        d.setDate(d.getDate()-i);
        var sunrise = d.getTime()+computeSunrise(dayOfYear(), true);
        var sunset = d.getTime()+computeSunrise(dayOfYear(), false);
        options.xAxis.plotBands.push({
            from: sunrise,
            to: sunset,
            color: '#FCFFC5'
        });
    };

    var chart = new Highcharts.Chart(options);
  }); // .getJSON
}

function showPressureChart(){

  var options = {
    chart: {
      renderTo: 'pressure-container',
      zoomType: 'x',
      width: window.chartWidth
    },
    title: { text: '' },
    xAxis: {
      type: "datetime",
      dateTimeLabelFormats: { hour: "%H. %M" }
    },
    yAxis: {
      title: {
        useHTML: true,
        text: "Pressure (hpa)"
      },
      min: 960
    },
    tooltip: {
      useHTML: true,
      borderColor: '#888',
      backgroundColor: '#eee',
      formatter: function () {
        return "<b>" + this.series.name + "</b><br/>" + Highcharts.dateFormat("%a %e %b %H:%M", this.x) + ": " + this.y.toFixed(1) + " hpa"
      }
    },
    series: []
  };

  $.getJSON('pywws/<?php echo $period;?>.json', function(json){

    var data = new Array();
    var Totaldata = new Array();
    var total = 0;

   $.each( json.data, function() {
      $.each ( this, function(tstmp, val) {
        if ( typeof tstmp != 'undefined' ){
          data.push( Array(Date.parse(tstmp), this["AbsPressure"]) ); 
        }
      }); // .each this
    }); // .each json



    var seriesOptions = {
      name: "Pressure (hpa)",
      data: data,
      lineWidth: 1,
      marker: { radius: 1 },
      type: 'line',
      yAxis: 0,
      color: '#00CC00',
    };

    options.series.push(seriesOptions);

    // Prints a vertical yellow bar showing daylight hours
    options.xAxis.plotBands = []
    for (var i = 31; i >= 0; i--) {
        var d = new Date();
        d.setHours(0,0,0,0);
        d.setDate(d.getDate()-i);
        var sunrise = d.getTime()+computeSunrise(dayOfYear(), true);
        var sunset = d.getTime()+computeSunrise(dayOfYear(), false);
        options.xAxis.plotBands.push({
            from: sunrise,
            to: sunset,
            color: '#FCFFC5'
        });
    };

    // Prints a vertical yellow bar showing daylight hours
    options.xAxis.plotBands = []
    for (var i = 31; i >= 0; i--) {
        var d = new Date();
        d.setHours(0,0,0,0);
        d.setDate(d.getDate()-i);
        var sunrise = d.getTime()+computeSunrise(dayOfYear(), true);
        var sunset = d.getTime()+computeSunrise(dayOfYear(), false);
        options.xAxis.plotBands.push({
            from: sunrise,
            to: sunset,
            color: '#FCFFC5'
        });
    };

    var chart = new Highcharts.Chart(options);
  }); // .getJSON
}


</script>

<?php
include_once "footer.php";
?>
