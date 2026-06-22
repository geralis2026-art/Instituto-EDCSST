<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validación para crear/editar certificados. Admin y capacitador (ver authorize()). */
class CertificadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();

        return $user && ($user->isAdmin() || $user->isCapacitador());
    }

    /** Normaliza el código único a mayúsculas y convierte 'activo' a booleano antes de validar. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo_unico' => $this->input('codigo_unico')
                ? strtoupper((string) $this->input('codigo_unico'))
                : null,
            'activo' => $this->boolean('activo'),
        ]);
    }

    /**
     * El PDF es opcional: si no se sube, se genera automáticamente con la plantilla del instituto.
     * Si no se provee código único, se genera automáticamente post-insert.
     */
    public function rules(): array
    {
        $certificadoId = $this->route('certificado')?->id;

        return [
            'capacitado_id'      => ['required', 'exists:capacitados,id'],
            'curso_id'           => ['required', 'exists:cursos,id'],
            'codigo_unico'       => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('certificados', 'codigo_unico')->ignore($certificadoId),
            ],
            'fecha_emision'      => ['required', 'date'],
            'intensidad_horaria' => ['required', 'integer', 'min:1', 'max:500'],
            'modalidad'          => ['nullable', 'in:virtual,presencial'],
            'anios_vigencia'     => ['required', 'integer', 'in:1,2'],
            'archivo_pdf'        => ['nullable', 'file', 'mimetypes:application/pdf', 'max:10240'],
            'activo'             => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'capacitado_id.required' => 'El capacitado es requerido.',
            'capacitado_id.exists'   => 'El capacitado seleccionado no es válido.',
            'curso_id.required'      => 'El curso es requerido.',
            'curso_id.exists'        => 'El curso seleccionado no es válido.',
            'codigo_unico.unique'    => 'Este código de certificado ya existe.',
            'fecha_emision.required' => 'La fecha de emisión es requerida.',
            'intensidad_horaria.required' => 'La intensidad horaria es requerida.',
            'archivo_pdf.mimetypes'  => 'El archivo debe ser un PDF válido.',
            'archivo_pdf.max'        => 'El PDF no puede pesar más de 10 MB.',
            'modalidad.in'           => 'La modalidad debe ser virtual o presencial.',
            'anios_vigencia.required' => 'La vigencia es requerida.',
            'anios_vigencia.in'      => 'La vigencia debe ser 1 o 2 años.',
        ];
    }
}
