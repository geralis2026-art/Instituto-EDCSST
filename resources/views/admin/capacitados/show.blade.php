@extends('layouts.admin')

@section('titulo', 'Detalles del Capacitado')

@section('contenido')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-start">
        <div>
            <a href="{{ route('admin.capacitados.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al listado
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $capacitado->nombre_completo }}</h1>
            <p class="text-gray-600 mt-2">Documento: <strong>{{ $capacitado->documento }}</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.capacitados.edit', $capacitado) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <form action="{{ route('admin.capacitados.destroy', $capacitado) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('¿Eliminar este capacitado? Solo se eliminará si no tiene certificados asociados.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4v2h16V7h-3z"/>
                    </svg>
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    {{-- Información General --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 border-b px-6 py-4">
            <h2 class="text-xl font-bold text-gray-900">Información General</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 font-medium">Nombre Completo</p>
                <p class="text-lg text-gray-900">{{ $capacitado->nombre_completo }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Documento</p>
                <p class="text-lg text-gray-900"><code class="bg-gray-100 px-2 py-1 rounded">{{ $capacitado->documento }}</code></p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Correo Electrónico</p>
                <p class="text-lg text-gray-900">{{ $capacitado->correo ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Teléfono</p>
                <p class="text-lg text-gray-900">{{ $capacitado->telefono ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 font-medium">Grupo Sanguíneo (RH)</p>
                <p class="text-lg text-gray-900">{{ $capacitado->rh ?? '—' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 font-medium">Total de Horas Capacitadas</p>
                <p class="text-3xl font-bold text-blue-600">{{ $capacitado->horas_capacitadas }} horas</p>
            </div>
            <div class="md:col-span-2 text-sm text-gray-500">
                <p>Registrado: {{ $capacitado->created_at->format('d/m/Y H:i') }}</p>
                <p>Última actualización: {{ $capacitado->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Certificados --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Certificados ({{ count($certificados) }})</h2>
        </div>

        @if($certificados->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Curso</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Horas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha Emisión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($certificados as $certificado)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-mono">
                                        {{ $certificado->codigo_unico }}
                                    </code>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $certificado->curso->nombre }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $certificado->curso->categoria?->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-semibold">
                                        {{ $certificado->intensidad_horaria }}h
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $certificado->fecha_emision->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <p class="text-lg">Este capacitado aún no tiene certificados emitidos.</p>
            </div>
        @endif
    </div>
</div>
@endsection
