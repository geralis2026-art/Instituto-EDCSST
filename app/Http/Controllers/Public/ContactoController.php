<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Mensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

        $recaptcha = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!($recaptcha->json('success') === true)) {
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
