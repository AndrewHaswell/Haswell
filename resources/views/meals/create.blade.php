@extends('layouts.app')
@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Add new meal</h3>
        {!! Form::open(['action' => 'MealsController@store']) !!}
        <table class="table table-striped table-hover">
          <tbody>
          <tr>
            <td colspan="3">Meal Name: <input type="text" name="meal_name"/>&nbsp;&nbsp;&nbsp;Servings: <input
                  type="text" name="meal_portion"/></td>

          </tr>
          <tr>
            <th id="test">Name</th>
            <th>Quantity</th>
            <th>Unit of Measure</th>
          </tr>
          <tr class="ingredient_row">
            <td class="ingredient"><select name="ingredient[]">{!! $select !!}</select></td>
            <td><input type="text" name="quantity[]" value="1"></td>
            <td><select name="unit[]">
                <option value="none">-</option>
                <option value="weight">g</option>
                <option value="volume">ml</option>
              </select></td>
          </tr>
          <tr>
            <td colspan="3">
              {!! Form::submit( 'Add meal', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>
          </tbody>
        </table>
      {!! Form::close() !!}
    </div>
  </div>
  </div>

  <script>
    $(function () {

      $(document.body).on('change', '.ingredient_row:last .ingredient', function () {
        $('.ingredient_row:last').clone().insertAfter('.ingredient_row:last');
      });

    });
  </script>
@endsection