@extends('layouts.app')

@section('content')
  <div class="container">

    <div class="row">
      <h1>Open Tickets</h1>
    </div>
    <div class="row">
      <table id="tickets" class="table">
        <tr>
          @foreach ($stats as $level => $count)
            <td width="7%">
              @if (is_numeric($level))
                {{$sort_level[$level]}}
              @else
                {{ucwords($level)}}
              @endif
            </td>
            <td width="3%"><strong>{{$count}}</strong></td>
          @endforeach
        </tr>
      </table>
    </div>

    @foreach ($formatted_tickets as $client => $ticket_level)
      <div class="row">
        <div class="col-md-12">
          <h3>{{$client}}</h3>
          <table id="tickets" class="table table-striped table-hover">
            <?php
            ksort($ticket_level);
            ?>
            <thead class="thead-dark">
            <tr>
              <th width="8%">Ticket ID</th>
              <th width="44%">Subject</th>
              <th width="8%">Level</th>
              <th width="12%">Assignee</th>
              <th width="14%" align="right">Updated</th>
              <th width="14%" align="right">Created</th>
            </tr>
            </thead>
            @foreach ($ticket_level as $priority => $tickets)
              <?php krsort($tickets); ?>
              @foreach($tickets as $created => $ticket_data)
                @foreach($ticket_data as $ticket_id => $ticket_info)
                  <tr>
                    <td title="{{$ticket_info['description']}}"><a href="<?= $link . $ticket_id; ?>"
                                                                   target="_blank">#{{$ticket_id}}</a></td>
                    <td align="left">{{$ticket_info['subject']}}</td>
                    <td<?= $priority < 2
                      ? ' style="color: red; font-weight: bold"'
                      : ''?>>{{$sort_level[$priority]}}</td>
                    <td<?= $ticket_info['assignee'] == 'Andrew Haswell'
                      ? ' style="color: green; font-weight: bold"'
                      : ''?>>{{$ticket_info['assignee']}}</td>
                    <td align="left">{{date('d/m/Y H:i', $ticket_info['updated_at'])}}</td>
                    <td align="left">{{date('d/m/Y H:i', $created)}}</td>
                  </tr>
                @endforeach
              @endforeach
            @endforeach
          </table>
        </div>
      </div>
    @endforeach
  </div>

@endsection