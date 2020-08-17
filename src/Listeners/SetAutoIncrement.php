<?php

namespace Sausin\DBSetAutoIncrement\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SetAutoIncrement
{
    /** @var string */
    public $action;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->action = Config::get('auto-increment.action', 'auto');
    }

    /**
     * Handle the event.
     *
     * @param  MigrationsEnded  $event
     * @return void
     */
    public function handle(MigrationsEnded $event)
    {
        if ($this->action !== 'auto') {
            return;
        }

        Artisan::call('db:set-auto-increment');
    }
}
