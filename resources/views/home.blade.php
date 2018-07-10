@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">

        <h2>Next {{$limit}} days:</h2>

        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th width="28%">Name</th>
            <th width="18%">Category</th>
            <th width="18%">Date</th>
            <th width="18%" style="text-align: right !important;">Amount</th>
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
              <td><?= !empty($schedule->category_id) && !empty($category_list[$schedule->category_id]) ? $category_list[$schedule->category_id]:'-'?></td>
              <td>{{date('D jS F Y',strtotime($schedule->payment_date))}}</td>
              <td align="right">{{number_format($schedule->amount, 2, '.',',')}}</td>
              <td>{{$account_list[$schedule->account_id]}}</td>
            </tr>

          @endforeach
          </tbody>
        </table>

        <h2>Last {{$limit}} days:</h2>

        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th width="28%">Name</th>
            <th width="18%">Category</th>
            <th width="18%">Date</th>
            <th width="18%" style="text-align: right !important;">Amount</th>
            <th width="18%">Account</th>
          </tr>
          </thead>

          <tbody>
          @foreach ($transactions as $transaction)

            <?php
            if ($transaction->type == 'debit')
              $transaction->amount = $transaction->amount * -1;
            ?>

            <tr>
              <td><a
                    href="/transactions/{{$transaction->id}}">{{ $transaction->name }}</a><?= !$transaction->confirmed
                  ? '&nbsp;&nbsp;&nbsp;<img src="http://www.rccanada.ca/rccforum/images/rccskin/misc/cross.png"/>'
                  : ''?></td>
              <td><?= !empty($transaction->category_id) && !empty($category_list[$transaction->category_id]) ? $category_list[$transaction->category_id]:'-'?></td>
              <td>{{date('D jS F Y',strtotime($transaction->payment_date))}}</td>
              <td align="right">{{number_format($transaction->amount, 2, '.',',')}}</td>
              <td>{{$account_list[$transaction->account_id]}}</td>

            </tr>

          @endforeach
          </tbody>
        </table>

      </div>
    </div>
  </div>

@endsection
