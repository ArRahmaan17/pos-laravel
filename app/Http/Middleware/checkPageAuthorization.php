<?php

namespace App\Http\Middleware;

use App\Models\AppMenu;
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
            if (AppMenu::where('route', $request->route()->action['as'])->where('dev_only', 0)->count() == 1) {
                return $next($request);
            } else {
                return redirect()->route('home')->with('error', "You don't have permission to access " . implode(' > ',explode('.',implode('', explode('.index', $request->route()->action['as'])))));
            }
        } else {
            if (AppMenu::join('customer_role_accessibilities as cra', 'app_menus.id', '=', 'cra.menuId')->where('cra.roleId', session('userLogged')['role']['id'])->where('route', $request->route()->action['as'])->where('dev_only', 0)->count() == 1) {
                return $next($request);
            } else {
                return redirect()->route('home')->with('error', "You don't have permission to access " . implode(' > ',explode('.',implode('', explode('.index', $request->route()->action['as'])))));
            }
        }
    }
}
