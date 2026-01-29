<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->tenant) {
            if (!$user->tenant->isActive()) {
                // Se for API, retorna JSON
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Tenant inactive or subscription expired.',
                        'code' => 'TENANT_INACTIVE'
                    ], 403);
                }

                abort(403, 'Sua conta está inativa. Por favor, renove sua assinatura.');
            }
        }

        return $next($request);
    }
}
