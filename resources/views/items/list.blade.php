@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>List</h3>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th width="80%">Name</th>
            <th width="20%">Quantity</th>
          </tr>
          </thead>
          <tbody>

          @foreach ($shopping_list as $list)

            <tr>
              <td>
                <span class="editable" id="name_{{$list->id}}">{{$list->name}}</span>
              </td>
              <td>
                <span class="editable" id="qty_{{$list->id}}">{{$list->qty}}</span>
              </td>
            </tr>
          @endforeach

          <tr>
            <td>
              <select id="shopping_items">
                @foreach ($all_items as $key => $item)
                  <option value="{{$key}}">{{$item}}</option>
                @endforeach
              </select>

            </td>
            <td>
              <input type="text" id="items_qty"/>
            </td>
          </tr>

          </tbody>
        </table>
      </tr>
    </div>
  </div>


  <script>
    //setup before functions
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1200;  //time in ms, 4 seconds

    //on keyup, start the countdown
    $(document.body).on('keyup', '#items_qty', function () {
      clearTimeout(typingTimer);
      if ($(this).val().length > 0) {
        typingTimer = setTimeout(doneTyping, doneTypingInterval, $(this));
      }
    });

    //on keydown, clear the countdown
    $(document.body).on('keydown', '#items_qty', function () {
      clearTimeout(typingTimer);
    });

    //user is "finished typing," do something
    function doneTyping(e) {

      var quantity = e.val();
      var id = $('#shopping_items option:selected').val();

      $.ajax
      (
        {
          url: '/ajax/update_shopping_items',
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

    };

  </script>

@endsection