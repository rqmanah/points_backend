<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

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
//    public function boot(): void
//    {
//        //
//        foreach (File::directories(app_path("Modules")) as $moduleDir) {
//
//            View::addLocation($moduleDir . "/views");
//        }
//    }
}
