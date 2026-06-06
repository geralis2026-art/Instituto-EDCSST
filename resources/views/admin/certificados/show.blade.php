@extends('layouts.admin')

@section('titulo', 'Detalle de Certificado')
@section('titulo_topbar', 'Certificados')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div>
            <a href="{{ route('admin.certificados.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
                <span>&larr;</span>
                Volver al listado
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $certificado->codigo_unico }}</h1>
            <p class="text-gray-600 mt-2">{{ $certificado->capacitado?->nombre_completo ?? 'Sin capacitado' }}</p>
        </div>
        <div class="flex gap-3">
            @if($certificado->pdf_url)
                <a href="{{ $certificado->pdf_url }}" target="_blank" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">Ver PDF</a>
            @endif
            <a href="{{ route('admin.certificados.edit', $certificado) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Editar</a>
            <form action="{{ route('admin.certificados.toggle-activo', $certificado) }}" method="POST" onsubmit="return confirm('{{ $certificado->activo ? 'Desactivar este certificado?' : 'Reactivar este certificado?' }}');">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 {{ $certificado->activo ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white rounded-lg transition">
                    {{ $certificado->activo ? 'Desactivar' : 'Reactivar' }}
                </button>
            </form>
            <form action="{{ route('admin.certificados.destroy', $certificado) }}" method="POST" onsubmit="return confirm('Eliminar este certificado y su PDF asociado?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Eliminar</button>
            </form>
        </div>
    </div>

    @if($certificado->isVencido())
        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
            <strong>Certificado vencido</strong> — venció el {{ $certificado->fecha_vencimiento->format('d/m/Y') }}. No disponible para descarga pública.
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Estado</p>
            <p class="text-xl font-semibold {{ $certificado->activo ? 'text-green-700' : 'text-gray-700' }}">{{ $certificado->activo ? 'Activo' : 'Inactivo' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Vigencia</p>
            <p class="text-xl font-semibold {{ $certificado->isVencido() ? 'text-red-600' : 'text-green-600' }}">
                {{ $certificado->isVencido() ? 'Vencido' : 'Vigente' }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Emisión / Vencimiento</p>
            <p class="text-sm font-semibold text-gray-900">{{ $certificado->fecha_emision->format('d/m/Y') }}</p>
            <p class="text-sm text-gray-600">{{ $certificado->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Horas</p>
            <p class="text-xl font-semibold text-gray-900">{{ $certificado->intensidad_horaria }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">PDF</p>
            <p class="text-xl font-semibold text-gray-900">{{ $certificado->archivo_pdf ? 'Cargado' : 'Sin archivo' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm text-gray-500">Capacitado</p>
            <p class="text-lg text-gray-900">{{ $certificado->capacitado?->nombre_completo ?? 'Sin capacitado' }}</p>
            <p class="text-sm text-gray-500">Documento: {{ $certificado->capacitado?->documento }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Curso</p>
            <p class="text-lg text-gray-900">{{ $certificado->curso?->nombre ?? 'Sin curso' }}</p>
            <p class="text-sm text-gray-500">Categoria: {{ $certificado->curso?->categoria?->nombre ?? 'Sin categoria' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Emitido por</p>
            <p class="text-lg text-gray-900">{{ $certificado->emitidoPor?->name ?? 'No registrado' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Archivo</p>
            <p class="text-lg text-gray-900 break-all">{{ $certificado->archivo_pdf ?: 'Sin archivo' }}</p>
        </div>
    </div>
</div>
@endsection
