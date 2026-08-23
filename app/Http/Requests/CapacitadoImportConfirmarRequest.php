<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación para confirmar la importación masiva de capacitados tras la previsualización. Admin o instructor. */
class CapacitadoImportConfirmarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isGestor() ?? false;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'uuid'],
            'filas' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'El token de sesión es requerido.',
            'token.uuid'     => 'El token de sesión no es válido.',
        ];
    }
}
