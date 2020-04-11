<?php

namespace Sausin\DBSetAutoIncrement\Commands;


use Sausin\DBSetAutoIncrement\DatabaseInfo;
use Sausin\DBSetAutoIncrement\HandleTables;
use Sausin\DBSetAutoIncrement\GetAttribute;
use Sausin\DBSetAutoIncrement\UpdateAttribute;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class SetAutoIncrementCommand extends Command
{
    use DatabaseInfo;
    use HandleTables;
    use GetAttribute;
    use UpdateAttribute;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:set-auto-increment
                            {--tables=* : The table(s) for which auto increment should be set}
                            {--value= : The auto increment value to be set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set auto increment for database table(s)';

    /** @var array */
    protected $skipTables;

    /** @var array */
    protected $onlyTables;

    /** @var string */
    protected $mode;

    /** @var array */
    protected $action;

    /** @var int */
    protected $autoIncrement;

    /** @var array */
    protected $supportedDrivers = ['mysql', 'sqlite'];

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'monitoring';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->mode = Config::get('auto-increment.mode', 'skip');
        $this->action = Config::get('auto-increment.action', 'auto');
        $this->skipTables = Config::get('auto-increment.skipTables', ['migrations']);
        $this->onlyTables = Config::get('auto-increment.onlyTables', []);
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->autoIncrement = $this->option('value') ?? Config::get('auto-increment.autoIncrement', 100001);
        
        if (! $this->isDatabaseCompatible()) {
            $this->info("Database {$driver} not supported");
            
            return;
        }

        $driver = ucfirst($this->driver);

        if ($this->option('tables')) {
            $this->{"update{$driver}Tables"}(collect($this->option('tables')));
            
            return;
        }

        $tables = collect([]);

        if ($this->mode === 'only') {
            $tables = collect($this->onlyTables);
        }

        if ($this->mode === 'skip') {
            $tables = collect($this->getTableList())->reject(function ($value) {
                return in_array($value, $this->skipTables, true);
            });
        }

        $this->{"update{$driver}Tables"}($tables);
    }
}
