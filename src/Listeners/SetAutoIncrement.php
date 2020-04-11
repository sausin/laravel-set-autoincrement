<?php

namespace Sausin\DBSetAutoIncrement\Listeners;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\MigrationsEnded;

class SetAutoIncrement implements ShouldQueue
{
    /** @var array */
    public $action;

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
        $this->action = Config::get('auto-increment.action', 'auto');
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
        
        Artisan::call('db:set-auto-increment');
    }
}
