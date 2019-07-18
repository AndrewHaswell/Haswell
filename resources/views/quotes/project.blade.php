@extends('layouts.basic')

@section('content')
  <div class="container-fluid">

    <div class="row">
      <h1>{{$project['name']}}</h1>
    </div>
    <div class="row">
      <div class="col-12">
        <hr/>
      </div>
    </div>

    <div class="row">
      <div class="col-1">
        &nbsp;
      </div>
      <div class="col-6">
        <h3>Objectives</h3>
        {!! trim($project['notebooks']['Objectives']) !!}
        <hr/>
        <h3>Scope</h3>
        {!! trim($project['notebooks']['Scope']) !!}
        <hr/>
        <h3>Key Assumptions</h3>
        {!! $project['notebooks']['Key Assumptions'] !!}
        <hr/>
        <h3>Internal Specification</h3>
        {!! $project['notebooks']['Internal Specification'] !!}
        <hr/>
        <h3>Internal Notes</h3>
        {!! $project['notebooks']['Internal Notes'] !!}
        @if (!empty($project['messages']))
          <hr/>
          <h3>Most Recent Message</h3>
          {!! current($project['messages']) !!}
        @endif
      </div>
      <div class="col-5">
        <p>Client: <strong>{{$project['company']}}</strong></p>
        <p>Board: <strong>{{$project['board']}}</strong></p>
        <p>Tags: <strong>{{ implode($project['tags']) }}</strong></p>
        <p>Project Progress: <strong>{{round($project['project_complete'],2)}}%</strong></p>
        <p>Remaining Time: <strong>{{$project['time_remaining']}}</strong></p>
        <p>Total Time: <strong>{{$project['total_time']}}</strong></p>

        <hr/>
        <h2>Tasks</h2>
        @foreach ($project['tasks'] as $section => $tasklist)
          <p><strong>{{$section}}</strong></p>
          @foreach ($tasklist as $task)
            <p><em>{{ $task['content'] }}</em><br/>Time: <strong>{{ $task['time'] }}</strong> / Progress:
              <strong>{{ $task['progress'] }}%</strong></p>
          @endforeach
        @endforeach
      </div>
    </div>

  </div>

@endsection