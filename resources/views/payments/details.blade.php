@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>{{$payment->name}}</h3>
        {!! Form::open(['action' => 'PaymentsController@store']) !!}
        <table class="table table-striped table-hover">

          <tbody>

          <tr>
            <th>{!! Form::label('name', 'Name: ') !!}</th>
            <td>{!! Form::text('name', $payment->name, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('amount', 'Amount: ') !!}</th>
            <td>{!! Form::text('amount', $payment->amount, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('start_date', 'Start Date: ') !!}</th>
            <td>{!! Form::text('start_date', $payment->start_date, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('interval', 'Interval: ') !!}</th>
            <td>{!! Form::text('interval', $payment->interval, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('end_date', 'End Date: ') !!}</th>
            <td>{!! Form::text('end_date', $payment->end_date, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('weekend', 'Weekend Type: ') !!}</th>
            <td>{!! Form::select('weekend', ['before' => 'Before', 'after'=>'After', 'none'=>'None'], $payment->type, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('type', 'Payment Type: ') !!}</th>
            <td>{!! Form::select('type', ['credit' => 'Credit', 'debit'=>'Debit'], $payment->type, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('account_id', 'Account: ') !!}</th>
            <td>{!! Form::select('account_id', $account_list, $payment->account_id, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <td colspan="2">
              {!! Form::hidden('payment_id', $payment->id) !!}
              {!! Form::submit( 'Update payment', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>

          </tbody>

        </table>
      {!! Form::close() !!}
    </div>
  </div>
  </div>
@endsection