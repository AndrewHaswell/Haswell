<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php
  if (empty($title))
    $title = 'SN0WMANX';
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

  <style>
    h2 {
      color: white;
      width: 40%;
      background-color: grey;
      padding: 10px;
      font-size: 16pt;
      }
    h4 {
      border-bottom: 1px solid black;
      padding: 10px 0;
      width: 40%;
      }
  </style>

</head>
<body>

<div class="container">
  <div class="row">
    <tr class="col-md-10 col-md-offset-1">
      <h1>Shopping List</h1>

      @foreach ($ingredient_list as $store => $store_list)
        <h2>{{$store}}</h2>

        @foreach ($store_list as $category => $item)

          <h4>{{$category}}</h4>

          <ul>

            @foreach ($item as $ingredient)

              <li type="square">{{$ingredient['name']}}</li>
            @endforeach

          </ul>

    @endforeach
    @endforeach

  </div>
</div>
</div>
</body>
</html>