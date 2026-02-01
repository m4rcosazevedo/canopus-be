<?php

namespace App\Modules\PaymentsSandbox\Http\Middleware;

use App\Modules\PaymentsSandbox\Models\SandboxIdempotency;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-Idempotency-Key');

        if (!$key) {
            return $next($request);
        }

        $cached = SandboxIdempotency::where('key', $key)->first();

        if ($cached) {
            return response()->json($cached->response_body, $cached->response_code)
                ->header('X-Idempotency-Hit', 'true');
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            SandboxIdempotency::create([
                'key' => $key,
                'response_body' => json_decode($response->getContent(), true),
                'response_code' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }
}
