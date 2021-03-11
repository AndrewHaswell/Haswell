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
        $csv = '10 Mar 21,DWPCMSGB2012,Giro,,£29.12,
10 Mar 21,"Card 14, Yorkshire Trading Comp DRIFFIELD",Card,,−£1.00,
10 Mar 21,"Card 14, Go North East Limited GATESHEAD",Card,,−£1.00,
10 Mar 21,"Card 14, Greggs WASHINGTON",Card,,−£1.60,
10 Mar 21,"Card 14, Sainsburys Sacat 0555 WASHINGTON",Card,,−£3.00,
10 Mar 21,"Card 14, Poundland Ltd WILLENHALL",Card,,−£3.00,
10 Mar 21,"Card 14, Asda Superstore WASHINGTON",Card,,−£5.50,
10 Mar 21,"Card 14, Subway 34279 Galleries Washington",Card,,−£7.68,
10 Mar 21,"Card 14, Asda Superstore WASHINGTON",Card,,−£13.50,
10 Mar 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£37.85,£303.66
10 Mar 21,"WLT 14, Wilko Retail Limited WASHINGTON",Card,Other,−£10.75,£341.51
10 Mar 21,"Card 14, Subway 34279 Galleries Washington",Card,Eating out,−£7.68,£352.26
10 Mar 21,"WLT 14, Poundland Ltd WILLENHALL",Card,Other,−£7.00,£359.94
10 Mar 21,"WLT 14, Poundland Ltd WILLENHALL",Card,Other,−£5.00,£366.94
10 Mar 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£3.05,£371.94
09 Mar 21,"WLT 14, Iceland WASHINGTON",Card,Groceries,−£7.25,£374.99
09 Mar 21,"WLT 14, B&M 688 Washington WASHINGTON",Card,Other,−£2.84,£382.24
08 Mar 21,"Card 14, B&M 688 Washington WASHINGTON",Card,Other,−£53.90,£385.08
08 Mar 21,"WLT 14, Wilko Retail Limited WASHINGTON",Card,Other,−£34.50,£438.98
08 Mar 21,"WLT 14, Aldi 29 774 WASHINGTON",Card,Groceries,−£32.31,£473.48
08 Mar 21,Paypal Payment,Direct Debit,,−£27.83,£505.79
08 Mar 21,"WLT 14, Kfc WASHINGTON",Card,Eating out,−£25.78,£533.62
08 Mar 21,"Card 14, Amznmktplace amazon.co.uk",Card,Other,−£24.99,£559.40
08 Mar 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£20.95,£584.39
08 Mar 21,"Card 14, Amazon.Co.Uk*Mm3Sh4B34 AMAZON.CO.UK",Card,Other,−£17.99,£605.34
08 Mar 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£12.12,£623.33
08 Mar 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£9.08,£635.45
08 Mar 21,"WLT 14, Poundland Ltd WILLENHALL",Card,Other,−£7.00,£644.53
08 Mar 21,"CLS 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.00,£651.53
05 Mar 21,"Card 14, Www.Takeaway.Je 01534876163",Card,Eating out,−£21.65,£653.53
05 Mar 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.75,£675.18
05 Mar 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£1.95,£677.93
04 Mar 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£29.08,£679.88
04 Mar 21,"WLT 14, Sainsburys Sacat 0555 WASHINGTON",Card,Groceries,−£14.00,£708.96
04 Mar 21,"WLT 14, Subway34279 WASHINGTON",Card,Eating out,−£7.68,£722.96
04 Mar 21,"WLT 14, Wilko Retail Limited WASHINGTON",Card,Other,−£4.00,£730.64
04 Mar 21,"WLT 14, Greggs WASHINGTON",Card,Eating out,−£3.85,£734.64
03 Mar 21,"Card 14, Ikea Ltd Shop Online LONDON",Card,Home,−£144.00,£738.49
03 Mar 21,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,−£8.99,£882.49
03 Mar 21,"Card 14, Amznmktplace amazon.co.uk",Card,Other,−£7.40,£891.48
03 Mar 21,Paypal Payment,Direct Debit,,−£3.99,£898.88
03 Mar 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£3.05,£902.87
03 Mar 21,DWPCMSGB2012SCHEME 710031041816,Giro,Earnings,£13.44,£905.92
02 Mar 21,"Card 14, Amznmktplace amazon.co.uk",Card,Other,−£20.99,£892.48
02 Mar 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£10.00,£913.47
02 Mar 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£8.56,£923.47
02 Mar 21,"WLT 14, Aldi 29 774 WASHINGTON",Card,Groceries,−£5.32,£932.03
02 Mar 21,"WLT 14, Poundland Ltd WILLENHALL",Card,Other,−£5.00,£937.35
02 Mar 21,"Card 14, Paypal *Smarty 35314369001",Card,Utilities,−£4.62,£942.35
02 Mar 21,"WLT 14, Home Bargains WASHINGTON",Card,Other,−£1.48,£946.97
01 Mar 21,"MOB, Andrew Haswell, Bills",Transfer,,"−£1,100.00",£948.45
01 Mar 21,"Card 14, Asda Superstore WASHINGTON",Card,Groceries,−£78.85,"£2,048.45"
01 Mar 21,"Card 14, Lidl Gb Washington WASHINGTON",Card,Groceries,−£73.83,"£2,127.30"
01 Mar 21,"WLT 14, Iceland WASHINGTON",Card,Groceries,−£33.67,"£2,201.13"
01 Mar 21,"WLT 14, B&M 688 Washington WASHINGTON",Card,Other,−£30.98,"£2,234.80"
01 Mar 21,Dvla-Yr55Wyu,Direct Debit,Getting around,−£28.87,"£2,265.78"
01 Mar 21,"WLT 14, Boots/0573 WASHINGTON",Card,Wellbeing,−£23.99,"£2,294.65"
01 Mar 21,"Card 14, Argos Ltd INTERNET",Card,Other,−£19.99,"£2,318.64"
01 Mar 21,"WLT 14, Poundland Ltd WILLENHALL",Card,Other,−£17.50,"£2,338.63"
01 Mar 21,"CLS 14, Poundland Ltd WILLENHALL",Card,Other,−£17.10,"£2,356.13"
01 Mar 21,"WLT 14, Subway34279 WASHINGTON",Card,Eating out,−£14.77,"£2,373.23"
01 Mar 21,"WLT 14, Sainsburys Sacat 0555 WASHINGTON",Card,Groceries,−£13.30,"£2,388.00"
01 Mar 21,"WLT 14, Wilko Retail Limited WASHINGTON",Card,Other,−£12.45,"£2,401.30"
01 Mar 21,"WLT 14, Yorkshire Trading Comp DRIFFIELD",Card,Other,−£11.24,"£2,413.75"
01 Mar 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£9.78,"£2,424.99"
01 Mar 21,"Card 14, Audible Uk adbl.co/pymt",Card,Other,−£7.99,"£2,434.77"
01 Mar 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£7.75,"£2,442.76"
01 Mar 21,"WLT 14, Iceland WASHINGTON",Card,Groceries,−£7.25,"£2,450.51"
01 Mar 21,"Card 14, Giffgaff London",Card,Utilities,−£6.00,"£2,457.76"
01 Mar 21,"Card 14, Amazon.Co.Uk*Mm2Z46E14 AMAZON.CO.UK",Card,Other,−£6.00,"£2,463.76"
01 Mar 21,"Card 14, Giffgaff London",Card,Utilities,−£6.00,"£2,469.76"
01 Mar 21,Paypal Payment,Direct Debit,,−£4.00,"£2,475.76"
01 Mar 21,"WLT 14, Greggs WASHINGTON",Card,Eating out,−£1.00,"£2,479.76"
26 Feb 21,GROSS INTEREST,Other,,£0.76,"£2,480.76"
26 Feb 21,"WLT 14, Dis Diner WASHINGTON",Card,Eating out,−£21.50,"£2,480.00"
26 Feb 21,Paypal Payment,Direct Debit,,−£3.19,"£2,501.50"
26 Feb 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.50,"£2,504.69"
26 Feb 21,VISUALSOFT LIMITED VISUALSOFT,Giro,Earnings,"£2,473.88","£2,507.19"
24 Feb 21,Esure Motor Ins Dr,Direct Debit,Finance,−£29.54,£33.31
24 Feb 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£6.62,£62.85
24 Feb 21,"FPS, S Brugman",Transfer,,£50.00,£69.47
23 Feb 21,"CLS 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.58,£19.47
22 Feb 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£23.35,£22.05
22 Feb 21,"WLT 14, Dis Diner WASHINGTON",Card,Eating out,−£22.50,£45.40
22 Feb 21,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,−£13.59,£67.90
22 Feb 21,"WLT 14, Poundland Ltd WILLENHALL",Card,Other,−£11.00,£81.49
22 Feb 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£10.79,£92.49
22 Feb 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£6.95,£103.28
22 Feb 21,"Card 14, Amazon.Co.Uk*Mf7Qm6Ir4 AMAZON.CO.UK",Card,Other,−£6.00,£110.23
22 Feb 21,"Card 14, Amznfreetime 353-12477661",Card,Entertainment,−£4.99,£116.23
22 Feb 21,"Card 14, Audible Uk adbl.co/pymt",Card,Other,−£2.99,£121.22';

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
