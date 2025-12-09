<?php

namespace App\Providers;

use App\Models\UserManagement\Permission;
use App\Models\AppSubscription;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (app()->environment('testing') || ! Schema::hasTable('permissions')) {
            $menus = [];
            $subscriptions = [];
        } else {
            try {
                $routes = collect(Route::getRoutes())->filter(function ($route) {
                    return str_contains($route->getActionName(), '@index') && array_find(
                        $route->action['middleware'],
                        fn($v) => $v === 'App\Http\Middleware\checkPageAuthorization'
                    ) && !array_find($route->action['middleware'], fn($v) => $v === 'App\Http\Middleware\AuthorizationOnly');
                })->all();
                $routes = array_values(collect($routes)->map(
                    fn($rt) =>
                    [
                        'ref' => $rt->action['as'],
                        'parent' => $rt->defaults['module'] ?? null,
                        'icon' => $rt->defaults['icon'] ?? null,
                        'id' => explode('/', $rt->uri)[1] ?? $rt->uri,
                    ]
                )->toArray());
                $menus = $routes;
                $subscriptions = [];
            } catch (\Exception $e) {
                $menus = [];
                $subscriptions = [];
            }
        }
        $parents = removeDuplicate($menus, 'parent');
        $parents = array_map(function ($parent) {
            $parent['ref'] = '#' . $parent['parent'];
            $parent['id'] = $parent['parent'];
            $parent['parent'] = null;
            return $parent;
        }, array_filter($parents, function ($parent) {
            return $parent['parent'] !== null;
        }));
        $menus = array_merge($menus, $parents);
        $menus = arrayTree($menus);
// dd($menus);
        View::composer('*', function ($view) use ($menus, $subscriptions) {
            $view->with([
                'menus' => $menus,
                'subscriptions' => $subscriptions,
                'serverTime' => now()->format('Y-m-d H:i:s'),
            ]);
        });
    }
}
