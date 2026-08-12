<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * LoginRequest::authenticate() intenta el guard "web" (empleados) y
     * luego "capacitados" (aula virtual); aquí solo hace falta ver cuál
     * quedó autenticado para redirigir al panel correspondiente.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::guard('capacitados')->check()) {
            $capacitado = Auth::guard('capacitados')->user();

            if ($capacitado->debe_cambiar_password) {
                return redirect()->route('aula.password.cambiar');
            }

            return redirect()->intended(route('aula.dashboard', absolute: false));
        }

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session (cierra el guard que esté activo).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::guard('capacitados')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
