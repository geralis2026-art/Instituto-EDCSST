@extends('layouts.admin')

@section('titulo', 'Gestión de Certificados')
@section('titulo_topbar', 'Certificados')

@section('contenido')
<div class="space-y-6">
    <x-admin.encabezado titulo="Certificados" subtitulo="Registra y administra los certificados emitidos.">
        @if(auth()->user()->isGestor())
            <a href="{{ route('admin.certificados.masivos') }}" class="btn-secundario">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Generación masiva
            </a>
        @endif
        <a href="{{ route('admin.certificados.create') }}" class="btn-primario">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo certificado
        </a>
    </x-admin.encabezado>

    {{-- Filtros --}}
    @php
        $chipsEstado = ['' => 'Todos', 'vigentes' => 'Vigentes', 'por_vencer' => 'Vencen en 30 días', 'vencidos' => 'Vencidos', 'inactivos' => 'Inactivos'];
    @endphp
    <div class="tarjeta-admin p-4 space-y-4">
    <form method="GET" role="search" class="flex flex-col lg:flex-row gap-3">
        @if($estado)<input type="hidden" name="estado" value="{{ $estado }}">@endif
        <div class="relative flex-1">
            <label for="busqueda" class="sr-only">Buscar certificados</label>
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="search" id="busqueda" name="busqueda" value="{{ $busqueda }}"
                   placeholder="Código, nombre o documento" class="campo-admin pl-10">
        </div>
        <label for="curso_id" class="sr-only">Filtrar por curso</label>
        <select id="curso_id" name="curso_id" data-auto-submit class="campo-admin lg:w-64">
            <option value="">Todos los cursos</option>
            @foreach($cursos as $curso)
                <option value="{{ $curso->id }}" @selected($cursoId == $curso->id)>{{ $curso->nombre }}</option>
            @endforeach
        </select>
        @include('admin.partials.filtro-instructor')
        <div class="flex gap-2">
            <button type="submit" class="btn-primario flex-1 lg:flex-none">Buscar</button>
            @if($busqueda || $cursoId || $instructorId || $estado)
                <a href="{{ route('admin.certificados.index') }}" class="btn-secundario flex-1 lg:flex-none">Limpiar</a>
            @endif
        </div>
    </form>

    @include('admin.partials.filtro-estado', ['ruta' => 'admin.certificados.index', 'chips' => $chipsEstado, 'actual' => $estado])
    </div>

    @if($estado === 'por_vencer')
        <p class="text-sm text-slate-600 -mt-2" role="status">Ordenados del que vence primero al que vence después.</p>
    @endif

    <div class="tarjeta-admin overflow-hidden">
        @if($certificados->total() > 0)
            <div class="px-4 py-3 border-b border-slate-200 text-sm text-slate-600">
                {{ number_format($certificados->total()) }} {{ Str::plural('certificado', $certificados->total()) }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="tabla-admin">
                <caption class="sr-only">Listado de certificados</caption>
                <thead>
                    <tr>
                        <th scope="col">Código</th>
                        <th scope="col">Capacitado</th>
                        <th scope="col" class="hidden lg:table-cell">Curso</th>
                        <th scope="col">Vigencia</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="!text-right"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificados as $certificado)
                        @php
                            $vencido = $certificado->isVencido();
                            $vencePronto30 = ! $vencido && $certificado->activo && $certificado->fecha_vencimiento
                                && $certificado->fecha_vencimiento->lte(today()->addDays(30));
                        @endphp
                        <tr class="{{ $certificado->activo ? '' : 'bg-slate-50/60' }}">
                            <td class="whitespace-nowrap">
                                <a href="{{ route('admin.certificados.show', $certificado) }}" class="codigo-admin hover:bg-amber-100 hover:text-slate-900">{{ $certificado->codigo_unico }}</a>
                                @if($certificado->created_at->isToday())<span class="chip-exito ml-1">Nuevo</span>@endif
                            </td>
                            <td class="min-w-[12rem]">
                                <span class="block font-semibold text-slate-900">{{ $certificado->capacitado?->nombre_completo ?? 'Sin capacitado' }}</span>
                                <span class="block text-sm text-slate-500 tabular-nums">{{ $certificado->capacitado?->documento }}</span>
                                <span class="block text-sm text-slate-600 lg:hidden">{{ $certificado->curso?->nombre ?? 'Sin curso' }}</span>
                            </td>
                            <td class="hidden lg:table-cell min-w-[12rem]">{{ $certificado->curso?->nombre ?? 'Sin curso' }}</td>
                            <td class="whitespace-nowrap tabular-nums">
                                <span class="block text-slate-900">{{ $certificado->fecha_emision->format('d/m/Y') }}</span>
                                <span class="block text-sm {{ $vencido ? 'text-red-700 font-semibold' : 'text-slate-500' }}">
                                    Vence {{ $certificado->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap">
                                <div class="flex flex-col items-start gap-1">
                                    @if($certificado->activo)
                                        <span class="chip-exito">Activo</span>
                                    @else
                                        <span class="chip-neutro">Inactivo</span>
                                    @endif
                                    @if($vencido)
                                        <span class="chip-peligro">Vencido</span>
                                    @elseif($vencePronto30)
                                        <span class="chip-alerta">Vence pronto</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="flex flex-wrap justify-end gap-2 ml-auto min-w-[15rem] max-w-[22rem]">
                                    <a href="{{ route('admin.certificados.show', $certificado) }}" class="accion-fila-acento"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>Ver <span class="sr-only">{{ $certificado->codigo_unico }}</span></a>
                                    @if($certificado->pdf_url)
                                        <a href="{{ $certificado->pdf_url }}" target="_blank" rel="noopener" class="accion-fila"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>PDF <span class="sr-only">de {{ $certificado->codigo_unico }} (pestaña nueva)</span></a>
                                    @endif
                                    <a href="{{ route('admin.certificados.edit', $certificado) }}" class="accion-fila"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Editar <span class="sr-only">{{ $certificado->codigo_unico }}</span></a>
                                    <form action="{{ route('admin.certificados.toggle-activo', $certificado) }}" method="POST" class="inline-flex"
                                          data-confirmar="{{ $certificado->activo ? '¿Desactivar el certificado ' . $certificado->codigo_unico . '? Dejará de aparecer como válido en la verificación pública.' : '¿Reactivar el certificado ' . $certificado->codigo_unico . '?' }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="accion-fila-aviso">
                                            @if($certificado->activo)<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>Desactivar
                                            @else<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Reactivar
                                            @endif <span class="sr-only">{{ $certificado->codigo_unico }}</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.certificados.destroy', $certificado) }}" method="POST" class="inline-flex"
                                          data-confirmar="¿Eliminar el certificado {{ $certificado->codigo_unico }} y su PDF? Esta acción no se puede deshacer.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="accion-fila-peligro"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Eliminar <span class="sr-only">{{ $certificado->codigo_unico }}</span></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!py-14 text-center">
                                @if($busqueda || $cursoId || $instructorId || $estado)
                                    <p class="font-semibold text-slate-800">No hay certificados que coincidan con los filtros.</p>
                                    <a href="{{ route('admin.certificados.index') }}" class="mt-2 inline-block text-blue-800 font-semibold hover:underline">Limpiar filtros</a>
                                @else
                                    <p class="font-semibold text-slate-800">Aún no hay certificados registrados.</p>
                                    <a href="{{ route('admin.certificados.create') }}" class="btn-primario mt-4">Registrar el primer certificado</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $certificados->appends(request()->query())->onEachSide(1)->links() }}
</div>
@endsection
