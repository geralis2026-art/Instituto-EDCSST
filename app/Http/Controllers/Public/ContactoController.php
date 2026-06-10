<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Mensaje;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactoController extends Controller
{
    /**
     * Muestra el formulario de contacto.
     */
    public function index()
    {
        return view('public.contacto');
    }

    /**
     * Procesa el envío del formulario de contacto.
     */
    public function enviar(Request $request)
    {
        $datos = $request->validate([
            'nombre'              => 'required|string|max:150',
            'correo'              => 'required|email|max:150',
            'mensaje'             => 'required|string|min:10|max:2000',
            'g-recaptcha-response' => 'required|string',
        ], [
            'nombre.required'               => 'Por favor ingresa tu nombre.',
            'correo.required'               => 'Por favor ingresa tu correo.',
            'correo.email'                  => 'El correo no tiene un formato válido.',
            'mensaje.required'              => 'Por favor escribe tu mensaje.',
            'mensaje.min'                   => 'El mensaje debe tener al menos 10 caracteres.',
            'g-recaptcha-response.required' => 'Por favor completa el captcha.',
        ]);

        try {
            $recaptcha = Http::timeout(5)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret'),
                'response' => $request->input('g-recaptcha-response'),
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
            'nombre' => $datos['nombre'],
            'correo' => $datos['correo'],
            'mensaje' => $datos['mensaje'],
            'estado' => Mensaje::ESTADO_NUEVO,
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('contacto')
            ->with('success', '¡Gracias! Tu mensaje fue enviado correctamente. Te responderemos pronto.');
    }
}
