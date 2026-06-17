@extends('layouts.admin')

@section('titulo', 'Previsualización de Importación')

@section('contenido')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Previsualización de Importación</h1>
        <p class="text-gray-600 mt-1">Revisa los datos antes de confirmar. Las filas con errores no se importarán.</p>
    </div>

    {{-- Resumen --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $resumen['total'] }}</p>
            <p class="text-sm text-gray-600">Filas totales</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $resumen['crear'] }}</p>
            <p class="text-sm text-gray-600">Nuevos capacitados</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $resumen['actualizar'] }}</p>
            <p class="text-sm text-gray-600">Actualizaciones</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $resumen['sin_curso'] }}</p>
            <p class="text-sm text-gray-600">Sin curso identificado</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $resumen['errores'] }}</p>
            <p class="text-sm text-gray-600">Filas con errores</p>
        </div>
    </div>

    <form action="{{ route('admin.capacitados.importar.confirmar') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left"></th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fila</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Documento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Correo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Curso</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($filas as $fila)
                            <tr class="{{ !empty($fila['errores']) ? 'bg-red-50' : 'hover:bg-gray-50' }} transition">
                                <td class="px-4 py-3">
                                    @if(empty($fila['errores']))
                                        <input type="checkbox" name="filas[]" value="{{ $fila['fila'] }}" checked
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $fila['fila'] }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $fila['datos']['nombre_completo'] ?: '—' }}</td>
                                <td class="px-4 py-3"><code class="bg-gray-100 px-2 py-1 rounded">{{ $fila['datos']['documento'] ?: '—' }}</code></td>
                                <td class="px-4 py-3">{{ $fila['datos']['correo'] ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    @if(!empty($fila['cursos']))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($fila['cursos'] as $curso)
                                                <span class="inline-block bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-medium">{{ $curso['nombre'] }}</span>
                                            @endforeach
                                            @foreach($fila['cursos_no_encontrados'] as $noEncontrado)
                                                <span class="inline-block bg-amber-100 text-amber-800 px-2 py-0.5 rounded text-xs font-medium" title="No se encontró en la plataforma">"{{ $noEncontrado }}" (?)</span>
                                            @endforeach
                                        </div>
                                    @elseif(!empty($fila['cursos_no_encontrados']))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($fila['cursos_no_encontrados'] as $noEncontrado)
                                                <span class="inline-block bg-amber-100 text-amber-800 px-2 py-0.5 rounded text-xs font-medium" title="No se encontró en la plataforma">"{{ $noEncontrado }}" (?)</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(!empty($fila['errores']))
                                        <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">
                                            Error: {{ implode(' ', $fila['errores']) }}
                                        </span>
                                    @elseif($fila['accion'] === 'crear')
                                        <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Crear</span>
                                    @else
                                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">Actualizar</span>
                                    @endif

                                    @if(empty($fila['errores']) && empty($fila['cursos']))
                                        <span class="inline-block bg-amber-100 text-amber-800 px-2 py-1 rounded-full text-xs font-semibold ml-1">Sin curso</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No hay filas para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Confirmar importación
            </button>
            <a href="{{ route('admin.capacitados.importar.form') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
