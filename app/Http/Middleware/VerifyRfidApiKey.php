<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyRfidApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.rfid.api_key');

        if (!$expected) {
            return response()->json([
                'message' => 'RFID API key is not configured.',
            ], 500);
        }

        $provided = $request->header('X-API-KEY');

        if (!is_string($provided) || !hash_equals($expected, $provided)) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
