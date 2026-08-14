<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('api');
        abort_if($user === null, 401, 'Unauthenticated.');
        if (! $user->is_active) {
            throw new AuthorizationException('Your account is inactive.');
        }

        return $next($request);
    }
}
