<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación del formulario público de contacto (incluye campo reCAPTCHA). */
class ContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'               => ['required', 'string', 'max:150'],
            'correo'               => ['required', 'email', 'max:150'],
            'mensaje'              => ['required', 'string', 'min:10', 'max:2000'],
            'acepta_politica'      => ['accepted'],
            'g-recaptcha-response' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'               => 'Por favor ingresa tu nombre.',
            'correo.required'               => 'Por favor ingresa tu correo.',
            'correo.email'                  => 'El correo no tiene un formato válido.',
            'mensaje.required'              => 'Por favor escribe tu mensaje.',
            'mensaje.min'                   => 'El mensaje debe tener al menos 10 caracteres.',
            'acepta_politica.accepted'      => 'Debes autorizar el tratamiento de tus datos personales para poder enviar el mensaje.',
            'g-recaptcha-response.required' => 'Por favor completa el captcha. Si no lo ves, activa la verificación de seguridad.',
        ];
    }
}
