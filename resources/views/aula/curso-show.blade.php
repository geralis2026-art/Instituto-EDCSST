@extends('layouts.aula')

@section('titulo', $matricula->curso->nombre)

@section('contenido')
@php
    $totalModulos = $matricula->curso->modulos->count();
    $modulosCompletados = count(array_intersect($matricula->curso->modulos->pluck('id')->all(), $completados));
@endphp

<div class="space-y-6">
    <div>
        <a href="{{ route('aula.dashboard') }}" class="text-sm text-blue-700 hover:text-amber-600 transition inline-flex items-center gap-1">
            <span>&larr;</span> Mis cursos
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ $matricula->curso->nombre }}</h1>
        @if($matricula->curso->descripcion_corta)
            <p class="text-gray-600 mt-1">{{ $matricula->curso->descripcion_corta }}</p>
        @endif

        @if($totalModulos > 0)
            <div class="mt-4 bg-white rounded-lg shadow p-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Módulos completados</span>
                    <span class="font-semibold">{{ $modulosCompletados }} de {{ $totalModulos }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="progress-gold h-2 rounded-full transition-all" style="width: {{ $matricula->porcentaje_avance }}%"></div>
                </div>
            </div>
        @endif
    </div>

    <div class="space-y-4">
        @forelse($matricula->curso->modulos as $i => $modulo)
            @php $completado = in_array($modulo->id, $completados); @endphp
            <div class="bg-white rounded-lg shadow p-5 border-l-4 {{ $completado ? 'border-green-500' : 'border-gray-200' }}">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 font-semibold text-sm {{ $completado ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        @if($completado)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 justify-between">
                            <h2 class="font-bold text-gray-900">{{ $modulo->titulo }}</h2>
                            @if($completado)
                                <span class="px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 rounded-full whitespace-nowrap">Completado</span>
                            @endif
                        </div>
                        @if($modulo->descripcion)
                            <p class="text-sm text-gray-600 mt-1">{{ $modulo->descripcion }}</p>
                        @endif

                        <ul class="mt-3 space-y-2">
                            @foreach($modulo->materiales as $material)
                                <li class="flex items-center gap-2.5 text-sm">
                                    <span class="w-7 h-7 rounded-md icon-gold flex items-center justify-center flex-shrink-0">
                                        @switch($material->tipo)
                                            @case('video')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 8.5l5 3.5-5 3.5v-7z"/></svg>
                                                @break
                                            @case('presentacion')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20V10M10 20V4M16 20v-7M3 20h18"/></svg>
                                                @break
                                            @case('taller')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 002 2h2a2 2 0 002-2m-6 9l2 2 4-4"/></svg>
                                                @break
                                            @default
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @endswitch
                                    </span>
                                    <span class="text-gray-400 uppercase text-[0.65rem] font-semibold tracking-wide flex-shrink-0">{{ \App\Models\Material::TIPOS[$material->tipo] ?? $material->tipo }}</span>
                                    @if($material->url)
                                        <a href="{{ $material->url }}" target="_blank" rel="noopener" class="flex-1 min-w-0 truncate text-blue-700 hover:text-amber-600 font-medium">{{ $material->titulo }}</a>
                                    @else
                                        <a href="{{ route('aula.materiales.descargar', $material) }}" class="flex-1 min-w-0 truncate text-blue-700 hover:text-amber-600 font-medium">{{ $material->titulo }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        @unless($completado)
                            <form method="POST" action="{{ route('aula.modulos.completar', $modulo) }}" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 sm:py-1.5 text-sm font-semibold bg-blue-950 text-white rounded-md hover:bg-blue-900 transition">
                                    Marcar como completado
                                </button>
                            </form>
                        @endunless
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                Este curso todavía no tiene módulos publicados.
            </div>
        @endforelse
    </div>

    @if($matricula->curso->quiz)
        <div class="bg-white rounded-lg shadow p-5 border-t-4 border-amber-500">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg icon-gold flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="font-bold text-gray-900">Quiz de validación</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Nota mínima para aprobar: {{ $matricula->curso->quiz->nota_minima }}%.
                        Intentos usados: {{ $matricula->intentos_usados }} de {{ $matricula->curso->quiz->intentos_maximos }}.
                    </p>

                    @if($matricula->completado)
                        <p class="mt-3 text-green-700 font-semibold flex items-center gap-1.5">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Ya aprobaste este curso. Tu certificado fue generado.
                        </p>
                    @elseif($matricula->intentos_usados >= $matricula->curso->quiz->intentos_maximos)
                        <p class="mt-3 text-red-700 font-semibold">Agotaste el número de intentos permitidos. Contacta al instituto.</p>
                    @else
                        <a href="{{ route('aula.quiz.show', $matricula) }}" class="inline-block w-full sm:w-auto text-center mt-3 px-5 py-2.5 text-sm btn-gold">
                            Presentar quiz
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
