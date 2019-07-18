<?php

namespace App\Http\Controllers;

use App\Models\BudgetMain;
use App\Models\Payment;
use Illuminate\Http\Request;

use App\Http\Requests;

class BudgetController extends Controller
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
    $main_categories = BudgetMain::all();
    $mortgage_ratio = 0.133603238866397;

    $incoming_payments = Payment::where('type', '=', 'credit')->get();
    $incoming = 0;

    foreach ($incoming_payments as $incoming_payment) {
      $incoming += $incoming_payment->amount;
    }
    $incoming = floor($incoming);

    // Category updates

      $cat_updates = [];

      $updates = Payment::where('budget_id', '>', '0')->get();

      foreach ($updates as $update) {

          if ($update->name == env('MORTGAGE_NAME', '')) {
              $loan = $update->amount * $mortgage_ratio;
              $mortgage = (float)$update->amount - $loan;
              $cat_updates[2] = $mortgage;
              $cat_updates[3] = $loan;
          } else if ($update->name == env('ELECTRIC_NAME', '')) {
              $cat_updates[8] = $update->amount * 0.5;
              $cat_updates[9] = $update->amount * 0.5;
          } else if ($update->name == env('COUNCIL_TAX_NAME', '')) {
              $amount = ($update->amount * 10) / 12;
              $cat_updates[7] = $amount;
          } else {
              $amount = $update->interval == '1 week' ?
                ($update->amount * 52) / 12 :
                $update->amount;
              $cat_updates[$update['budget_id']] = $amount;
          }
      }

      array_walk($cat_updates, function (&$v) {
          $v = ceil($v);
      });

    return view('budget.details', compact(['main_categories',
                                           'cat_updates',
                                           'incoming']));
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
