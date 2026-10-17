<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // CSP: allow self, inline styles/scripts for SvelteKit, and images from self + data + storage + AR.
        // Filament admin (Alpine.js/Livewire) evaluates expressions at runtime,
        // so it needs 'unsafe-eval' — serve a dedicated policy there.
        if ($request->is('admin*') || $request->is('livewire/*')) {
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' blob:; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self'";
        } else {
            $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data: https://dkstatics-public.digikala.com https://images.unsplash.com https://i.pravatar.cc; font-src 'self' data:; connect-src 'self' https://modelviewer.dev https://unpkg.com; frame-ancestors 'self'";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
