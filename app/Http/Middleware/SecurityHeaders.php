<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (! config('app.debug')) {
            $response->headers->set('Content-Security-Policy', implode('; ', [
                "default-src 'self'",
                "img-src 'self' data:",
                "style-src 'self' https://fonts.googleapis.com",
                "font-src 'self' https://fonts.gstatic.com data:",
                "script-src 'self' https://cdn.jsdelivr.net",
                "connect-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
            ]));
        }

        return $response;
    }
}
