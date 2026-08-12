<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso a usuarios con rol 'admin' o 'instructor' (multi-instructor).
 * El capacitador (solo lectura + certificados) recibe 403.
 * El scope de propiedad (PropietarioScope) se encarga de que un instructor
 * solo pueda ver/editar sus propios registros dentro de estas rutas.
 */
class EnsureUserIsGestor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! ($user->isAdmin() || $user->isInstructor())) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
