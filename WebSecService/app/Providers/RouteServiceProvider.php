<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            // Load web routes
            $webRoutesPath = base_path('routes/web.php');
            if (file_exists($webRoutesPath)) {
                Route::middleware('web')
                    ->group($webRoutesPath);
            }

            // Load API routes
            $apiRoutesPath = base_path('routes/api.php');
            if (file_exists($apiRoutesPath)) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group($apiRoutesPath);
            }
        });
    }
}
