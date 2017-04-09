@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <h3>Accounts</h3>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th>Account Name</th>
            <th>Account Type</th>
            <th>Account Balance</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($accounts as $account)
            <tr>
              <th scope="row"><a href="/accounts/{{$account->id}}">{{ $account->name }}</a></th>
              <td>{{ ucwords($account->type) }}</td>
              <td>{{ number_format($account->balance, 2, '.', '') }}</td>
            </tr>
          @endforeach
          </tbody>


        </table>
      </div>
    </div>
  </div>
@endsection