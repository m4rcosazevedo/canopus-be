<?php

namespace App\Modules\Menu\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class MenuServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['api', 'auth:sanctum'])
            ->prefix('api/menus')
            ->group(function () {
                Route::get('/', [\App\Modules\Menu\Http\Controllers\MenuController::class, 'index']);
            });
    }
}
