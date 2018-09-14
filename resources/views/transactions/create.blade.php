@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3><?= !empty($transaction->name) ?
            $transaction->name :
            'Add new transaction'?></h3>
        {!! Form::open(['action' => 'TransactionsController@store']) !!}
        <table class="table table-striped table-hover">

          <tbody>

          <tr>
            <th>{!! Form::label('name', 'Name: ') !!}</th>
            <td>{!! Form::text('name', (!empty($transaction->name) ? $transaction->name : ''), [
    'class'=>'form-control']) !!}</td>
          </tr>

          <?php
          $now = \Carbon\Carbon::now(new DateTimeZone('Europe/London'));
          $now = $now->format('Y-m-d H:i:s');
          ?>

          <tr>
            <th>{!! Form::label('payment_date', 'Payment Date: ') !!}</th>
            <td>{!! Form::text('payment_date', (!empty($transaction->payment_date) ? $transaction->payment_date : $now), [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('type', 'Transaction Type: ') !!}</th>
            <td>{!! Form::select('type', ['credit' => 'Credit', 'debit'=>'Debit'], (!empty($transaction->type) ? $transaction->type : 'debit'), [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('account_id', 'Account: ') !!}</th>
            <td>{!! Form::select('account_id', $account_list, (!empty($transaction->account_id) ? $transaction->account_id : ''), [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('category_id', 'Category: ') !!}</th>
            <td>{!! Form::select('category_id', [0=>'No Category'], (!empty($transaction->category_id) ? $transaction->category_id : 0), [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('amount', 'Amount: ') !!}</th>
            <td>{!! Form::text('amount', (!empty($transaction->amount) ? $transaction->amount : ''), [
    'class'=>'form-control']) !!}</td>
          </tr>

          @if (empty($transaction->name))
            <?php $account_list[0] = ''; ?>
            <tr>
              <th>{!! Form::label('transfer', 'Transfer: ') !!}</th>
              <td>{!! Form::select('transfer', $account_list, '0', [
    'class'=>'form-control']) !!}</td>
            </tr>
            {!! Form::hidden('confirmed', '1') !!}

          @else

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

            {!! Form::hidden('transaction_id', $transaction->id) !!}
            {!! Form::hidden('transaction_category_id', $transaction->category_id, ['id' => 'default_category_id']) !!}

          @endif

          <tr>
            <td colspan="2">
              {!! Form::submit( (!empty($transaction->name) ? 'Update':'Add').' transaction', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>

          </tbody>

        </table>
      {!! Form::close() !!}
    </div>
  </div>
  </div>

  <script type="application/javascript">

    $(function () {

      var initial_account_id = $('#account_id').val();
      get_categories(initial_account_id);

      $('body').on('change', '#account_id', get_categories);
      $('body').on('change', '#transfer', function () {
        var transfer_id = $(this).val();
        if (transfer_id != 0) {
          $('#category_id').html('<option value="0">No Category</option>');
        } else {
          var account_id = $('#account_id').val();
          get_categories(account_id);
        }
      });

      function get_categories(e) {
        $.ajax
        (
          {
            url: '/ajax/get_categories/' + this.value,
            dataType: 'html',
            type: 'GET',
            async: true,
            success: function (html) {
              $('#category_id').html(html);
              var category_id = $('#default_category_id').val();
              if (category_id > 0) {
                $('#category_id').val(category_id);
                $('#default_category_id').val(0);
              }
            }
          }
        );
      }
    });
  </script>

@endsection