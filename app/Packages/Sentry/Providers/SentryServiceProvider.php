<?php

namespace App\Packages\Sentry\Providers;

use Illuminate\Support\ServiceProvider;

class SentryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('production') && config('sentry.dsn') !== null) {
            $this->app->register(\Sentry\Laravel\ServiceProvider::class);
            $this->app->alias('sentry', \Sentry\Laravel\Facade::class);
        }
    }
}
