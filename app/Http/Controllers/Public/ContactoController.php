<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactoRequest;
use App\Mail\NuevoMensajeContacto;
use App\Models\ConfiguracionSitio;
use App\Models\Mensaje;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        $mensaje = Mensaje::create([
            'nombre'  => $datos['nombre'],
            'correo'  => $datos['correo'],
            'mensaje' => $datos['mensaje'],
            'estado'  => Mensaje::ESTADO_NUEVO,
            'ip'      => $request->ip(),
        ]);

        $this->notificarAlInstituto($mensaje);

        return redirect()
            ->route('contacto')
            ->with('success', '¡Gracias! Tu mensaje fue enviado correctamente. Te responderemos pronto.');
    }

    /** Avisa al instituto por correo. Un fallo aquí no debe romper el envío del mensaje de contacto. */
    private function notificarAlInstituto(Mensaje $mensaje): void
    {
        $correoInstituto = ConfiguracionSitio::obtener()->correo_contacto;

        if (!$correoInstituto) {
            return;
        }

        try {
            Mail::to($correoInstituto)->send(new NuevoMensajeContacto($mensaje));
        } catch (\Throwable $e) {
            report($e);
            Log::warning('No se pudo notificar al instituto sobre un mensaje de contacto nuevo', [
                'mensaje_id' => $mensaje->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
