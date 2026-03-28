<?php

namespace App\Providers;

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
                        fn ($v) => $v === 'App\Http\Middleware\checkPageAuthorization'
                    ) && ! array_find($route->action['middleware'], fn ($v) => $v === 'App\Http\Middleware\AuthorizationOnly');
                })->all();
                $routes = array_values(collect($routes)->map(
                    fn ($rt) => [
                        'ref' => $rt->action['as'],
                        'parent' => $rt->action['parent'],
                        'icon' => $rt->action['icon'] ?? null,
                        'parent-icon' => $rt->action['parent-icon'] ?? null,
                        'name' => str(str_replace('-', ' ', explode('/', $rt->uri)[1] ?? $rt->uri))->title(),
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
            $parent['ref'] = '#'.(is_array($parent['parent']) ? $parent['parent'][1] : $parent['parent']);
            $parent['name'] = Str(is_array($parent['parent']) ? $parent['parent'][1] : $parent['parent'])->title();
            $parent['id'] = is_array($parent['parent']) ? $parent['parent'][1] : $parent['parent'];
            $parent['parent'] = null;
            $parent['icon'] = $parent['parent-icon'];

            return $parent;
        }, array_filter($parents, function ($parent) {
            return $parent['parent'] !== null;
        }));
        $menus = array_merge(array_map(function ($menu) {
            $menu['parent'] = is_array($menu['parent']) ? $menu['parent'][1] : $menu['parent'];

            return $menu;
        }, $menus), $parents);
        $menus = arrayTree($menus);
        View::composer('template.parent', function ($view) use ($menus, $subscriptions) {
            $view->with([
                'menus' => $menus,
                'subscriptions' => $subscriptions,
                'serverTime' => now()->format('Y-m-d H:i:s'),
            ]);
        });
    }
}
