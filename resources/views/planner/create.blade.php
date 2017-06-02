@extends('layouts.app')
@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Meal Planner</h3>
        {!! Form::open(['action' => 'PlannerController@store']) !!}
        <table class="table table-striped table-hover">
          <tbody>

          <tr>
            <th>Day</th>
            @foreach ($meal_types as $meal_type)
              <th>{{ucwords($meal_type)}}</th>
            @endforeach
          </tr>

          @foreach ($days as $day)
            <tr>
              <th>{{ucwords($day)}}</th>
              @foreach ($meal_types as $meal_type)

                <?php
                $key = $day . '_' . str_replace(' ', '', $meal_type);
                $default = !empty($set_meals[$key])
                  ? $set_meals[$key]
                  : 0;
                ?>

                <td>{!! Form::select($key, $meals, $default, ['class'=>'form-control']) !!}</td>
              @endforeach
            </tr>
          @endforeach

          <tr>
            <td colspan="{{count($meal_types)+1}}">
              {!! Form::submit( 'Update Planner', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>
          </tbody>
        </table>
      {!! Form::close() !!}
    </div>
  </div>
  </div>
@endsection