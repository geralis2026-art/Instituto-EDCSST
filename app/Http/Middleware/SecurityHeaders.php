<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Agrega cabeceras de seguridad a todas las respuestas: CSP, HSTS
 * (solo producción), X-Frame-Options, X-Content-Type-Options,
 * Referrer-Policy y Permissions-Policy.
 *
 * Genera un nonce CSP por petición (disponible como
 * `$request->attributes->get('csp_nonce')`) para permitir scripts
 * inline confiables sin habilitar 'unsafe-inline' en script-src.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);

        $response = $next($request);

        $csp = [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' https://www.google.com https://www.gstatic.com https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' https://fonts.bunny.net",
            "img-src 'self' data: blob: https://www.gstatic.com",
            "frame-src https://www.google.com",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];

        // Solo en producción: fuerza que recursos http:// se carguen por https://
        if (app()->environment('production')) {
            $csp[] = "upgrade-insecure-requests";
        }

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        // HSTS solo en producción — fuerza HTTPS por 2 años con preload
        if (app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=63072000; includeSubDomains; preload'
            );
        }

        return $response;
    }
}
