<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación de la búsqueda pública de certificados por documento o código único. */
class ConsultaBuscarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_busqueda' => ['required', 'in:documento,codigo'],
            'valor'         => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'valor.required' => 'Por favor ingresa un valor de búsqueda.',
        ];
    }
}
