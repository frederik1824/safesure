<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyFirebaseWebhookSecret
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = env('FIREBASE_WEBHOOK_SECRET');
        $headerSecret = $request->header('X-SafeSure-Webhook-Secret');

        if (!$secret || $headerSecret !== $secret) {
            return response()->json(['message' => 'Unauthorized Webhook Access'], 401);
        }

        return $next($request);
    }
}
