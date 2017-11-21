@foreach ($formatted_tickets as $priority => $tickets)
  @foreach($tickets as $assignee => $ticket_data)
    @foreach($ticket_data as $ticket_id => $ticket_info)


      "{{$priority}}","{{$ticket_id}}","{{$assignee}}","{{$ticket_info['subject']}}","{{date('d/m/Y H:i:s', $ticket_info['assigned_at'])}}"</br>

    @endforeach
  @endforeach
@endforeach