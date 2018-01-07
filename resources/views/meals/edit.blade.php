@extends('layouts.app')
@section('content')
  <div class="container">
    <div class="row">
      <tr class="col-md-10 col-md-offset-1">
        <h3>Edit meal</h3>
        {!! Form::open(['action' => 'MealsController@store']) !!}
        <table class="table table-striped table-hover">
          <tbody>
          <tr>
            <td colspan="3"><input type="text" style="width:50%; text-align: center" name="meal_name"
                                   value="{{$meal->name}}"/>&nbsp;&nbsp;&nbsp;Servings:
              <input
                  type="text" style="width:10%; text-align: center" name="meal_portion" value="{{$meal->portion}}"/>
            </td>
          </tr>
          <tr>
            <th id="test">Name</th>
            <th>Quantity</th>
            <th>Unit of Measure</th>
          </tr>
          @foreach ($ingredients as $ingredient)
            <tr class="ingredient_row">
              <td class="ingredient"><select name="ingredient[]">
                  <optgroup label='Current Selection'>
                    <option value='{{$ingredient->id}}'>{{$ingredient->name}}</option>
                  </optgroup>
                  {!! $select !!}</select></td>
              <td><input type="text" name="quantity[]" value="{{$ingredient->pivot->quantity}}"></td>
              <td><select name="unit[]">
                  <option value="none" @if ($ingredient->pivot->unit == 'none') selected="selected" @endif>-</option>
                  <option value="weight"@if ($ingredient->pivot->unit == 'weight') selected="selected" @endif>g</option>
                  <option value="volume"@if ($ingredient->pivot->unit == 'volume') selected="selected" @endif>ml</option>
                </select></td>
            </tr>
          @endforeach
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
              <input type="hidden" name="meal_id" value="{{$meal->id}}"/>
              <input type="hidden" name="update" value="true"/>
              {!! Form::submit( 'Update meal', ['class' => 'btn btn-primary form-control']) !!}
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