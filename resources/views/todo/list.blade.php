@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Todo List</h3>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th>Title</th>
            <th>Time</th>
            <th>Priority</th>
          </tr>
          </thead>
          <tbody>

          @foreach ($todo_list as $todo)

            <tr>
              <td title="{!! $todo->description !!}">{!! $todo->title !!}</td>
              <td>{!! $todo->time !!}</td>
              <td>{!! $todo->priority !!}</td>
            </tr>

          @endforeach
          </tbody>
        </table>
      </tr>
    </div>
  </div>

@endsection