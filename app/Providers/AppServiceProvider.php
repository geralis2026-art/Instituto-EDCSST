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
     * Rate limiters para rutas públicas y panel admin.
     *
     * Públicas — limitan por IP:
     *   - consulta-publica:      10/min  (búsqueda de certificados)
     *   - verificacion-publica:  10/min  (verificación de autenticidad)
     *   - contacto-publica:       3/min  (formulario de contacto)
     *
     * Admin — limitan por ID de usuario autenticado:
     *   - admin-general:        120/min  (navegación y lectura — 2 req/s)
     *   - admin-escritura:       30/min  (create/store/update/destroy)
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('consulta-publica', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(fn() => back()
                    ->withInput()
                    ->withErrors(['valor' => 'Demasiadas consultas. Espera un momento antes de intentar de nuevo.'])
                );
        });

        RateLimiter::for('verificacion-publica', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(fn() => back()
                    ->withInput()
                    ->withErrors(['codigo' => 'Demasiadas verificaciones. Espera un momento antes de intentar de nuevo.'])
                );
        });

        RateLimiter::for('contacto-publica', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip())
                ->response(fn() => back()
                    ->withInput()
                    ->withErrors(['mensaje' => 'Has enviado demasiados mensajes. Espera unos minutos antes de intentar de nuevo.'])
                );
        });

        RateLimiter::for('admin-general', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?? $request->ip())
                ->response(fn() => redirect()->back()
                    ->withErrors(['throttle' => 'Demasiadas solicitudes. Espera un momento e intenta de nuevo.'])
                );
        });

        RateLimiter::for('admin-escritura', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?? $request->ip())
                ->response(fn() => redirect()->back()
                    ->withErrors(['throttle' => 'Has realizado demasiadas operaciones seguidas. Espera un momento.'])
                );
        });
    }
}
