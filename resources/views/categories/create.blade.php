@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Add new category</h3>
        {!! Form::open(['action' => 'CategoryController@store']) !!}
        <table class="table table-striped table-hover">

          <tbody>

          <tr>
            <th>{!! Form::label('title', 'Title: ') !!}</th>
            <td>{!! Form::text('title', '', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('account_id', 'Account: ') !!}</th>
            <td>{!! Form::select('account_id', $account_list, '', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <td colspan="2">
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