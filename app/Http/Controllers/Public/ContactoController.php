<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactoRequest;
use App\Models\Mensaje;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Formulario de contacto público. Valida los datos, verifica el
 * captcha (reCAPTCHA v2) contra la API de Google y guarda el
 * mensaje en la bandeja de entrada del admin.
 */
class ContactoController extends Controller
{
    /** Muestra el formulario de contacto. */
    public function index()
    {
        return view('public.contacto');
    }

    /** Procesa el envío del formulario de contacto. */
    public function enviar(ContactoRequest $request)
    {
        $datos = $request->validated();

        try {
            $recaptcha = Http::timeout(5)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret'),
                'response' => $datos['g-recaptcha-response'],
                'remoteip' => $request->ip(),
            ]);
        } catch (ConnectionException $e) {
            Log::warning('reCAPTCHA: fallo de conexión con Google', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors(['g-recaptcha-response' => 'No se pudo verificar el captcha. Inténtalo de nuevo.']);
        }

        if (!$recaptcha->successful() || $recaptcha->json('success') !== true) {
            Log::warning('reCAPTCHA v2: verificación fallida', [
                'errors' => $recaptcha->json('error-codes'),
                'ip'     => $request->ip(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['g-recaptcha-response' => 'La verificación del captcha falló. Inténtalo de nuevo.']);
        }

        Mensaje::create([
            'nombre'  => $datos['nombre'],
            'correo'  => $datos['correo'],
            'mensaje' => $datos['mensaje'],
            'estado'  => Mensaje::ESTADO_NUEVO,
            'ip'      => $request->ip(),
        ]);

        return redirect()
            ->route('contacto')
            ->with('success', '¡Gracias! Tu mensaje fue enviado correctamente. Te responderemos pronto.');
    }
}
