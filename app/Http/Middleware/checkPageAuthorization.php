<?php

namespace App\Http\Middleware;

use App\Models\UserManagement\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkPageAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (getRole() == 'Developer') {
            return $next($request);
        } elseif (getRole() == 'Manager') {
            if (Permission::where('route', $request->route()->action['as'])->where('dev_only', 0)->count() == 1) {
                return $next($request);
            } else {
                return redirect()->route('dashboard.index')->with('error', "You don't have permission to access ".implode(' > ', explode('.', implode('', explode('.index', $request->route()->action['as'])))));
            }
        } else {
            if (Permission::join('customer_role_accessibilities as cra', 'permissions.id', '=', 'cra.menuId')->where('cra.role_id', session('userLogged')['role']['id'])->where('route', $request->route()->action['as'])->where('dev_only', 0)->count() == 1) {
                return $next($request);
            } else {
                return redirect()->route('dashboard.index')->with('error', "You don't have permission to access ".implode(' > ', explode('.', implode('', explode('.index', $request->route()->action['as'])))));
            }
        }
    }
}
