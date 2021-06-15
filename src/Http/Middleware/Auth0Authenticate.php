<?php

namespace Adaptdk\GsvAuth0Provider\Http\Middleware;

use Closure;

class Auth0Authenticate
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return response(['error' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
