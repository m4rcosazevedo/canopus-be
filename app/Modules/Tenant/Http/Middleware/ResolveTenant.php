<?php
namespace App\Modules\Tenant\Http\Middleware;

use Closure;

class ResolveTenant
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            app()->instance('tenant_id', auth()->user()->tenant_id);
        }

        return $next($request);
    }
}
