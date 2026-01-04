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
        // Set default locale to Indonesian
        app()->setLocale('id');
        config(['app.locale' => 'id']);
        config(['app.fallback_locale' => 'en']);
    }
}
