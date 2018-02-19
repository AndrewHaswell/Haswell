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
    $response = $client->get(env('TICKET_URL', '') . '?organisation_id=22804471&status_id=open', ['auth' => [env('TICKET_USERNAME', ''),
                                                                                                             env('TICKET_PASSWORD', '')]]);
    $result = json_decode((string)$response->getBody());
    $formatted_tickets = [];
    foreach ($result->tickets as $ticket) {

      if ($ticket->status == 'Open')
      $formatted_tickets[$ticket->priority][$ticket->assignee][$ticket->id] = ['subject'     => $ticket->subject,
                                                                                                'description' => $ticket->description,
                                                                                                'assigned_at' => $ticket->assigned_at,];
    }
    return view('tickets.ticket_list', compact(['formatted_tickets']));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
