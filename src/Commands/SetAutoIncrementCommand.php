<?php

namespace Sausin\DBSetAutoIncrement\Commands;


use Sausin\DBSetAutoIncrement\DatabaseInfo;
use Sausin\DBSetAutoIncrement\GetAttribute;
use Sausin\DBSetAutoIncrement\UpdateAttribute;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class SetAutoIncrementCommand extends Command
{
    use DatabaseInfo;
    use GetAttribute;
    use UpdateAttribute;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:set-auto-increment
                            {--table= : The table for which auto increment should be set}
                            {--value= : The auto increment value to be set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set auto increment for database table(s)';

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
        $autoIncrement = $this->option('value') ?? Config::get('auto-increment.autoIncrement');

        if ($this->option('table')) {
            // set auto increment for specific table
        }
        
        // perform auto increment for all tables as per config settings
    }
}
