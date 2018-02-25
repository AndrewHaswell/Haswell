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
            @if (!$empty || round($account->balance,2) != 0)
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

                if ($account->balance < 0 && $account->type == 'cash') {
                  if (isset($overdrawn)) {
                    $overdrawn += $account->balance;
                  } else {
                    $overdrawn = $account->balance;
                  }
                }

                if (!empty($subtotal[$account->type])) {
                  $subtotal[$account->type] += $account->balance;
                } else {
                  $subtotal[$account->type] = $account->balance;
                }

                ?>
              </tr>
            @endif
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
          @if (!$hidden && !empty($overdrawn))
            <tr>
              <td colspan="4">&nbsp;</td>
            </tr>
            <tr style="color: red">
              <th>Overdrawn</th>
              <td colspan="2">&nbsp;</td>
              <td align="right">{{ number_format($overdrawn, 2, '.', '') }}</td>
            </tr>
          @endif
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr>
            <th>Total Assets</th>
            <td colspan="2">&nbsp;</td>
            <td align="right">{{ number_format($total, 2, '.', '') }}</td>
          </tr>
          @if ($hidden)
            <?php
            $dmp = \App\Models\Payment::where('name','=','DMP')->get();
            $dmp_amount = $dmp[0]->amount;

            $mam_loan = \App\Models\Payment::where('name','=','Mam')->get();
            $dmp_amount += $mam_loan[0]->amount;

            $repayment = abs($total);
            $months = ceil($repayment / $dmp_amount);
            $year = date('Y');
            $month = date('m');
            $date = new DateTime($year . '-' . $month . '-01');
            $date->add(new DateInterval('P' . $months . 'M'));
            ?>
            <tr>
              <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
              <th>Final Repayment Date</th>
              <td colspan="2">&nbsp;</td>
              <td align="right">{{ $date->format('M Y') }}</td>
            </tr>
          @endif
          </tbody>

        </table>
      </div>
    </div>
  </div>
@endsection