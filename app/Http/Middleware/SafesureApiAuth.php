<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SafesureApiAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = config('services.safesure.api_token');

        if (empty($configuredToken)) {
            return response()->json([
                'error' => 'API configuration error. Token not set.'
            ], 500);
        }

        // Try getting token from X-API-Key header
        $token = $request->header('X-API-Key');

        // Fallback to Bearer token in Authorization header
        if (!$token) {
            $token = $request->bearerToken();
        }

        if (!$token || $token !== $configuredToken) {
            return response()->json([
                'error' => 'Unauthorized. Invalid or missing API Key.'
            ], 401);
        }

        return $next($request);
    }
}
