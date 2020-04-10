<?php

namespace Sausin\DBSetAutoIncrement\Listeners;

use Sausin\DBSetAutoIncrement\DatabaseInfo;
use Sausin\DBSetAutoIncrement\GetAttribute;
use Sausin\DBSetAutoIncrement\UpdateAttribute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\MigrationsEnded;

class SetAutoIncrement implements ShouldQueue
{
    use DatabaseInfo;
    use GetAttribute;
    use UpdateAttribute;

    /** @var array */
    public $skipTables;

    /** @var array */
    public $onlyTables;

    /** @var string */
    public $mode;

    /** @var array */
    public $action;

    /** @var int */
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
        $this->mode = Config::get('auto-increment.mode', 'skip');
        $this->action = Config::get('auto-increment.action', 'auto');
        $this->skipTables = Config::get('auto-increment.skipTables', ['migrations']);
        $this->onlyTables = Config::get('auto-increment.onlyTables', []);
        $this->autoIncrement = Config::get('auto-increment.autoIncrement', 100001);
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MigrationsEnded $event)
    {
        if ($this->action !== 'auto') {
            return;
        }

        $driver = $this->getDatabaseName();
        
        if (! in_array($driver, $this->supportedDrivers)) {
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
