@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">

        <?php $balance = $account->balance; ?>
        <h3>{{$account->name}} (&pound;{{number_format($balance, 2, '.',',')}})</h3>
        <h3 style="font-size: 14pt;background-color: #444;padding: 8px; color: #fff">{{date('F Y',strtotime($date))}}</h3>


        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Balance</th>
          </tr>
          </thead>
          <tbody>

          <?php $previous_month = ''; ?>

          @foreach ($schedules as $schedule)
            <?php
            $this_month = date('F Y', strtotime($schedule->payment_date));
            if (empty($previous_month) || $this_month != $previous_month)
            {
            ?>
            <tr>
              <td class="account_month" colspan="3"><?=$this_month?></td>
              <td class="account_month" align="right"><?=number_format($balance, 2, '.', ',')?></td>
            </tr>
            <?php
            }
            $previous_month = $this_month;
            ?>
            <tr>
              <th scope="row"><a href="/schedules/{{$schedule->id}}">{{ $schedule->name }}</a></th>
              <td>{{ date('D jS F Y',strtotime($schedule->payment_date)) }}</td>
              <?php if ($schedule->type == 'debit') {
                $schedule->amount *= -1;
              }?>
              <td align="right">{{ number_format($schedule->amount, 2, '.',',') }}</td>
              <td align="right">{{ number_format($balance, 2, '.',',') }}</td>
              <?php $balance -= $schedule->amount; ?>
            </tr>
          @endforeach

        </table>
      </div>
    </div>
  </div>
@endsection