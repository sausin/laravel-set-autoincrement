<?php

namespace Sausin\DBSetAutoIncrement;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;

class SetAutoIncrementProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerListener();
    }

    /**
     * Register the listener.
     *
     * @return void
     */
    protected function registerListener()
    {
        $events = $this->app->make(Dispatcher::class);

        $events->listen(
            \Illuminate\Database\Events\MigrationsEnded::class,
            Listeners\SetAutoIncrement::class
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! defined('DB_AUTO_INCREMENT_PATH')) {
            define('DB_AUTO_INCREMENT_PATH', realpath(__DIR__.'/../'));
        }

        $this->configure();
        $this->offerPublishing();
    }

    /**
     * Setup the configuration for AutoIncrement.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/auto-increment.php',
            'auto-increment'
        );
    }

    /**
     * Setup the resource publishing groups for AutoIncrement.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/auto-increment.php' => config_path('auto-increment.php'),
            ], 'auto-increment-config');
        }
    }
}
