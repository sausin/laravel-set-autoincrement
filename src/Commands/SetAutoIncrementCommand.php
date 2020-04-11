<?php

namespace Sausin\DBSetAutoIncrement\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Sausin\DBSetAutoIncrement\DatabaseInfo;
use Sausin\DBSetAutoIncrement\GetAttribute;
use Sausin\DBSetAutoIncrement\UpdateAttribute;

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

            $this->info('Specified tables have been updated');

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
        $this->info('Tables have been updated as per config');
    }

    /**
     * Update AUTO INCREMENT value in mysql tables.
     *
     * @param  Collection $tables
     * @return void
     */
    protected function updateMysqlTables(Collection $tables): void
    {
        $tables->filter(function ($table) {
            return $this->getAutoIncrement('Mysql', $table) < $this->autoIncrement;
        })->map(function ($table) {
            $this->updateAutoIncrement('Mysql', $table);
        });
    }

    /**
     * Update AUTO INCREMENT value in sqlite tables.
     *
     * @param  Collection $tables
     * @return void
     */
    protected function updateSqliteTables(Collection $tables): void
    {
        // the auto increment value is reduced by 1 as SQLITE uses it in this way
        $this->autoIncrement--;

        $tables->filter(function ($table) {
            return $this->getAutoIncrement('Sqlite', $table) < $this->autoIncrement;
        })->map(function ($table) {
            $this->updateAutoIncrement('Sqlite', $table);
        });
    }
}
