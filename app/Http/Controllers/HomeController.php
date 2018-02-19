<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Account;
use App\Models\Ingredients;
use App\Models\Meals;
use App\Models\Schedule;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * @param int $limit
   *
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */
  public function index($limit = 7)
  {
    $accounts = Account::all();
    $account_list = [];
    $hidden_accounts = [];
    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
      if ($account->hidden) {
        $hidden_accounts[] = $account->id;
      }
    }
    $schedules = Schedule::whereNotIn('account_id', $hidden_accounts)->where('payment_date', '>', Carbon::parse('today'))->where('payment_date', '<=', Carbon::parse($limit . ' days'))->where('transfer', 0)->orderBy('payment_date', 'asc')->orderBy('type', 'desc')->get();
    $transactions = Transaction::whereNotIn('account_id', $hidden_accounts)->where('name', 'NOT LIKE', '%->%')->where('name', 'NOT LIKE', '%<-%')->where('payment_date', '>', Carbon::parse('-' . $limit . ' days'))->orderBy('payment_date', 'desc')->get();

    return view('home', compact(['schedules',
                                 'transactions',
                                 'account_list',
                                 'limit']));
  }

}
