@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">

        <?php $balance = $account->balance; ?>
        <h2>{{$account->name}}</h2>
        <p class="current_balance">Current Balance: <strong>&pound;{{number_format($balance, 2, '.',',')}}</strong>
          @if ($account->confirmed_balance*100 !=  $balance*100) (&pound;{{number_format($account->confirmed_balance, 2, '.',',')}})@endif</p>
        <p class="upcoming_link">&nbsp;&nbsp;<a href="#" id="show_upcoming">Show Upcoming</a></p>

        <table id="upcoming" class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th width="35%">Name</th>
            <th width="20%">Date</th>
            <th width="15%">Category</th>
            <th width="15%">Amount</th>
            <th width="15%">Balance</th>
          </tr>
          </thead>
          <tbody>

          <?php $previous_month = '';?>
          @foreach ($schedules as $schedule)
            <?php
            $this_month = date('F Y', strtotime($schedule->payment_date));
            if (empty($previous_month) || $this_month != $previous_month)
            {
            ?>
            <tr>
              <td class="account_month future" colspan="4"><?=$this_month?></td>
              <td align="right" class="account_month future"><?=number_format($future_balance, 2, '.', ',')?></td>
            </tr>
            <?php
            }
            $previous_month = $this_month;
            ?>
            <tr>
              <th scope="row">{{ $schedule->name }}</th>
              <td>{{ date('D jS F Y',strtotime($schedule->payment_date)) }}</td>
              <td><?= !empty($schedule->category_id) && !empty($category_list[$schedule->category_id]) ? $category_list[$schedule->category_id]:'-'?></td>
              <?php if ($schedule->type == 'debit') {
                $schedule->amount *= -1;
              }?>
              <td align="right">{{ number_format($schedule->amount, 2, '.',',') }}</td>
              <td align="right">{{ number_format($future_balance, 2, '.',',') }}</td>
              <?php $future_balance -= $schedule->amount; ?>
            </tr>

          @endforeach
          </tbody>
        </table>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th width="35%">Name</th>
            <th width="20%">Date</th>
            <th width="15%">Category</th>
            <th width="15%">Amount</th>
            <th width="15%">Balance</th>
          </tr>
          </thead>
          <tbody>
          <?php
          $actual_month = date('F Y');
          $previous_month = '';
          $by_category = [];
          $category_list[0] = 'Other';
          ?>
          @foreach ($transactions as $transaction)
            <?php
            $this_month = date('F Y', strtotime($transaction->payment_date));
            if (empty($previous_month) || $this_month != $previous_month)
            {
            if (!empty($previous_month) && $previous_month == $actual_month){
              echo '<tr><td colspan="5"><div id="piechart" style="width: 100%; height: 380px;"></div></td></tr>';
            }
            ?>
            <tr>
              <td class="account_month" colspan="4"><?=$this_month?></td>
              <td align="right" class="account_month"><?=number_format($balance, 2, '.', ',')?></td>
            </tr>
            <?php
            }
            $previous_month = $this_month;
            ?>
            <tr>
              <th scope="row"><a
                    href="/transactions/{{$transaction->id}}">{{ $transaction->name }}<?= !$transaction->confirmed
                    ? '&nbsp;&nbsp;&nbsp;<img src="http://www.rccanada.ca/rccforum/images/rccskin/misc/cross.png"/>'
                    : ''?></a></th>
              <td>{{ date('D jS F Y',strtotime($transaction->payment_date)) }}</td>
              <td><?= !empty($transaction->category_id) && !empty($category_list[$transaction->category_id]) ? $category_list[$transaction->category_id]:'-'?></td>
              <?php if ($transaction->type == 'debit') {
                if ($this_month == $actual_month && ($transaction->transfer == false)) {
                  if (!empty($by_category[$category_list[$transaction->category_id]])) {
                    $by_category[$category_list[$transaction->category_id]] += (float)$transaction->amount;
                  } else {
                    $by_category[$category_list[$transaction->category_id]] = (float)$transaction->amount;
                  }
                }
                $transaction->amount *= -1;
              }?>
              <td align="right">{{ number_format($transaction->amount, 2, '.',',') }}</td>
              <td align="right">{{ number_format($balance, 2, '.',',') }}</td>
              <?php $balance -= $transaction->amount; ?>
            </tr>
          @endforeach
          </tbody>
        </table>
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
          echo "['" . $key . "'," . $value . "],";
          if (strtolower($key) == 'alcohol') {
            $alcohol = $row_count;
          }
          if (strtolower($key) == 'takeaway') {
            $takeaway = $row_count;
          }
          $row_count++;
        }
        ?>
      ]);
      var formatter = new google.visualization.NumberFormat({
        prefix: '£'
      });
      formatter.format(data, 1); // Apply formatter to second column
      var options = {
        pieSliceText: 'label',
        title: '{{$actual_month}}',
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