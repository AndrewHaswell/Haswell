<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php
  $title = 'PHONE';
  ?>

  <title>{{$title}}</title>

  <!-- JQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <!-- Fonts -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"
        integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

  <!-- Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css"
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

  <script type="application/javascript">

    $(function () {

      $('.mark_as_done').bind("click", function () {
        var id = $(this).attr('id');
        var name = $('#name_' + id).val();
        $('#price_area div.ingredient').text(name);
        var price = $('#price_' + id).val();
        $('#price_area input.price').val(price);
        $('#current_id').val(id);
        $('#price_area').show();
      });

      $('input.price').bind("click", function () {
        $(this).val('');
      });

      $('#go').bind("click", function () {
        var id = $('#current_id').val();
        $('#shopping_row_' + id).hide();

        var orig_price = parseFloat($('#price_' + id).val());
        var price = parseFloat($('#price_area input.price').val());

        var name = $('#name_' + id).val();

        var new_row = '<p class="checked_row" id="checked_row_' + id + '">' +
          '<input class="mark_as_not_done" id="undo_' + id + '" type="checkbox"/>' +
          '<label for="undo_' + id + '">' + name + '</label>' +
          '<span class="ingredient_price" id="ingredient_price_' + id + '">' + price.toFixed(2) + '</span>' +
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

    });

  </script>
  <style>
    body {
      font-size: 14pt;
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
      font-size:   14pt;
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

    #price_area {
      display:          none;
      position:         fixed;
      top:              130px;
      left:             40px;
      right:            40px;
      border:           2px solid black;
      background-color: rgba(255, 255, 255, 0.9);
      }

    #price_area div.ingredient, #price_area input.price {
      text-align:  center;
      font-weight: bold;
      font-size:   16pt;
      padding:     10px;
      }

    #price_area input.price {
      font-size: 30pt;
      width:     90%;
      padding:   5%;
      margin:    0 5%;
      border:    1px dashed grey;
      }

    #price_area button {
      display:   block;
      width:     90%;
      padding:   8%;
      margin:    5%;
      font-size: 18pt;
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

<div class="container">
  <div class="row">
    @foreach ($ingredient_list as $store => $store_list)
      @foreach ($store_list as $category => $item)
        <div class="spacer">&nbsp;</div>
        @foreach ($item as $ingredient)
          <p class="shopping_row" id="shopping_row_{{$ingredient['id']}}"><input class="mark_as_done"
                                                                                 id="{{$ingredient['id']}}"
                                                                                 type="checkbox"/> <label
                for="{{$ingredient['id']}}">{{$ingredient['name']}}</label><span class="ingredient_price"
                                                                                 id="ingredient_price_{{$ingredient['id']}}">{{$ingredient['price']}}</span>
          </p>
          <input type="hidden" id="name_{{$ingredient['id']}}" value="{{$ingredient['original_name']}}"/>
          <input type="hidden" id="price_{{$ingredient['id']}}" value="{{$ingredient['price']}}"/>
        @endforeach
      @endforeach
    @endforeach
  </div>
  <div class="spacer">&nbsp;</div>

  <div class="row">
    <strong>TOTAL: </strong><span class="ingredient_price" style="font-weight:bold">&pound;<span
          id="total_display">0.00</span></span>
    <input type="hidden" id="total" value="0"/>
  </div>

  <div class="spacer">&nbsp;</div>
  <div id="checked_off" class="row">

  </div>
</div>
</div>
</body>
</html>