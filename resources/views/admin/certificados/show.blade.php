@extends('layouts.admin')

@section('titulo', 'Detalle de Certificado')
@section('titulo_topbar', 'Certificados')

@section('contenido')
@php
    $vencido  = $certificado->isVencido();
    $dias     = $certificado->fecha_vencimiento ? (int) today()->diffInDays($certificado->fecha_vencimiento, false) : null;
    $pronto   = ! $vencido && $certificado->activo && $dias !== null && $dias <= 30;
    $esGestor = auth()->user()->isGestor();
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    <x-admin.encabezado :titulo="$certificado->codigo_unico"
                        :subtitulo="$certificado->capacitado?->nombre_completo ?? 'Sin capacitado'"
                        :volver="route('admin.certificados.index')">
        <a href="{{ route('admin.certificados.pdf', $certificado) }}" target="_blank" rel="noopener" class="btn-secundario">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Ver PDF<span class="sr-only"> (pestaña nueva)</span>
        </a>
        {{-- Reenviar correo: admin o instructor (dueño del certificado, ver PropietarioScope). Oculto si comunicaciones está desactivado (Fase 3 aún no pagada). --}}
        @if($esGestor)
            @if(config('features.comunicaciones'))
                <form action="{{ route('admin.certificados.reenviar-correo', $certificado) }}" method="POST" class="inline-flex" data-confirmar="¿Reenviar el certificado por correo al capacitado?">
                    @csrf
                    <button type="submit" class="btn-secundario">Reenviar por correo</button>
                </form>
            @endif
            <a href="{{ route('admin.certificados.edit', $certificado) }}" class="btn-secundario">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </a>
            <form action="{{ route('admin.certificados.toggle-activo', $certificado) }}" method="POST" class="inline-flex"
                  data-confirmar="{{ $certificado->activo ? '¿Desactivar el certificado ' . $certificado->codigo_unico . '? Dejará de aparecer como válido en la verificación pública.' : '¿Reactivar el certificado ' . $certificado->codigo_unico . '?' }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-secundario !border-amber-300 !text-amber-900 hover:!bg-amber-50">
                    {{ $certificado->activo ? 'Desactivar' : 'Reactivar' }}
                </button>
            </form>
            <form action="{{ route('admin.certificados.destroy', $certificado) }}" method="POST" class="inline-flex"
                  data-confirmar="¿Eliminar el certificado {{ $certificado->codigo_unico }} y su PDF? Esta acción no se puede deshacer.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-peligro">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Eliminar
                </button>
            </form>
        @endif
    </x-admin.encabezado>

    {{-- Aviso de estado --}}
    @if($vencido)
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-900 px-4 py-3 rounded-lg" role="status">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <p><strong>Certificado vencido.</strong> Venció el {{ $certificado->fecha_vencimiento->format('d/m/Y') }} y no está disponible para descarga pública.</p>
        </div>
    @elseif($pronto)
        <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-950 px-4 py-3 rounded-lg" role="status">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-amber-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <p><strong>Vence {{ $dias <= 0 ? 'hoy' : ($dias === 1 ? 'mañana' : "en {$dias} días") }}</strong> ({{ $certificado->fecha_vencimiento->format('d/m/Y') }}).</p>
        </div>
    @elseif(! $certificado->activo)
        <div class="flex items-start gap-3 bg-slate-100 border border-slate-300 text-slate-800 px-4 py-3 rounded-lg" role="status">
            <p><strong>Certificado inactivo.</strong> No aparece como válido en la verificación pública.</p>
        </div>
    @endif

    {{-- Datos del certificado --}}
    <section class="acento-certificados tarjeta-admin tarjeta-tinte acento-borde-t p-6" aria-labelledby="titulo-datos">
        <div class="flex flex-wrap items-center gap-3 mb-5">
            <h2 id="titulo-datos" class="text-base font-semibold text-slate-900">Datos del certificado</h2>
            @if($certificado->activo)<span class="chip-exito">Activo</span>@else<span class="chip-neutro">Inactivo</span>@endif
            @if($vencido)<span class="chip-peligro">Vencido</span>
            @elseif($pronto)<span class="chip-alerta">Vence pronto</span>
            @elseif($certificado->activo)<span class="chip-exito">Vigente</span>@endif
        </div>

        <dl class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-6">
            <div>
                <dt class="text-sm text-slate-600">Emisión</dt>
                <dd class="mt-0.5 font-semibold text-slate-900 tabular-nums">{{ $certificado->fecha_emision->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-600">Vencimiento</dt>
                <dd class="mt-0.5 font-semibold tabular-nums {{ $vencido ? 'text-red-700' : 'text-slate-900' }}">{{ $certificado->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-600">Intensidad horaria</dt>
                <dd class="mt-0.5 font-semibold text-slate-900 tabular-nums">{{ $certificado->intensidad_horaria }} horas</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-600">Modalidad</dt>
                <dd class="mt-0.5 font-semibold text-slate-900">{{ $certificado->modalidad ? ucfirst($certificado->modalidad) : '—' }}</dd>
            </div>
        </dl>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <section class="acento-capacitados tarjeta-admin acento-borde-t p-6" aria-labelledby="titulo-capacitado">
            <h2 id="titulo-capacitado" class="text-sm font-semibold uppercase tracking-wider acento-texto">Capacitado</h2>
            @if($certificado->capacitado)
                <a href="{{ route('admin.capacitados.show', $certificado->capacitado) }}" class="mt-3 block text-lg font-semibold text-slate-900 hover:text-blue-800 hover:underline">{{ $certificado->capacitado->nombre_completo }}</a>
                <p class="mt-1 text-slate-600 tabular-nums">{{ $certificado->capacitado->tipo_documento ?? 'CC' }} {{ $certificado->capacitado->documento }}</p>
            @else
                <p class="mt-3 text-slate-600">Sin capacitado asociado.</p>
            @endif
        </section>

        <section class="acento-cursos tarjeta-admin acento-borde-t p-6" aria-labelledby="titulo-curso">
            <h2 id="titulo-curso" class="text-sm font-semibold uppercase tracking-wider acento-texto">Curso</h2>
            <p class="mt-3 text-lg font-semibold text-slate-900">{{ $certificado->curso?->nombre ?? 'Sin curso' }}</p>
            <p class="mt-1 text-slate-600">{{ $certificado->curso?->categoria?->nombre ?? 'Sin categoría' }}</p>
        </section>
    </div>

    <section class="tarjeta-admin p-6" aria-labelledby="titulo-registro">
        <h2 id="titulo-registro" class="text-base font-semibold text-slate-900 mb-4">Registro</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="text-sm text-slate-500">Emitido por</dt>
                <dd class="mt-0.5 text-slate-900">{{ $certificado->emitidoPor?->name ?? 'No registrado' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Archivo PDF</dt>
                <dd class="mt-0.5 text-slate-900 [overflow-wrap:anywhere]">
                    @if($certificado->archivo_pdf)<span class="chip-exito mr-1">Cargado</span><span class="text-sm text-slate-600">{{ $certificado->archivo_pdf }}</span>
                    @else<span class="chip-neutro">Sin archivo</span>@endif
                </dd>
            </div>
        </dl>
    </section>
</div>
@endsection
