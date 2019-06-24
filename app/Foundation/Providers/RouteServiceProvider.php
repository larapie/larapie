<?php

namespace App\Foundation\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends \Larapie\Core\Providers\RouteServiceProvider
{
    public function register()
    {
        $this->mapWebRoutes('',
            base_path(config('larapie.foundation.path') . '/Routes/web.php'),
            config('larapie.foundation.namespace') . '\\Http\\Controllers\Web'
        );

        $this->mapApiRoutes('',
            base_path(config('larapie.foundation.path') . '/Routes/api.php'),
            config('larapie.foundation.namespace') . '\\Http\\Controllers\Api'
        );
    }
}
