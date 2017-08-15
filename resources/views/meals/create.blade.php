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
            <th>{!! Form::label('name', 'Name: ') !!}</th>
            <td>{!! Form::text('name', '', [
    'class'=>'form-control']) !!}</td>
          </tr>
          @foreach ($ingredients as $category => $ingredient)

            <?php
            asort($ingredient);
            ?>

            <tr class="warning">
              <td colspan="5">{{$category}}:</td>
            </tr>
            <tr>
              <?php $counter = 1; ?>
              @foreach ($ingredient as $id => $ingredient_name)
                <td>
                  {{ Form::checkbox('ingredients[]', $id, null, ['class' => 'field']) }}&nbsp;&nbsp;{!! Form::label('ingredients', $ingredient_name) !!}
                </td>
                <?php $counter++;?>
                @if ($counter > 5)
                  <?php $counter = 1;?>
            </tr>
            <tr>
              @endif
              @endforeach
            </tr>
          @endforeach
          <tr>
            <td colspan="2">
              {!! Form::submit( 'Add meal', ['class' => 'btn btn-primary form-control']) !!}
            </td>
          </tr>
          </tbody>
        </table>
      {!! Form::close() !!}
    </div>
  </div>
  </div>
@endsection