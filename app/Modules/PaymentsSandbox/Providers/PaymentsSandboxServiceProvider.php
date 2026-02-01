<?php

namespace App\Modules\PaymentsSandbox\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Modules\PaymentsSandbox\Http\Middleware\IdempotencyMiddleware;

class PaymentsSandboxServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::prefix('api/sandbox')
            ->middleware(['api', IdempotencyMiddleware::class])
            ->namespace('App\Modules\PaymentsSandbox\Controllers')
            ->group(__DIR__ . '/../routes/api.php');
    }

    public function register(): void
    {
        //
    }
}
