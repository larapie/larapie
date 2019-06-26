<?php

namespace App\Packages\Actions\Providers;

use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Lorisleiva\Actions\ActionServiceProvider::class);
    }
}
