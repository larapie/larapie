<?php

namespace App\Foundation\Http\Middleware;

use Closure;

class InjectApplicationJsonHeader
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->expectsJson())
            $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
