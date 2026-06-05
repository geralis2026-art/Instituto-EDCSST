<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug((string) $this->input('nombre')),
            'destacado' => $this->boolean('destacado'),
            'activo' => $this->boolean('activo'),
        ]);
    }

    public function rules(): array
    {
        $cursoId = $this->route('curso')?->id;

        return [
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cursos', 'slug')->ignore($cursoId),
            ],
            'descripcion_corta' => 'required|string',
            'duracion' => 'required|string|max:255',
            'intensidad_horaria' => 'required|integer|min:1|max:10000',
            'imagen' => 'nullable|string|max:255',
            'destacado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'categoria_id.required' => 'La categoria es requerida.',
            'categoria_id.exists' => 'La categoria seleccionada no es valida.',
            'nombre.required' => 'El nombre del curso es requerido.',
            'slug.unique' => 'Ya existe un curso con este nombre.',
            'descripcion_corta.required' => 'La descripcion corta es requerida.',
            'duracion.required' => 'La duracion es requerida.',
            'intensidad_horaria.required' => 'La intensidad horaria es requerida.',
            'intensidad_horaria.integer' => 'La intensidad horaria debe ser un numero entero.',
            'intensidad_horaria.min' => 'La intensidad horaria debe ser mayor a cero.',
        ];
    }
}
