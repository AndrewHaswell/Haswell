@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">


        <h2>Next {{$limit}} transactions:</h2>

        <table class="table table-striped table-hover">
          <thead>
          <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Account</th>
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
      </div>
    </div>
  </div>

@endsection
