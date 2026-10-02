@extends('layouts.admin')

@section('titulo', 'Detalle de Curso')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-5xl mx-auto space-y-6">

    <x-admin.encabezado :titulo="$curso->nombre" :subtitulo="$curso->categoria?->nombre ?? 'Sin categoría'" :volver="route('admin.cursos.index')">
        @if(auth()->user()->isGestor())
            <a href="{{ route('admin.cursos.edit', $curso) }}" class="btn-secundario">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </a>
            <form action="{{ route('admin.cursos.destroy', $curso) }}" method="POST" class="inline-flex"
                  data-confirmar="¿Eliminar el curso «{{ $curso->nombre }}»? Solo se eliminará si no tiene certificados asociados.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-peligro">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Eliminar
                </button>
            </form>
        @endif
    </x-admin.encabezado>

    {{-- Resumen --}}
    <section class="tarjeta-admin tarjeta-tinte acento-borde-t overflow-hidden" aria-label="Resumen del curso">
        <div class="flex flex-col sm:flex-row">
            <div class="sm:w-64 shrink-0 bg-marca-navy">
                @if($curso->imagen)
                    <img src="{{ $curso->imagen_url }}" alt="{{ $curso->nombre }}" class="w-full h-48 sm:h-full object-cover">
                @else
                    <div class="h-48 sm:h-full flex items-end p-5 border-b-4 sm:border-b-0 sm:border-r-4 border-amber-400">
                        <span class="font-titulo text-lg font-semibold text-white leading-snug">{{ $curso->nombre }}</span>
                    </div>
                @endif
            </div>

            <div class="flex-1 p-6">
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($curso->activo)<span class="chip-exito">Activo</span>@else<span class="chip-neutro">Inactivo</span>@endif
                    @if($curso->destacado)<span class="chip-alerta">Destacado en el inicio</span>@endif
                </div>
                <dl class="grid grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-5">
                    <div>
                        <dt class="text-sm text-slate-600">Duración</dt>
                        <dd class="mt-0.5 font-semibold text-slate-900">{{ $curso->duracion }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-600">Intensidad horaria</dt>
                        <dd class="mt-0.5 font-semibold text-slate-900 tabular-nums">{{ $curso->intensidad_horaria }} horas</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-slate-600">Certificados emitidos</dt>
                        <dd class="mt-0.5 font-semibold text-slate-900 tabular-nums">
                            @if($curso->certificados_count > 0)
                                <a href="{{ route('admin.certificados.index', ['curso_id' => $curso->id]) }}" class="text-blue-800 hover:underline">{{ $curso->certificados_count }}</a>
                            @else
                                0
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="tarjeta-admin p-6" aria-labelledby="titulo-descripcion">
        <h2 id="titulo-descripcion" class="text-base font-semibold text-slate-900 mb-3">Descripción corta</h2>
        <p class="text-slate-700 max-w-prose">{{ $curso->descripcion_corta }}</p>
        <p class="mt-5 pt-4 border-t border-slate-100 text-sm text-slate-500">
            Dirección pública: <span class="font-mono">{{ $curso->slug }}</span> · Registrado el {{ $curso->created_at->format('d/m/Y H:i') }} · Actualizado el {{ $curso->updated_at->format('d/m/Y H:i') }}
        </p>
    </section>

    @if($curso->tiene_aula_virtual && config('features.aula_virtual'))
        <section class="tarjeta-admin p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-slate-900">Aula virtual</h2>
                <div class="flex gap-2">
                    @if(auth()->user()->isGestor())
                        <a href="{{ route('admin.cursos.quiz.edit', $curso) }}" class="accion-fila">Quiz</a>
                    @endif
                    <a href="{{ route('admin.cursos.matriculas.index', $curso) }}" class="accion-fila">Matrículas</a>
                    @if(auth()->user()->isGestor())
                        <a href="{{ route('admin.cursos.modulos.create', $curso) }}" class="accion-fila">+ Módulo</a>
                    @endif
                </div>
            </div>

            <div class="space-y-3">
                @forelse($curso->modulos as $modulo)
                    <div class="flex items-center justify-between border border-slate-200 rounded-lg px-4 py-3">
                        <div>
                            <p class="font-medium text-slate-900">{{ $modulo->titulo }}</p>
                            <p class="text-xs text-slate-500">{{ $modulo->materiales_count }} material(es) &middot; {{ $modulo->activo ? 'Activo' : 'Inactivo' }}</p>
                        </div>
                        @if(auth()->user()->isGestor())
                            <a href="{{ route('admin.cursos.modulos.edit', [$curso, $modulo]) }}" class="text-sm text-blue-800 hover:underline font-medium">Editar &rarr;</a>
                        @endif
                    </div>
                @empty
                    <p class="text-slate-500 text-sm">Este curso todavía no tiene módulos.@if(auth()->user()->isGestor()) Crea el primero con el botón "+ Módulo".@endif</p>
                @endforelse
            </div>
        </section>
    @endif
</div>
@endsection
