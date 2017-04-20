@extends('layouts.app')
@section('content')
  <?php
  $first_letter = 'z';
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <h2>Payments</h2>
        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th>Name</th>
            <th>Start</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Account</th>
            <th>Transfer Account</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($payments as $payment)

            <?php
            $this_first_letter = strtoupper(substr($payment->name, 0, 1));
            ?>

            @if ($this_first_letter != $first_letter)
              <tr class="success">
                <td colspan="6"><h4>{{$this_first_letter}}</h4></td>
              </tr>
            @endif

            <?php
            $first_letter = $this_first_letter;

            $start_date = $payment->start_date . ' - ' . $payment->interval;

            if (empty($payment->end_date)) {
              if (strpos($payment->interval, 'year') !== false) {
                $start_date = 'Annually - ' . date('jS M', strtotime($payment->start_date));
              } else if (strpos($payment->interval, 'month') !== false) {
                $start_date = date('jS', strtotime($payment->start_date)) . ' of the month';
              } else if (strpos($payment->interval, 'week') !== false) {
                $start_date = 'Every ' . date('l', strtotime($payment->start_date));
              } else if (strpos($payment->interval, 'days') !== false) {
                $start_date = 'Every ' . $payment->interval . ' from ' . date('jS M y', strtotime($payment->start_date));
              }
            } else {

              $begin = new DateTime($payment->start_date);
              $end = new DateTime();
              $end->setTimestamp(strtotime((string)$payment->end_date));
              $interval = DateInterval::createFromDateString($payment->interval);
              $period = new DatePeriod($begin, $interval, $end);
              $period_count = iterator_count($period);

              if (strpos($payment->interval, 'month') !== false) {
                $start_date = $period_count . ' monthly payment' . ($period_count > 1
                    ? 's'
                    : '') . ' from ' . date('jS M y', strtotime($payment->start_date));
              }
            }
            $transfer_account = '-';
            if (!empty($payment->transfer_account_id)) {
              $transfer_account = $account_list[$payment->transfer_account_id];
              $payment->type = 'transfer';
            }

            ?>

            <tr>
              <th><a href="/payments/{{$payment->id}}">{{ $payment->name }}</a></th>
              <td>{{ $start_date }}</td>
              <td>{{ ucwords($payment->type) }}</td>
              <td align="right">{{ $payment->amount }}</td>
              <td>{{ $account_list[$payment->account_id] }}</td>
              <td>{{ $transfer_account }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection