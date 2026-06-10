<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Validación para crear/editar categorías. Solo admin (ver authorize()). */
class CategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /** Genera el slug desde el nombre y normaliza el booleano 'activo' antes de validar. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug((string) $this->input('nombre')),
            'activo' => $this->boolean('activo'),
        ]);
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categoria')?->id;

        return [
            'nombre' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categorias', 'slug')->ignore($categoriaId),
            ],
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la categoria es requerido.',
            'slug.unique' => 'Ya existe una categoria con este nombre.',
        ];
    }
}
