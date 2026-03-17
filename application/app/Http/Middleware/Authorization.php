<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! empty(session('userLogged')['user']) && ! empty(session('userLogged')['company'])) {
            return $next($request);
        } else {
            return redirect()->route('auth.login');
        }
    }
}
