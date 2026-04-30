<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyFirebaseWebhookSecret
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = env('FIREBASE_WEBHOOK_SECRET');
        $signature = $request->header('X-SafeSure-Signature');

        if (!$secret) {
            return response()->json(['message' => 'System configuration error'], 500);
        }

        if (!$signature) {
            return response()->json(['message' => 'Missing signature'], 401);
        }

        $payload = json_encode($request->all(), JSON_UNESCAPED_SLASHES);
        $computedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($computedSignature, $signature)) {
            Log::warning("Invalid HMAC signature from Firebase Webhook", [
                'received' => $signature,
                'computed' => $computedSignature
            ]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
