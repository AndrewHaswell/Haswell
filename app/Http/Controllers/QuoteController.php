<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

use App\Http\Requests;

class QuoteController extends Controller
{

    private $teamwork_companies;
    private $teamwork_projects;

    private $quote_list;
    private $teamwork_calls;
    private $teamwork_unwanted_ids;
    private $teamwork_blocked_ids;

    public function __construct()
    {
        $this->middleware('auth');

        $this->teamwork_calls = 0;

        $this->teamwork_companies = explode(',', env('TEAMWORK_COMPANIES', ''));
        $this->teamwork_unwanted_ids = explode(',', env('TEAMWORK_UNWANTED_IDS', ''));
        $this->teamwork_blocked_ids = explode(',', env('TEAMWORK_BLOCKED_IDS', ''));
        $this->teamwork_projects = new \stdClass();
        $this->quote_list = [];
    }

    /**
     * @param bool $show_all
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Andrew Haswell
     */

    public function teamwork($show_all = false)
    {

        dd('jh');

        if ($show_all) {
            $this->teamwork_unwanted_ids = [];
        }
        foreach ($this->teamwork_companies as $company_id) {
            $this->format_projects($this->teamwork_get_projects($company_id));
        }

        $project_list = $this->teamwork_projects;
        $quote_list = $this->quote_list;
        $call_count = $this->teamwork_calls;
        $link = env('TEAMWORK_LINK', '');

        return view('quotes.teamwork', compact([
          'project_list',
          'link',
          'call_count',
          'quote_list'
        ]));
    }

    /**
     *
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @author Andrew Haswell
     */

    public function people($id)
    {
        //$id = 322119;
        //$people = $this->teamwork_curl('people/' . $id);
        //$id = 470881;

        $project = $this->get_project_data($id);
        return view('quotes.project', compact(['project']));

        $project = $this->teamwork_curl('projects/' . $id . '/latestActivity');
    }

    /**
     * @author Andrew Haswell
     */

    public function something()
    {
        $project = $this->get_project_data(470771);
        return view('quotes.project', compact(['project']));
    }

    /**
     * @param $id
     *
     * @return array
     * @author Andrew Haswell
     */

    private function get_project_data($id)
    {
        $tw_project = $this->teamwork_curl('projects/' . $id);
        $project_messages = $this->teamwork_curl('projects/' . $id . '/posts');

        // Get some project messages
        $msg = [];
        if (!empty($project_messages->posts) && is_array($project_messages->posts)) {
            foreach ($project_messages->posts as $messages) {
                $msg[$messages->id] = $messages->{'author-first-name'} . ' ' . $messages->{'author-last-name'} . ': ' . $messages->body;
            }
            krsort($msg);
        }

        $tw_project = $tw_project->project;

        $boardName = !empty($tw_project->boardData->column->name) ?
          $tw_project->boardData->column->name :
          'None';

        $project = [
          'name'     => $tw_project->name,
          'company'  => $tw_project->company->name,
          'board'    => $boardName,
          'messages' => $msg,
        ];

        if (!empty($tw_project->tags)) {
            foreach ($tw_project->tags as $tag) {
                $project['tags'][] = $tag->name;
            }
        }

        $project['notebooks'] = $this->get_notebooks($id);
        $parameters = ['includeCompletedTasks' => 1];
        $scope = $this->teamwork_curl('projects/' . $id . '/tasks', $parameters);



        if (!empty($scope->STATUS) && $scope->STATUS == 'OK') {

            $taskList = $scope->{'todo-items'};
            $total_task_time = 0;
            $minute_complete = 0;

            foreach ($taskList as $task) {

                if (strtolower($task->content) == 'quote for work') {
                    continue;
                }

                if (!empty($task->tags)) {
                    foreach ($task->tags as $tag) {
                        if ($tag->id == 69717 && in_array($task->{'todo-list-name'}, ['Peer Review','User Acceptance Testing'])) {
                            continue 2;
                        }
                    }
                }

                $project['tasks'][$task->{'todo-list-name'}][$task->id] = [
                  'content'  => $task->content,
                  'time'     => $this->minutes_to_time($task->{'estimated-minutes'}),
                  'progress' => $task->progress
                ];

                $minute_complete += $task->{'estimated-minutes'} * $task->progress;

                $total_task_time = $total_task_time + $task->{'estimated-minutes'};
            }

            $project['minute_complete'] = $minute_complete;

            $project['project_complete'] = !empty($minute_complete) ?
              ($minute_complete / ($total_task_time*100)) * 100 :
              0;

            $project['time_remaining'] = $this->minutes_to_time($total_task_time - (($total_task_time/100)*$project['project_complete']));

            $project['total_time_minutes'] = $total_task_time;
            $project['total_time'] = $this->minutes_to_time($total_task_time);
        }

        return $project;
    }

    /**
     * @param $project_id
     *
     * @return array
     * @author Andrew Haswell
     */

    private function get_notebooks($project_id)
    {
        $notebooks = [];

        $required_notebooks = [
          'Objectives',
          'Scope',
          'Key Assumptions',
          'Internal Notes',
          'Internal Specification'
        ];

        $parameters = ['includeContent' => 1];
        $notebook_list = $this->teamwork_curl('projects/' . $project_id . '/notebooks', $parameters);

        if (!empty($notebook_list->project->notebooks)) {
            foreach ($notebook_list->project->notebooks as $notebook) {

                if (in_array($notebook->name, $required_notebooks)) {
                    $notebooks[$notebook->name] = trim(strip_tags($notebook->content, '<strong><p><ul><li>'));
                } else {
                    $notebooks[$notebook->name] = 'None';
                }
            }
        }

        return array_reverse($notebooks);
    }

    /**
     * @param $minutes
     *
     * @return false|string
     * @author Andrew Haswell
     */

    private function minutes_to_time($minutes)
    {
        $hours = (int)floor($minutes / 60);
        $minutes = (int)$minutes - ($hours * 60);
        return $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
    }

    /**
     * @param $project_list
     *
     * @return bool
     * @author Andrew Haswell
     */

    private function format_projects($project_list)
    {
        if (!empty($project_list->projects)) {
            foreach ($project_list->projects as $project) {

                // Is it a tech project?
                if (substr((string)$project->name, 0, 8) != '_Project') {
                    continue;
                }

                // Set the defaults so we can find it in the list
                $company = (string)$project->company->name;
                $id = (int)$project->id;

                $tags = $project->tags;

                foreach ($tags as $key => $tag) {
                    if (in_array($tag->id, $this->teamwork_unwanted_ids)) {
                        unset($tags[$key]);
                    }
                    if (in_array($tag->id, $this->teamwork_blocked_ids)) {
                        unset($tags);
                        break;
                    }
                }

                if (!empty($tags)) {
                    $tag = current($tags);
                } else {
                    continue;
                }

                $tag_id = (int)$tag->id;
                $tag_name = (string)$tag->name;

                // Do we already have this client?
                if (empty($this->teamwork_projects->$company)) {
                    $this->teamwork_projects->$company = new \stdClass();
                }

                // Do we already have this tag for the client?
                if (empty($this->teamwork_projects->$company->$tag_id)) {
                    $this->teamwork_projects->$company->$tag_id = new \stdClass();
                    $this->quote_list[$tag_id] = $tag_name;
                }

                // Create this project in the global object
                $this->teamwork_projects->$company->$tag_id->$id = new \stdClass();

                // Let's use an alias now to keep things simpler
                $this_project = &$this->teamwork_projects->$company->$tag_id->$id;
                $this_project->name = $project->name;
                $this_project->date = strtotime($project->{'created-on'});
                $this_project->updated = strtotime($project->{'last-changed-on'});

                if ($tag_id == 20654) {
                    $this_project->milestones = new \stdClass();
                    $project_milestones = $this->teamwork_curl('projects/' . $id . '/milestones');
                    if (!empty($project_milestones->milestones) && is_array($project_milestones->milestones)) {
                        foreach ($project_milestones->milestones as $milestone) {
                            $deadline = strtotime(implode('-', sscanf((string)$milestone->deadline, "%04d%02d%02d")));
                            $this_project->milestones->$deadline = (string)$milestone->title;
                        }
                    }
                }

                // Leave the messages for the moment - ajax them in?
                if (0) {
                    $this_project->messages = new \stdClass();
                    $project_messages = $this->teamwork_curl('projects/' . $id . '/posts');
                    foreach ($project_messages->posts as $message) {
                        $post_id = $message->{'post-id'};
                        $this_message = [
                          'author' => (string)$message->{'author-first-name'} . ' ' . (string)$message->{'author-last-name'},
                          'body'   => (string)$message->{'html-body'},
                          'date'   => (string)$message->{'last-changed-on'},
                        ];
                        $this_project->messages->$post_id = $this_message;
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param $company_id
     *
     * @return mixed
     * @author Andrew Haswell
     */

    private function teamwork_get_projects($company_id)
    {
        return $this->teamwork_curl('companies/' . $company_id . '/projects');
    }

    /**
     * Request a response from Teamwork and return the result as an object
     *
     * @param $url
     * @param null $parameters
     * @param string $format
     *
     * @return mixed
     * @author Andrew Haswell
     */

    public function teamwork_curl($url, $parameters = null, $format = 'json')
    {

        $auth = [
          'auth' => [
            env('TEAMWORK_USERNAME', ''),
            env('TEAMWORK_PASSWORD', '')
          ],
        ];

        $url = env('TEAMWORK_URL', '') . '/' . $url . '.' . $format;

        if (!empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }

        $client = new Client();
        $response = $client->get($url, $auth);
        $this->teamwork_calls++;
        return json_decode((string)$response->getBody());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $client = new Client();
        $response = $client->get(env('QUOTE_URL', '') . '?assignee_id=220&status_ids=1,2,6,11', [
          'auth' => [
            env('TICKET_USERNAME', ''),
            env('TICKET_PASSWORD', '')
          ]
        ]);
        $result = json_decode((string)$response->getBody());

        $formatted_quotes = [];
        $status_codes = [];
        $teamwork_codes = [
          1  => 'Not in Teamwork',
          2  => 'Request - Awaiting Quote',
          6  => 'Quote - Client Changes Requested',
          11 => 'Quote - Awaiting Internal Approval'
        ];

        $sla = $this->sla_list();

        foreach ($result->quotes as $quote) {

            $status_codes[$quote->status_id] = $quote->status;

            $sla_days = !empty($sla[$quote->account_ref]) ?
              $sla[$quote->account_ref] :
              3;
            $sla_date = Carbon::createFromTimestamp($quote->created_at)->addWeekDays($sla_days);
            $difference = Carbon::now()->diffInHours($sla_date, false);
            $difference = $difference < 0 ?
              0 :
              $difference;

            $formatted_quotes[$quote->status_id][$quote->created_at] = [
              'subject'     => $quote->subject,
              'description' => $quote->description,
              'teamwork_id' => (int)$quote->teamwork_id,
              'id'          => $quote->id,
              'client'      => $quote->client,
              'sla_left'    => $difference,
            ];
        }
        ksort($formatted_quotes);

        return view('quotes.quote_list', compact([
          'formatted_quotes',
          'status_codes',
          'teamwork_codes',
          'sla'
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function sla_list()
    {
        $sla_list = [
          'COUNTRYH' => 3,
          'CANTERBU' => 7,
          'CANTERNZ' => 7,
          'MITRESPO' => 7,
          'AIRBORNE' => 7,
          'BOXFRESH' => 7,
          'BEAUTYBA' => 3,
          'CRAFTYAR' => 3,
          'IMSFLOOR' => 3,
          'MODAINP'  => 3,
          'BLUEINC'  => 3
        ];
        return $sla_list;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
