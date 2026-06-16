<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación para subir el archivo Excel de importación masiva de capacitados. Solo admin. */
class CapacitadoImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'archivo_excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivo_excel.required' => 'Debes seleccionar un archivo Excel.',
            'archivo_excel.mimes' => 'El archivo debe ser un Excel (.xlsx o .xls).',
            'archivo_excel.max' => 'El archivo no puede pesar más de 10 MB.',
        ];
    }
}
