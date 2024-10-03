<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty(session('userLogged')) && empty(session('userLogged')['company']) && empty(session('userLogged')['role'])) {
            return $next($request);
        } elseif (! empty(session('userLogged')) && empty(session('userLogged')['company']) && ! empty(session('userLogged')['role'])) {
            return redirect()->route('select-customer-company');
        } else {
            return redirect()->route('home');
        }
    }
}
