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
            <th>Interval</th>
            <th>End</th>
            <th>Type</th>
            <th>Amount</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($payments as $payment)

            <?php
            $this_first_letter = strtoupper(substr($payment->name, 0, 1));
            ?>

            @if ($this_first_letter != $first_letter)
              <tr>
                <td colspan="6"><h2>{{$this_first_letter}}</h2></td>
              </tr>
            @endif

            <?php
            $first_letter = $this_first_letter;
            ?>

            <tr>
              <th><a href="/payments/{{$payment->id}}">{{ $payment->name }}</a></th>
              <td>{{ date('j M',strtotime($payment->start_date)) }}</td>
              <td>{{ ucwords($payment->interval) }}</td>
              <?php if ($payment->end_date != '') { ?>
              <td>{{ date('j M y',strtotime($payment->end_date)) }}</td>
              <?php } else { ?>
              <td>-</td>
              <?php }?>
              <td>{{ ucwords($payment->type) }}</td>
              <td>{{ $payment->amount }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection