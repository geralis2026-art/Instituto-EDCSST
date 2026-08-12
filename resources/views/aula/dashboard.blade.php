@extends('layouts.aula')

@section('titulo', 'Mis cursos')

@section('contenido')
@php
    $totalCursos = $matriculas->count();
    $completados = $matriculas->where('completado', true)->count();
    $enCurso = $totalCursos - $completados;
@endphp

<div class="space-y-6 sm:space-y-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Mis cursos</h1>
        <p class="text-gray-600 mt-1">Estos son los cursos que tienes asignados en el aula virtual.</p>
    </div>

    @if($totalCursos > 0)
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white rounded-lg shadow p-3 sm:p-5 text-center">
                <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalCursos }}</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Curso{{ $totalCursos === 1 ? '' : 's' }} asignado{{ $totalCursos === 1 ? '' : 's' }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 sm:p-5 text-center">
                <p class="text-2xl sm:text-3xl font-bold text-amber-600">{{ $enCurso }}</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">En curso</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 sm:p-5 text-center">
                <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ $completados }}</p>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Completado{{ $completados === 1 ? '' : 's' }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($matriculas as $matricula)
            @php $avance = $matricula->porcentaje_avance; @endphp
            <a href="{{ route('aula.cursos.show', $matricula) }}"
               class="card-gold-hover flex flex-col bg-white rounded-lg shadow p-5 border border-gray-100">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-10 h-10 rounded-lg icon-gold flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    @if($matricula->completado)
                        <span class="px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 rounded-full whitespace-nowrap">Completado</span>
                    @else
                        <span class="px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full whitespace-nowrap">En curso</span>
                    @endif
                </div>

                <h2 class="font-bold text-gray-900 mt-3">{{ $matricula->curso->nombre }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $matricula->curso->duracion }}</p>

                <div class="mt-auto pt-4">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Avance</span>
                        <span>{{ $avance }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="progress-gold h-2 rounded-full transition-all" style="width: {{ $avance }}%"></div>
                    </div>
                </div>
            </a>
        @empty
            <div class="sm:col-span-2 lg:col-span-3 bg-white rounded-lg shadow p-10 text-center text-gray-500">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Todavía no tienes cursos asignados en el aula virtual.
            </div>
        @endforelse
    </div>
</div>
@endsection
