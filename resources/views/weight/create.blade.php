@extends('layouts.app')
@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Log Weight</h3>
        {!! Form::open(['action' => 'WeightController@store']) !!}
        <table class="table table-striped table-hover">
          <tbody>
          <tr>
            <td>Weight (in Kg): <input type="number" step="0.1" name="weight"/>&nbsp;</td>
          </tr>
          <tr>
            <td>Body Fat %: <input type="number" step="0.1" name="bodyfat"/>&nbsp;</td>
          </tr>
          <tr>
            <td>
              {!! Form::submit( 'Add Weight', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>
          </tbody>
        </table>
        {!! Form::close() !!}
      </tr>
    </div>
  </div>
  @endsection