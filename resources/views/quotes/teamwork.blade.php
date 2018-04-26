@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>Teamwork</h1>
        <table id="quotes" class="table table-striped table-hover">
          @foreach ($project_list as $client => $tags)
            <tr>
              <td colspan="3" class="bg-primary"><h4>{{$client}}</h4></td>
            </tr>
            @foreach ($tags as $tag_id => $tag)
              <tr>
                <td colspan="3" class="bg-info"><h5><strong>{{$quote_list[$tag_id]}}<!-- {{$tag_id}} --></strong></h5></td>
              </tr>
              <tr>
                <td><strong>Project Name</strong></td>
                <td align="right"><strong>Updated</strong></td>
                <td align="right"><strong>Created</strong></td>
              </tr>

              @foreach ($tag as $project_id => $project)

                <tr @if ($tag_id == 20654) class="urgent_row" @endif>
                  <td>
                    <a href="{{str_replace('{$project_id}', $project_id, $link)}}"
                       target="_blank">{{trim(str_ireplace('_Project:', '', $project->name))}}</a>
                  </td>
                  <td align="right">
                    {{date('jS F Y',$project->updated)}}
                  </td>
                  <td align="right">
                    {{date('jS F Y',$project->date)}}
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