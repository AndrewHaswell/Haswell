<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;

class TransactionsController extends Controller
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
  public function detail($id)
  {
    $transaction = Transaction::findOrfail($id);

    $accounts = Account::orderBy('type')->orderBy('name')->get();
    $account_list = [];
    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
    }

    // Show the accounts
    return view('transactions.create', compact(['transaction',
                                                 'account_list']));
  }

  /**
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */

  public function index()
  {
    $accounts = Account::orderBy('type')->where('active', '=', true)->where('hidden', '=', false)->orderBy('name')->get();
    $account_list = [];
    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
    }
    $title = 'Transactions';
    $transaction = new \stdClass();
    return view('transactions.create', compact(['transaction', 'account_list',
                                                'title']));
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
    $transfer_id = 0;
    if (!empty($request->transaction_id)) {
      $transaction = Transaction::findOrFail($request->transaction_id);

      // Are we linked?
      $linked = Transfer::where('from_id', $request->transaction_id)->get();
      if ($linked->count()) {
        $transfer_id = $linked[0]->to_id;
      } else {
        $linked = Transfer::where('to_id', $request->transaction_id)->get();
        if ($linked->count()) {
          $transfer_id = $linked[0]->from_id;
        }
      }

      if (!empty($request->delete) && $request->delete == 'delete') {
        $transaction->delete();
        // Also delete any linked transaction
        if (!empty($transfer_id)) {
          $transaction = Transaction::findOrFail($transfer_id);
          $transaction->delete();
        }
        return Redirect::to(url('/accounts/' . $transaction->account_id));
      }
    } else {
      $transaction = new Transaction();
    }

    // Are we a transfer?
    if (!empty($request->transfer)) {
      $transfer_from = Account::findOrFail($request->account_id);
      $transfer_to = Account::findOrFail($request->transfer);
      $transfer_to_name = $request->name . ' -> ' . $transfer_to->name;
      $transfer_from_name = $request->name . ' <- ' . $transfer_from->name;
      $transfer = 1;
    } else {
      $transfer = 0;
    }

    $transaction->account_id = $request->account_id;
    $transaction->name = (!empty($transfer_to_name) ?
      $transfer_to_name :
      $request->name);
    $transaction->payment_date = $request->payment_date;
    $transaction->type = $request->type;
    $transaction->transfer = $transfer;
    $transaction->amount = $request->amount;
    if (!empty($request->category_id) && $transfer == 0) {
      $transaction->category_id = $request->category_id;
    }
    $transaction->confirmed = $request->confirmed;

    $transaction->save();

    if ($transfer_id) {
      $transfer_transaction = Transaction::findOrFail($transfer_id);
      $transfer_transaction->amount = $request->amount;
      $transfer_transaction->save();
    }

    // If it's a transfer we'll make the opposite transaction as well
    if (!empty($request->transfer)) {
      $transfer_to_id = $transaction->id;
      $transaction = $transaction->replicate();
      $transaction->name = $transfer_from_name;
      $transaction->account_id = $request->transfer;
      $transaction->transfer = $transfer;
      $transaction->type = ($request->type == 'credit'
        ? 'debit'
        : 'credit');
      $transaction->save();
      $transfer_from_id = $transaction->id;

      // Save to our transfer table both ways
      $transfer = new Transfer();
      $transfer->from_id = $transfer_from_id;
      $transfer->to_id = $transfer_to_id;
      $transfer->save();
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
