<?php

namespace App\Foundation\Providers;

use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends \Larapie\Core\Providers\RouteServiceProvider
{
    public function register()
    {
        $this->mapWebRoutes('',
            base_path(config('larapie.foundation.path').'/Routes/web.php'),
            config('larapie.foundation.namespace').'\\Http\\Controllers\Web'
        );

        $this->mapApiNoAuth('',
            base_path(config('larapie.foundation.path').'/Routes/api.php'),
            config('larapie.foundation.namespace').'\\Http\\Controllers\Api'
        );
    }

    protected function mapApiNoAuth(string $prefix, string $path, string $namespace)
    {
        Route::middleware('api:noauth')
            ->domain(config('larapie.api_url') ?? config('app.url'))
            ->prefix($prefix = config('larapie.api_url') === null ? 'api/'.$prefix : $prefix)
            ->namespace($namespace.'\\'.'Api')
            ->group($path);
    }
}
