@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">

        <h2>Next {{$next_limit}} days:</h2>

        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th width="28%">Name</th>
            <th width="15%">Category</th>
            <th width="27%">Date</th>
            <th width="12%" style="text-align: right !important;">Amount</th>
            <th width="18%">Account</th>
          </tr>
          </thead>

          <tbody>
          @foreach ($schedules as $schedule)

            <?php
            if ($schedule->type == 'debit')
              $schedule->amount = $schedule->amount * -1;
            ?>

            <tr>
              <td>{{$schedule->name}}</td>
              <td><?= !empty($schedule->category_id) && !empty($category_list[$schedule->category_id]) ?
                  $category_list[$schedule->category_id] :
                  '-'?></td>
              <td>{{date('D jS F Y',strtotime($schedule->payment_date))}}</td>
              <td align="right">{{number_format($schedule->amount, 2, '.',',')}}</td>
              <td>{{!empty($account_list[$schedule->account_id]) ? $account_list[$schedule->account_id] : ''}}</td>
            </tr>

          @endforeach
          </tbody>
        </table>

        <h2>Last {{$previous_limit}} days:</h2>

        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th width="28%">Name</th>
            <th width="15%">Category</th>
            <th width="27%">Date</th>
            <th width="12%" style="text-align: right !important;">Amount</th>
            <th width="18%">Account</th>
          </tr>
          </thead>

          <tbody>
          <?php
          $by_category = [];
          $category_list[0] = 'Other';
          ?>
          @foreach ($transactions as $transaction)

            <?php
            if ($transaction->type == 'debit') {
              if (!empty($by_category[$category_list[$transaction->category_id]])) {
                $by_category[$category_list[$transaction->category_id]] += (float)$transaction->amount;
              } else {
                $by_category[$category_list[$transaction->category_id]] = (float)$transaction->amount;
              }
              $transaction->amount = $transaction->amount * -1;
            }
            ?>

            <tr>
              <td><a
                    href="/transactions/{{$transaction->id}}">{{ $transaction->name }}</a><?= !$transaction->confirmed ?
                  '&nbsp;&nbsp;&nbsp;<img src="http://www.rccanada.ca/rccforum/images/rccskin/misc/cross.png"/>' :
                  ''?></td>
              <td><?= !empty($transaction->category_id) && !empty($category_list[$transaction->category_id]) ?
                  $category_list[$transaction->category_id] :
                  '-'?></td>
              <td>{{date('D jS F Y',strtotime($transaction->payment_date))}}</td>
              <td align="right">{{number_format($transaction->amount, 2, '.',',')}}</td>
              <td>{{!empty($account_list[$transaction->account_id]) ? $account_list[$transaction->account_id] : ''}}</td>

            </tr>

          @endforeach
          </tbody>
        </table>
        <div id="piechart" style="width: 100%; height: 500px;"></div>
      </div>
    </div>
  </div>

  <?php arsort($by_category);?>

  <script type="text/javascript">
    google.charts.load('current', {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Category');
      data.addColumn('number', 'Amount');
      data.addRows([
        <?php
          $row_count = 0;
          foreach ($by_category as $key => $value) {
              if (strtolower(substr($key, 0, 3)) != 'dd ') {
                  echo "['" . $key . "'," . $value . "],";
                  if (strtolower($key) == 'alcohol') {
                      $alcohol = $row_count;
                  }
                  if (strtolower($key) == 'takeaway') {
                      $takeaway = $row_count;
                  }
                  $row_count++;
              }
          }
        ?>
      ]);
      var formatter = new google.visualization.NumberFormat({
        prefix: '£'
      });
      formatter.format(data, 1); // Apply formatter to second column
      var options = {
        pieSliceText: 'label',
        title: 'Last {{$previous_limit}} Days',
        is3D: true,
        <?php
        if (isset($alcohol) || isset($takeaway)) {
          $slices = [];
          $slices[] = 'slices: {';
          if (isset($alcohol))
          $slices[] = $alcohol . ': {offset: 0.1},';
          if (isset($takeaway))
          $slices[] = $takeaway . ': {offset: 0.1},';
          $slices[] = '},';
          echo implode("\n", $slices);
        }
        ?>
      };
      var chart = new google.visualization.PieChart(document.getElementById('piechart'));
      chart.draw(data, options);
    }
  </script>

@endsection
