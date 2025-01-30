<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {

            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'message' => 'Token is Invalid'
                ], 401);
            } else if ($e instanceof TokenExpiredException) {
                return response()->json([
                    'message' => 'Token is Expired'
                ], 401);
            } else if ($e instanceof TokenBlacklistedException) {
                return response()->json([
                    'message' => 'Token is in black list'
                ], 401);
            } else {
                return response()->json(['message' => 'Token is not found'], 401);
            }
        }
        return $next($request);
    }
}
