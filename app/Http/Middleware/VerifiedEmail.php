<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedEmail
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
        if (! $user->hasVerifiedEmail()) {
            throw new AuthorizationException('Your email is not verified. Please verify your email first.');
        }

        return $next($request);
    }
}
