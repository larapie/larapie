<?php

Route::get('/authorized', function () {
    return response()->json(Auth::user() !== null);
})->middleware(\App\Modules\Auth0\Middleware\Auth0AuthenticationMiddleware::class);
