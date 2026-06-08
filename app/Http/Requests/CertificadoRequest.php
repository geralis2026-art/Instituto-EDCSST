<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CertificadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Normaliza el código único a mayúsculas y convierte 'activo' a booleano antes de validar. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo_unico' => $this->input('codigo_unico')
                ? strtoupper(trim((string) $this->input('codigo_unico')))
                : null,
            'activo' => $this->boolean('activo'),
        ]);
    }

    /**
     * El PDF es obligatorio en creación y opcional en edición.
     * Si no se provee código único, se genera automáticamente post-insert.
     */
    public function rules(): array
    {
        $certificadoId = $this->route('certificado')?->id;
        $pdfRule = $certificadoId ? 'nullable' : 'required';

        return [
            'capacitado_id' => 'required|exists:capacitados,id',
            'curso_id' => 'required|exists:cursos,id',
            'codigo_unico' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('certificados', 'codigo_unico')->ignore($certificadoId),
            ],
            'fecha_emision' => 'required|date',
            'intensidad_horaria' => 'required|integer|min:1|max:10000',
            'modalidad' => 'nullable|in:virtual,presencial',
            'archivo_pdf' => [$pdfRule, 'file', 'mimetypes:application/pdf', 'max:10240'],
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'capacitado_id.required' => 'El capacitado es requerido.',
            'capacitado_id.exists' => 'El capacitado seleccionado no es valido.',
            'curso_id.required' => 'El curso es requerido.',
            'curso_id.exists' => 'El curso seleccionado no es valido.',
            'codigo_unico.unique' => 'Este codigo de certificado ya existe.',
            'fecha_emision.required' => 'La fecha de emision es requerida.',
            'intensidad_horaria.required' => 'La intensidad horaria es requerida.',
            'archivo_pdf.required' => 'Debes cargar el PDF del certificado.',
            'archivo_pdf.mimetypes' => 'El archivo debe ser un PDF válido.',
            'archivo_pdf.max' => 'El PDF no puede pesar mas de 10 MB.',
            'modalidad.in' => 'La modalidad debe ser virtual o presencial.',
        ];
    }
}
