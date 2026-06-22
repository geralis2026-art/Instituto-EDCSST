<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación para confirmar la importación masiva de capacitados tras la previsualización. Solo admin. */
class CapacitadoImportConfirmarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'size:40'],
            'filas' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'El token de sesión es requerido.',
            'token.size'     => 'El token de sesión no es válido.',
        ];
    }
}
