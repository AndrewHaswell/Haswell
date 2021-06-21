@extends('layouts.app')
@section('content')

  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <h2>Budget</h2>

        <?php $total = 0;?>

        @foreach($main_categories as $main_category)
          <table class="table table-striped table-hover">
            <thead class="thead-default">
            <tr>
              <th colspan="2">{{$main_category->name}}</th>
            </tr>
            </thead>
            <tbody>

            </tr>

            <?php $sub_categories = $main_category->BudgetSub()->get();?>

            <?php $sub_total = 0; ?>

            @foreach ($sub_categories as $sub_category)

                <?php
                if (isset($cat_updates[$sub_category->id]) && $cat_updates[$sub_category->id] > $sub_category->balance) {
                    $sub_category->balance = $cat_updates[$sub_category->id];
                }

              <tr>
                <td>{{$sub_category->name}}</td>
                <td align="right">
                  @if ($sub_category->balance > 0)
                  &pound;{{number_format($sub_category->balance,2)}}
                  @else
                    -
                  @endif</td>
              </tr>
              <?php $total += (float)$sub_category->balance;?>
              <?php $sub_total += (float)$sub_category->balance;?>
            @endforeach
            <tr>
              <td><strong>Sub-Total</strong></td>
              <td align="right"><strong>&pound;{{number_format($sub_total,2)}}</strong></td>
            </tr>
            </tbody>
          </table>
        @endforeach
        <table class="table table-striped table-hover">
          <tbody>
          <tr>
            <td>TOTAL:</td>
            <td align="right"><strong>&pound;{{number_format($total,2)}}</strong></td>
          </tr>
          <tr>
            <td>INCOMING:</td>
            <td align="right"><strong>&pound;{{number_format($incoming,2)}}</strong></td>
          </tr>
          <tr>
            <td>OVERFLOW:</td>
            <td align="right"><strong>&pound;{{number_format($incoming-$total,2)}}</strong></td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection