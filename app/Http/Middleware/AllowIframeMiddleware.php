<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowIframeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only relax framing in local/dev (for VS Code preview).
        // In production, leave security headers intact — stripping them
        // conflicts with LiteSpeed Cache and triggers Chrome error-frame reloads.
        if (app()->environment('local')) {
            $response->headers->remove('X-Frame-Options');
        }

        return $response;
    }
}
