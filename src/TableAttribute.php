<?php

namespace Sausin\DBSetAutoIncrement;

use Illuminate\Support\Facades\DB;

trait TableAttribute
{
    protected function getAutoIncrement($driver, $table): int
    {
        $method = "get{$driver}AutoIncrement";

        return $this->{$method}($table);
    }
    
    protected function getMysqlAutoIncrement($table): int
    {
        return data_get(DB::select("SHOW TABLE STATUS WHERE NAME = '{$table}'"), '0.Auto_increment', 0);
    }
    
    protected function getSqliteAutoIncrement($table): int
    {
        return data_get(DB::select("SELECT * FROM SQLITE_SEQUENCE WHERE NAME = '{$table}'"), '0.seq', 0);
    }
}
