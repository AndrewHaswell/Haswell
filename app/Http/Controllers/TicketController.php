<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

use App\Http\Requests;

class TicketController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $client = new Client();

    $client_list = [32303105,
                    22804471,
                    490384,
                    22347236,
                    24652416,
                    23798527,
                    21216018];

    $formatted_tickets = [];

    $stats = [0       => 0,
              1       => 0,
              2       => 0,
              3       => 0,
              'total' => 0];

    $sort_level = ['Urgent' => 0,
                   'High'   => 1,
                   'Normal' => 2,
                   'Low'    => 3];

    foreach ($client_list as $client_id) {

      $response = $client->get(env('TICKET_URL', '') . '?organisation_id=' . $client_id . '&status_id=open', ['auth' => [env('TICKET_USERNAME', ''),
                                                                                                                         env('TICKET_PASSWORD', '')]]);

      $result = json_decode((string)$response->getBody());

      foreach ($result->tickets as $ticket) {


        if ($ticket->status == 'Open') {

          if (empty($ticket->assignee)) {
            $ticket->assignee = 'Unassigned';
          }
          $stats[$sort_level[$ticket->priority]]++;
          $stats['total']++;

          $formatted_tickets[$ticket->organisation][$sort_level[$ticket->priority]][$ticket->created_at][$ticket->id] = ['subject'     => $ticket->subject,
                                                                                                                         'assignee'    => $ticket->assignee,
                                                                                                                         'description' => $ticket->description,
                                                                                                                         'updated_at'  => $ticket->updated_at,];
        }
      }
    }

    ksort($formatted_tickets);
    $sort_level = array_flip($sort_level);
    $link = env('SUPPORT_TICKET_URL', '');
    return view('tickets.ticket_list', compact(['formatted_tickets',
                                                'link',
                                                'sort_level',
                                                'stats']));
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

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  int                      $id
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
