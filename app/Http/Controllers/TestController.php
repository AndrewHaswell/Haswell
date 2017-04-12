<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\Payment as Payment;
use App\Models\Schedule;
use Carbon\Carbon;

use App\Http\Requests;

class TestController extends Controller
{
  /**
   * @author Andrew Haswell
   */

  public function test()
  {
    $this->update_schedules();

    // Add schedules to transactions
    $today = Carbon::today();
    $active = Schedule::where('payment_date', '=', $today)->get();

    foreach ($active as $schedule) {

      $transaction = new Transaction();

      $transaction->name = $schedule->name;
      $transaction->payment_date = $today;
      $transaction->type = $schedule->type;
      $transaction->account_id = $schedule->account_id;
      $transaction->confirmed = false;
      $transaction->amount = $schedule->amount;
      $transaction->save();
    }
  }

  public function bank_holidays()
  {

    $year = '2018';

    $mayday = Carbon::parse('first monday of may '.$year);
    $spring_bank_holiday = Carbon::parse('last monday of may '.$year);
    $summer_bank_holiday = Carbon::parse('last monday of august '.$year);

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


    echo "\n\r\n\r" . Kohana::debug(Router::$controller . ' : ' . Router::$method . ' : ' . __FILE__ . ' : ' . __LINE__) . "\n\r";
    echo Kohana::debug($new_year);
    echo Kohana::debug($good_friday);
    echo Kohana::debug($easter_monday);
    echo Kohana::debug($mayday);
    echo Kohana::debug($spring_bank_holiday);
    echo Kohana::debug($summer_bank_holiday);
    echo Kohana::debug($christmas);
    echo Kohana::debug($boxing_day);
    exit();
  }

  /**
   * @author Andrew Haswell
   */

  public function update_schedules()
  {
    $payments = Payment::all();
    $accounts = Account::all();

    $account_list = [];

    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
    }

    // Clear the schedule table
    Schedule::truncate();

    $now = Carbon::today();
    $absolute_end = new DateTime();
    $absolute_end->modify('+1 year');

    foreach ($payments as $payment) {

      echo '<h2>' . $payment->name . '</h2>';

      // Work out our payments dates
      $begin = new DateTime($payment->start_date);
      $end = new DateTime();

      // Get the end date or set it a year ahead
      $end_time = !empty($payment->end_date)
        ? strtotime((string)$payment->end_date)
        : strtotime('next year');

      $end->setTimestamp($end_time);

      $interval = DateInterval::createFromDateString($payment->interval);
      $period = new DatePeriod($begin, $interval, $end);

      foreach ($period as $dt) {
        // If we need to compensate for the weekend, do the checks
        if ($payment->weekend != 'none') {
          $weekday = $dt->format('N');
          // Alter the date for a weekend
          if ($weekday >= 6) {
            $modify = $payment->weekend == 'before'
              ? 'last friday'
              : 'next monday';
            $dt->modify($modify);
          }
        }

        if ($dt >= $now && $dt < $absolute_end) {

          if ($payment->transfer_account_id > 0) {
            $transfer_to_name = 'Transferred to ' . $account_list[$payment->transfer_account_id];
            $transfer_from_name = 'Transferred from ' . $account_list[$payment->account_id];
          }

          $schedule = new Schedule();
          $schedule->name = (!empty($transfer_to_name)
            ? $transfer_to_name
            : $payment->name);
          $schedule->account_id = $payment->account_id;
          $schedule->amount = $payment->amount;
          $schedule->type = $payment->type;
          $schedule->payment_date = (string)$dt->format('Y-m-d');
          $schedule->save();

          if ($payment->transfer_account_id > 0) {
            $schedule = new Schedule();
            $schedule->name = $transfer_from_name;
            $schedule->account_id = $payment->transfer_account_id;
            $schedule->amount = $payment->amount;
            $schedule->type = ($payment->type == 'credit'
              ? 'debit'
              : 'credit');
            $schedule->payment_date = (string)$dt->format('Y-m-d');
            $schedule->save();
          }

          unset($transfer_to_name);
        }
      }
    }
  }

  /**
   * @author Andrew Haswell
   */

}
