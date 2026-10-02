@extends('layouts.admin')

@section('titulo', 'Mensajes')
@section('titulo_topbar', 'Mensajes de contacto')

@section('contenido')
<div class="space-y-6">
    <x-admin.encabezado titulo="Bandeja de mensajes" subtitulo="Mensajes recibidos desde el formulario de contacto del sitio." />

    @php
        $chipsEstado = ['' => 'Todos', 'nuevo' => 'Nuevos', 'leido' => 'Leídos', 'respondido' => 'Respondidos'];
    @endphp
    <div class="tarjeta-admin p-4 space-y-4">
        <form method="GET" role="search" class="flex flex-col sm:flex-row gap-3">
            @if($estado)<input type="hidden" name="estado" value="{{ $estado }}">@endif
            <div class="relative flex-1">
                <label for="busqueda" class="sr-only">Buscar mensajes</label>
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="search" id="busqueda" name="busqueda" value="{{ $busqueda }}"
                       placeholder="Nombre o correo" class="campo-admin pl-10">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primario flex-1 sm:flex-none">Buscar</button>
                @if($busqueda || $estado)
                    <a href="{{ route('admin.mensajes.index') }}" class="btn-secundario flex-1 sm:flex-none">Limpiar</a>
                @endif
            </div>
        </form>
        @include('admin.partials.filtro-estado', ['ruta' => 'admin.mensajes.index', 'chips' => $chipsEstado, 'actual' => $estado])
    </div>

    <div class="tarjeta-admin overflow-hidden">
        @if($mensajes->total() > 0)
            <div class="px-4 py-3 border-b border-slate-200 text-sm text-slate-600">
                {{ number_format($mensajes->total()) }} {{ Str::plural('mensaje', $mensajes->total()) }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="tabla-admin">
                <caption class="sr-only">Mensajes recibidos</caption>
                <thead>
                    <tr>
                        <th scope="col">Remitente</th>
                        <th scope="col" class="hidden md:table-cell">Mensaje</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="hidden sm:table-cell">Fecha</th>
                        <th scope="col" class="!text-right"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mensajes as $mensaje)
                        <tr class="{{ $mensaje->estado === 'nuevo' ? 'bg-blue-50/60' : '' }}">
                            <td class="min-w-[11rem]">
                                <a href="{{ route('admin.mensajes.show', $mensaje) }}" class="text-slate-900 hover:text-blue-800 hover:underline {{ $mensaje->estado === 'nuevo' ? 'font-bold' : 'font-semibold' }}">{{ $mensaje->nombre }}</a>
                                <span class="block text-sm text-slate-500 [overflow-wrap:anywhere]">{{ $mensaje->correo }}</span>
                            </td>
                            <td class="hidden md:table-cell max-w-xs">
                                <p class="text-slate-700 truncate">{{ $mensaje->mensaje }}</p>
                            </td>
                            <td class="whitespace-nowrap">
                                @if($mensaje->estado === 'nuevo')<span class="chip-info">Nuevo</span>
                                @elseif($mensaje->estado === 'respondido')<span class="chip-exito">Respondido</span>
                                @else<span class="chip-neutro">{{ $mensaje->estado_formateado }}</span>@endif
                            </td>
                            <td class="hidden sm:table-cell whitespace-nowrap text-sm text-slate-600 tabular-nums">{{ $mensaje->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-2 ml-auto min-w-[10rem]">
                                    <a href="{{ route('admin.mensajes.show', $mensaje) }}" class="accion-fila-acento"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Ver <span class="sr-only">el mensaje de {{ $mensaje->nombre }}</span></a>
                                    <form action="{{ route('admin.mensajes.destroy', $mensaje) }}" method="POST" class="inline-flex" data-confirmar="¿Eliminar el mensaje de {{ $mensaje->nombre }}?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="accion-fila-peligro"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Eliminar <span class="sr-only">el mensaje de {{ $mensaje->nombre }}</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="!py-14 text-center">
                                @if($busqueda || $estado)
                                    <p class="font-semibold text-slate-800">No hay mensajes que coincidan con los filtros.</p>
                                    <a href="{{ route('admin.mensajes.index') }}" class="mt-2 inline-block text-blue-800 font-semibold hover:underline">Limpiar filtros</a>
                                @else
                                    <p class="font-semibold text-slate-800">Aún no hay mensajes.</p>
                                    <p class="text-slate-500 mt-1">Llegarán aquí cuando alguien use el formulario de contacto.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $mensajes->appends(request()->query())->links() }}
</div>
@endsection
