@extends('layouts.app')
@section('content')
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

                <li>{{$ingredient['name']}}</li>
              @endforeach

            </ul>

      @endforeach
      @endforeach

    </div>
  </div>
  </div>
@endsection