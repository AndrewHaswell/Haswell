@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Ingredients</h3>

        <table class="table table-striped table-hover">
          <thead class="thead-default">
          <tr>
            <th>Name</th>
            <th>Store</th>
            <th>Price</th>
            <th>Category</th>
            <th>Nutrition</th>
            <th width="2%">&nbsp;</th>
          </tr>
          </thead>
          <tbody>

          @foreach ($ingredients as $ingredient)

            <?php

            if ((float)$ingredient->fat > 17.5) {
              $css_fat = 'red';
            } else if ((float)$ingredient->fat < 3) {
              $css_fat = 'green';
            } else {
              $css_fat = 'orange';
            }

            if ((float)$ingredient->saturates > 5) {
              $css_saturates = 'red';
            } else if ((float)$ingredient->saturates < 1.5) {
              $css_saturates = 'green';
            } else {
              $css_saturates = 'orange';
            }

            if ((float)$ingredient->sugars > 22.5) {
              $css_sugars = 'red';
            } else if ((float)$ingredient->sugars < 5) {
              $css_sugars = 'green';
            } else {
              $css_sugars = 'orange';
            }
            if ((float)$ingredient->salt > 1.5) {
              $css_salt = 'red';
            } else if ((float)$ingredient->salt < 0.3) {
              $css_salt = 'green';
            } else {
              $css_salt = 'orange';
            }
            ?>
            <tr>
              <th>
                <span class="editable" id="name_{{$ingredient->id}}">{{$ingredient->name}}</span>
              </th>
              <td>
                <span class="editable" id="shop_{{$ingredient->id}}">{{$ingredient->shop}}</span>
              </td>
              <td>
                <span class="editable" id="price_{{$ingredient->id}}">{{$ingredient->price}}</span>
              </td>
              <td>
                <span class="editable" id="category_{{$ingredient->id}}">{{$ingredient->category}}</span>
              </td>
              <td>
                <table class="table table-bordered table-striped">
                  <tr>
                    <td>Energy:</td>
                    <td><div class="nutrition"><span class="editable"
                                                     id="energy_{{$ingredient->id}}">{{round($ingredient->energy)}}</span></div> Kcal</td>
                  </tr>
                  <tr>
                    <td>Fat:</td>
                    <td>
                      <div class="nutrition"><span class="editable"
                                                   id="fat_{{$ingredient->id}}">{{round($ingredient->fat)}}</span></div>g&nbsp;&nbsp;(<div class="nutrition"><span class="editable"
                                                   id="saturates_{{$ingredient->id}}">{{round($ingredient->saturates)}}</span></div>g)
                    </td>
                  </tr>
                  <tr>
                    <td>Carb:</td>
                    <td>
                      <div class="nutrition"><span class="editable"
                                                   id="carb_{{$ingredient->id}}">{{round($ingredient->carb)}}</span></div>g&nbsp;&nbsp;(<div class="nutrition"><span class="editable"
                                                   id="sugars_{{$ingredient->id}}">{{round($ingredient->sugars)}}</span></div>g)
                    </td>
                  </tr>
                  <tr>
                    <td>Fibre:</td>
                    <td><div class="nutrition"><span class="editable"
                                                     id="fibre_{{$ingredient->id}}">{{round($ingredient->fibre)}}</span></div>g</td>
                  </tr>
                  <tr>
                    <td>Protein:</td>
                    <td><div class="nutrition"><span class="editable"
                                                     id="protein_{{$ingredient->id}}">{{round($ingredient->protein)}}</span></div>g</td>
                  </tr>
                  <tr>
                    <td>Salt:</td>
                    <td><div class="nutrition"><span class="editable"
                                                     id="salt_{{$ingredient->id}}">{{round($ingredient->salt)}}</span></div>g</td>
                  </tr>
                </table>
              </td>
              <td>
                <span class="remove_ingredient">X</span>
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
        typingTimer = setTimeout(doneTyping, doneTypingInterval, $(this));
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