@extends('layouts.app')
@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <h2>Payments</h2>
        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Amount</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($payments as $payment)
            <tr>
              <td><a href="#">{{ $payment->name }}</a></td>
              <td>{{ $payment->start_date }} -> {{ $payment->interval }} -> {{ $payment->end_date }}</td>
              <td>{{ $payment->type }}</td>
              <td>{{ $payment->amount }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection