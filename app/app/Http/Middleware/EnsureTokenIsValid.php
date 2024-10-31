<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Authorization token is required.'], 400);
        }

        $token = str_replace('Bearer ', '', $request->header('Authorization'));

        if ($token !== env('API_TOKEN')) {
            return response()->json(['error' => 'Invalid authorization token.'], 401);
        }

        return $next($request);
    }
}
