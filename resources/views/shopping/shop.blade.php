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

</head>
<body>

{!! Form::open(['action' => 'ShoppingController@store']) !!}
@foreach ($ingredient_list as $store => $store_list)
  @foreach ($store_list as $category => $item)
    @foreach ($item as $ingredient)
      <p>{{ Form::checkbox('ingredient[]', $ingredient['id']) }} {{$ingredient['name']}}</p>
    @endforeach
  @endforeach
@endforeach
{!! Form::submit( 'Remove Ingredients', ['class' => 'btn btn-primary form-control']) !!}
{!! Form::close() !!}

</body>

</html>