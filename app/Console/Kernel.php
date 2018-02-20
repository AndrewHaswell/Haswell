<?php

namespace App\Console;

use App\Console\Commands\CheckQuotes;
use App\Console\Commands\UpdatePayments;
use App\Console\Commands\DatabaseBackup;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [UpdatePayments::class,
                         CheckQuotes::class,
                         DatabaseBackup::class];

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule $schedule
   *
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    $schedule->command('payments:update')->daily()->sendOutputTo('payment_update.log');
    $schedule->command('db:backup')->daily()->sendOutputTo('payment_update.log');
    $schedule->command('quotes:check')->hourly()->between('9.00','16:59');
  }
}
