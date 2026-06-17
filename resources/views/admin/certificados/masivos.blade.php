@extends('layouts.admin')

@section('titulo', 'Generación Masiva de Certificados')
@section('titulo_topbar', 'Certificados masivos')

@section('contenido')
<div class="space-y-6" x-data="{ fechaGlobal: '{{ now()->toDateString() }}', vigenciaGlobal: '1', activoGlobal: true }">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Generación Masiva de Certificados</h1>
            <p class="text-gray-600 mt-1">Genera certificados a partir de las solicitudes pendientes (importación de capacitados)</p>
        </div>
        <a href="{{ route('admin.certificados.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            Volver a certificados
        </a>
    </div>

    @if($solicitudes->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            <p class="text-lg">No hay solicitudes de certificación pendientes.</p>
            <a href="{{ route('admin.capacitados.importar.form') }}" class="text-blue-600 hover:text-blue-900 mt-2 inline-block">
                Importar capacitados desde Excel →
            </a>
        </div>
    @else
        <form action="{{ route('admin.certificados.generar-masivos') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-blue-800 mb-1">Fecha de emisión para todos</label>
                    <input type="date" x-model="fechaGlobal" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-800 mb-1">Vigencia para todos</label>
                    <select x-model="vigenciaGlobal" class="w-36 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">1 año</option>
                        <option value="2">2 años</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="block text-sm font-medium text-blue-800">Activo para todos</label>
                    <input type="checkbox" x-model="activoGlobal" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                </div>
                <button type="button"
                        @click="document.querySelectorAll('.fecha-emision').forEach(el => el.value = fechaGlobal); document.querySelectorAll('.anios-vigencia').forEach(el => el.value = vigenciaGlobal); document.querySelectorAll('.activo-cert').forEach(el => el.checked = activoGlobal)"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Aplicar a todas las filas
                </button>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left"></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Capacitado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Curso</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha emisión</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Intensidad (h)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Modalidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Vigencia</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Activo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($solicitudes as $solicitud)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 align-top">
                                        <input type="checkbox" name="solicitudes[{{ $solicitud->id }}][incluir]" value="1"
                                               checked
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <span class="font-medium text-gray-900">{{ $solicitud->capacitado->nombre_completo }}</span>
                                        <p class="text-xs text-gray-500"><code class="bg-gray-100 px-1 rounded">{{ $solicitud->capacitado->documento }}</code></p>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        @if($solicitud->curso_id)
                                            <input type="hidden" name="solicitudes[{{ $solicitud->id }}][curso_id]" value="{{ $solicitud->curso_id }}">
                                            {{ $solicitud->curso->nombre }}
                                        @else
                                            <select name="solicitudes[{{ $solicitud->id }}][curso_id]"
                                                    class="px-2 py-1 border border-amber-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                                                <option value="">Selecciona un curso...</option>
                                                @foreach($cursos as $curso)
                                                    <option value="{{ $curso->id }}" data-horas="{{ $curso->intensidad_horaria }}">{{ $curso->nombre }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-xs text-amber-600 mt-1">Sin curso identificado: "{{ $solicitud->curso_texto }}"</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="date" name="solicitudes[{{ $solicitud->id }}][fecha_emision]"
                                               value="{{ now()->toDateString() }}"
                                               class="fecha-emision px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="number" name="solicitudes[{{ $solicitud->id }}][intensidad_horaria]"
                                               value="{{ $solicitud->curso?->intensidad_horaria }}" min="1" max="500"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <select name="solicitudes[{{ $solicitud->id }}][modalidad]"
                                                class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="" @selected(!$solicitud->modalidad)>Sin especificar</option>
                                            <option value="virtual" @selected($solicitud->modalidad === 'virtual')>Virtual</option>
                                            <option value="presencial" @selected($solicitud->modalidad === 'presencial')>Presencial</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <select name="solicitudes[{{ $solicitud->id }}][anios_vigencia]"
                                                class="anios-vigencia w-28 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="1">1 año</option>
                                            <option value="2">2 años</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 align-top text-center">
                                        <input type="hidden" name="solicitudes[{{ $solicitud->id }}][activo]" value="0">
                                        <input type="checkbox" name="solicitudes[{{ $solicitud->id }}][activo]" value="1"
                                               checked
                                               class="activo-cert rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Generar certificados seleccionados
                </button>
                <a href="{{ route('admin.certificados.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    @endif
</div>
@endsection
