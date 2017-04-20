<?php

namespace App\Console\Commands;

use Phelium\Component\MySQLBackup;
use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'db:backup';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

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
    mail('andy@snowmanx.com', 'Cron Ran - DB Backup', 'Cron Ran', 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=iso-8859-1');
    $Dump = new MySQLBackup(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
    $Dump->setCompress('zip');
    $Dump->setFilename('bkp_' . time());
    $Dump->dump();
  }
}
