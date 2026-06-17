<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación para actualizar la configuración del sitio. Solo admin (ver authorize()). */
class ConfiguracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre_instituto' => 'required|string|max:255',
            'descripcion'      => 'nullable|string|max:2000',
            'telefono'         => 'nullable|string|max:30',
            'correo_contacto'  => 'nullable|email|max:255',
            'direccion'        => 'nullable|string|max:500',
            'whatsapp'         => 'nullable|string|max:30',
            'facebook'         => 'nullable|url|max:500',
            'instagram'        => 'nullable|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_instituto.required' => 'El nombre del instituto es requerido.',
            'correo_contacto.email'     => 'El correo de contacto debe ser una dirección válida.',
            'facebook.url'              => 'El enlace de Facebook debe ser una URL válida.',
            'instagram.url'             => 'El enlace de Instagram debe ser una URL válida.',
        ];
    }
}
