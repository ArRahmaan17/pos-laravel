<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (getLevel() === 3) {
            return $next($request);
        } else {
            return redirect()->route('dashboard.index')->with('error', "You don't have permission to access ".implode(' > ', explode('.', implode('', explode('.index', $request->route()->action['as'])))));
        }
    }
}
