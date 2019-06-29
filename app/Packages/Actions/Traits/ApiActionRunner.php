<?php

namespace App\Packages\Actions\Traits;

use Illuminate\Http\Request;
use Lorisleiva\Actions\Action;

trait ApiActionRunner
{
    protected function callFromApi(Action $action)
    {
        $request = tap(request(), function (Request &$request) {
            $request->headers->set('Accept', 'application/json');
        });

        return $action->runAsController($request)->toResponse($request)->getData(true)['data'];
    }
}
