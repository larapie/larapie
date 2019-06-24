<?php

namespace App\Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Larapie\Core\Contracts\Scheduling;

class UserServiceProvider extends ServiceProvider implements Scheduling
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    public function schedule(\Illuminate\Console\Scheduling\Schedule $schedule): void
    {
        //Schedule commands or jobs here.
    }

}
