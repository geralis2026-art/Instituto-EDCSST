@extends('layouts.admin')

@section('titulo', 'Detalle de Categoría')
@section('titulo_topbar', 'Categorías')

@section('contenido')
<div class="max-w-5xl mx-auto space-y-6">

    <x-admin.encabezado :titulo="$categoria->nombre" :subtitulo="$categoria->descripcion ?: 'Sin descripción'" :volver="route('admin.categorias.index')">
        <a href="{{ route('admin.categorias.edit', $categoria) }}" class="btn-secundario">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editar
        </a>
        <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" class="inline-flex"
              data-confirmar="¿Eliminar la categoría «{{ $categoria->nombre }}»? Solo se eliminará si no tiene cursos asociados.">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-peligro">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Eliminar
            </button>
        </form>
    </x-admin.encabezado>

    <section class="tarjeta-admin tarjeta-tinte acento-borde-t p-6" aria-label="Resumen">
        <div class="flex flex-wrap items-center gap-3 mb-5">
            @if($categoria->activo)<span class="chip-exito">Activa</span>@else<span class="chip-neutro">Inactiva</span>@endif
        </div>
        <dl class="grid grid-cols-2 gap-x-8 gap-y-5">
            <div>
                <dt class="text-sm text-slate-600">Cursos asociados</dt>
                <dd class="mt-0.5 font-titulo text-3xl font-bold text-slate-900 tabular-nums">{{ $categoria->cursos_count }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-600">Dirección pública</dt>
                <dd class="mt-2 font-mono text-slate-900 [overflow-wrap:anywhere]">{{ $categoria->slug }}</dd>
            </div>
        </dl>
    </section>

    <section class="tarjeta-admin overflow-hidden" aria-labelledby="titulo-cursos">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h2 id="titulo-cursos" class="text-base font-semibold text-slate-900">Cursos de esta categoría</h2>
            <a href="{{ route('admin.cursos.create') }}" class="btn-secundario"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Nuevo curso</a>
        </div>
        <div class="overflow-x-auto">
            <table class="tabla-admin">
                <caption class="sr-only">Cursos de la categoría {{ $categoria->nombre }}</caption>
                <tbody>
                    @forelse($cursos as $curso)
                        <tr>
                            <td>
                                <a href="{{ route('admin.cursos.show', $curso) }}" class="font-semibold text-slate-900 hover:text-blue-800 hover:underline">{{ $curso->nombre }}</a>
                                <span class="block text-sm text-slate-500">{{ $curso->duracion }} · {{ $curso->intensidad_horaria }} horas</span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                @if($curso->activo)<span class="chip-exito">Activo</span>@else<span class="chip-neutro">Inactivo</span>@endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="!py-12 text-center text-slate-600">Esta categoría aún no tiene cursos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{ $cursos->links() }}
</div>
@endsection
