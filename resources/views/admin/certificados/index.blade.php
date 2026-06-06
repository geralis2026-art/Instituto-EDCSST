@extends('layouts.admin')

@section('titulo', 'Gestion de Certificados')
@section('titulo_topbar', 'Certificados')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion de Certificados</h1>
            <p class="text-gray-600 mt-1">Registra y administra certificados PDF existentes</p>
        </div>
        <a href="{{ route('admin.certificados.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Nuevo Certificado
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-[1fr_240px_auto_auto] gap-3">
            <input type="text" name="busqueda" placeholder="Buscar por codigo, nombre o documento..." value="{{ $busqueda }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="curso_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos los cursos</option>
                @foreach($cursos as $curso)
                    <option value="{{ $curso->id }}" @selected($cursoId == $curso->id)>{{ $curso->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Buscar</button>
            @if($busqueda || $cursoId)
                <a href="{{ route('admin.certificados.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition text-center">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Codigo</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Capacitado</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Curso</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Emisión / Vence</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($certificados as $certificado)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $certificado->codigo_unico }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $certificado->capacitado?->nombre_completo ?? 'Sin capacitado' }}</span>
                                <p class="text-sm text-gray-500">{{ $certificado->capacitado?->documento }}</p>
                            </td>
                            <td class="px-6 py-4">{{ $certificado->curso?->nombre ?? 'Sin curso' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900">{{ $certificado->fecha_emision->format('d/m/Y') }}</p>
                                <p class="text-xs {{ $certificado->isVencido() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    Vence: {{ $certificado->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $certificado->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $certificado->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                                @if($certificado->isVencido())
                                    <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Vencido</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2 text-sm font-medium">
                                    <a href="{{ route('admin.certificados.show', $certificado) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.certificados.edit', $certificado) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>
                                    @if($certificado->pdf_url)
                                        <a href="{{ $certificado->pdf_url }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            PDF
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.certificados.toggle-activo', $certificado) }}" method="POST" class="inline" onsubmit="return confirm('{{ $certificado->activo ? 'Desactivar este certificado?' : 'Reactivar este certificado?' }}');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg {{ $certificado->activo ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }} transition font-medium">
                                            @if($certificado->activo)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                Desactivar
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Reactivar
                                            @endif
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.certificados.destroy', $certificado) }}" method="POST" class="inline" onsubmit="return confirm('Eliminar este certificado y su PDF asociado?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-lg">No hay certificados registrados.</p>
                                <a href="{{ route('admin.certificados.create') }}" class="text-blue-600 hover:text-blue-900 mt-2 inline-block">Registrar el primer certificado &rarr;</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $certificados->appends(request()->query())->links() }}
    </div>
</div>
@endsection
