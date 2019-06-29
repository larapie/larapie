<?php

namespace App\Foundation\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Larapie\Core\Support\Facades\Larapie;

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
        if ($this->requestComesFromApiDomain($request)) {
            $request->headers->set('Accept', 'application/json');
            $request->headers->set('Content-Type', 'application/json');
        }

        return $next($request);
    }

    protected function requestComesFromApiDomain($request)
    {
        return Str::contains($this->getFormattedRequestUrl($request), $this->getFormattedApiUrl());
    }

    protected function getFormattedApiUrl()
    {
        return parse_url(Larapie::getApiUrl())['host'].(parse_url(Larapie::getApiUrl())['path'] ?? '');
    }

    protected function getFormattedRequestUrl($request)
    {
        return parse_url($request->url())['host'].(parse_url($request->url())['path'] ?? '');
    }
}
