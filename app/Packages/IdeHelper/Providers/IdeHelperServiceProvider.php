<?php


namespace App\Packages\IdeHelper\Providers;


use Illuminate\Support\ServiceProvider;

class IdeHelperServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->environment('production'))
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
    }
}