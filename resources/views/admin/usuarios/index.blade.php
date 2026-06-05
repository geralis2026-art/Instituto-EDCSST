@extends('layouts.admin')

@section('titulo', 'Gestión de Usuarios')
@section('titulo_topbar', 'Usuarios')

@section('contenido')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Usuarios del sistema</h1>
            <p class="text-gray-600 mt-1">Administra las cuentas con acceso al panel</p>
        </div>
        <a href="{{ route('admin.usuarios.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Nuevo Usuario
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($usuarios as $usuario)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ $usuario->name }}
                        @if($usuario->id === auth()->id())
                            <span class="ml-2 text-xs text-blue-600">(tú)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $usuario->email }}</td>
                    <td class="px-6 py-4">
                        @if($usuario->activo)
                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Activo</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        @if($usuario->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.usuarios.toggle-activo', $usuario) }}" class="inline">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1 text-xs rounded {{ $usuario->activo ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} transition">
                                    {{ $usuario->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1 text-xs bg-red-100 text-red-800 hover:bg-red-200 rounded transition">
                                    Eliminar
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
