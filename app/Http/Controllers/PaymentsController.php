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

    public function import()
    {
        $categories = [];
        $csv = '08 Dec 20,"Card 14, Poundstretcher 206 WASHINGTON BA",Card,,−£2.98,
07 Dec 20,"Card 14, Fenham Stores Ltd - Wa WASHINGTON",Card,,−£4.03,
07 Dec 20,"Card 14, Dplay Entertainment Li London",Card,,−£4.99,
07 Dec 20,"Card 14, Fenham Stores Ltd - Wa WASHINGTON",Card,,−£5.25,
07 Dec 20,"Card 14, Asda Superstore WASHINGTON",Card,Groceries,−£48.90,£796.60
07 Dec 20,"WLT 14, Sainsburys Sacat 0555 WASHINGTON",Card,Groceries,−£36.00,£845.50
07 Dec 20,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£22.47,£881.50
07 Dec 20,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£21.50,£903.97
07 Dec 20,"WLT 14, Wilko Retail Limited WASHINGTON",Card,Other,−£11.25,£925.47
07 Dec 20,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,−£5.52,£936.72
07 Dec 20,"WLT 14, B & Q 1203 WASHINGTON",Card,Home,−£4.70,£942.24
07 Dec 20,"Card 14, Asda George Com Leeds LEEDS",Card,Groceries,£45.90,£946.94
07 Dec 20,"Card 14, Asda George Com Leeds LEEDS",Card,Groceries,£11.05,£901.04
04 Dec 20,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£6.06,£889.99
03 Dec 20,"WLT 14, Lidl Gb Washington WASHINGTON",Card,Groceries,−£40.36,£896.05
03 Dec 20,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£19.75,£936.41
03 Dec 20,"WLT 14, B&M 688 Washington WASHINGTON",Card,Other,−£16.98,£956.16
03 Dec 20,"WLT 14, Iceland WASHINGTON",Card,Groceries,−£12.00,£973.14
02 Dec 20,"Card 14, Amazon.Co.Uk*My5M90Qg4 AMAZON.CO.UK",Card,Other,−£23.14,£985.14
02 Dec 20,"Card 14, Amznmktplace amazon.co.uk",Card,Other,−£14.99,"£1,008.28"
02 Dec 20,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£9.66,"£1,023.27"
02 Dec 20,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£1.58,"£1,032.93"
01 Dec 20,"MOB, Andrew Haswell, Bills",Transfer,,"−£1,180.00","£1,034.51"
01 Dec 20,Dvla-Yr55Wyu,Direct Debit,Getting around,−£28.87,"£2,214.51"';

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
                // Do nothing - we have details. Maybe we do an update on dates or something later?
            } else {
                $add = new Transaction();
                $add->account_id = 22;
                $add->name = $transaction[1];
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
        //
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
        //
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
