<?php

namespace Adaptdk\GsvAuth0Provider\Http\Middleware;

use Closure;

class Auth0Authenticate
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (!auth($guard)->check()) {
            return response(['error' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
