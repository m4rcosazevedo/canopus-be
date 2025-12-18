<?php

use App\Http\Middleware\AuditTransaction;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(
            prepend: [
                ForceJsonResponse::class
            ]
        );

        $middleware->appendToGroup('api', [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // Para aplicar em TODAS as requisições (Web e API)
        $middleware->append(AuditTransaction::class);
        /* Ou, se quiser aplicar apenas em grupos específicos:
           $middleware->web(append: [AuditTransaction::class]);
           $middleware->api(append: [AuditTransaction::class]);
        */

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
