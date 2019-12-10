<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    $title = 'PHONE';

    $shopping_list = @json_decode($shopping_list, true);
    if (!is_array($shopping_list)) {
        exit('No shopping list set.');
    }

    ?>

  <title>{{$title}}</title>

  <!-- JQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script type="text/javascript" src="{{ URL::asset('js/phone.js') }}"></script>

  <!-- Fonts -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"
        integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

  <!-- Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css"
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
  {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

  <style>
    body {
      font-size: 11pt;
      margin:    10px;
      }

    h2 {
      color:            white;
      width:            100%;
      background-color: grey;
      padding:          10px;
      font-size:        16pt;
      }

    h4 {
      border-bottom: 1px solid black;
      padding:       10px 0;
      width:         100%;
      }

    label {
      font-weight: normal;
      font-size:   11pt;
      }

    input[type=checkbox] {
      /* Double-sized Checkboxes */
      -ms-transform:     scale(1.5); /* IE */
      -moz-transform:    scale(1.5); /* FF */
      -webkit-transform: scale(1.5); /* Safari and Chrome */
      -o-transform:      scale(1.5); /* Opera */
      padding:           10px;
      margin-right:      10px;
      }

    p.shopping_row {
      border-bottom: 1px dotted #afafaf;
      }

    #price_area, #quick_add_area {
      display:          none;
      position:         fixed;
      top:              20px;
      left:             40px;
      right:            40px;
      border:           2px solid black;
      background-color: rgba(255, 255, 255, 0.9);
      }

    #price_area div.ingredient, #price_area input.price, #quick_add_area input.ingredient, #quick_add_area input.price {
      text-align:  center;
      font-weight: bold;
      font-size:   16pt;
      padding:     10px;
      }

    #quick_add_area input.ingredient {
      width:  90%;
      margin: 5%;
      border: 1px dashed grey;
      }

    #price_area input.price, #quick_add_area input.price {
      font-size: 30pt;
      width:     90%;
      padding:   5%;
      margin:    0 5%;
      border:    1px dashed grey;
      }

    #price_area button, .add_item button, #quick_add_area button {
      display:   block;
      width:     90%;
      padding:   8%;
      margin:    5%;
      font-size: 18pt;
      }

    .add_item button {
      padding:   2%;
      font-size: 11pt;
      }

    .ingredient_price {
      float: right;
      }

    .spacer {
      margin: 3px;
      }

    #checked_off p label {
      text-decoration: line-through;
      }

    #checked_off p {
      color: darkgrey;
      }
  </style>

</head>
<body>

<div id="price_area">
  <div class="ingredient">-</div>
  <input class="price" type="number" value="0"/>
  <input type="hidden" id="current_id" value="0"/>

  <button id="go" type="button" class="btn btn-primary">Cross Off</button>
  <button id="cancel" type="button" class="btn btn-danger">Cancel</button>

</div>

<div id="quick_add_area">
  <input type="text" id="qa_name" class="ingredient" value=""/>
  <input id="qa_price" class="price" type="number" value="0"/>
  <input type="hidden" id="qa_current_id" value="0"/>

  <button id="qa_add" type="button" class="btn btn-primary">Add Item</button>
  <button id="qa_cancel" type="button" class="btn btn-danger">Cancel</button>

</div>

<div class="container">
  <div class="row">

    @foreach ($shopping_list as $ingredient)

      <p class="shopping_row" id="shopping_row_{{$ingredient['id']}}"
         @if ($ingredient['checked']) style="display:none"@endif><input class="mark_as_done"
                                                                        id="mark_{{$ingredient['id']}}"
                                                                        type="checkbox"/> <label
            for="{{$ingredient['id']}}">{{$ingredient['name']}}</label><span class="ingredient_price"
                                                                             id="ingredient_price_{{$ingredient['id']}}">{{$ingredient['price']}}</span>
      </p>
      <input type="hidden" id="name_{{$ingredient['id']}}" value="{{$ingredient['original_name']}}"/>
      <input type="hidden" id="price_{{$ingredient['id']}}" value="{{$ingredient['price']}}"/>
      <input type="hidden" id="display_{{$ingredient['id']}}" value="{{$ingredient['name']}}"/>

    @endforeach
  </div>
  <div class="spacer">&nbsp;</div>

  <div class="row">
    <strong>TOTAL: </strong><span class="ingredient_price" style="font-weight:bold">&pound;<span
          id="total_display">0.00</span></span>
    <input type="hidden" id="total" value="0"/>
  </div>

  <div class="spacer">&nbsp;</div>

  <div class="row add_item">
    <button id="add_item_button" type="button" class="btn btn-primary">Add Item</button>
  </div>

  <div class="spacer">&nbsp;</div>
  <div id="checked_off" class="row">
    @foreach ($shopping_list as $ingredient)
      @if ($ingredient['checked'])
        <p class="checked_row" id="checked_row_{{$ingredient['id']}}">
          <input class="mark_as_not_done" id="undo_{{$ingredient['id']}}" type="checkbox"/>
          <label for="undo_{{$ingredient['id']}}">{{$ingredient['name']}}</label>
          <span class="ingredient_price"
                id="ingredient_price_hidden_{{$ingredient['id']}}">{{$ingredient['price']}}</span>
        </p>

      @endif
    @endforeach
  </div>
</div>
</div>
</body>
</html>

<script>
  $(function () {

    save_shopping_list();

    // Show pricing area to add item

    $(document).on("click", '.mark_as_done', function () {
      var id = $(this).attr('id').replace('mark_', '');
      $(this).prop('checked', false);
      var name = $('#name_' + id).val();
      $('#price_area div.ingredient').text(name);
      var price = $('#price_' + id).val();
      $('#price_area input.price').val(price);
      $('#current_id').val(id);
      $('#price_area').show();
    });



    $(document).on("click", '.mark_as_not_done', function () {

      if (confirm('Return item to shopping list?')) {

        var id = $(this).attr('id').replace('undo_', '');
        var price = parseFloat($('#ingredient_price_hidden_' + id).html());
        $('#checked_row_' + id).hide();
        $('#ingredient_price_' + id).html(price.toFixed(2));
        $('#shopping_row_' + id).show();

        var total = parseFloat($('#total').val());
        var new_total = parseFloat(total - price).toFixed(2);

        $('#total').val(new_total);
        $('#total_display').text(new_total);

      }

      save_shopping_list();

    });







    $('input.price').bind("click", function () {
      $(this).val('');
    });







    $('#qa_add').bind("click", function () {
      var name = $('#qa_name').val();
      var price = parseFloat($('#qa_price').val());
      var id = name.replace(' ', '_') + '_' + (price * 100);

      if (name.length > 0 && price > 0) {
        name = name.toLowerCase().replace(/\b[a-z]/g, function (letter) {
          return letter.toUpperCase();
        });

        var new_row = '<p class="added_row" id="added_row_' + id + '">' +
          '<input class="remove_added_item" id="remove_' + id + '" type="checkbox"/>' +
          '<label for="remove_' + id + '">' + name + '</label>' +
          '<span class="ingredient_price" id="ingredient_price_added_' + id + '">' + price.toFixed(2) + '</span>' +
          '</p>';

        var total = parseFloat($('#total').val());
        var new_total = parseFloat(price + total).toFixed(2);

        $('#total').val(new_total);
        $('#total_display').text(new_total);

        $('#checked_off').append(new_row);
        $('#quick_add_area').hide();

        save_shopping_list();

      }



    });

    // Check off item from shopping list

    $('#go').bind("click", function () {
      var id = $('#current_id').val();
      $('#shopping_row_' + id).hide();

      var orig_price = parseFloat($('#price_' + id).val());
      var price = parseFloat($('#price_area input.price').val());

      var name = $('#name_' + id).val();

      var new_row = '<p class="checked_row" id="checked_row_' + id + '">' +
        '<input class="mark_as_not_done" id="undo_' + id + '" type="checkbox"/>' +
        '<label for="undo_' + id + '">' + name + '</label>' +
        '<span class="ingredient_price" id="ingredient_price_hidden_' + id + '">' + price.toFixed(2) + '</span>' +
        '</p>';

      $('#checked_off').append(new_row);

      if (Math.round(orig_price * 100) != Math.round(price * 100)) {

        $.ajax
        (
          {
            url: '/ajax/update_ingredient_prices',
            dataType: 'json',
            type: 'POST',
            async: true,
            data:
              {
                "_token": "{{ csrf_token() }}",
                id: id,
                price: price
              },
            success: function (data) {
              // Data is whatever gets returned
            }
          }
        );
      }

      if (price) {
        var total = parseFloat($('#total').val());
      }
      var new_total = parseFloat(price + total).toFixed(2);

      $('#total').val(new_total);
      $('#total_display').text(new_total);
      $('#price_area').hide();

      save_shopping_list();

    });

    $('#add_item_button').bind("click", function () {
      $('#quick_add_area').show();
    });

    $('#qa_cancel').bind("click", function () {
      $('#quick_add_area').hide();
    })

    $(document).on("click", '.remove_added_item', function () {

      if (confirm('Are you sure you want to remove this added item?')) {
        var id = $(this).attr('id').replace('remove_', '');

        var price = parseFloat($('#ingredient_price_added_' + id).html());
        var total = parseFloat($('#total').val());
        var new_total = parseFloat(total - price).toFixed(2);

        $('#total').val(new_total);
        $('#total_display').text(new_total);

        $('#added_row_' + id).remove();

        save_shopping_list();
      }

    });

    $('#cancel').bind("click", function () {
      var id = $('#current_id').val();
      $('input#' + id).prop('checked', false);
      $('#price_area').hide();
    });

    $('input.price').bind("keyup", function () {
      var value = $(this).val().replace(/\D/g, '');
      value = pad(value, 3);
      var pence = value.substr(value.length - 2);
      var pounds = parseInt(value.substr(0, value.length - 2));
      $(this).val(pounds + '.' + pence);
    });

    function pad(str, max) {
      str = str.toString();
      return str.length < max ? pad("0" + str, max) : str;
    }

    function save_shopping_list() {

      console.clear();

      var list = [];
      $('.shopping_row:visible,.checked_row:visible,.added_row:visible').each(function () {
        var this_item = [];
        var id = $(this).attr('id').split('_').pop();
        var classname = $(this).attr('class');
        this_item.push(id);
        this_item.push($('#name_' + id).val());
        this_item.push($('#price_' + id).val());
        this_item.push($('#display_' + id).val());

        console.log(classname);

        var checked = classname != 'shopping_row';

        console.log(checked);

        this_item.push(checked);
        list.push(this_item);
      });

      console.log(list);

      $.ajax
      (
        {
          url: '/ajax/save_shopping_list',
          dataType: 'json',
          type: 'POST',
          async: true,
          data:
            {
              "_token": "{{ csrf_token() }}",
              list: list
            },
          success: function (data) {
            // Data is whatever gets returned
          }
        }
      );
    }

  });


</script>