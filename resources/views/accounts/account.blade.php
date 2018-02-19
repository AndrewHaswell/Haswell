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
            <th width="35%">Date</th>
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
              <td class="account_month future" colspan="3"><?=$this_month?></td>
              <td align="right" class="account_month future"><?=number_format($future_balance, 2, '.', ',')?></td>
            </tr>
            <?php
            }
            $previous_month = $this_month;
            ?>
            <tr>
              <th scope="row">{{ $schedule->name }}</th>
              <td>{{ date('D jS F Y',strtotime($schedule->payment_date)) }}</td>
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
            <th width="35%">Date</th>
            <th width="15%">Amount</th>
            <th width="15%">Balance</th>
          </tr>
          </thead>
          <tbody>
          <?php $previous_month = '';?>
          @foreach ($transactions as $transaction)
            <?php
            $this_month = date('F Y', strtotime($transaction->payment_date));
            if (empty($previous_month) || $this_month != $previous_month)
            {
            ?>
            <tr>
              <td class="account_month" colspan="3"><?=$this_month?></td>
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
              <?php if ($transaction->type == 'debit') {
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


@endsection