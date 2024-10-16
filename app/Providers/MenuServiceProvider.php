<?php

namespace App\Providers;

use App\Models\AppMenu;
use App\Models\AppSubscription;
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
        $sidebarAppMenu = AppMenu::where('place', 0)->get()->setHidden([])->toArray();
        $profileAppMenu = AppMenu::where('place', 1)->get()->setHidden([])->toArray();
        $sidebarAppMenu = buildTree($sidebarAppMenu);
        $profileAppMenu = buildTree($profileAppMenu);
        $subscriptions = AppSubscription::all();
        // $sidebarAppMenu = [];
        // $profileAppMenu = [];
        // $sidebarAppMenu = buildTree($sidebarAppMenu);
        // $profileAppMenu = buildTree($profileAppMenu);
        View::composer('*', function ($view) use ($sidebarAppMenu, $profileAppMenu, $subscriptions) {
            $view->with([
                'sidebarAppMenu' => $sidebarAppMenu,
                'profileAppMenu' => $profileAppMenu,
                'subscriptions' => $subscriptions,
            ]);
        });
    }
}
