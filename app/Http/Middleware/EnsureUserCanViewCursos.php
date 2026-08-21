<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe el acceso de solo lectura a cursos a admin y capacitador.
 * El instructor (multi-instructor) no tiene acceso a la sección de cursos
 * en ninguna forma: solo Edna (admin) puede ver, crear y editar cursos.
 */
class EnsureUserCanViewCursos
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->isInstructor()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
