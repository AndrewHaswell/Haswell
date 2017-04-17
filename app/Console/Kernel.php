<?php

namespace App\Console;

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
    $schedule->command('db:backup')->everyFiveMinutes();
    $schedule->command('payments:update')->everyFiveMinutes();
  }
}
