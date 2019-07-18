@extends('layouts.app')
@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Current Weight Data</h3>
        <table class="table table-striped table-hover">
          <tbody>
          <tr>
            <td><strong>Name</strong></td>
            <td colspan="2">{{$name}}</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>Height</strong></td>
            <td><strong>Weight</strong></td>
          </tr>
          <tr>
            <td><strong>Metric</strong></td>
            <td>{{$height_in_m}}m</td>
            <td>{{$weight_in_kg}}kg</td>
          </tr>
          <tr>
            <td><strong>Imperial</strong></td>
            <td>{{$height_imperial}}</td>
            <td>{{$weight_imperial}}</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>Result</strong></td>
            <td><strong>Description</strong></td>
          </tr>
          <tr>
            <td><strong>BMI</strong></td>
            <td>{{$bmi}}</td>
            <td>{{$bmi_desc}}</td>
          </tr>
          <tr>
            <td><strong>Body Fat</strong></td>
            <td>{{$bodyfat}}%</td>
            <td>{{$bodyfat_desc}}</td>
          </tr>
          </tbody>
        </table>
      </tr>
    </div>
    <div class="row">
      <div id="curve_chart" style="width: 100%; height: 500px"></div>
    </div>
  </div>
  <script type="text/javascript">
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable({!!$chart_data!!});

      var options = {
        title: 'Weight',
        legend: {position: 'top'},
        colors: ['red', 'grey', 'grey', 'grey'],
        series: {
          0: {lineWidth: 3, lineDashStyle: [15, 3], pointSize: 6},
          1: {lineWidth: 1, lineDashStyle: [6, 3]},
          2: {lineWidth: 1, lineDashStyle: [6, 3]},
          3: {lineWidth: 1, lineDashStyle: [6, 3]}
        },
        hAxis: {
          slantedText: true, slantedTextAngle: 70
        }
      };

      var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

      chart.draw(data, options);
    }
  </script>
@endsection