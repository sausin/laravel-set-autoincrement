<?php

namespace Sausin\DBSetAutoIncrement;

use Illuminate\Support\Facades\DB;

trait UpdateAttribute
{
    protected function updateAutoIncrement($driver, $table): void
    {
        $method = "update{$driver}AutoIncrement";

        $this->{$method}($table);
    }

    protected function updateMysqlAutoIncrement($table): void
    {
        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT={$this->autoIncrement}");
    }

    protected function updateSqliteAutoIncrement($table): void
    {
        DB::statement("INSERT OR REPLACE INTO SQLITE_SEQUENCE('name', 'seq') VALUES('{$table}', {$this->autoIncrement})");
    }
}
