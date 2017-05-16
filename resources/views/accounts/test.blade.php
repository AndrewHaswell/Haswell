@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <h3>Accounts</h3>

        <?php $total = 0; $subtotal = [];?>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th>Account Name</th>
            <th>Account Type</th>
            <th>Upcoming</th>
            <th style="text-align: right !important;">Account Balance</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($accounts as $account)
            <tr>
              <th scope="row"><a href="/accounts/{{$account->id}}">{{ $account->name }}</a></th>
              <td>{{ ucwords($account->type) }}</td>
              <td>
                <select class="future_month" id="future_month_{{$account->id}}">
                  <option value="0">-</option>
                  @foreach ($months as $month_id => $month_name)
                    <option value="{{$month_id}}">{{$month_name}}</option>
                  @endforeach
                </select>
              </td>
              <td align="right">{{ number_format($account->balance, 2, '.', '') }}</td>
              <?php
              $total += $account->balance;
              if (!empty($subtotal[$account->type])) {
                $subtotal[$account->type] += $account->balance;
              } else {
                $subtotal[$account->type] = $account->balance;
              }

              ?>
            </tr>
          @endforeach
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
          @foreach ($subtotal as $type => $balance)
            <tr>
              <th>{{ucwords($type)}}</th>
              <td colspan="2">&nbsp;</td>
              <td align="right">{{ number_format($balance, 2, '.', '') }}</td>
            </tr>
          @endforeach
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr>
            <th>Total Assets</th>
            <td colspan="2">&nbsp;</td>
            <td align="right">{{ number_format($total, 2, '.', '') }}</td>
          </tr>
          </tbody>


        </table>
      </div>
    </div>
  </div>
@endsection