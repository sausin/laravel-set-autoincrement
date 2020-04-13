<?php

namespace Sausin\DBSetAutoIncrement;

use Illuminate\Support\Facades\DB;

trait UpdateAttribute
{
    /**
     * @param string $driver
     */
    protected function updateAutoIncrement($driver, $table): void
    {
        $method = "update{$driver}AutoIncrement";
        
        // perform changes as a transaction to avoid
        // borking the database in case of issues.
        DB::transaction(function () use ($method, $table) {
            $this->{$method}($table);
        });
    }

    protected function updateMysqlAutoIncrement($table): void
    {
        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT={$this->autoIncrement}");
    }

    protected function updateSqliteAutoIncrement($table): void
    {
        DB::statement("DELETE FROM SQLITE_SEQUENCE WHERE name = '{$table}'");
        DB::statement("INSERT INTO SQLITE_SEQUENCE(name, seq) VALUES('{$table}', {$this->autoIncrement})");
    }
}
