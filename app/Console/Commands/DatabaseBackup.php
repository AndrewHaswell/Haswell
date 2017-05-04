<?php

namespace App\Console\Commands;

use Phelium\Component\MySQLBackup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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
    $Dump = new MySQLBackup(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
    $Dump->setCompress('zip');
    $filename = 'bkp_' . time();
    $Dump->setFilename($filename);
    $Dump->dump();

    Mail::raw('Database Backup Attached', function ($message) use ($filename) {
      $message->from('andy@snowmanx.com', 'SnowmanX');
      $message->to('andy@snowmanx.com', 'SnowmanX');
      $message->subject('Database backup');
      $message->attach($filename . '.zip');
    });
  }
}
