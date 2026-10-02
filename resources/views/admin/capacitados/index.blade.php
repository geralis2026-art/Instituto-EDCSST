@extends('layouts.admin')

@section('titulo', 'Gestión de Capacitados')
@section('titulo_topbar', 'Capacitados')

@section('contenido')
<div class="space-y-6">
    <x-admin.encabezado titulo="Capacitados" subtitulo="Personas registradas que pueden recibir certificados.">
        @if(auth()->user()->isGestor())
            <a href="{{ route('admin.capacitados.importar.form') }}" class="btn-secundario">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Importar Excel
            </a>
            <a href="{{ route('admin.capacitados.link-registro') }}" class="btn-secundario">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Link de registro
            </a>
        @endif
        <a href="{{ route('admin.capacitados.create') }}" class="btn-primario">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo capacitado
        </a>
    </x-admin.encabezado>

    {{-- Búsqueda y filtros --}}
    @php
        $chipsEstado = [
            ''                 => 'Todos',
            'con_certificados' => 'Con certificados',
            'sin_certificados' => 'Sin certificados',
            'por_vencer'       => 'Por vencer (30 días)',
            'vencidos'         => 'Con certificado vencido',
            'pendientes'       => 'Solicitud pendiente',
            'nuevos'           => 'Nuevos este mes',
        ];
    @endphp
    <div class="tarjeta-admin p-4 space-y-4">
    <form method="GET" role="search" class="flex flex-col sm:flex-row gap-3">
        @if($estado)<input type="hidden" name="estado" value="{{ $estado }}">@endif
        <div class="relative flex-1">
            <label for="busqueda" class="sr-only">Buscar capacitados</label>
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="search" id="busqueda" name="busqueda" value="{{ $busqueda }}"
                   placeholder="Nombre, documento o correo"
                   class="campo-admin pl-10">
        </div>
        @include('admin.partials.filtro-instructor')
        <div class="flex gap-2">
            <button type="submit" class="btn-primario flex-1 sm:flex-none">Buscar</button>
            @if($busqueda || $instructorId || $estado)
                <a href="{{ route('admin.capacitados.index') }}" class="btn-secundario flex-1 sm:flex-none">Limpiar</a>
            @endif
        </div>
    </form>
    @include('admin.partials.filtro-estado', ['ruta' => 'admin.capacitados.index', 'chips' => $chipsEstado, 'actual' => $estado])
    </div>

    {{-- Tabla --}}
    <div class="tarjeta-admin overflow-hidden">
        @if($capacitados->total() > 0)
            <div class="px-4 py-3 border-b border-slate-200 text-sm text-slate-600">
                {{ number_format($capacitados->total()) }} {{ Str::plural('capacitado', $capacitados->total()) }}
                @if($busqueda) para «{{ $busqueda }}» @endif
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="tabla-admin">
                <caption class="sr-only">Listado de capacitados</caption>
                <thead>
                    <tr>
                        <th scope="col">Capacitado</th>
                        <th scope="col">Documento</th>
                        <th scope="col" class="hidden md:table-cell">Contacto</th>
                        <th scope="col" class="!text-right">Horas</th>
                        <th scope="col" class="!text-right"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($capacitados as $capacitado)
                        <tr>
                            <td class="min-w-[12rem]">
                                <a href="{{ route('admin.capacitados.show', $capacitado) }}" class="font-semibold text-slate-900 hover:text-blue-800 hover:underline">
                                    {{ $capacitado->nombre_completo }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="codigo-admin">{{ $capacitado->tipo_documento ?? 'CC' }} {{ $capacitado->documento }}</span>
                            </td>
                            <td class="hidden md:table-cell">
                                <span class="block text-slate-700 [overflow-wrap:anywhere]">{{ $capacitado->correo ?? '—' }}</span>
                                @if($capacitado->telefono)
                                    <span class="block text-sm text-slate-500 tabular-nums">{{ $capacitado->telefono }}</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap tabular-nums font-semibold text-slate-900">
                                {{ $capacitado->horas_capacitadas }} h
                            </td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-2 ml-auto min-w-[14rem] max-w-[18rem]">
                                    <a href="{{ route('admin.capacitados.show', $capacitado) }}" class="accion-fila-acento"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Ver <span class="sr-only">{{ $capacitado->nombre_completo }}</span></a>
                                    <a href="{{ route('admin.capacitados.edit', $capacitado) }}" class="accion-fila"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Editar <span class="sr-only">{{ $capacitado->nombre_completo }}</span></a>
                                    <form action="{{ route('admin.capacitados.destroy', $capacitado) }}" method="POST" class="inline-flex"
                                          data-confirmar="¿Eliminar a {{ $capacitado->nombre_completo }}? Solo se eliminará si no tiene certificados asociados.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="accion-fila-peligro"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Eliminar <span class="sr-only">{{ $capacitado->nombre_completo }}</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="!py-14 text-center">
                                @if($busqueda || $instructorId || $estado)
                                    <p class="font-semibold text-slate-800">No hay resultados para tu búsqueda.</p>
                                    <a href="{{ route('admin.capacitados.index') }}" class="mt-2 inline-block text-blue-800 font-semibold hover:underline">Limpiar filtros</a>
                                @else
                                    <p class="font-semibold text-slate-800">Aún no hay capacitados registrados.</p>
                                    <p class="text-slate-500 mt-1">Créalos uno a uno, impórtalos desde Excel o comparte el link de registro.</p>
                                    <a href="{{ route('admin.capacitados.create') }}" class="btn-primario mt-4">Crear el primer capacitado</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $capacitados->appends(request()->query())->links() }}
</div>
@endsection
