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
            <th>Complete</th>
          </tr>
          </thead>
          <tbody>

          @foreach ($todo_list as $todo)

            <tr id="row_{!! $todo->id !!}">
              <td title="{!! $todo->description !!}">{!! $todo->title !!}</td>
              <td>{!! $todo->time !!}</td>
              <td>{!! $todo->priority !!}</td>
              <td><input type="checkbox" class="todo_done" id="{!! $todo->id !!}"/></td>
            </tr>

          @endforeach
          </tbody>
        </table>
      </tr>
    </div>
  </div>

  <script>

    $(document.body).on('click', '.todo_done', function () {

      var id = $(this).attr('id');

      $.ajax
      (
        {
          url: '/ajax/update_todo',
          dataType: 'json',
          type: 'POST',
          async: true,
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id
          },
          success: function (data) {
            $('#row_' + id).css("text-decoration", "line-through");
          }
        }
      );

    });

  </script>

@endsection