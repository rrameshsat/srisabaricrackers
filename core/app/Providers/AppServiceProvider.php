<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useBootstrap();
        view()->composer('*', function ($settings) {
            $shared = app(SettingsService::class)->sharedViewData();
            $settings->with('setting', $shared['setting']);
            $settings->with('extra_settings', $shared['extra_settings']);
            $settings->with('menus', $shared['menus']);

            if (!session()->has('popup')) {
                view()->share('visit', 1);
            }
            session()->put('popup', 1);
        });
    }

    public function register()
    {
        $this->app->singleton(SettingsService::class);
    }
}
