@extends('layouts.admin')

@section('titulo', 'Nuevo Módulo')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('admin.cursos.show', $curso) }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2">
        <span>&larr;</span> Volver a {{ $curso->nombre }}
    </a>

    <h1 class="text-2xl font-bold text-gray-900">Nuevo módulo</h1>

    <form action="{{ route('admin.cursos.modulos.store', $curso) }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('titulo') border-red-500 @enderror">
            @error('titulo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('descripcion') }}</textarea>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            Activo
        </label>

        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Crear módulo</button>
    </form>
</div>
@endsection
