@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Todo List</h3>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th width="70%">Title</th>
            <th width="10%">Time</th>
            <th width="10%">Priority</th>
            <th width="10%">Complete</th>
          </tr>
          </thead>
          <tbody>

          @foreach ($scheduled_list as $todo)

            <?php
            $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', (string)$todo->scheduled_time, 'Europe/London');
            ?>

            <tr style="color: red" id="row_{!! $todo->id !!}">
              <td title="{!! $todo->description !!}">{!! $todo->title !!} ({!! $date->format('jS M Y') !!})</td>
              <td>{!! $todo->time !!}</td>
              <td>-</td>
              <td><input type="checkbox" class="todo_done" id="1_{!! $todo->id !!}"/></td>
            </tr>

          @endforeach
          @foreach ($todo_list as $todo)

            <tr id="row_{!! $todo->id !!}">
              <td title="{!! $todo->description !!}">{!! $todo->title !!}</td>
              <td>{!! $todo->time !!}</td>
              <td>{!! $todo->priority !!}</td>
              <td><input type="checkbox" class="todo_done" id="1_{!! $todo->id !!}"/></td>
            </tr>

          @endforeach
          </tbody>
        </table>
      </tr>
    </div>

    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Recently Complete List</h3>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th width="90%">Title</th>
            <th width="10%">Complete</th>
          </tr>
          </thead>
          <tbody>

          @foreach ($done_list as $todo)

            <tr style="text-decoration: line-through" id="row_{!! $todo->id !!}">
              <td title="{!! $todo->description !!}">{!! $todo->title !!}</td>
              <td><input type="checkbox" class="todo_done" id="0_{!! $todo->id !!}"/></td>
            </tr>

          @endforeach
          </tbody>
        </table>
      </tr>
    </div>
  </div>

  <script>

    $(document.body).on('click', '.todo_done', function () {

      var list = $(this).attr('id').split('_');
      var id = list.pop();
      var complete = list.pop();

      $.ajax
      (
        {
          url: '/ajax/update_todo',
          dataType: 'json',
          type: 'POST',
          async: true,
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id,
            "complete": complete
          },
          success: function (data) {
            if (complete > 0) {
              $('#row_' + id).css("text-decoration", "line-through");
            } else {
              $('#row_' + id).css("text-decoration", "none");
            }

          }
        }
      );

    });

  </script>

@endsection