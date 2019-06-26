<?php

namespace App\Modules\Auth0\Middleware;

use App\Modules\Auth0\Actions\CreateOrUpdateUserFromTokenAction;
use Auth0\Login\Contract\Auth0UserRepository;
use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\Exception\InvalidTokenException;
use Closure;
use Illuminate\Http\Request;

class Auth0AuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = (new CreateOrUpdateUserFromTokenAction([
                "token" => $request->bearerToken()
            ]))->run();

            if (! $user) {
                return response()->json(['error' => 'Unauthorized user.'], 401);
            }

            \Auth::login($user);
        } catch (InvalidTokenException $e) {
            return response()->json(['error' => 'Invalid or no token set.'], 401);
        } catch (CoreException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
