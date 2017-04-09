<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class TransactionsController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function detail($id)
  {
    $transaction = Transaction::findOrfail($id);

    $accounts = Account::all();
    $account_list = [];
    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
    }

    // Show the accounts
    return view('transactions.details', compact(['transaction',
                                                 'account_list']));
  }

  /**
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */

  public function index()
  {
    $accounts = Account::all();
    $account_list = [];
    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
    }

    return view('transactions.create', compact('account_list'));
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
    if (!empty($request->transaction_id)) {
      $transaction = Transaction::findOrFail($request->transaction_id);
    } else {
      $transaction = new Transaction();
    }

    // Are we a transfer?
    if (!empty($request->transfer)) {
      $transfer_from = Account::findOrFail($request->account_id);
      $transfer_to = Account::findOrFail($request->transfer);
      $transfer_to_name = 'Transferred to ' . $transfer_to->name ;
      $transfer_from_name = 'Transferred from ' . $transfer_from->name;
    }

    $transaction->account_id = $request->account_id;
    $transaction->name = (!empty($transfer_to_name)
      ? $transfer_to_name
      : $request->name);
    $transaction->payment_date = $request->payment_date;
    $transaction->type = $request->type;
    $transaction->amount = $request->amount;
    $transaction->confirmed = $request->confirmed;

    $transaction->save();

    // If it's a transfer we'll make the opposite transaction as well
    if (!empty($request->transfer)) {
      $transaction = $transaction->replicate();
      $transaction->name = $transfer_from_name;
      $transaction->account_id = $request->transfer;
      $transaction->type = ($request->type == 'credit'
        ? 'debit'
        : 'credit');
      $transaction->save();
    }

    return Redirect::to(url('/accounts/' . $transaction->account_id));
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
