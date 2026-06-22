<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación para actualizar el estado y notas internas de un mensaje de contacto. Solo admin. */
class MensajeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'estado'         => ['required', 'in:nuevo,leido,respondido'],
            'notas_internas' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'estado.required' => 'El estado es requerido.',
            'estado.in'       => 'El estado seleccionado no es válido.',
        ];
    }
}
