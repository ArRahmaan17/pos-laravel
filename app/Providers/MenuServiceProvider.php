<?php

namespace App\Providers;

use App\Models\UserManagement\Permission;
use App\Models\AppSubscription;
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
        if (app()->environment('testing') || ! Schema::hasTable('permissions') || ! Schema::hasTable('app_subscriptions')) {
            $sidebarAppMenu = [];
            $profileAppMenu = [];
            $subscriptions = [];
        } else {
            try {
                $sidebarAppMenu = Permission::where('place', 0)->orderBy('created_at')->get()->setHidden([])->toArray();
                $profileAppMenu = Permission::where('place', 1)->orderBy('created_at')->get()->setHidden([])->toArray();
                $subscriptions = AppSubscription::all();
            } catch (\Exception $e) {
                $sidebarAppMenu = [];
                $profileAppMenu = [];
                $subscriptions = [];
            }
        }

        $sidebarAppMenu = buildTree($sidebarAppMenu);
        $profileAppMenu = buildTree($profileAppMenu);

        View::composer('*', function ($view) use ($sidebarAppMenu, $profileAppMenu, $subscriptions) {
            $view->with([
                'sidebarAppMenu' => $sidebarAppMenu,
                'profileAppMenu' => $profileAppMenu,
                'subscriptions' => $subscriptions,
                'serverTime' => now()->format('Y-m-d H:i:s'),
            ]);
        });
    }
}
