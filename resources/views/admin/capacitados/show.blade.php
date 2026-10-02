@extends('layouts.admin')

@section('titulo', 'Detalles del Capacitado')
@section('titulo_topbar', 'Capacitados')

@section('contenido')
@php
    $iniciales = collect(preg_split('/\s+/', trim($capacitado->nombre_completo)))->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $vigentes  = $certificados->filter(fn ($c) => $c->activo && ! $c->isVencido())->count();
    $porVencer = $certificados->filter(fn ($c) => $c->activo && ! $c->isVencido() && $c->fecha_vencimiento && $c->fecha_vencimiento->lte(today()->addDays(30)))->count();
    $vencidos  = $certificados->filter(fn ($c) => $c->isVencido())->count();
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    <x-admin.encabezado :titulo="$capacitado->nombre_completo"
                        :subtitulo="'Documento ' . ($capacitado->tipo_documento ?? 'CC') . ' ' . $capacitado->documento"
                        :volver="route('admin.capacitados.index')">
        <a href="{{ route('admin.capacitados.edit', $capacitado) }}" class="btn-secundario">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editar
        </a>
        <form action="{{ route('admin.capacitados.destroy', $capacitado) }}" method="POST" class="inline-flex"
              data-confirmar="¿Eliminar a {{ $capacitado->nombre_completo }}? Solo se eliminará si no tiene certificados asociados.">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-peligro">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Eliminar
            </button>
        </form>
    </x-admin.encabezado>

    {{-- Resumen --}}
    <section aria-label="Resumen" class="tarjeta-admin tarjeta-tinte acento-borde-t p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-6">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <span class="w-16 h-16 rounded-full acento-icono font-titulo text-xl font-bold flex items-center justify-center shrink-0" aria-hidden="true">{{ $iniciales }}</span>
                <div class="min-w-0">
                    <p class="text-lg font-semibold text-slate-900 truncate">{{ $capacitado->nombre_completo }}</p>
                    <p class="mt-0.5"><span class="codigo-admin">{{ $capacitado->tipo_documento ?? 'CC' }} {{ $capacitado->documento }}</span></p>
                </div>
            </div>

            <dl class="grid grid-cols-3 gap-6 text-center md:text-left">
                <div>
                    <dt class="text-sm text-slate-600">Horas</dt>
                    <dd class="font-titulo text-3xl font-bold text-slate-900 tabular-nums">{{ $capacitado->horas_capacitadas }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-600">Certificados</dt>
                    <dd class="font-titulo text-3xl font-bold text-slate-900 tabular-nums">{{ $certificados->count() }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-600">Vigentes</dt>
                    <dd class="font-titulo text-3xl font-bold text-green-700 tabular-nums">{{ $vigentes }}</dd>
                </div>
            </dl>
        </div>

        @if($porVencer > 0 || $vencidos > 0)
            <div class="mt-5 pt-5 border-t border-slate-200 flex flex-wrap gap-3 text-sm">
                @if($porVencer > 0)
                    <span class="chip-alerta">{{ $porVencer }} {{ $porVencer === 1 ? 'certificado vence' : 'certificados vencen' }} en los próximos 30 días</span>
                @endif
                @if($vencidos > 0)
                    <span class="chip-peligro">{{ $vencidos }} {{ $vencidos === 1 ? 'certificado vencido' : 'certificados vencidos' }}</span>
                @endif
            </div>
        @endif
    </section>

    {{-- Contacto --}}
    <section class="tarjeta-admin p-6" aria-labelledby="titulo-contacto">
        <h2 id="titulo-contacto" class="text-base font-semibold text-slate-900 mb-4">Datos de contacto</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-5">
            <div>
                <dt class="text-sm text-slate-500">Correo electrónico</dt>
                <dd class="mt-0.5 text-slate-900 [overflow-wrap:anywhere]">
                    @if($capacitado->correo)
                        <a href="mailto:{{ $capacitado->correo }}" class="text-blue-800 hover:underline">{{ $capacitado->correo }}</a>
                    @else — @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Teléfono</dt>
                <dd class="mt-0.5 text-slate-900 tabular-nums">
                    @if($capacitado->telefono)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $capacitado->telefono) }}" class="text-blue-800 hover:underline">{{ $capacitado->telefono }}</a>
                    @else — @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Grupo sanguíneo (RH)</dt>
                <dd class="mt-0.5 text-slate-900">{{ $capacitado->rh ?? '—' }}</dd>
            </div>
        </dl>
        <p class="mt-5 pt-4 border-t border-slate-100 text-sm text-slate-500">
            Registrado el {{ $capacitado->created_at->format('d/m/Y H:i') }} · Última actualización {{ $capacitado->updated_at->format('d/m/Y H:i') }}
        </p>
    </section>

    {{-- Certificados --}}
    <section class="acento-certificados tarjeta-admin overflow-hidden" aria-labelledby="titulo-certificados">
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-slate-200">
            <h2 id="titulo-certificados" class="text-base font-semibold text-slate-900">Certificados <span class="text-slate-500 font-normal">({{ $certificados->count() }})</span></h2>
            <div class="flex flex-wrap gap-2">
                @if($certificados->count() >= 2)
                    <a href="{{ route('admin.capacitados.descargarCertificados', $capacitado) }}" class="btn-secundario">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar todos en PDF
                    </a>
                @endif
                <a href="{{ route('admin.certificados.create') }}" class="btn-primario">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo certificado
                </a>
            </div>
        </div>

        @if($certificados->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="tabla-admin">
                    <caption class="sr-only">Certificados de {{ $capacitado->nombre_completo }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">Código</th>
                            <th scope="col">Curso</th>
                            <th scope="col">Emisión</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="!text-right"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificados as $certificado)
                            @php
                                $vencido = $certificado->isVencido();
                                $pronto  = ! $vencido && $certificado->activo && $certificado->fecha_vencimiento && $certificado->fecha_vencimiento->lte(today()->addDays(30));
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap"><span class="codigo-admin">{{ $certificado->codigo_unico }}</span></td>
                                <td class="min-w-[12rem]">
                                    <span class="block font-medium text-slate-900">{{ $certificado->curso->nombre }}</span>
                                    <span class="block text-sm text-slate-500">{{ $certificado->curso->categoria?->nombre ?? '—' }} · {{ $certificado->intensidad_horaria }} h</span>
                                </td>
                                <td class="whitespace-nowrap tabular-nums">
                                    <span class="block">{{ $certificado->fecha_emision->format('d/m/Y') }}</span>
                                    <span class="block text-sm {{ $vencido ? 'text-red-700 font-semibold' : 'text-slate-500' }}">Vence {{ $certificado->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</span>
                                </td>
                                <td class="whitespace-nowrap">
                                    <div class="flex flex-col items-start gap-1">
                                        @if(! $certificado->activo)
                                            <span class="chip-neutro">Inactivo</span>
                                        @elseif($vencido)
                                            <span class="chip-peligro">Vencido</span>
                                        @elseif($pronto)
                                            <span class="chip-alerta">Vence pronto</span>
                                        @else
                                            <span class="chip-exito">Vigente</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.certificados.show', $certificado) }}" class="accion-fila-acento">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver <span class="sr-only">{{ $certificado->codigo_unico }}</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <p class="font-semibold text-slate-800">Este capacitado aún no tiene certificados.</p>
                <p class="text-slate-500 mt-1">Cuando se emita el primero aparecerá aquí.</p>
            </div>
        @endif
    </section>
</div>
@endsection
