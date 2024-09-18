<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class randomTop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'randomTop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Random cursor in top';

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
     * @return int
     */
    public function handle()
    {
        DB::update('update cursors set top=1');
        return 0;
    }
}
