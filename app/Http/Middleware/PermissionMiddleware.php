<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // if (!Auth::user() || !Auth::user()->hasPermissionTo($permission)) {
        //     return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        // }
        return $next($request);
        // return $next($request);
    }
}

