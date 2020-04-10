<?php

return [
    // Following tables will be skipped from adjustments to auto increment
    'skipTables' => ['migrations', 'notifications', 'jobs', 'failed_jobs'],

    // Instead of skipping, only do the adjustments to the following tables.
    'onlyTables' => [],

    // Mode to be used for setting to the auto increment. If this is
    // set to 'skip', the all tables other than the ones in the
    // skip tables array above will be updated with the auto
    // increment value. If this is set to 'only', then only
    // the specified tables will be updated.
    'mode' => 'skip',

    // Auto increment value to be set in the relevant tables.
    // New entries to the relevant tables will be created
    // using this value as the starting point.
    'autoIncrement' => 100001,

];
