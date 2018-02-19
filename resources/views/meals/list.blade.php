@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Meals</h3>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th align="left">Name</th>
            <th align="center">Energy</th>
            <th align="center">Fat</th>
            <th align="center">Saturates</th>
            <th align="center">Carb</th>
            <th align="center">Sugars</th>
            <th align="center">Fibre</th>
            <th align="center">Protein</th>
            <th align="center">Salt</th>
          </tr>
          </thead>
          <tbody>

          @foreach ($meals as $meal)

            <tr>
              <th align="left">
                <a href="{{ url('/meals/'.$meal->id.'/edit') }}" id="name_">{{$meal->name}}</a>
              </th>
              <td align="center">
                {{round($meal->energy)}}
              </td>
              <td align="center">
                {{round($meal->fat,2)}}
              </td>
              <td align="center">
                {{round($meal->saturates,2)}}
              </td>
              <td align="center">
                {{round($meal->carb,2)}}
              </td>
              <td align="center">
                {{round($meal->sugars,2)}}
              </td>
              <td align="center">
                {{round($meal->fibre,2)}}
              </td>
              <td align="center">
                {{round($meal->protein,2)}}
              </td>
              <td align="center">
                {{round($meal->salt,2)}}
              </td>

            </tr>
          @endforeach
          </tbody>
        </table>
      </tr>
    </div>
  </div>

  <script>

    $(function () {

      $(document.body).on('click', '.remove_ingredient', function () {
        alert($(this).attr('class'));
      });

      $(document.body).on('click', '.editable', function () {
        var original_id = $(this).attr('id');
        var ref = original_id.split('_');
        var id = ref.pop();
        var original_value = $(this).html();

        $(this).parent().html('<input type="text" class="edit_box" id="' + original_id + '" value="' + original_value + '" />');
      });

      //setup before functions
      var typingTimer;                //timer identifier
      var doneTypingInterval = 1200;  //time in ms, 4 seconds

      //on keyup, start the countdown
      $(document.body).on('keyup', '.edit_box', function () {
        clearTimeout(typingTimer);
        if ($(this).val().length > 0) {
          typingTimer = setTimeout(doneTyping, doneTypingInterval, $(this));
        }
      });

      //on keydown, clear the countdown
      $(document.body).on('keydown', '.edit_box', function () {
        clearTimeout(typingTimer);
      });

      //user is "finished typing," do something
      function doneTyping(e) {

        var original_id = e.attr('id');
        var ref = original_id.split('_');
        var id = ref.pop();
        var type = ref.pop();
        var value = e.val();

        $.ajax
        (
          {
            url: '/ajax/update_ingredients',
            dataType: 'json',
            type: 'POST',
            async: true,
            data: {
              "_token": "{{ csrf_token() }}",
              "type": type,
              "id": id,
              "value": value
            },
            success: function (data) {
              $('#' + original_id).parent().html('<span class="editable" id="' + original_id + '">' + value + '</span>');
            }
          }
        );

      }

    });

  </script>
@endsection