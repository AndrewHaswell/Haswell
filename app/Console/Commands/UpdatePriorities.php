<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Todo;

class UpdatePriorities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'priorities:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increments the priority of Todo items based on time';

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
      Todo::where('priority', '>', 1)->decrement('priority');
    }
}
