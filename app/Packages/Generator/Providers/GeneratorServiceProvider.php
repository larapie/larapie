<?php

namespace App\Packages\Horizon\Providers;

use Illuminate\Support\ServiceProvider;
use Larapie\Generator\LarapieGeneratorServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! $this->app->environment('production')) {
            $this->app->register(LarapieGeneratorServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
