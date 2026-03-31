<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the role middleware alias used in route definitions
        \Illuminate\Support\Facades\Route::aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
    }
}
