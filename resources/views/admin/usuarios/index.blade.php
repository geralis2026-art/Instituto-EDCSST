@extends('layouts.admin')

@section('titulo', 'Gestión de Usuarios')
@section('titulo_topbar', 'Usuarios')

@section('contenido')
<div class="space-y-6">
    <x-admin.encabezado titulo="Usuarios" subtitulo="Cuentas con acceso al panel administrativo.">
        <a href="{{ route('admin.usuarios.create') }}" class="btn-primario">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo usuario
        </a>
    </x-admin.encabezado>

    @php
        $chipsEstado = [
            ''            => 'Todos',
            'admin'       => 'Administradores',
            'instructor'  => 'Instructores',
            'capacitador' => 'Capacitadores',
            'activos'     => 'Activos',
            'inactivos'   => 'Inactivos',
        ];
    @endphp
    <div class="tarjeta-admin p-4">
        @include('admin.partials.filtro-estado', ['ruta' => 'admin.usuarios.index', 'chips' => $chipsEstado, 'actual' => $estado, 'titulo' => 'Mostrar'])
    </div>

    <div class="tarjeta-admin overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 text-sm text-slate-600">
            {{ number_format($usuarios->total()) }} {{ Str::plural('usuario', $usuarios->total()) }}
        </div>
        <div class="overflow-x-auto">
            <table class="tabla-admin">
                <caption class="sr-only">Usuarios del sistema</caption>
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="!text-right"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr class="{{ $usuario->activo ? '' : 'bg-slate-50/60' }}">
                            <td class="min-w-[14rem]">
                                <span class="font-semibold text-slate-900">{{ $usuario->name }}</span>
                                @if($usuario->id === auth()->id())<span class="chip-neutro ml-1">Tú</span>@endif
                                <span class="block text-sm text-slate-500 [overflow-wrap:anywhere]">{{ $usuario->email }}</span>
                            </td>
                            <td class="whitespace-nowrap">
                                @if($usuario->isAdmin())<span class="chip-info">Administrador</span>
                                @elseif($usuario->isInstructor())<span class="chip-alerta">Instructor</span>
                                @else<span class="chip-neutro">Capacitador</span>@endif
                            </td>
                            <td class="whitespace-nowrap">
                                @if($usuario->activo)<span class="chip-exito">Activo</span>@else<span class="chip-peligro">Inactivo</span>@endif
                            </td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-2 ml-auto min-w-[14rem] max-w-[20rem]">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="accion-fila-acento"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Editar <span class="sr-only">{{ $usuario->name }}</span></a>
                                    @if($usuario->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.usuarios.toggle-activo', $usuario) }}" class="inline-flex"
                                              data-confirmar="{{ $usuario->activo ? '¿Desactivar a ' . $usuario->name . '? No podrá iniciar sesión.' : '¿Activar a ' . $usuario->name . '? Podrá iniciar sesión.' }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="accion-fila-aviso">
                                                @if($usuario->activo)<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>Desactivar
                                                @else<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Activar
                                                @endif
                                                <span class="sr-only">{{ $usuario->name }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="inline-flex"
                                              data-confirmar="¿Eliminar a {{ $usuario->name }}? Perderá el acceso al panel.">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="accion-fila-peligro"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Eliminar <span class="sr-only">{{ $usuario->name }}</span></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="!py-14 text-center">
                                <p class="font-semibold text-slate-800">No hay usuarios con ese filtro.</p>
                                <a href="{{ route('admin.usuarios.index') }}" class="mt-2 inline-block text-blue-800 font-semibold hover:underline">Ver todos</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $usuarios->appends(request()->query())->links() }}
</div>
@endsection
