@extends('layouts.admin')

@section('titulo', 'Editar Módulo')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('admin.cursos.show', $curso) }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2">
        <span>&larr;</span> Volver a {{ $curso->nombre }}
    </a>

    <h1 class="text-2xl font-bold text-gray-900">Editar módulo</h1>

    <form action="{{ route('admin.cursos.modulos.update', [$curso, $modulo]) }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
            <input type="text" name="titulo" value="{{ old('titulo', $modulo->titulo) }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('titulo') border-red-500 @enderror">
            @error('titulo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('descripcion', $modulo->descripcion) }}</textarea>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('activo', $modulo->activo))>
            Activo
        </label>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Guardar cambios</button>
            <form action="{{ route('admin.cursos.modulos.destroy', [$curso, $modulo]) }}" method="POST" onsubmit="return confirm('¿Eliminar este módulo y su material?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">Eliminar módulo</button>
            </form>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Material del módulo</h2>

        <div class="space-y-2 mb-5">
            @forelse($modulo->materiales as $material)
                <div class="flex items-center justify-between border border-gray-200 rounded-lg px-4 py-2 text-sm">
                    <div>
                        <span class="text-xs uppercase text-gray-500">{{ \App\Models\Material::TIPOS[$material->tipo] ?? $material->tipo }}</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $material->titulo }}</span>
                    </div>
                    <form action="{{ route('admin.cursos.materiales.destroy', [$curso, $material]) }}" method="POST" onsubmit="return confirm('¿Eliminar este material?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </div>
            @empty
                <p class="text-gray-500 text-sm">Este módulo todavía no tiene material.</p>
            @endforelse
        </div>

        <form action="{{ route('admin.cursos.materiales.store', [$curso, $modulo]) }}" method="POST" enctype="multipart/form-data" class="border-t border-gray-200 pt-4 space-y-3">
            @csrf
            <h3 class="text-sm font-semibold text-gray-700">Agregar material</h3>

            @error('archivo')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                    <input type="text" name="titulo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                    <select name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Models\Material::TIPOS as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo (documentos, talleres, presentaciones)</label>
                <input type="file" name="archivo" class="w-full text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">O enlace externo (típico para videos)</label>
                <input type="url" name="url" placeholder="https://..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">Agregar material</button>
        </form>
    </div>
</div>
@endsection
