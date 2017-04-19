<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Account;
use App\Models\Schedule;
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
      $limit = 15;
      $accounts = Account::all();
      $account_list = [];
      foreach ($accounts as $account) {
        $account_list[$account->id] = $account->name;
      }
      $schedules = Schedule::where('payment_date', '>', Carbon::parse('today'))->orderBy('payment_date', 'asc')->orderBy('type', 'desc')->limit($limit)->get();

        return view('home', compact(['schedules','account_list','limit']));
    }
}
