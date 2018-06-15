@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <h3>{{$transaction->name}}</h3>
        {!! Form::open(['action' => 'TransactionsController@store']) !!}
        <table class="table table-striped table-hover">

          <tbody>

          <tr>
            <th>{!! Form::label('name', 'Name: ') !!}</th>
            <td>{!! Form::text('name', $transaction->name, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('payment_date', 'Payment Date: ') !!}</th>
            <td>{!! Form::text('payment_date', $transaction->payment_date, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('type', 'Transaction Type: ') !!}</th>
            <td>{!! Form::select('type', ['credit' => 'Credit', 'debit'=>'Debit'], $transaction->type, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('amount', 'Amount: ') !!}</th>
            <td>{!! Form::text('amount', $transaction->amount, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('account_id', 'Account: ') !!}</th>
            <td>{!! Form::select('account_id', $account_list, $transaction->account_id, [
    'class'=>'form-control']) !!}</td>
          </tr>
          <tr>
            <th>{!! Form::label('confirmed', 'Confirmed: ') !!}</th>
            <td>{!! Form::select('confirmed', ['1' => 'Yes', '0'=>'No'], $transaction->confirmed, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <!--Form::checkbox('name', 'value', true);-->
          <tr>
            <th>{!! Form::label('delete', 'Tick box to delete: ') !!}</th>
            <td>{!! Form::checkbox('delete', 'delete', false, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <td colspan="2">
              {!! Form::hidden('transaction_id', $transaction->id) !!}
              {!! Form::submit( 'Update transaction', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>

          </tbody>

        </table>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
@endsection