@extends('layouts.admin')

@section('titulo', 'Detalle de Curso')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div>
            <a href="{{ route('admin.cursos.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
                <span>&larr;</span>
                Volver al listado
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $curso->nombre }}</h1>
            <p class="text-gray-600 mt-2">{{ $curso->categoria?->nombre ?? 'Sin categoria' }}</p>
        </div>
        @if(auth()->user()->isGestor())
            <div class="flex gap-3">
                <a href="{{ route('admin.cursos.edit', $curso) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Editar</a>
                <form action="{{ route('admin.cursos.destroy', $curso) }}" method="POST" onsubmit="return confirm('Eliminar este curso? Solo se eliminara si no tiene certificados asociados.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Eliminar</button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Estado</p>
            <p class="text-xl font-semibold {{ $curso->activo ? 'text-green-700' : 'text-gray-700' }}">{{ $curso->activo ? 'Activo' : 'Inactivo' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Destacado</p>
            <p class="text-xl font-semibold text-gray-900">{{ $curso->destacado ? 'Si' : 'No' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Duracion</p>
            <p class="text-xl font-semibold text-gray-900">{{ $curso->duracion }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Certificados</p>
            <p class="text-xl font-semibold text-gray-900">{{ $curso->certificados_count }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div>
            <p class="text-sm text-gray-500">Intensidad horaria</p>
            <p class="text-lg text-gray-900">{{ $curso->intensidad_horaria }} horas</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Descripcion corta</p>
            <p class="text-lg text-gray-900">{{ $curso->descripcion_corta }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Imagen</p>
            @if($curso->imagen)
                <img src="{{ $curso->imagen_url }}" alt="{{ $curso->nombre }}" class="h-28 w-auto rounded-lg border border-gray-200 object-cover mt-2">
                <p class="text-xs text-gray-400 mt-1">{{ $curso->imagen }}</p>
            @else
                <p class="text-lg text-gray-900">Sin imagen configurada</p>
            @endif
        </div>
        <div class="text-sm text-gray-500">
            <p>Slug: {{ $curso->slug }}</p>
            <p>Registrado: {{ $curso->created_at->format('d/m/Y H:i') }}</p>
            <p>Ultima actualizacion: {{ $curso->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @if($curso->tiene_aula_virtual && config('features.aula_virtual'))
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Aula Virtual</h2>
                <div class="flex gap-2">
                    @if(auth()->user()->isGestor())
                        <a href="{{ route('admin.cursos.quiz.edit', $curso) }}" class="px-3 py-1.5 text-sm bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200 transition font-medium">Quiz</a>
                    @endif
                    <a href="{{ route('admin.cursos.matriculas.index', $curso) }}" class="px-3 py-1.5 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition font-medium">Matrículas</a>
                    @if(auth()->user()->isGestor())
                        <a href="{{ route('admin.cursos.modulos.create', $curso) }}" class="px-3 py-1.5 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition font-medium">+ Módulo</a>
                    @endif
                </div>
            </div>

            <div class="space-y-3">
                @forelse($curso->modulos as $modulo)
                    <div class="flex items-center justify-between border border-gray-200 rounded-lg px-4 py-3">
                        <div>
                            <p class="font-medium text-gray-900">{{ $modulo->titulo }}</p>
                            <p class="text-xs text-gray-500">{{ $modulo->materiales_count }} material(es) &middot; {{ $modulo->activo ? 'Activo' : 'Inactivo' }}</p>
                        </div>
                        @if(auth()->user()->isGestor())
                            <a href="{{ route('admin.cursos.modulos.edit', [$curso, $modulo]) }}" class="text-sm text-blue-700 hover:text-amber-600 font-medium">Editar &rarr;</a>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Este curso todavía no tiene módulos.@if(auth()->user()->isGestor()) Crea el primero con el botón "+ Módulo".@endif</p>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection
