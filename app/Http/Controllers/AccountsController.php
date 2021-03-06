<?php

namespace App\Http\Controllers;

use App\Models\Additional;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;
use App\Models\Account;
use App\Models\Payment;
use Carbon\Carbon;
use App\Models\Schedule;
use DateTime;
use DatePeriod;
use DateInterval;

class AccountsController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function all_accounts()
  {
    return $this->index(false, false);
  }

  public function hidden_accounts()
  {
    return $this->index(true, false);
  }

  /**
   * @param bool $hidden
   * @param bool $empty
   *
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */

  public function index($hidden = false, $empty = true)
  {

    if (!Auth::check()) {
      echo 'Not logged in';
    }

    if ($hidden) {
      $empty = false;
    }

    // Get our accounts
    $accounts = Account::orderBy('type', 'asc')->where('active','=', true)->where('hidden', '=', $hidden)->orderBy('name', 'asc')->get();

    $title = 'Accounts';

    // Update the balance based on current balance and transaction since that point
    foreach ($accounts as &$account) {
      $account = $this->get_current_balance($account, false);
    }

    $months = [];

    // Work out our next however many months
    $begin = new DateTime();
    $end = new DateTime();

    $start_time = strtotime('+1 months');
    $begin->setTimestamp($start_time);
    $end_time = strtotime('+12 months');
    $end->setTimestamp($end_time);

    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($begin, $interval, $end);

    foreach ($period as $dt) {
      $months[$dt->format('n')] = $dt->format('F');
    }

    // Show the accounts
    return view('accounts.test', compact(['accounts',
                                          'title',
                                          'months',
                                          'hidden',
                                          'empty']));
  }

  /**
   * @param $id
   *
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */
  public function detail($id)
  {
    $account = Account::findOrFail($id);
    $future_account = clone $account;
    $title = 'Accounts - ' . $account->name;
    $account = $this->get_current_balance($account, false);
    $transactions = $account->transactions()->orderBy('payment_date', 'desc')->orderBy('type', 'desc')->orderBy('name', 'asc')->get();
    $end_of_month = Carbon::parse('+30 days');
    $schedules = $account->schedules()->where('payment_date', '<=', $end_of_month)->where('payment_date', '>', Carbon::parse('today'))->orderBy('payment_date', 'desc')->orderBy('type', 'desc')->orderBy('name', 'asc')->get();
    $future_balance = $this->get_future_balance($future_account, $end_of_month)->balance;

    $categories = Category::all();
    $category_list = [];
    foreach ($categories as $category) {
      $category_list[$category->id] = $category->title;
    }

    return view('accounts.account', compact(['account',
                                             'schedules',
                                             'transactions',
                                             'title',
                                             'category_list',
                                             'end_of_month',
                                             'future_balance']));
  }

  public function future($id, $month)
  {
    $dt = Carbon::createFromFormat('!m', $month);
    $this_month = $dt->format('F');
    $date = new Carbon('last day of ' . $this_month . ' ' . date('Y'));

    if ($month <= date('n')) {
      $date->addYear(1);
    }

    $account = Account::findOrFail($id);
    $account = $this->get_future_balance($account, $date);
    $transactions = $account->transactions()->orderBy('payment_date', 'desc')->orderBy('type', 'desc')->orderBy('name', 'asc')->get();
    $schedules = $account->schedules()->where('payment_date', '<=', $date)->where('payment_date', '>', Carbon::parse('today'))->orderBy('payment_date', 'desc')->orderBy('type', 'desc')->orderBy('name', 'asc')->get();
    return view('accounts.account_future', compact('account', 'transactions', 'schedules', 'date'));
  }

  /**
   * @author Andrew Haswell
   */

  public function balance()
  {
    $this->get_accounts();
  }

  /**
   * @author Andrew Haswell
   */

  public function get_accounts()
  {
    $accounts = Account::all();
    foreach ($accounts as $account) {
      $account = $this->get_current_balance($account, false);
      dump($account);
    }
  }

  /**
   * @param $account
   *
   * @return mixed
   * @author Andrew Haswell
   */

  public function get_current_balance($account, $confirmed_only = true)
  {
    $account->confirmed_balance = $account->balance;
    $transactions = $account->transactions()->where('payment_date', '>=', $account->balance_date)->get();
    foreach ($transactions as $transaction) {

      if ($transaction->type == 'debit') {
        $account->balance -= $transaction->amount;
        if ($transaction->confirmed) {
          $account->confirmed_balance -= $transaction->amount;
        }
      } else {
        $account->balance += $transaction->amount;
        if ($transaction->confirmed) {
          $account->confirmed_balance += $transaction->amount;
        }
      }
    }

      $account->confirmed_balance = round($account->confirmed_balance,2);
      $account->balance = round($account->balance,2);

    return $account;
  }

  /**
   * @param $account
   * @param $date
   *
   * @return mixed
   * @author Andrew Haswell
   */

  public function get_future_balance($account, $date)
  {
    $transactions = $account->transactions()->where('payment_date', '>=', $account->balance_date)->get();
    foreach ($transactions as $transaction) {
      if ($transaction->type == 'debit') {
        $account->balance -= $transaction->amount;
      } else {
        $account->balance += $transaction->amount;
      }
    }

    //dump($account);

    $schedules = $account->schedules()->where('payment_date', '<=', $date)->where('payment_date', '>', Carbon::parse('today'))->orderBy('payment_date', 'asc')->get();

    foreach ($schedules as $schedule) {

      if ($schedule->type == 'debit') {
        $account->balance -= $schedule->amount;
      } else {
        $account->balance += $schedule->amount;
      }
    }

    return $account;
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
