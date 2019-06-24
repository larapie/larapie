<?php

namespace App\Foundation\Providers;

class RouteServiceProvider extends \Larapie\Core\Providers\RouteServiceProvider
{
    public function register()
    {
        $this->mapWebRoutes('',
            base_path(config('larapie.foundation.path').'/Routes/web.php'),
            config('larapie.foundation.namespace').'\\Http\\Controllers\Web'
        );

        $this->mapApiRoutes('',
            base_path(config('larapie.foundation.path').'/Routes/api.php'),
            config('larapie.foundation.namespace').'\\Http\\Controllers\Api'
        );
    }
}
