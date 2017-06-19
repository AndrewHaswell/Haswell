<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Schedule;
use App\Models\ScheduleUpdate;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class SchedulesController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
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
    $schedule = Schedule::findOrFail($request->schedule_id);
    $update = new ScheduleUpdate();

    $schedule->name = $request->name;
    $update->name = $request->name;

    $schedule->payment_date = $request->payment_date;
    $update->payment_date = $request->payment_date;

    $schedule->type = $request->type;
    $update->type = $request->type;

    $schedule->amount = $request->amount;
    $update->amount = $request->amount;

    $schedule->account_id = $request->account_id;
    $update->account_id = $request->account_id;

    $schedule->save();
    $update->save();
    return Redirect::to(url('/accounts/' . $schedule->account_id));
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
    $schedule = Schedule::findOrfail($id);

    $accounts = Account::all();
    $account_list = [];
    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
    }

    // Show the accounts
    return view('schedules.details', compact(['schedule',
                                              'account_list']));
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
