@extends('layouts.admin')

@section('titulo', 'Mensajes')
@section('titulo_topbar', 'Mensajes de contacto')

@section('contenido')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bandeja de mensajes</h1>
            <p class="text-gray-600 mt-1">Mensajes recibidos desde el formulario público</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="busqueda" placeholder="Buscar por nombre o correo..." value="{{ request('busqueda') }}"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1 min-w-[200px]">
            <select name="estado" class="px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[220px]">
                <option value="">Todos los estados</option>
                <option value="nuevo"      @selected(request('estado') === 'nuevo')>Nuevos</option>
                <option value="leido"      @selected(request('estado') === 'leido')>Leídos</option>
                <option value="respondido" @selected(request('estado') === 'respondido')>Respondidos</option>
            </select>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Filtrar</button>
            @if(request('busqueda') || request('estado'))
                <a href="{{ route('admin.mensajes.index') }}" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Remitente</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Mensaje</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($mensajes as $mensaje)
                        <tr class="hover:bg-gray-50 transition {{ $mensaje->estado === 'nuevo' ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900 {{ $mensaje->estado === 'nuevo' ? 'font-bold' : '' }}">
                                    {{ $mensaje->nombre }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $mensaje->correo }}</p>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-sm text-gray-700 truncate">{{ $mensaje->mensaje }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $colores = [
                                        'nuevo'      => 'bg-blue-100 text-blue-800',
                                        'leido'      => 'bg-gray-100 text-gray-700',
                                        'respondido' => 'bg-green-100 text-green-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $colores[$mensaje->estado] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $mensaje->estado_formateado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $mensaje->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.mensajes.show', $mensaje) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition text-sm font-medium">
                                        Ver
                                    </a>
                                    <form action="{{ route('admin.mensajes.destroy', $mensaje) }}" method="POST" onsubmit="return confirm('¿Eliminar este mensaje?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition text-sm font-medium">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                No hay mensajes{{ request('estado') ? ' con este estado' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $mensajes->appends(request()->query())->links() }}
    </div>

</div>
@endsection
