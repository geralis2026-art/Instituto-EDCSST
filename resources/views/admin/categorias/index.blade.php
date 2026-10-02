@extends('layouts.admin')

@section('titulo', 'Gestión de Categorías')
@section('titulo_topbar', 'Categorías')

@section('contenido')
<div class="space-y-6">
    <x-admin.encabezado titulo="Categorías" subtitulo="Agrupan los cursos del catálogo público.">
        <a href="{{ route('admin.categorias.create') }}" class="btn-primario">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva categoría
        </a>
    </x-admin.encabezado>

    {{-- Búsqueda y filtros --}}
    @php
        $chipsEstado = [
            ''           => 'Todas',
            'activas'    => 'Activas',
            'inactivas'  => 'Inactivas',
            'con_cursos' => 'Con cursos',
            'sin_cursos' => 'Sin cursos',
        ];
    @endphp
    <div class="tarjeta-admin p-4 space-y-4">
        <form method="GET" role="search" class="flex flex-col sm:flex-row gap-3">
            @if($estado)<input type="hidden" name="estado" value="{{ $estado }}">@endif
            <div class="relative flex-1">
                <label for="busqueda" class="sr-only">Buscar categorías</label>
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" id="busqueda" name="busqueda" value="{{ $busqueda }}"
                       placeholder="Nombre o descripción" class="campo-admin pl-10">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primario flex-1 sm:flex-none">Buscar</button>
                @if($busqueda || $estado)
                    <a href="{{ route('admin.categorias.index') }}" class="btn-secundario flex-1 sm:flex-none">Limpiar</a>
                @endif
            </div>
        </form>
        @include('admin.partials.filtro-estado', ['ruta' => 'admin.categorias.index', 'chips' => $chipsEstado, 'actual' => $estado])
    </div>

    <div class="tarjeta-admin overflow-hidden">
        @if($categorias->total() > 0)
            <div class="px-4 py-3 border-b border-slate-200 text-sm text-slate-600">
                {{ number_format($categorias->total()) }} {{ Str::plural('categoría', $categorias->total()) }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="tabla-admin">
                <caption class="sr-only">Listado de categorías</caption>
                <thead>
                    <tr>
                        <th scope="col">Categoría</th>
                        <th scope="col" class="!text-right">Cursos</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="!text-right"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $categoria)
                        <tr class="{{ $categoria->activo ? '' : 'bg-slate-50/60' }}">
                            <td class="min-w-[14rem]">
                                <a href="{{ route('admin.categorias.show', $categoria) }}" class="font-semibold text-slate-900 hover:text-blue-800 hover:underline">{{ $categoria->nombre }}</a>
                                <span class="block text-sm text-slate-500">{{ $categoria->descripcion ?: 'Sin descripción' }}</span>
                            </td>
                            <td class="text-right whitespace-nowrap tabular-nums font-semibold text-slate-900">{{ $categoria->cursos_count }}</td>
                            <td class="whitespace-nowrap">
                                @if($categoria->activo)
                                    <span class="chip-exito">Activa</span>
                                @else
                                    <span class="chip-neutro">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-2 ml-auto min-w-[14rem] max-w-[18rem]">
                                    <a href="{{ route('admin.categorias.show', $categoria) }}" class="accion-fila-acento">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver <span class="sr-only">{{ $categoria->nombre }}</span>
                                    </a>
                                    <a href="{{ route('admin.categorias.edit', $categoria) }}" class="accion-fila">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Editar <span class="sr-only">{{ $categoria->nombre }}</span>
                                    </a>
                                    <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" class="inline-flex"
                                          data-confirmar="¿Eliminar la categoría «{{ $categoria->nombre }}»? Solo se eliminará si no tiene cursos asociados.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="accion-fila-peligro">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Eliminar <span class="sr-only">{{ $categoria->nombre }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="!py-14 text-center">
                                @if($busqueda || $estado)
                                    <p class="font-semibold text-slate-800">No hay categorías que coincidan con los filtros.</p>
                                    <a href="{{ route('admin.categorias.index') }}" class="mt-2 inline-block text-blue-800 font-semibold hover:underline">Limpiar filtros</a>
                                @else
                                    <p class="font-semibold text-slate-800">Aún no hay categorías registradas.</p>
                                    <a href="{{ route('admin.categorias.create') }}" class="btn-primario mt-4">Crear la primera categoría</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $categorias->appends(request()->query())->links() }}
</div>
@endsection
