<?php

namespace App\Modules\Permission\Http\Middleware;

use App\Modules\UserType\Enums\UserTypeIdEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Manipula a requisição.
     *
     * @param Request $request
     * @param  Closure  $next
     * @param  string  ...$permissions
     * @return Response
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->user_type_id === UserTypeIdEnum::ROOT->value) {
            return $next($request);
        }

        if (!$user->hasAllPermissions($permissions)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
