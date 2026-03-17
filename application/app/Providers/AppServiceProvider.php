<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('database.default') === 'sqlite') {
            config()->set('database-triggers.enabled', false);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::macro('meta', function (array $attributes, \Closure $callback) {
            $attributes = array_merge([
            ], $attributes);

            return Route::group($attributes, $callback);
        });
    }
}
