<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\ScheduleUpdate;
use App\Models\Transaction;
use App\Models\Transfer;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\Payment as Payment;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'payments:update';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Checks through the payments and makes any due today into transactions';

    /**
     * UpdatePayments constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {


        $this->update_schedules();
        $this->calculate_bills();
        $this->calculate_savings();

        $account_list = [];
        $accounts = Account::all();
        foreach ($accounts as $account) {
            $account_list[$account->id] = $account->name;
        }

        // Add schedules to transactions
        $today = Carbon::today();
        $active = Schedule::where('payment_date', '=', $today)->where('transfer', '!=', '-1')->get();

        foreach ($active as $schedule) {
            $transaction = new Transaction();

            $name = !empty($schedule->transfer) ?
              $schedule->name . ' -> ' . $account_list[$schedule->transfer] :
              $schedule->name;

            echo 'Transaction Added: ' . $name . "\n\r";

            $transaction->name = $name;
            $transaction->payment_date = $today;
            $transaction->type = $schedule->type;
            $transaction->account_id = $schedule->account_id;
            $transaction->category_id = $schedule->category_id;
            $transaction->confirmed = $schedule->confirmed;
            $transaction->amount = $schedule->amount;
            $transaction->save();

            // If we're meant to transfer the money
            if (!empty($schedule->transfer)) {

                $transfer_from_id = $transaction->id;

                $name = $schedule->name . ' <- ' . $account_list[$schedule->account_id];
                $transaction = new Transaction();
                $transaction->name = $name;
                $transaction->payment_date = $today;
                $transaction->type = ($schedule->type == 'credit' ?
                  'debit' :
                  'credit');
                $transaction->account_id = $schedule->transfer;
                $transaction->confirmed = $schedule->confirmed;
                $transaction->amount = $schedule->amount;
                $transaction->save();

                $transfer_to_id = $transaction->id;

                $transfer_link = new Transfer();
                $transfer_link->from_id = $transfer_from_id;
                $transfer_link->to_id = $transfer_to_id;
                $transfer_link->save();
            }
        }
    }

    /**
     * @author Andrew Haswell
     */

    public function calculate_savings()
    {
        $accounts = Account::where('type', '=', 'current')->where('hidden', '=', 0)->get();
        $today = Carbon::today();
        $savings_account_name = env('SAVINGS_ACC_NAME', 'Savings');
        $savings_account = Account::where('name', '=', $savings_account_name)->firstOrFail();
        $minimum_account_level = env('SAVINGS_ACC_MINIMUM', 60);

        dump('Savings Account Name: ' . $savings_account_name);
        dump($savings_account);
        dump('Minimum Account Level: ' . $minimum_account_level);
        dump($accounts);

        foreach ($accounts as $account) {

            dump($account);

            $account = $this->get_current_balance($account);

            dump($account->balance);

            $schedules = $account->schedules()->orderBy('payment_date', 'asc')->orderBy('type', 'asc')->get();

            /* Debugging for Andy Haswell (26/05/2021) */
            dump('DEBUG - ' . __NAMESPACE__ . '::' . __FUNCTION__ . '() #' . __LINE__);
            dd($schedules);
            /* End of Debugging */

            foreach ($schedules as $schedule) {

                if ($schedule->type == 'debit') {
                    $account->balance -= $schedule->amount;
                } else {
                    // Is it pay?
                    if ($schedule->amount > 500) {
                        $salary = Schedule::where('name', '=', $schedule->name)->where('payment_date', '<',
                          $schedule->payment_date)->orderBy('payment_date', 'desc')->first();
                        if (!empty($salary)) {
                            $saving_date = Carbon::parse($salary->payment_date);
                            /*              if ($saving_date->format('N') != 6) {
                                            $saving_date->modify('next saturday');
                                          }*/
                        } else {
                            $account->balance += $schedule->amount;
                            continue;
                        }
                        $saving_balance = $account->balance;

                        if ($saving_date >= $today) {

                            // Make sure we never go lower than our minimum account level
                            $saving_balance = ($saving_balance - $minimum_account_level);

                            // If that still leaves something to save
                            if ($saving_balance > 0) {

                                // Round down to nearest 10 pound
                                $saving_balance = floor($saving_balance / 10) * 10;

                                if ($saving_balance > 0) {

                                    $transfer_to_name = 'Saved to ' . $savings_account->name;
                                    $transfer_from_name = 'Saved from ' . $account->name;


                                    dump($transfer_to_name);
                                    dump($transfer_from_name);
                                    dump($saving_balance);


                                    // Create a savings transaction
                                    $saving_schedule = new Schedule();
                                    $saving_schedule->name = $transfer_to_name;
                                    $saving_schedule->account_id = $account->id;
                                    $saving_schedule->amount = $saving_balance;
                                    $saving_schedule->type = 'debit';
                                    $saving_schedule->transfer = $savings_account->id;
                                    $saving_schedule->payment_date = $saving_date;
                                    $saving_schedule->save();

                                    $saving_schedule = new Schedule();
                                    $saving_schedule->name = $transfer_from_name;
                                    $saving_schedule->account_id = $savings_account->id;
                                    $saving_schedule->amount = $saving_balance;
                                    $saving_schedule->transfer = $account->id;
                                    $saving_schedule->type = 'credit';
                                    $saving_schedule->payment_date = $saving_date;
                                    $saving_schedule->save();

                                    $account->balance -= $saving_balance;
                                }
                            }
                        }
                    }
                    $account->balance += $schedule->amount;
                }
            }
        }
    }

    /**
     * @author Andrew Haswell
     */

    public function calculate_bills()
    {
        $bills_account_id = env('BILLING_ACC_ID', '1');
        $joint_account_id = env('JOINT_ACC_ID', '22');
        $bills_account = Account::where('id', '=', $bills_account_id)->firstOrFail();

        $paydays = Schedule::orderBy('payment_date', 'asc')->where('name', '=', 'Visualsoft')->get();

        foreach ($paydays as $key => $payday) {

            if (empty($paydays[$key + 1])) {
                break;
            }

            $from = Carbon::parse($payday->payment_date);
            $to = Carbon::parse($paydays[$key + 1]->payment_date)->subseconds(1);

            if ($key === 0) {
                $bills_account = $this->get_current_balance($bills_account, $from->subseconds(1));
                $start_balance = $bills_account->balance;
            }

            $schedules = $bills_account->schedules()->orderBy('payment_date', 'asc')->whereBetween('payment_date', [
              $from,
              $to
            ])->orderBy('type', 'asc')->get();

            foreach ($schedules as $schedule) {
                if ($schedule->type == 'debit') {
                    $bills_account->balance -= $schedule->amount;
                } else {
                    $bills_account->balance += $schedule->amount;
                }
            }

            $end_balance = $bills_account->balance;

            $bill_payment = (ceil(abs($end_balance) / 10) * 10) + 30;

            if ($bill_payment > 0) {

                $pay_name = 'Bills Transfer';

                $schedule = new Schedule();
                $schedule->name = $pay_name;
                $schedule->account_id = $bills_account_id;
                $schedule->amount = $bill_payment;
                $schedule->transfer = -1;
                $schedule->type = 'credit';
                $schedule->confirmed = 'N';
                $schedule->payment_date = (string)$from->addseconds(1)->format('Y-m-d');

                $schedule->save();

                $schedule = new Schedule();
                $schedule->name = $pay_name;
                $schedule->account_id = $joint_account_id;
                $schedule->amount = $bill_payment;
                $schedule->transfer = $bills_account_id;
                $schedule->type = 'debit';
                $schedule->confirmed = 'N';
                $schedule->payment_date = (string)$from->addseconds(1)->format('Y-m-d');

                $schedule->save();
            }

            $start_balance = round($end_balance + $bill_payment, 2);
            $bills_account->balance = $start_balance;
        }
    }

    /**
     * @param $account
     *
     * @author Andrew Haswell
     */

    private function get_current_balance($account, $date = null)
    {
        $transactions = $account->transactions()->where('payment_date', '>=', $account->balance_date)->get();
        foreach ($transactions as $transaction) {
            if ($transaction->type == 'debit') {
                $account->balance -= $transaction->amount;
            } else {
                $account->balance += $transaction->amount;
            }
        }

        if (!empty($date)) {

            $schedules = $account->schedules()->where('payment_date', '<=', $date)->where('payment_date', '>', Carbon::parse('today'))->orderBy('payment_date', 'asc')->get();

            foreach ($schedules as $schedule) {

                if ($schedule->type == 'debit') {
                    $account->balance -= $schedule->amount;
                } else {
                    $account->balance += $schedule->amount;
                }
            }

        }

        $account->balance = round($account->balance, 2);

        return $account;
    }

    /**
     * @return array
     * @author Andrew Haswell
     */

    public function get_updates()
    {
        $updates = ScheduleUpdate::all();

        $base64_updates = [];

        if (!empty($updates)) {

            foreach ($updates as $update) {

                $update_string = implode('_', [
                  $update->name,
                  $update->payment_date,
                  $update->type,
                  $update->account_id
                ]);

                $base64_updates[$update->id] = base64_encode($update_string);
            }
        }
        return $base64_updates;
    }

    /**
     * @param null $year
     *
     * @return array
     * @author Andrew Haswell
     */

    public function bank_holidays($year = null)
    {
        if (empty($year)) {
            $year = new Carbon();
            $year = $year->format('Y');
        }
        $mayday = Carbon::parse('first monday of may ' . $year);
        $spring_bank_holiday = Carbon::parse('last monday of may ' . $year);
        $summer_bank_holiday = Carbon::parse('last monday of august ' . $year);

        $easter = new Carbon('21st March ' . $year);
        $easter_days = easter_days($year);
        $good_friday = clone $easter;
        $good_friday->addDays($easter_days - 2);
        $easter_monday = clone $easter;
        $easter_monday->addDays($easter_days + 1);

        $christmas = new Carbon('25th December ' . $year);
        if ($christmas->format('N') == 6) {
            $christmas->modify('next monday');
        } else if ($christmas->format('N') > 6) {
            $christmas->modify('next tuesday');
        }

        $boxing_day = new Carbon('26th December ' . $year);
        if ($boxing_day->format('N') == 6) {
            $boxing_day->modify('next monday');
        } else if ($boxing_day->format('N') > 6) {
            $boxing_day->modify('next tuesday');
        }
        $new_year = new Carbon('1st January ' . $year);
        if ($new_year->format('N') == 6) {
            $new_year->modify('next monday');
        } else if ($new_year->format('N') > 6) {
            $new_year->modify('next tuesday');
        }

        $bank_holidays = [
          $new_year,
          $good_friday,
          $easter_monday,
          $mayday,
          $spring_bank_holiday,
          $summer_bank_holiday,
          $christmas,
          $boxing_day
        ];

        return $bank_holidays;
    }

    /**
     * @author Andrew Haswell
     */

    public function update_schedules()
    {
        $payments = Payment::all();
        $holidays = $this->bank_holidays();

        $days_of_the_week = [
          'sunday',
          'monday',
          'tuesday',
          'wednesday',
          'thursday',
          'friday',
          'saturday'
        ];

        // Clear the schedule table
        Schedule::truncate();

        $now = Carbon::today();
        $absolute_end = new DateTime();
        $absolute_end->modify('+18 months');
        $end_time_default = $absolute_end->format('U');

        $updates = $this->get_updates();

        foreach ($payments as $payment) {

            // Work out our payments dates
            $begin = new DateTime($payment->start_date);
            $end = new DateTime();
            $additionals = $payment->additional()->get();

            // Get the end date or set it a year ahead
            $end_time = !empty($payment->end_date) ?
              strtotime((string)$payment->end_date) :
              $end_time_default;

            $end->setTimestamp($end_time);

            $interval = DateInterval::createFromDateString($payment->interval);
            $period = new DatePeriod($begin, $interval, $end);

            foreach ($period as $dt) {
                // If we need to compensate for the weekend, do the checks
                if ($payment->weekend != 'none') {

                    $weekday = $dt->format('N');
                    // Alter the date for a weekend
                    if ($weekday >= 6) {
                        $modify = $payment->weekend == 'before' ?
                          'last friday' :
                          'next monday';
                        $dt->modify($modify);
                    }

                    $bank_holidays = $holidays;
                    $reverse_bank_holidays = array_reverse($holidays);

                    $holidays = $payment->weekend == 'before' ?
                      $reverse_bank_holidays :
                      $bank_holidays;

                    foreach ($holidays as $holiday) {

                        if ($holiday == $dt) {

                            $modify = $payment->weekend == 'before' ?
                              '-1 day' :
                              '+1 day';
                            $dt->modify($modify);

                            $weekday = $dt->format('N');

                            // Alter the date for a weekend
                            if ($weekday >= 6) {
                                $modify = $payment->weekend == 'before' ?
                                  'last friday' :
                                  'next monday';
                                $dt->modify($modify);
                            }
                        }
                    }
                }

                // Does the payment come out on a specific day of the week
                if (!empty($payment->day)) {
                    if (in_array(strtolower($payment->day), $days_of_the_week)) {
                        // Check we're not already on the right day first
                        if (strtolower($dt->format('l')) != strtolower($payment->day)) {
                            $dt->modify('next ' . strtolower($payment->day));
                        }
                    }
                }

                if ($dt >= $now && $dt < $absolute_end) {

                    $payment->payment_date = (string)$dt->format('Y-m-d H:i:s');

                    $base_64_payment = $this->encode_it($payment);

                    if (in_array($base_64_payment, $updates)) {

                        $key = array_search($base_64_payment, $updates);

                        $scheduleUpdate = ScheduleUpdate::findOrFail($key);

                        $schedule = new Schedule();
                        $schedule->name = $scheduleUpdate->name;
                        $schedule->account_id = $scheduleUpdate->account_id;
                        $schedule->amount = $scheduleUpdate->amount;
                        $schedule->type = $scheduleUpdate->type;
                        $schedule->confirmed = $payment->confirmed;
                        $schedule->transfer = $payment->transfer_account_id;
                        if ($payment->transfer_account_id == 0) {
                            $schedule->category_id = $payment->category_id;
                        }
                        $schedule->payment_date = $scheduleUpdate->payment_date;
                        $schedule->save();
                    } else {
                        $schedule = new Schedule();
                        $schedule->name = $payment->name;
                        $schedule->account_id = $payment->account_id;
                        $schedule->amount = $payment->amount;
                        $schedule->type = $payment->type;
                        $schedule->confirmed = $payment->confirmed;
                        $schedule->transfer = $payment->transfer_account_id;
                        if ($payment->transfer_account_id == 0) {
                            $schedule->category_id = $payment->category_id;
                        }
                        $schedule->payment_date = $payment->payment_date;
                        $schedule->save();
                    }

                    if ($payment->transfer_account_id > 0) {
                        $schedule = new Schedule();
                        $schedule->name = $payment->name;
                        $schedule->account_id = $payment->transfer_account_id;
                        $schedule->amount = $payment->amount;
                        $schedule->transfer = -1;
                        $schedule->type = ($payment->type == 'credit' ?
                          'debit' :
                          'credit');
                        $schedule->confirmed = $payment->confirmed;
                        $schedule->payment_date = (string)$dt->format('Y-m-d');
                        $schedule->save();
                    }
                }
            }
        }
    }

    /**
     * @param $payment
     *
     * @return string
     * @author Andrew Haswell
     */

    public function encode_it($payment)
    {
        return base64_encode(implode('_', [
          $payment->name,
          $payment->payment_date,
          $payment->type,
          $payment->account_id
        ]));
    }
}
