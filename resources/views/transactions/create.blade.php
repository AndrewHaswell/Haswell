@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Add new transaction</h3>
        {!! Form::open(['action' => 'TransactionsController@store']) !!}
        <table class="table table-striped table-hover">

          <tbody>

          <tr>
            <th>{!! Form::label('name', 'Name: ') !!}</th>
            <td>{!! Form::text('name', '', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <?php
          $now = \Carbon\Carbon::now(new DateTimeZone('Europe/London'));
          $now = $now->format('Y-m-d H:i:s');
          ?>

          <tr>
            <th>{!! Form::label('payment_date', 'Payment Date: ') !!}</th>
            <td>{!! Form::text('payment_date', $now, [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('type', 'Transaction Type: ') !!}</th>
            <td>{!! Form::select('type', ['credit' => 'Credit', 'debit'=>'Debit'], 'debit', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('account_id', 'Account: ') !!}</th>
            <td>{!! Form::select('account_id', $account_list, '', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('amount', 'Amount: ') !!}</th>
            <td>{!! Form::text('amount', '', [
    'class'=>'form-control']) !!}</td>
          </tr>
          <?php $account_list[0] = ''; ?>
          <tr>
            <th>{!! Form::label('transfer', 'Transfer: ') !!}</th>
            <td>{!! Form::select('transfer', $account_list, '0', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <td colspan="2">
              {!! Form::hidden('confirmed', '1') !!}
              {!! Form::submit( 'Add transaction', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>

          </tbody>

        </table>
      {!! Form::close() !!}
    </div>
  </div>
  </div>
@endsection