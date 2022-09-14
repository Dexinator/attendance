<?php
include('header.php');

?>
<!-- Styles -->
<style>
  #chartdiv {
    width: 100%;
    height: 500px;
    max-width: 100%
  }
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<!-- Chart code -->


<div class="container" style="margin-top:30px">
  <div class="card">
    <div class="card-header"><b>Attendance Chart</b></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <label>Día de inicio</label>
          <div class="field">
            <input type="text" id="selected_dates" >
            <div id="radio_period">
              <label for="PMensual">Mensual</label>
              <input id="PMensual" type="radio" name="periodo" value="Mensual" onchange="monthgraph(start_date,end_date);">
              <label for="PDiario">Diario</label>
              <input id="PDiario" type="radio" name="periodo" value="Diario" onchange="monthgraph(start_date,end_date);">
            </div>
          </div>

        </table>
      </div>
      <div id="attendance_pie_chart" style="width: 100%; height: 400px;">


        <div id="chartdiv"></div>


      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <tr>
            <th>Student Name</th>
            <th>Attendance Status</th>
          </tr>
          Salida
        </table></div>
      </div>
    </div>
  </div>






  <script>
    function addElement () {
  // crea un nuevo div
  // y añade contenido
  var newDiv = document.createElement("div");
  var newContent = document.createTextNode("Hola!¿Qué tal?");
  newDiv.appendChild(newContent); //añade texto al div creado.

  // añade el elemento creado y su contenido al DOM
  var currentDiv = document.getElementById("div1");
  document.body.insertBefore(newDiv, currentDiv);
}



var chart
var start_date;
var end_date;
flatpickr('#selected_dates', {
  "mode":"range",
  "locale": "es",
  "dateFormat": "d-m-Y",
  onChange: function(dates) {
    if (dates.length == 2) {
      start_date = dates[0].valueOf();
      end_date = dates[1].valueOf();
      console.log(start_date);
      console.log(end_date);

            // interact with selected dates here
          }
        }
      }
      );
var perc_asistencias;
var asistencia;
var faltas;
var leyendas;
function monthgraph(start_date,end_date){
  asistencia = [];
  faltas = [];
  leyendas= []
  var periodicidad= $('input[name=periodo]:checked', '#radio_period').val();
  console.log(periodicidad);
  $.ajax({
    type: "GET",
    url: "obtain_graphs_data.php?start_date="+start_date+"&end_date="+end_date+"&periodicidad="+periodicidad,   
    dataType: 'JSON',            
    success: function(data){

      console.log(data);
      var data = JSON.stringify(data);
      var obj = JSON.parse(data);
      for(var i in obj){
        asistencia.push(obj[i]['asistencia']);
        faltas.push(obj[i]['faltas']);
        leyendas.push(obj[i]['Month']);
      }
      console.log(asistencia);





    }
  }
  )
  var root = am5.Root.new("chartdiv");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
root.setThemes([
  am5themes_Animated.new(root)
  ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/xy-chart/
chart = root.container.children.push(am5xy.XYChart.new(root, {
  panX: true,
  panY: true,
  wheelX: "panX",
  wheelY: "zoomX",
  pinchZoomX:true
}));


// Add cursor
// https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
  behavior: "none"
}));
cursor.lineY.set("visible", false);


// Generate random data
var date = new Date();
date.setHours(0, 0, 0, 0);
var value = 100;

function generateData() {
  value = Math.round((Math.random() * 10 - 5) + value);
  am5.time.add(date, "day", 1);
  return {
    date: date.getTime(),
    value: value
  };
}

function generateDatas(count) {
  var data = [];
  for (var i = 0; i < count; ++i) {
    data.push(generateData());
  }
  return data;
}


// Create axes
// https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
var xAxis = chart.xAxes.push(am5xy.DateAxis.new(root, {
  maxDeviation: 0.2,
  baseInterval: {
    timeUnit: "month",
    count: 1
  },
  renderer: am5xy.AxisRendererX.new(root, {}),
  tooltip: am5.Tooltip.new(root, {})
}));

var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  renderer: am5xy.AxisRendererY.new(root, {})
}));


// Add series
// https://www.amcharts.com/docs/v5/charts/xy-chart/series/
var series = chart.series.push(am5xy.LineSeries.new(root, {
  name: "Series",
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "value",
  valueXField: "date",
  tooltip: am5.Tooltip.new(root, {
    labelText: "{valueY}"
  })
}));


// Add scrollbar
// https://www.amcharts.com/docs/v5/charts/xy-chart/scrollbars/
chart.set("scrollbarX", am5.Scrollbar.new(root, {
  orientation: "horizontal"
}));


// Set data
var data = generateDatas(1200);
series.data.setAll(data);


// Make stuff animate on load
// https://www.amcharts.com/docs/v5/concepts/animations/
series.appear(1000);
chart.appear(1000, 100);



  
}
</script>
</body>


</html>
