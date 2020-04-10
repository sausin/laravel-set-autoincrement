<?php

namespace Sausin\DBSetAutoIncrement\Listeners;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\MigrationsEnded;

class SetAutoIncrement implements ShouldQueue
{
    /** @var array */
    public $skipTables;

    /** @var array */
    public $onlyTables;

    /** @var array */
    public $mode;

    /** @var array */
    public $autoIncrement;

    /** @var array */
    public $supportedDrivers = ['mysql', 'sqlite', 'pgsql'];

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
        $this->mode = Config::get('auto-increment.mode');
        $this->skipTables = Config::get('auto-increment.skipTables');
        $this->onlyTables = Config::get('auto-increment.onlyTables');
        $this->autoIncrement = Config::get('auto-increment.autoIncrement');
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MigrationsEnded $event)
    {
        $driver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        
        if (! in_array($driver, $this->supportedDrivers)) {
            return;
        }

        $tables = collect([]);

        if ($this->mode === 'only') {
            $tables = collect($this->onlyTables);
        }

        if ($this->mode === 'skip') {
            $tables = collect(DB::connection()->getDoctrineSchemaManager()->listTableNames())->reject(function ($value) {
                return in_array($value, $this->skipTables, true);
            });
        }

        $driver = ucfirst($driver);

        $this->{"update{$driver}Tables"}($tables);
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
            return DB::select("SHOW TABLE STATUS WHERE NAME = '{$table}'")[0]->Auto_increment < $this->autoIncrement;
        })->map(function ($table) {
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT={$this->autoIncrement}");
        });
    }

    /**
     * Update AUTO INCREMENT value in postgres tables.
     *
     * @param  Collection $tables
     * @return void
     */
    protected function updateMysqlTables(Collection $tables): void
    {
        $tables->filter(function ($table) {
            return DB::select("SHOW TABLE STATUS WHERE NAME = '{$table}'")[0]->Auto_increment < $this->autoIncrement;
        })->map(function ($table) {
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT={$this->autoIncrement}");
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
            return data_get(DB::select("SELECT * FROM SQLITE_SEQUENCE WHERE NAME = '{$table}'"), '0.seq') < $this->autoIncrement;
        })->map(function ($table) {
            DB::statement("INSERT OR REPLACE INTO SQLITE_SEQUENCE('name', 'seq') VALUES('{$table}', {$this->autoIncrement})");
        });
    }
}
