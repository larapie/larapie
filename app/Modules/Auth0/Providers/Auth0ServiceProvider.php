<?php

namespace App\Modules\Auth0\Providers;

use Auth0\Login\LaravelCacheWrapper;
use Auth0\SDK\Helpers\Cache\CacheHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class Auth0ServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Auth0\Login\LoginServiceProvider::class);

        $this->app->bind(CacheHandler::class, function () {
            return new LaravelCacheWrapper(Cache::store());
        });
    }
}
