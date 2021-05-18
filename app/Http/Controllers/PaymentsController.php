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
        $csv = '17 May 21,"Card 14, Sainsburys Opt 0045 WASHINGTON",Card,,−£1.00,
17 May 21,"Card 14, Fenham Stores Ltd - Wa WASHINGTON",Card,,−£5.25,
17 May 21,"Card 14, Asda Superstore WASHINGTON",Card,,−£19.04,
17 May 21,"Card 14, Waylandgames SOUTHEND",Card,,−£57.65,
17 May 21,"WLT 14, Dis Diner WASHINGTON",Card,Eating out,−£19.95,£145.85
17 May 21,"Card 14, Www.Takeaway.Je 01534876163",Card,Eating out,−£19.49,£165.80
17 May 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£15.43,£185.29
17 May 21,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,−£13.78,£200.72
17 May 21,"CLS 21, Lloyds Pharmacy WASHINGTON",Card,Wellbeing,−£9.35,£214.50
17 May 21,"Card 14, Amznmktplace amazon.co.uk",Card,Other,−£6.99,£223.85
17 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£5.00,£230.84
17 May 21,"Card 14, Shudder-855-744-1217 NEW YORK CITY",Card,Entertainment,−£4.99,£235.84
17 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.99,£240.83
17 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.94,£243.82
14 May 21,"WLT 14, Lidl Gb Washington WASHINGTON",Card,Groceries,−£70.25,£246.76
14 May 21,"WLT 14, Iceland WASHINGTON",Card,Groceries,−£22.50,£317.01
14 May 21,"WLT 14, B&M 688 Washington WASHINGTON",Card,Other,−£18.89,£339.51
14 May 21,"Card 14, Just Eat London",Card,Eating out,−£14.17,£358.40
13 May 21,"WLT 14, Dis Diner WASHINGTON",Card,Eating out,−£9.45,£372.57
13 May 21,Paypal Payment,Direct Debit,,−£6.99,£382.02
13 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£5.50,£389.01
12 May 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£20.55,£394.51
12 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£1.50,£415.06
12 May 21,"CLS 21, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£1.50,£416.56
12 May 21,DWPCMSGB2012SCHEME 710031041816,Giro,Earnings,£42.58,£418.06
11 May 21,"CLS 21, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.38,£375.48
10 May 21,"WLT 14, Lidl Gb Washington WASHINGTON",Card,Groceries,−£58.99,£377.86
10 May 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£25.98,£436.85
10 May 21,"WLT 14, Iceland WASHINGTON",Card,Groceries,−£25.50,£462.83
10 May 21,"Card 14, Just Eat London",Card,Eating out,−£21.56,£488.33
10 May 21,"WLT 14, B&M 688 Washington WASHINGTON",Card,Other,−£14.03,£509.89
10 May 21,"Card 14, Amznmktplace amazon.co.uk",Card,Other,−£9.99,£523.92
10 May 21,"Card 14, Audible Uk adbl.co/pymt",Card,Other,−£1.99,£533.91
07 May 21,"Card 14, Sp * Dipped Uk Ltd BOURNEMOUTH",Card,Wellbeing,−£57.36,£535.90
07 May 21,"Card 14, Pizza Box HOUGHTON LE S",Card,Eating out,−£12.60,£593.26
07 May 21,"Card 14, Ayp Healthcare PRESTON",Card,Wellbeing,−£11.43,£605.86
07 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£11.00,£617.29
07 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£4.50,£628.29
07 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£3.50,£632.79
07 May 21,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,£12.99,£636.29
07 May 21,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,£9.99,£623.30
06 May 21,"Card 14, Just Eat.Co.Uk Ltd London",Card,Eating out,−£23.04,£613.31
06 May 21,"WLT 14, Sainsburys Sacat 0555 WASHINGTON",Card,Groceries,−£12.50,£636.35
06 May 21,"Card 14, Pizza Box HOUGHTON LE S",Card,Eating out,−£12.40,£648.85
06 May 21,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,−£9.98,£661.25
06 May 21,"WLT 14, Subway 34279 Galleries Washington",Card,Eating out,−£8.98,£671.23
06 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£5.25,£680.21
05 May 21,"WLT 14, Go North East Limited GATESHEAD",Card,Holidays,−£7.80,£685.46
05 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£7.08,£693.26
05 May 21,"WLT 14, Acropolis Street Food Newcastle upo",Card,Eating out,−£7.00,£700.34
05 May 21,"CLS 21, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£6.98,£707.34
05 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£6.05,£714.32
05 May 21,"WLT 14, Mcdonalds NEWCASTLE UPO",Card,Eating out,−£4.79,£720.37
05 May 21,"Card 14, Poundland TYNE AND WEAR",Card,Other,−£2.00,£725.16
05 May 21,Paypal Payment,Direct Debit,,−£1.58,£727.16
04 May 21,"MOB, Andrew Haswell, Luxplus",Transfer,,"−£1,000.00",£728.74
04 May 21,"WLT 14, As Ltd NEWCASTLE",Card,Style,−£68.00,"£1,728.74"
04 May 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£61.29,"£1,796.74"
04 May 21,"WLT 14, Iz *Esclavage Ltd Newcastle upo",Card,Other,−£55.00,"£1,858.03"
04 May 21,"WLT 14, Wilko Retail Limited WASHINGTON",Card,Other,−£40.00,"£1,913.03"
04 May 21,Dvla-Yr55Wyu,Direct Debit,Getting around,−£28.87,"£1,953.03"
04 May 21,"WLT 14, Primark NEWCASTLE UPO",Card,Style,−£26.00,"£1,981.90"
04 May 21,"WLT 14, Kfc WASHINGTON",Card,Eating out,−£24.99,"£2,007.90"
04 May 21,"WLT 14, Dis Diner WASHINGTON",Card,Eating out,−£19.95,"£2,032.89"
04 May 21,"CLS 14, Go North East Limited GATESHEAD",Card,Holidays,−£15.60,"£2,052.84"
04 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£15.13,"£2,068.44"
04 May 21,"WLT 14, Poundland Ltd WILLENHALL",Card,Other,−£15.00,"£2,083.57"
04 May 21,"WLT 14, Claires Accessories NEWCASTLE 2",Card,Style,−£10.00,"£2,098.57"
04 May 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£9.58,"£2,108.57"
04 May 21,"WLT 14, Card Factory WASHINGTON",Card,Other,−£8.70,"£2,118.15"
04 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£7.62,"£2,126.85"
04 May 21,"WLT 14, Starbucks NEWCASTLE",Card,Eating out,−£6.65,"£2,134.47"
04 May 21,"WLT 14, Iz *Scented Melts Newcastle upo",Card,Other,−£6.48,"£2,141.12"
04 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£6.07,"£2,147.60"
04 May 21,"WLT 14, Taco Bell - Grainger S NEWCASLTE",Card,Eating out,−£5.99,"£2,153.67"
04 May 21,"WLT 14, Krispy Kreme Eldon Squ NEWCASTLE",Card,Groceries,−£5.45,"£2,159.66"
04 May 21,"WLT 14, Poundland TYNE AND WEAR",Card,Other,−£5.00,"£2,165.11"
04 May 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£3.58,"£2,170.11"
30 Apr 21,GROSS INTEREST,Other,,£0.70,"£2,173.69"
30 Apr 21,"WLT 14, Sumup *Toukon Martial WASHINGTON",Card,Wellbeing,−£37.00,"£2,172.99"
30 Apr 21,"Card 14, Just Eat London",Card,Eating out,−£14.77,"£2,209.99"
30 Apr 21,"WLT 14, Dis Diner WASHINGTON",Card,Eating out,−£11.00,"£2,224.76"
30 Apr 21,Paypal Payment,Direct Debit,,−£4.38,"£2,235.76"
29 Apr 21,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,−£22.98,"£2,240.14"
29 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£4.00,"£2,263.12"
29 Apr 21,"Card 14, Amazon.Co.Uk*Mk4Sm1Oy4 AMAZON.CO.UK",Card,Other,−£3.49,"£2,267.12"
28 Apr 21,"CLS 14, Sainsburys Pfs 0045 WASHINGTON",Card,Fuel,−£20.41,"£2,270.61"
28 Apr 21,"Card 14, Amznmktplace amazon.co.uk",Card,Other,−£12.99,"£2,291.02"
28 Apr 21,"WLT 14, Sainsburys Sacat 0555 WASHINGTON",Card,Groceries,−£8.00,"£2,304.01"
28 Apr 21,"Card 14, Audible Uk adbl.co/pymt",Card,Other,−£7.99,"£2,312.01"
28 Apr 21,"WLT 14, Savers Health & Beauty TYNE & WEAR",Card,Wellbeing,−£4.79,"£2,320.00"
28 Apr 21,"WLT 14, Sumup *Lebanos Newcastle upo",Card,Eating out,−£4.00,"£2,324.79"
28 Apr 21,"WLT 14, Wilko Retail Limited WASHINGTON",Card,Other,−£3.20,"£2,328.79"
28 Apr 21,DWPCMSGB2012SCHEME 710031041816,Giro,Earnings,£13.45,"£2,331.99"
27 Apr 21,"WLT 14, Lidl Gb Washington WASHINGTON",Card,Groceries,−£69.27,"£2,318.54"
27 Apr 21,"WLT 14, Iceland WASHINGTON",Card,Groceries,−£33.89,"£2,387.81"
27 Apr 21,"WLT 14, B&M 688 Washington WASHINGTON",Card,Other,−£24.97,"£2,421.70"
27 Apr 21,"WLT 14, May Hong Chop Suey Hou WASHINGTON",Card,Eating out,−£23.30,"£2,446.67"
27 Apr 21,"Card 14, Giffgaff London",Card,Utilities,−£6.00,"£2,469.97"
27 Apr 21,"Card 14, Giffgaff London",Card,Utilities,−£6.00,"£2,475.97"
27 Apr 21,"WLT 14, Mcdonalds WASHINGTON",Card,Eating out,−£3.77,"£2,481.97"
26 Apr 21,"WLT 14, Lidl Gb Washington WASHINGTON",Card,Groceries,−£17.09,"£2,485.74"
26 Apr 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£13.00,"£2,502.83"
26 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£12.05,"£2,515.83"
26 Apr 21,"WLT 14, Savers Health & Beauty WASHINGTON",Card,Wellbeing,−£11.82,"£2,527.88"
26 Apr 21,"CLS 14, Kfc WASHINGTON",Card,Eating out,−£11.18,"£2,539.70"
26 Apr 21,"WLT 14, Asda Superstore WASHINGTON",Card,Groceries,−£9.87,"£2,550.88"
26 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£5.08,"£2,560.75"
26 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£4.28,"£2,565.83"
26 Apr 21,"WLT 14, Go North East Limited GATESHEAD",Card,Holidays,−£3.40,"£2,570.11"
26 Apr 21,"WLT 14, Mcdonalds WASHINGTON",Card,Eating out,−£2.38,"£2,573.51"
26 Apr 21,"WLT 14, Childrens Society WASHINGTON",Card,Other,−£1.98,"£2,575.89"
26 Apr 21,"WLT 14, Pricewise Bargains WASHINGTON",Card,Groceries,−£1.00,"£2,577.87"
26 Apr 21,"MOB, Andrew Haswell",Transfer,,£50.00,"£2,578.87"
26 Apr 21,"MOB, Andrew Haswell",Transfer,,£25.00,"£2,528.87"
26 Apr 21,"MOB, Andrew Haswell",Transfer,,£25.00,"£2,503.87"
26 Apr 21,"FPS, Visualsoft Limited, VISUALSOFT",Transfer,,"£2,474.48","£2,478.87"
23 Apr 21,"WLT 14, May Hong Chop Suey Hou WASHINGTON",Card,Eating out,−£5.50,£4.39
23 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£3.88,£9.89
22 Apr 21,Esure Motor Ins Dr,Direct Debit,Finance,−£29.54,£13.77
22 Apr 21,"Card 14, Epoch *Winchester GB",Card,Other,−£15.00,£43.31
22 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.00,£58.31
22 Apr 21,"MOB, Andrew Haswell",Transfer,,£50.00,£60.31
21 Apr 21,Paypal Payment,Direct Debit,,−£6.49,£10.31
21 Apr 21,"Card 14, Amznfreetime 353-12477661",Card,Entertainment,−£4.99,£16.80
19 Apr 21,"Card 14, Pizza Box HOUGHTON LE S",Card,Eating out,−£15.70,£21.79
19 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£9.00,£37.49
19 Apr 21,"Card 14, Amznmktplace Amazon.Co AMAZON.CO.UK",Card,Other,−£8.99,£46.49
19 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£5.78,£55.48
19 Apr 21,"WLT 14, Fenham Stores Ltd - Wa WASHINGTON",Card,Groceries,−£2.58,£61.26
16 Apr 21,"Card 14, Screwfix Direct YEOVIL",Card,Home,−£7.77,£63.84
16 Apr 21,"Card 14, Shudder-855-744-1217 NEW YORK CITY",Card,Entertainment,−£4.99,£71.61
15 Apr 21,"Card 14, Asda Superstore WASHINGTON",Card,Groceries,−£86.27,£76.60
15 Apr 21,"WLT 14, Shoe Zone Ltd 1154 WASHINGTON",Card,Style,−£19.98,£162.87';

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
