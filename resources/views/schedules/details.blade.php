@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>{{$schedule->name}}</h3>
        {!! Form::open(['action' => 'SchedulesController@store']) !!}
        <table class="table table-striped table-hover">

          <tbody>

          <tr>
            <th>{!! Form::label('name', 'Name: ') !!}</th>
            <td>{!! Form::text('name', $schedule->name, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('payment_date', 'Payment Date: ') !!}</th>
            <td>{!! Form::text('payment_date', $schedule->payment_date, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('type', 'Transaction Type: ') !!}</th>
            <td>{!! Form::select('type', ['credit' => 'Credit', 'debit'=>'Debit'], $schedule->type, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('amount', 'Amount: ') !!}</th>
            <td>{!! Form::text('amount', $schedule->amount, [
    'class'=>'form-control']) !!}</td>
          </tr>
          <tr>
            <th>{!! Form::label('account_id', 'Account: ') !!}</th>
            <td>{!! Form::select('account_id', $account_list, $schedule->account_id, [
    'class'=>'form-control']) !!}</td>
          </tr>
          <tr>
            <th>{!! Form::label('confirmed', 'Confirmed: ') !!}</th>
            <td>{!! Form::select('confirmed', ['1' => 'Yes', '0'=>'No'], $schedule->confirmed, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <td colspan="2">
              {!! Form::hidden('schedule_id', $schedule->id) !!}
              {!! Form::submit( 'Update schedule', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>

          </tbody>

        </table>
      {!! Form::close() !!}
    </div>
  </div>
  </div>
@endsection