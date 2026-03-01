<?php

namespace App\Modules\Area\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AreaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware(['api', 'auth:sanctum'])
            ->prefix('api')
            ->group(base_path('app/Modules/Area/Routes/api.php'));
    }
}
