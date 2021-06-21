<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Payment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
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
        // Remove out of date payments
        Payment::where('end_date', '<', Carbon::parse('today'))->where('end_date', '!=', '')->delete();

        $accounts = Account::orderBy('type')->orderBy('name')->get();
        $account_list = [];
        foreach ($accounts as $account) {
            $account_list[$account->id] = $account->name;
        }

        $payments = Payment::orderBy('name', 'asc')->orderBy('interval', 'desc')->get();
        return view('payments.payments', compact([
          'payments',
          'account_list'
        ]));
    }

    public function import($csv)
    {
        $categories = [];

        $import = array_reverse(explode("\n", trim($csv)));
        DB::enableQueryLog();

        $categories = [
          'Groceries'     => 4,
          'Eating out'    => 2,
          'Style'         => 26,
          'Fuel'          => 5,
          'Entertainment' => 8,
          'Earnings'      => 18,
          'Utilities'     => 38,
        ];

        foreach ($import as $transaction) {

            $transaction = str_getcsv($transaction);

            $transactionDate = Carbon::parse($transaction[0]);

            $amount = str_replace('£', '', $transaction[4]);
            $amount = str_replace(',', '', $amount);
            $amount = (float)str_replace('−', '-', $amount);

            $type = $amount < 0 ?
              'debit' :
              'credit';
            $amount = number_format(abs($amount), 2, '.', '');

            $category_id = !empty($categories[$transaction[3]]) ?
              $categories[$transaction[3]] :
              0;

            $payment = Transaction::where('type', '=', $type)->where('amount', '=', $amount)->where('payment_date',
              '>=', $transactionDate->subDays(2)->toIso8601String())->where('payment_date', '<=',
              $transactionDate->addDays(4)->toIso8601String())->get();

            if ($payment->count() > 0) {
                /* Debugging for Andy Haswell (21/06/2021) */
                dump('DEBUG (' . date('H:i:s') . ') - ' . __NAMESPACE__ . '::' . __FUNCTION__ . '() #' . __LINE__);
                dump('Duplicate transaction - skipping:');
                dump($transaction);
                dump($payment);
                /* End of Debugging */
            } else {
                $name = preg_replace('/^(WLT|Card|CLS)+\s[0-9]{2},\s/Ui', '', $transaction[1]);
                $add = new Transaction();
                $add->account_id = 22;
                $add->name = trim($name);
                $add->payment_date = Carbon::parse($transaction[0])->toIso8601String();
                $add->type = $type;
                $add->amount = $amount;
                $add->category_id = $category_id;
                $add->confirmed = true;
                $add->save();

                echo '<h3>Added</h3>';
                dump($add);
            }
        }

        dd('Done');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('payments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->import(trim($request->statement));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = Payment::findOrfail($id);
        $accounts = Account::all();
        $account_list = [];
        foreach ($accounts as $account) {
            $account_list[$account->id] = $account->name;
        }

        // Show the accounts
        return view('payments.details', compact([
          'payment',
          'account_list'
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
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
     * @param \Illuminate\Http\Request $request
     * @param int $id
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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
