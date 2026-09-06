<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea rutas de módulos de Fase 3 aún no pagados (config/features.php).
 * Devuelve 404 (no 403) para que la sección quede indistinguible de una
 * ruta inexistente mientras no se active.
 */
class EnsureFeatureHabilitada
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (! config("features.{$feature}")) {
            abort(404);
        }

        return $next($request);
    }
}
