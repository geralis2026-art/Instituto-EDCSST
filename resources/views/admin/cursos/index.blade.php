@extends('layouts.admin')

@section('titulo', 'Gestión de Cursos')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="space-y-6">
    <x-admin.encabezado titulo="Cursos" subtitulo="Oferta académica del instituto.">
        @if(auth()->user()->isGestor())
            <a href="{{ route('admin.cursos.create') }}" class="btn-primario">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo curso
            </a>
        @endif
    </x-admin.encabezado>

    {{-- Búsqueda y filtros --}}
    @php
        $chipsEstado = [
            ''                 => 'Todos',
            'activos'          => 'Activos',
            'inactivos'        => 'Inactivos',
            'destacados'       => 'Destacados',
            'sin_certificados' => 'Sin certificados',
        ];
    @endphp
    <div class="tarjeta-admin p-4 space-y-4">
        <form method="GET" role="search" class="flex flex-col lg:flex-row gap-3">
            @if($estado)<input type="hidden" name="estado" value="{{ $estado }}">@endif
            <div class="relative flex-1">
                <label for="busqueda" class="sr-only">Buscar cursos</label>
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" id="busqueda" name="busqueda" value="{{ $busqueda }}"
                       placeholder="Nombre o descripción" class="campo-admin pl-10">
            </div>
            <label for="categoria_id" class="sr-only">Filtrar por categoría</label>
            <select id="categoria_id" name="categoria_id" data-auto-submit class="campo-admin lg:w-64">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected($categoriaId == $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            @include('admin.partials.filtro-instructor')
            <div class="flex gap-2">
                <button type="submit" class="btn-primario flex-1 lg:flex-none">Buscar</button>
                @if($busqueda || $categoriaId || $instructorId || $estado)
                    <a href="{{ route('admin.cursos.index') }}" class="btn-secundario flex-1 lg:flex-none">Limpiar</a>
                @endif
            </div>
        </form>
        @include('admin.partials.filtro-estado', ['ruta' => 'admin.cursos.index', 'chips' => $chipsEstado, 'actual' => $estado])
    </div>

    <div class="tarjeta-admin overflow-hidden">
        @if($cursos->total() > 0)
            <div class="px-4 py-3 border-b border-slate-200 text-sm text-slate-600">
                {{ number_format($cursos->total()) }} {{ Str::plural('curso', $cursos->total()) }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="tabla-admin">
                <caption class="sr-only">Listado de cursos</caption>
                <thead>
                    <tr>
                        <th scope="col">Curso</th>
                        <th scope="col" class="hidden md:table-cell">Categoría</th>
                        <th scope="col" class="!text-right">Horas</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="!text-right"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cursos as $curso)
                        <tr class="{{ $curso->activo ? '' : 'bg-slate-50/60' }}">
                            <td class="min-w-[14rem]">
                                <a href="{{ route('admin.cursos.show', $curso) }}" class="font-semibold text-slate-900 hover:text-blue-800 hover:underline">{{ $curso->nombre }}</a>
                                <span class="block text-sm text-slate-500">{{ $curso->duracion }} · {{ $curso->certificados_count }} {{ Str::plural('certificado', $curso->certificados_count) }}</span>
                                <span class="block text-sm text-slate-600 md:hidden">{{ $curso->categoria?->nombre ?? 'Sin categoría' }}</span>
                            </td>
                            <td class="hidden md:table-cell whitespace-nowrap">{{ $curso->categoria?->nombre ?? 'Sin categoría' }}</td>
                            <td class="text-right whitespace-nowrap tabular-nums font-semibold text-slate-900">{{ $curso->intensidad_horaria }} h</td>
                            <td class="whitespace-nowrap">
                                <div class="flex flex-col items-start gap-1">
                                    @if($curso->activo)<span class="chip-exito">Activo</span>@else<span class="chip-neutro">Inactivo</span>@endif
                                    @if($curso->destacado)<span class="chip-alerta">Destacado</span>@endif
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-2 ml-auto min-w-[14rem] max-w-[18rem]">
                                    <a href="{{ route('admin.cursos.show', $curso) }}" class="accion-fila-acento"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Ver <span class="sr-only">{{ $curso->nombre }}</span></a>
                                    @if(auth()->user()->isGestor())
                                        <a href="{{ route('admin.cursos.edit', $curso) }}" class="accion-fila"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Editar <span class="sr-only">{{ $curso->nombre }}</span></a>
                                        <form action="{{ route('admin.cursos.destroy', $curso) }}" method="POST" class="inline-flex"
                                              data-confirmar="¿Eliminar el curso «{{ $curso->nombre }}»? Solo se eliminará si no tiene certificados asociados.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="accion-fila-peligro"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Eliminar <span class="sr-only">{{ $curso->nombre }}</span></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="!py-14 text-center">
                                @if($busqueda || $categoriaId || $instructorId || $estado)
                                    <p class="font-semibold text-slate-800">No hay cursos que coincidan con los filtros.</p>
                                    <a href="{{ route('admin.cursos.index') }}" class="mt-2 inline-block text-blue-800 font-semibold hover:underline">Limpiar filtros</a>
                                @else
                                    <p class="font-semibold text-slate-800">Aún no hay cursos registrados.</p>
                                    @if(auth()->user()->isGestor())
                                        <a href="{{ route('admin.cursos.create') }}" class="btn-primario mt-4">Crear el primer curso</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $cursos->appends(request()->query())->onEachSide(1)->links() }}
</div>
@endsection
