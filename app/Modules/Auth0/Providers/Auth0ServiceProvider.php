<?php

namespace App\Modules\Auth0\Providers;

use App\Modules\Auth0\Services\Auth0Service;
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

        $this->app->bind(
            CacheHandler::class,
            function() {
                static $cacheWrapper = null;
                if ($cacheWrapper === null) {
                    $cache = Cache::store();
                    $cacheWrapper = new LaravelCacheWrapper($cache);
                }
                return $cacheWrapper;
            });

        // Override the current Auth0 Service Provider until they have resolved their caching issue

    }
}
