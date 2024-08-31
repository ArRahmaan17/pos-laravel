<?php

namespace App\Providers;

use App\Models\AppMenu;
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
        $sidebarAppMenu = AppMenu::where('place', 0)->get()->toArray();
        $profileAppMenu = AppMenu::where('place', 1)->get()->toArray();
        $sidebarAppMenu = buildTree($sidebarAppMenu);
        $profileAppMenu = buildTree($profileAppMenu);
        dd($sidebarAppMenu, $profileAppMenu);
        View::composer('*', function ($view) use ($sidebarAppMenu, $profileAppMenu) {
            $view->with([
                'sidebarAppMenu' => $sidebarAppMenu,
                'profileAppMenu' => $profileAppMenu,
            ]);
        });
    }
}
