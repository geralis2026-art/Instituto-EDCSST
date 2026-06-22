<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación del formulario público de verificación de autenticidad de certificados. */
class VerificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'Por favor ingresa el código del certificado.',
        ];
    }
}
