<?php

namespace Sausin\DBSetAutoIncrement;

use Illuminate\Support\Facades\DB;

trait DatabaseInfo
{
    protected function isDatabaseCompatible(): bool
    {
        $this->driver = $this->getDatabaseName();
        
        return in_array($this->driver, $this->supportedDrivers));
    }

    protected function getDatabaseName(): string
    {
        return DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }
    
    protected function getTableList(): array
    {
        return DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }
}
