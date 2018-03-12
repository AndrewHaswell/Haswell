@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>Quotes</h1>
        <table id="quotes" class="table table-striped table-hover">
          @foreach ($formatted_quotes as $status_id => $quotes)
            <tr>
              <td colspan="5" class="bg-primary"><h4>{{$teamwork_codes[$status_id]}} ({{count($quotes)}})</h4></td>
            </tr>

            <?php krsort($quotes); ?>

            @foreach($quotes as $created_at => $quote_info)

              <tr>
                <td title="{{$quote_info['description']}}">#<a href="@if (!empty($quote_info['teamwork_id']))https://visualsoftacm1.teamwork.com/#projects/{{$quote_info['teamwork_id']}}/overview/summary @else https://support.visualsoft.co.uk/bespoke/view/{{$quote_info['id']}}@endif" target="_blank">{{$quote_info['id']}}</a></td>
                <td align="left"><strong @if ($status_id < 3 && $quote_info['sla_left'] < 8) style="color:red" @endif > {{$quote_info['client']}}</strong></td>
                <td align="left">{{$quote_info['subject']}}</td>
                <td align="left">@if ($status_id < 3) {{$quote_info['sla_left']}} @else - @endif</td>
                <td title="{{date('g:ia - jS F Y', $created_at)}}" align="right">{{date('j M y', $created_at)}}</td>
              </tr>

            @endforeach
          @endforeach
        </table>
      </div>

    </div>
  </div>

@endsection