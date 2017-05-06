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
    // Backup the database
    $database_config = config('database.connections.mysql');
    $Dump = new MySQLBackup($database_config['host'], $database_config['username'], $database_config['password'], $database_config['database']);
    $Dump->setCompress('zip');
    $filename = 'bkp_' . time();
    $Dump->setFilename($filename);
    $Dump->dump();

    // Email the db backup
    Mail::raw('Cron ran - database backup attached.', function ($message) use ($filename) {
      $message->from('andy@snowmanx.com', 'SnowmanX');
      $message->to('andy@snowmanx.com', 'Andy Haswell');
      $message->subject('Database Backup Attached');
      $message->attach($filename . '.zip');
    });

    // Remove the backup from the server
    unlink($filename . '.zip');
  }
}
