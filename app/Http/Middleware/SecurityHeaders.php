<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    private const CSP = [
        "default-src 'self'",

        // Scripts propios + reCAPTCHA (Google) + Chart.js (jsdelivr)
        "script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://cdn.jsdelivr.net",

        // Estilos propios + inline (Tailwind/Blade) + fuentes Bunny
        "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",

        // Fuentes
        "font-src 'self' https://fonts.bunny.net",

        // Imágenes: propias, data URIs y HTTPS externo (imágenes de cursos, etc.)
        "img-src 'self' data: https:",

        // iframes: reCAPTCHA usa iframe de Google
        "frame-src https://www.google.com",

        // Conexiones AJAX/fetch solo al propio servidor
        "connect-src 'self'",

        // Bloquear plugins (Flash, etc.)
        "object-src 'none'",

        // Evitar inyección de base href
        "base-uri 'self'",

        // Formularios solo pueden enviar al mismo origen
        "form-action 'self'",
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');
        $response->headers->set('Content-Security-Policy', implode('; ', self::CSP));

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
