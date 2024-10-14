<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class unSelectCustomerCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! empty(session('userLogged')) && empty(session('userLogged')['company']) && ! empty(session('userLogged')['role'])) {
            return $next($request);
        } elseif (empty(session('userLogged'))) {
            return redirect()->route('auth.login')->with('error', 'Please report to your manager to add the accessibility menu role');
        } elseif (! empty(session('userLogged'))) {
            return redirect()->route('home');
        }
    }
}
