<?php

namespace App\Console\Commands;

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
    mail('andy@snowmanx.com', 'Testy cron', 'Testing a cron', 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=iso-8859-1');
  }
}
