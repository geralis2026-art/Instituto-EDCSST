@extends('layouts.admin')

@section('titulo', 'Detalle de Categoria')
@section('titulo_topbar', 'Categorias')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div>
            <a href="{{ route('admin.categorias.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
                <span>&larr;</span>
                Volver al listado
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $categoria->nombre }}</h1>
            <p class="text-gray-600 mt-2">{{ $categoria->descripcion ?: 'Sin descripcion' }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.categorias.edit', $categoria) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Editar</a>
            <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('Eliminar esta categoria? Solo se eliminara si no tiene cursos asociados.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Eliminar</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Estado</p>
            <p class="text-xl font-semibold {{ $categoria->activo ? 'text-green-700' : 'text-gray-700' }}">{{ $categoria->activo ? 'Activa' : 'Inactiva' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Cursos asociados</p>
            <p class="text-xl font-semibold text-gray-900">{{ $categoria->cursos_count }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Slug</p>
            <p class="text-xl font-semibold text-gray-900">{{ $categoria->slug }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Cursos de esta categoria</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <tbody class="divide-y divide-gray-200">
                    @forelse($cursos as $curso)
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.cursos.show', $curso) }}" class="font-medium text-blue-600 hover:text-blue-900">{{ $curso->nombre }}</a>
                                <p class="text-sm text-gray-500">{{ $curso->duracion }} - {{ $curso->intensidad_horaria }} horas</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $curso->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $curso->activo ? 'Activo' : 'Inactivo' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-gray-500">Esta categoria aun no tiene cursos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $cursos->links() }}
</div>
@endsection
