<?php

namespace App\Providers;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureRateLimiting();

        View::composer('*', function ($view) {
            $view->with('cspNonce', request()->attributes->get('csp_nonce', ''));
        });

        View::composer('layouts.admin', function ($view) {
            $user = Auth::user();
            $nuevos = ($user instanceof User && $user->isAdmin())
                ? Mensaje::nuevos()->count()
                : 0;

            $view->with('mensajesNuevos', $nuevos);
        });
    }

    /**
     * Rate limiters para las rutas públicas sensibles.
     * Todos limitan por IP para evitar abuso desde una misma conexión.
     *   - consulta-publica:    10/min  (búsqueda de certificados)
     *   - verificacion-publica: 10/min (verificación de autenticidad)
     *   - contacto-publica:      3/min (formulario de contacto)
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('consulta-publica', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(fn() => response()->json(
                    ['message' => 'Demasiadas consultas. Espera un momento antes de intentar de nuevo.'], 429
                ));
        });

        RateLimiter::for('verificacion-publica', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(fn() => response()->json(
                    ['message' => 'Demasiadas verificaciones. Espera un momento antes de intentar de nuevo.'], 429
                ));
        });

        RateLimiter::for('contacto-publica', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())
                ->response(fn() => back()
                    ->withInput()
                    ->withErrors(['mensaje' => 'Has enviado demasiados mensajes. Espera unos minutos antes de intentar de nuevo.'])
                );
        });
    }
}
