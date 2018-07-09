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

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css"
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <style>
    h2 {
      color:            white;
      width:            40%;
      background-color: grey;
      padding:          10px;
      font-size:        16pt;
      }

    h4 {
      border-bottom: 1px solid black;
      padding:       10px 0;
      width:         40%;
      }
  </style>

</head>
<body>
<div class="container">
  <div class="row">
    <tr class="col-md-10 col-md-offset-1">
      {!! Form::open(['action' => 'ShoppingController@store']) !!}
      @foreach ($ingredient_list as $store => $store_list)
        <h2>{{$store}}</h2>
        @foreach ($store_list as $category => $item)
          <h4>{{$category}}</h4>
          <ul style="list-style-type: none">
            @foreach ($item as $ingredient)
              <li>{{ Form::checkbox('ingredient[]', $ingredient['id']) }} {{$ingredient['name']}}</li>
            @endforeach
          </ul>
        @endforeach
      @endforeach
      {!! Form::submit( 'Remove Items', ['class' => 'btn btn-primary form-control']) !!}
      {!! Form::close() !!}
    </tr>
  </div>
</div>

</body>

</html>