@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Update Shopping List</h3>
        {!! Form::open(['action' => 'IngredientsController@store']) !!}
        <table class="table table-striped table-hover">

          <tbody>

          <tr>
            <th>{!! Form::label('name', 'Name: ') !!}</th>
            <td>{!! Form::text('name', '', [
    'class'=>'form-control']) !!}</td>
          </tr>
          <tr>
            <th>{!! Form::label('price', 'Price: ') !!}</th>
            <td>{!! Form::text('price', '', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('shop', 'Shop: ') !!}</th>
            <td>{!! Form::text('shop', '', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <th>{!! Form::label('category', 'Category: ') !!}</th>
            <td>{!! Form::text('category', '', [
    'class'=>'form-control']) !!}</td>
          </tr>

          <tr>
            <td colspan="2">
              {!! Form::submit( 'Add ingredient', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>

          </tbody>

        </table>
      {!! Form::close() !!}
    </div>
  </div>
  </div>
@endsection