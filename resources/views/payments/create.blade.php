@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <tr class="col-md-10 col-md-offset-1">
                <h3>New Statement</h3>
                {!! Form::open(['action' => 'PaymentsController@store']) !!}
                <table class="table table-striped table-hover">
                    <tbody>
                    <tr>
                        <th>{!! Form::label('statement', 'Statement: ') !!}</th>
                        <td>{!! Form::textarea('statement', null, ['class'=>'form-control']) !!}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            {!! Form::submit( 'Add Statement', ['class' => 'btn btn-primary form-control']) !!}
                        </td>
                    </tr>
                    </tbody>
                </table>
            {!! Form::close() !!}
        </div>
    </div>
@endsection