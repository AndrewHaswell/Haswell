@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">

        <h2>Next {{$limit}} transactions:</h2>

        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th width="40%">Name</th>
            <th width="20%">Date</th>
            <th width="20%" style="text-align: right !important;">Amount</th>
            <th width="20%">Account</th>
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
              <td>{{date('D jS F Y',strtotime($schedule->payment_date))}}</td>
              <td align="right">{{number_format($schedule->amount, 2, '.',',')}}</td>
              <td>{{$account_list[$schedule->account_id]}}</td>
            </tr>

          @endforeach
          </tbody>
        </table>

        <h2>Recent transactions:</h2>

        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th width="40%">Name</th>
            <th width="20%">Date</th>
            <th width="20%" style="text-align: right !important;">Amount</th>
            <th width="20%">Account</th>
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
