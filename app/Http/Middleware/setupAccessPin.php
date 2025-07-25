<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class setupAccessPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! empty(session('userLogged')['user']['pin']) && ($request->getBasePath() !== 'privacy/request-access-pin' && $request->getBasePath() != 'privacy/access-pin')) {
            return $next($request);
        } else {
            return redirect()->route('privacy.request-access-pin');
        }
    }
}
