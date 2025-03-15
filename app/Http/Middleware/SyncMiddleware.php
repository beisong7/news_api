<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('SyncKey')) {
            return response()->json([
                'status' => 'error',
                'message' => 'SyncKey header is required'
            ], 400);
        }

        // validate the header value if needed
        $apiKey = $request->header('SyncKey');
        if ($apiKey !== config('secret.sync_key')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Sync key provided'
            ], 401);
        }

        return $next($request);
    }
}
