<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\ScheduleUpdate;
use App\Models\Transaction;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\Payment as Payment;
use App\Models\Schedule;
use App\Models\Additional;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
   * Create a new command instance.
   *
   * @return void
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

    // Add schedules to transactions
    $today = Carbon::today();
    $active = Schedule::where('payment_date', '=', $today)->get();

    foreach ($active as $schedule) {

      $transaction = new Transaction();

      echo 'Transaction Added: ' . $schedule->name . "\n\r";

      $transaction->name = $schedule->name;
      $transaction->payment_date = $today;
      $transaction->type = $schedule->type;
      $transaction->account_id = $schedule->account_id;
      $transaction->confirmed = false;
      $transaction->amount = $schedule->amount;
      $transaction->save();
    }
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

        $update_string = implode('_', [$update->name,
                                       $update->payment_date,
                                       $update->type,
                                       $update->account_id]);

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

    $bank_holidays = [$new_year,
                      $good_friday,
                      $easter_monday,
                      $mayday,
                      $spring_bank_holiday,
                      $summer_bank_holiday,
                      $christmas,
                      $boxing_day];

    return $bank_holidays;
  }

  /**
   * @author Andrew Haswell
   */

  public function update_schedules()
  {
    $payments = Payment::all();
    $accounts = Account::all();
    $holidays = $this->bank_holidays();

    $account_list = [];

    foreach ($accounts as $account) {
      $account_list[$account->id] = $account->name;
    }

    // Clear the schedule table
    Schedule::truncate();

    $now = Carbon::today();
    $absolute_end = new DateTime();
    $absolute_end->modify('+1 years');

    $updates = $this->get_updates();

    foreach ($payments as $payment) {

      // Work out our payments dates
      $begin = new DateTime($payment->start_date);
      $end = new DateTime();
      $additionals = $payment->additional()->get();

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

          $bank_holidays = $holidays;
          $reverse_bank_holidays = array_reverse($holidays);

          $holidays = $payment->weekend == 'before'
            ? $reverse_bank_holidays
            : $bank_holidays;

          foreach ($holidays as $holiday) {

            if ($holiday == $dt) {

              $modify = $payment->weekend == 'before'
                ? '-1 day'
                : '+1 day';
              $dt->modify($modify);

              $weekday = $dt->format('N');

              // Alter the date for a weekend
              if ($weekday >= 6) {
                $modify = $payment->weekend == 'before'
                  ? 'last friday'
                  : 'next monday';
                $dt->modify($modify);
              }
            }
          }
        }

        if ($dt >= $now && $dt < $absolute_end) {

          if ($payment->transfer_account_id > 0) {
            $transfer_to_name = 'Transferred to ' . $account_list[$payment->transfer_account_id];
            $transfer_from_name = 'Transferred from ' . $account_list[$payment->account_id];
            $transfer = 1;
          } else {
            $transfer = 0;
          }

          $payment->name = !empty($transfer_to_name)
            ? $transfer_to_name
            : $payment->name;

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
            $schedule->transfer = $transfer;
            $schedule->payment_date = $scheduleUpdate->payment_date;
            $schedule->save();
          } else {
            $schedule = new Schedule();
            $schedule->name = $payment->name;
            $schedule->account_id = $payment->account_id;
            $schedule->amount = $payment->amount;
            $schedule->type = $payment->type;
            $schedule->transfer = $transfer;
            $schedule->payment_date = $payment->payment_date;
            $schedule->save();
          }

          if ($payment->transfer_account_id > 0) {
            $schedule = new Schedule();
            $schedule->name = $transfer_from_name;
            $schedule->account_id = $payment->transfer_account_id;
            $schedule->amount = $payment->amount;
            $schedule->transfer = $transfer;
            $schedule->type = ($payment->type == 'credit'
              ? 'debit'
              : 'credit');
            $schedule->payment_date = (string)$dt->format('Y-m-d');
            $schedule->save();
          }

          unset($transfer_to_name);
        }

        // Add any additionals
        if ($additionals->count() > 0) {
          foreach ($additionals as $additional) {

            $add_dt = clone($dt);

            if (!empty($additional->weekday)) {
              $dow_text = date('l', strtotime("Sunday +" . $additional->weekday . " days"));
              if ((string)$add_dt->format('Y-m-d') != $additional->weekday) {
                $add_dt->modify('next ' . $dow_text);
              }
            }

            if (!empty($additional->start_date)) {
              $add_start_date = Carbon::parse($additional->start_date);
              if ($add_dt < $add_start_date) {
                continue;
              }
            }

            if (!empty($additional->end_date)) {
              $add_end_date = Carbon::parse($additional->end_date);
              if ($add_dt >= $add_end_date) {
                continue;
              }
            }

            if ($additional->transfer_account_id > 0) {
              $transfer_to_name = 'Transferred to ' . $account_list[$additional->transfer_account_id];
              $transfer_from_name = 'Transferred from ' . $account_list[$additional->account_id];
              $transfer = 1;
            } else {
              $transfer = 0;
            }

            $schedule = new Schedule();
            $schedule->name = (!empty($transfer_to_name)
              ? $transfer_to_name
              : $additional->name);
            $schedule->account_id = $additional->account_id;
            $schedule->amount = $additional->amount;
            $schedule->type = $additional->type;
            $schedule->transfer = $transfer;
            $schedule->payment_date = (string)$add_dt->format('Y-m-d');
            $schedule->save();

            if ($additional->transfer_account_id > 0) {
              $schedule = new Schedule();
              $schedule->name = $transfer_from_name;
              $schedule->account_id = $additional->transfer_account_id;
              $schedule->amount = $additional->amount;
              $schedule->transfer = $transfer;
              $schedule->type = ($additional->type == 'credit'
                ? 'debit'
                : 'credit');
              $schedule->payment_date = (string)$add_dt->format('Y-m-d');
              $schedule->save();
            }
            unset($transfer_to_name);
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
    return base64_encode(implode('_', [$payment->name,
                                       $payment->payment_date,
                                       $payment->type,
                                       $payment->account_id]));
  }
}
