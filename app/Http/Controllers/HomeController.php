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
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $limit = 10;
    $accounts = Account::all();
    $account_list = [];
    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
    }
    $schedules = Schedule::where('payment_date', '>', Carbon::parse('today'))->where('transfer', 0)->orderBy('payment_date', 'asc')->orderBy('type', 'desc')->limit($limit)->get();

    $transactions = Transaction::where('transfer', 0)->orderBy('payment_date', 'desc')->limit(($limit * 4))->get();

    return view('home', compact(['schedules',
                                 'transactions',
                                 'account_list',
                                 'limit']));
  }

  public function meal_test()
  {

    $meal = Meals::find(1);

    $meal->ingredients()->attach([1,
                                  2,
                                  3]);
  }
}
