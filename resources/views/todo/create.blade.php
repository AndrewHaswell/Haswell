@extends('layouts.app')

@section('content')
  <div class="container">
    <tr class="row">
    <tr class="col-md-10 col-md-offset-1">
      <h3>New Todo Item</h3>

      @if (count($errors) > 0)
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
              <p>{{ $error }}</p>
            @endforeach
        </div>
      @endif

      {!! Form::open(['action' => 'TodoController@store']) !!}
      <table class="table table-striped table-hover">
        <?php
        $now = \Carbon\Carbon::now(new DateTimeZone('Europe/London'));
        $now = $now->format('Y-m-d H:i:s');
        ?>
        <tbody>

        <tr>
          <th>{!! Form::label('title', 'Title: ') !!}</th>
          <td>{!! Form::text('title', '', [
    'class'=>'form-control']) !!}</td>
        </tr>

        <tr>
          <th>{!! Form::label('description', 'Description: ') !!}</th>
          <td>{!! Form::text('description', '', [
    'class'=>'form-control']) !!}</td>
        </tr>

        <tr>
          <th>{!! Form::label('time', 'Time (in hours): ') !!}</th>
          <td>{!! Form::number('time', '', [
    'class'=>'form-control','step' => '0.01']) !!}</td>
        </tr>

        <tr>
          <td>{!! Form::label('date', 'Scheduled Date: ') !!}</td>
          <td>{!! Form::text('scheduled_time', null, ['id'=>'datepicker',
            'class'=>'form-control']) !!}</td>
        </tr>

        <tr>
          <th>{!! Form::label('priority', 'Priority: ') !!}</th>
          <td> {!!  Form::selectRange('priority', 1, 20, 10, ['class'=>'form-control']) !!}
        </tr>

        <tr>
          <td colspan="2">
            {!! Form::hidden('added_time', $now) !!}
            {!! Form::hidden('complete', 'N') !!}
            {!! Form::submit( 'Add Todo Item', ['class' => 'btn btn-primary form-control']) !!}
          </td>
        </tr>

        </tbody>

      </table>
    {!! Form::close() !!}
  </div>
  </div>
  </div>

  <script>

  </script>
@endsection