@extends('layouts.admin')

@section('titulo', 'Gestion de Cursos')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion de Cursos</h1>
            <p class="text-gray-600 mt-1">Administra la oferta academica del instituto</p>
        </div>
        <a href="{{ route('admin.cursos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Nuevo Curso
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-[1fr_240px_auto_auto] gap-3">
            <input type="text" name="busqueda" placeholder="Buscar por nombre o descripcion..." value="{{ $busqueda }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <select name="categoria_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas las categorias</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected($categoriaId == $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Buscar</button>
            @if($busqueda || $categoriaId)
                <a href="{{ route('admin.cursos.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition text-center">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Curso</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Horas</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($cursos as $curso)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $curso->nombre }}</span>
                                <p class="text-sm text-gray-500">{{ $curso->duracion }} · {{ $curso->certificados_count }} certificados</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $curso->categoria?->nombre ?? 'Sin categoria' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $curso->intensidad_horaria }}h</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $curso->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $curso->activo ? 'Activo' : 'Inactivo' }}</span>
                                    @if($curso->destacado)
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">Destacado</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.cursos.show', $curso) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.cursos.edit', $curso) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.cursos.destroy', $curso) }}" method="POST" class="inline" onsubmit="return confirm('Eliminar este curso? Solo se eliminara si no tiene certificados asociados.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-lg">No hay cursos registrados.</p>
                                <a href="{{ route('admin.cursos.create') }}" class="text-blue-600 hover:text-blue-900 mt-2 inline-block">Crear el primer curso &rarr;</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $cursos->appends(request()->query())->links() }}
    </div>
</div>
@endsection
