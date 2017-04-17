<?php

namespace App\Console\Commands;
require 'vendor/autoload.php';

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
      $Dump = new MySQLBackup('localhost', 'imperial_iop', '9004lestat', 'imperial_accounts');
      $Dump->setCompress('zip');
      $Dump->setFilename('bkp_'.time());
      $Dump->dump();
    }
}
