@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>Teamwork</h1>
      <!-- Call Count: {{$call_count}}-->
        <table id="quotes" class="table table-striped table-hover">
          @foreach ($project_list as $client => $tags)
            <tr>
              <td colspan="4" class="bg-primary"><h4>{{$client}}</h4></td>
            </tr>
            <tr class="column_headers">
              <td><strong>Project Name</strong></td>
              <td align="right"><strong>Updated</strong></td>
              <td align="right"><strong>Created</strong></td>
              <td align="right"><strong>Deadline</strong></td>
            </tr>
          @foreach ($tags as $tag_id => $tag)
              <tr>
                <td colspan="4" class="bg-info"><strong>{{$quote_list[$tag_id]}}<!-- {{$tag_id}} --></strong>
                </td>
              </tr>

              @foreach ($tag as $project_id => $project)

                <tr class="@if ($tag_id == 20654) urgent_row @endif teamwork_row">
                  <td>
                    <a href="{{str_replace('{$project_id}', $project_id, $link)}}"
                       target="_blank">{{trim(str_ireplace('_Project:', '', $project->name))}}</a>
                  </td>
                  <td align="right">
                    {{date('D jS F Y',$project->updated)}}
                  </td>
                  <td align="right">
                    {{date('D jS F Y',$project->date)}}
                  </td>
                  <td align="right">
                    @if (!empty($project->milestones))
                      <?php
                      $milestone_title = current($project->milestones);
                      $milestone_date = key($project->milestones);
                      ?>
                      @if (!empty($milestone_date))
                        <span title="{{$milestone_title}}">{{date('D jS F Y',$milestone_date)}}</span>
                      @endif
                    @endif
                  </td>
                </tr>

              @endforeach
            @endforeach
          @endforeach
        </table>
      </div>

    </div>
  </div>

@endsection