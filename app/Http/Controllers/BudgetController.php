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
    $incoming = 50; // Cos of the extra bit overtime

    foreach ($incoming_payments as $incoming_payment) {
      $incoming += $incoming_payment->amount;
    }

    $incoming = floor($incoming);

    // Category updates

    $cat_updates = [];

    // First ID is budget ID, second is payment ID
    $updates = ['pocket_money' => [42,
                                   71],
                'mortgage'     => [[2,
                                    3],
                                   3],
                'water'        => [6,
                                   34],];

    // Pocket Money - 42
    foreach ($updates as $name => $ids) {
      $this_payment = Payment::findOrFail($ids[1]);
      if ($name == 'mortgage') {
        $amount = $this_payment->amount * $mortgage_ratio;
        $cat_updates[3] = $amount;
        $cat_updates[2] = $this_payment->amount - $cat_updates[3];
      } else {
        if ($this_payment->interval == '1 week') {
          $amount = ($this_payment->amount * 52) / 12;
        } else {
          $amount = $this_payment->amount;
        }
        $cat_updates[$ids[0]] = $amount;
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