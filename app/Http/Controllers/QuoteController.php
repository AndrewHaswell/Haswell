<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

use App\Http\Requests;

class QuoteController extends Controller
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
    $response = $client->get(env('QUOTE_URL', '') . '?assignee_id=220&status_ids=1,2,6,11', ['auth' => [env('TICKET_USERNAME', ''),
                                                                                                       env('TICKET_PASSWORD', '')]]);
    $result = json_decode((string)$response->getBody());

    $formatted_quotes = [];
    $status_codes = [];
    $teamwork_codes = [1  => 'Not in Teamwork',
                       2  => 'Request - Awaiting Quote',
                       6  => 'Quote - Client Changes Requested',
                       11 => 'Quote - Awaiting Internal Approval'];

    $sla = $this->sla_list();

    foreach ($result->quotes as $quote) {

      $status_codes[$quote->status_id] = $quote->status;

      $sla_days = !empty($sla[$quote->account_ref])
        ? $sla[$quote->account_ref]
        : 3;
      $sla_date = Carbon::createFromTimestamp($quote->created_at)->addWeekDays($sla_days);
      $difference = Carbon::now()->diffInHours($sla_date, false);
      $difference = $difference < 0
        ? 0
        : $difference;

      $formatted_quotes[$quote->status_id][$quote->created_at] = ['subject'     => $quote->subject,
                                                                  'description' => $quote->description,
                                                                  'teamwork_id' => (int)$quote->teamwork_id,
                                                                  'id'          => $quote->id,
                                                                  'client'      => $quote->client,
                                                                  'sla_left'    => $difference,];
    }
    ksort($formatted_quotes);

    return view('quotes.quote_list', compact(['formatted_quotes',
                                              'status_codes',
                                              'teamwork_codes',
                                              'sla']));
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
    $sla_list = ['COUNTRYH' => 3,
                 'CANTERBU' => 7,
                 'CANTERNZ' => 7,
                 'MITRESPO' => 7,
                 'AIRBORNE' => 7,
                 'BOXFRESH' => 7,
                 'BEAUTYBA' => 3,
                 'CRAFTYAR' => 3,
                 'IMSFLOOR' => 3,
                 'MODAINP'  => 3,
                 'BLUEINC'  => 3];
    return $sla_list;
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
