@extends('layouts.admin')

@section('titulo', 'Importar Capacitados')

@section('contenido')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Importar Capacitados desde Excel</h1>
        <p class="text-gray-600 mt-1">Carga masiva de capacitados y solicitudes de certificación</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800 space-y-2">
            <p class="font-semibold">¿Cómo funciona?</p>
            <ol class="list-decimal list-inside space-y-1">
                <li>Descarga la plantilla y compártela (por ejemplo, vía un formulario de Google).</li>
                <li>En la columna <code class="bg-blue-100 px-1 rounded">curso</code>, el nombre debe coincidir con uno de los listados en la hoja "Cursos disponibles" de la plantilla.</li>
                <li>Sube el archivo diligenciado. Verás una previsualización antes de guardar nada.</li>
                <li>Si un documento ya existe, se actualizarán sus datos; si no, se creará un nuevo capacitado.</li>
                <li>Cuando el curso coincide, se genera una solicitud de certificación pendiente para generar el certificado más adelante.</li>
            </ol>
        </div>

        <a href="{{ route('admin.capacitados.descargarPlantilla') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Descargar plantilla Excel
        </a>

        <form action="{{ route('admin.capacitados.importar') }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-4 border-t">
            @csrf
            <div>
                <label for="archivo_excel" class="block text-sm font-medium text-gray-700 mb-1">Archivo Excel (.xlsx, .xls — máx 10 MB)</label>
                <input type="file" id="archivo_excel" name="archivo_excel" accept=".xlsx,.xls" required
                       class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:bg-blue-600 file:text-white file:rounded-l-lg file:cursor-pointer hover:file:bg-blue-700">
                @error('archivo_excel')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Previsualizar importación
                </button>
                <a href="{{ route('admin.capacitados.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
