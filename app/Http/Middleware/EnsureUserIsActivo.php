<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea el acceso a usuarios con activo = false.
 * Si el usuario en sesión fue desactivado por un admin, lo desloguea y redirige al login.
 * Aplicado a todas las rutas del panel /admin.
 */
class EnsureUserIsActivo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al administrador.']);
        }

        return $next($request);
    }
}
