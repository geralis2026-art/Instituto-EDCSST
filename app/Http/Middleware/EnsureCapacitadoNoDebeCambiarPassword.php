<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si el capacitado tiene debe_cambiar_password = true (primer ingreso al
 * aula virtual, contraseña inicial sin cambiar), lo redirige a la pantalla
 * de cambio obligatorio y bloquea el resto del aula virtual hasta que lo haga.
 * Aplicado a todas las rutas de /aula excepto la de cambio de contraseña.
 */
class EnsureCapacitadoNoDebeCambiarPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $capacitado = Auth::guard('capacitados')->user();

        if ($capacitado && $capacitado->debe_cambiar_password && !$request->routeIs('aula.password.cambiar*')) {
            return redirect()->route('aula.password.cambiar');
        }

        return $next($request);
    }
}
