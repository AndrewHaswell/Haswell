@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>Crafty Arts</h1>
        <table id="tickets" class="table table-striped table-hover">
          @foreach ($formatted_tickets as $priority => $tickets)
            <tr>
              <td colspan="3" class="bg-primary"><h4>{{$priority}}</h4></td>
            </tr>

            <?php ksort($tickets); ?>

            @foreach($tickets as $assignee => $ticket_data)

              <tr>
                <td colspan="3" class="bg-success"><strong>{{$assignee}}</strong></td>
              </tr>

              @foreach($ticket_data as $ticket_id => $ticket_info)

                <tr>
                  <td title="{{$ticket_info['description']}}">#{{$ticket_id}}</td>
                  <td align="center">{{$ticket_info['subject']}}</td>
                  <td align="right">{{date('H:i d-M-Y', $ticket_info['assigned_at'])}}</td>
                </tr>

              @endforeach
            @endforeach
          @endforeach
        </table>
      </div>

    </div>
  </div>

@endsection