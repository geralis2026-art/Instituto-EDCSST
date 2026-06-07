<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso a usuarios con rol = 'admin'.
 * Los capacitadores reciben 403 al intentar acceder a rutas exclusivas de admin.
 * Aplicado en el segundo grupo de rutas /admin (CRUD completo, mensajes, usuarios).
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
