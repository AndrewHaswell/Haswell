<?php

namespace App\Console;

use App\Console\Commands\CheckQuotes;
use App\Console\Commands\UpdatePayments;
use App\Console\Commands\DatabaseBackup;
use App\Console\Commands\UpdatePriorities;
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
                         UpdatePriorities::class,
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
    $schedule->command('payments:update')->hourly()->sendOutputTo('payment_update.log');
    $schedule->command('db:backup')->hourly()->sendOutputTo('payment_update.log');
    $schedule->command('priorities:update')->daily();
  }
}
