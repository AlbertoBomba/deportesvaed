<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 1. AÑADE ESTA LÍNEA:
use Lunar\Admin\Support\Facades\LunarPanel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 2. AÑADE ESTA LÍNEA:
        LunarPanel::register();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
