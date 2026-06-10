<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validación para crear/editar capacitados. Solo admin (ver authorize()). */
class CapacitadoRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * Obtener las reglas de validación que aplican a la solicitud.
     */
    public function rules(): array
    {
        $capacitadoId = $this->route('capacitado')?->id;

        return [
            'nombre_completo' => 'required|string|max:255',
            'documento' => [
                'required',
                'string',
                'min:4',
                'max:50',
                Rule::unique('capacitados', 'documento')
                    ->ignore($capacitadoId),
            ],
            'correo'    => 'nullable|email|max:150',
            'telefono'  => 'nullable|string|max:20',
            'rh'        => 'nullable|string|max:10',
        ];
    }

    /**
     * Mensajes de validación personalizados en español.
     */
    public function messages(): array
    {
        return [
            'nombre_completo.required' => 'El nombre completo es requerido.',
            'nombre_completo.max' => 'El nombre no puede exceder 255 caracteres.',
            
            'documento.required' => 'El documento es requerido.',
            'documento.unique' => 'Este documento ya está registrado en el sistema.',
            'documento.min' => 'El documento debe tener al menos 4 caracteres.',
            'documento.max' => 'El documento no puede exceder 50 caracteres.',
            
            'correo.email' => 'El correo debe ser una dirección válida.',
            'correo.max' => 'El correo no puede exceder 150 caracteres.',
            
            'telefono.max'  => 'El teléfono no puede exceder 20 caracteres.',
            'rh.max'        => 'El RH no puede exceder 10 caracteres.',
        ];
    }
}
