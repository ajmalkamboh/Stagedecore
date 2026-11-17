<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('business_settings')) {
                    $view->with('globalSettings', \App\Models\BusinessSetting::current());
                }
            } catch (\Throwable $e) {
                // In case migration has not run
            }
        });
    }
}
