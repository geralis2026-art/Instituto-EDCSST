@extends('layouts.admin')

@section('titulo', 'Dashboard')
@section('titulo_topbar', 'Panel de control')

@section('contenido')
@php
    $esAdmin  = auth()->user()->isAdmin();
    $esGestor = auth()->user()->isGestor();
    $diasPara = function ($fecha) {
        $dias = (int) today()->diffInDays($fecha);
        return match (true) {
            $dias <= 0 => 'hoy',
            $dias === 1 => 'mañana',
            default => "en {$dias} días",
        };
    };
@endphp

<div class="space-y-8">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-[28px] font-bold text-slate-900">Hola, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</h1>
            <p class="text-slate-600 mt-1 first-letter:uppercase">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.capacitados.create') }}" class="btn-secundario">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo capacitado
            </a>
            <a href="{{ route('admin.certificados.create') }}" class="btn-primario">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo certificado
            </a>
        </div>
    </div>

    {{-- ============ REQUIERE ATENCIÓN ============ --}}
    <section aria-labelledby="titulo-atencion" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="tarjeta-admin p-6">
            <h2 id="titulo-atencion" class="text-base font-semibold text-slate-900">Requiere atención</h2>
            <ul class="mt-4 divide-y divide-slate-100">
                @if($esAdmin)
                    <li>
                        <a href="{{ route('admin.mensajes.index') }}" class="flex items-center gap-3 py-3 -mx-2 px-2 rounded-lg hover:bg-slate-50 group">
                            <span class="font-titulo text-2xl font-bold tabular-nums w-10 {{ $mensajesNuevos > 0 ? 'text-red-700' : 'text-slate-400' }}">{{ $mensajesNuevos }}</span>
                            <span class="flex-1">
                                <span class="block font-medium text-slate-900">Mensajes nuevos</span>
                                <span class="block text-sm text-slate-500">{{ $mensajesNuevos > 0 ? 'Esperan respuesta' : 'Todo respondido' }}</span>
                            </span>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('admin.certificados.index', ['estado' => 'por_vencer']) }}" class="flex items-center gap-3 py-3 -mx-2 px-2 rounded-lg hover:bg-slate-50 group">
                        <span class="font-titulo text-2xl font-bold tabular-nums w-10 {{ $totalPorVencer > 0 ? 'text-amber-700' : 'text-slate-400' }}">{{ $totalPorVencer }}</span>
                        <span class="flex-1">
                            <span class="block font-medium text-slate-900">Certificados por vencer</span>
                            <span class="block text-sm text-slate-500">{{ $totalPorVencer > 0 ? 'En los próximos 30 días' : 'Ninguno en los próximos 30 días' }}</span>
                        </span>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </li>
                @if($esGestor)
                    <li>
                        <a href="{{ route('admin.certificados.masivos') }}" class="flex items-center gap-3 py-3 -mx-2 px-2 rounded-lg hover:bg-slate-50 group">
                            <span class="font-titulo text-2xl font-bold tabular-nums w-10 {{ $solicitudesPendientes > 0 ? 'text-blue-800' : 'text-slate-400' }}">{{ $solicitudesPendientes }}</span>
                            <span class="flex-1">
                                <span class="block font-medium text-slate-900">Solicitudes pendientes</span>
                                <span class="block text-sm text-slate-500">{{ $solicitudesPendientes > 0 ? 'Certificados por generar' : 'Nada por generar' }}</span>
                            </span>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="acento-certificados tarjeta-admin acento-borde-t p-6 lg:col-span-2 min-w-0">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h2 class="text-base font-semibold text-slate-900">Próximos a vencer</h2>
                @if($totalPorVencer > 0)
                    <a href="{{ route('admin.certificados.index', ['estado' => 'por_vencer']) }}" class="text-sm font-semibold text-blue-800 hover:underline">Ver los {{ $totalPorVencer }}</a>
                @endif
            </div>

            @if($porVencer->isEmpty())
                <p class="text-slate-600 py-6">Ningún certificado vence en los próximos 30 días.</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($porVencer as $cert)
                        @php $dias = (int) today()->diffInDays($cert->fecha_vencimiento); @endphp
                        <li class="flex items-center gap-4 py-3">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.certificados.show', $cert) }}" class="block font-medium text-slate-900 hover:text-blue-800 hover:underline truncate">{{ $cert->capacitado?->nombre_completo ?? 'Sin capacitado' }}</a>
                                <span class="block text-sm text-slate-500 truncate">{{ $cert->curso?->nombre ?? 'Sin curso' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="{{ $dias <= 7 ? 'chip-peligro' : 'chip-alerta' }}">Vence {{ $diasPara($cert->fecha_vencimiento) }}</span>
                                <span class="block text-xs text-slate-500 mt-1 tabular-nums">{{ $cert->fecha_vencimiento->format('d/m/Y') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    {{-- ============ CIFRAS ============ --}}
    <section aria-label="Cifras generales" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="acento-certificados tarjeta-admin tarjeta-tinte acento-borde-t p-6 sm:col-span-2">
            <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-medium text-slate-600">Certificados emitidos</p>
                <span class="insignia-acento"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg></span>
            </div>
            <p class="font-titulo text-5xl font-bold text-slate-900 mt-2 tabular-nums">{{ number_format($totalCertificados) }}</p>
            <p class="text-sm text-slate-600 mt-2">
                <span class="font-semibold acento-texto">+{{ $certificadosMesActual }}</span> este mes
                <span class="text-slate-300 mx-1" aria-hidden="true">·</span>
                <span class="font-semibold acento-texto">{{ $certificadosHoy }}</span> hoy
            </p>
        </div>
        <div class="acento-capacitados tarjeta-admin tarjeta-tinte acento-borde-t p-6">
            <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-medium text-slate-600">Capacitados</p>
                <span class="insignia-acento"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
            </div>
            <p class="font-titulo text-3xl font-bold text-slate-900 mt-2 tabular-nums">{{ number_format($totalCapacitados) }}</p>
            <p class="text-sm text-slate-600 mt-2"><span class="font-semibold acento-texto">+{{ $capacitadosMesActual }}</span> este mes</p>
        </div>
        <div class="acento-cursos tarjeta-admin tarjeta-tinte acento-borde-t p-6">
            <div class="flex items-start justify-between gap-3">
                <p class="text-sm font-medium text-slate-600">Horas capacitadas</p>
                <span class="insignia-acento"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
            </div>
            <p class="font-titulo text-3xl font-bold text-slate-900 mt-2 tabular-nums">{{ number_format($horasCapacitadasTotal) }}</p>
            <p class="text-sm text-slate-600 mt-2">acumuladas</p>
        </div>
    </section>

    {{-- Gráfica + Top capacitados --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="acento-certificados lg:col-span-2 tarjeta-admin p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4">Certificados emitidos — últimos 12 meses</h2>
            <div class="relative h-72">
                <canvas id="chartCertificados" role="img"
                        aria-label="Gráfica de barras: certificados emitidos por mes en los últimos 12 meses. Total del periodo: {{ array_sum($certificadosPorMes['data']) }}."></canvas>
            </div>
            <details class="mt-3 text-sm">
                <summary class="cursor-pointer text-slate-600 hover:text-slate-900">Ver datos en tabla</summary>
                <table class="mt-2 w-full text-sm">
                    <thead><tr class="text-left text-slate-500"><th class="py-1 font-medium">Mes</th><th class="py-1 font-medium text-right">Certificados</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($certificadosPorMes['labels'] as $i => $mes)
                            <tr><td class="py-1">{{ $mes }}</td><td class="py-1 text-right tabular-nums">{{ $certificadosPorMes['data'][$i] ?? 0 }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </details>
        </div>

        <div class="acento-capacitados tarjeta-admin p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4">Top capacitados por horas</h2>
            @if($topCapacitados->isEmpty())
                <p class="text-sm text-slate-500">Sin datos aún.</p>
            @else
                <ol class="space-y-3">
                    @foreach($topCapacitados as $c)
                        <li class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full acento-icono text-xs font-bold flex items-center justify-center shrink-0 tabular-nums">{{ $loop->iteration }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $c->nombre_completo }}</p>
                                <p class="text-xs text-slate-500 tabular-nums">{{ $c->documento }}</p>
                            </div>
                            <span class="text-sm font-bold text-slate-900 tabular-nums whitespace-nowrap">{{ $c->horas_capacitadas }} h</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    {{-- Últimos certificados + Cursos más certificados --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="acento-certificados lg:col-span-2 min-w-0 tarjeta-admin p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    Últimos certificados
                    @if($certificadosHoy > 0)
                        <span class="chip-exito">{{ $certificadosHoy }} {{ $certificadosHoy === 1 ? 'nuevo' : 'nuevos' }} hoy</span>
                    @endif
                </h2>
                <a href="{{ route('admin.certificados.index') }}" class="text-sm font-semibold text-blue-800 hover:underline">Ver todos</a>
            </div>
            @if($ultimosCertificados->isEmpty())
                <p class="text-sm text-slate-500">Sin certificados aún.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <th scope="col" class="pb-2 font-medium">Capacitado</th>
                                <th scope="col" class="pb-2 font-medium">Curso</th>
                                <th scope="col" class="pb-2 font-medium">Código</th>
                                <th scope="col" class="pb-2 font-medium">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($ultimosCertificados as $cert)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-2.5 pr-3 font-medium text-slate-800 max-w-[200px]">
                                        <span class="flex items-center gap-2">
                                            <span class="truncate">{{ $cert->capacitado->nombre_completo }}</span>
                                            @if($cert->created_at->isToday())<span class="chip-exito">Nuevo</span>@endif
                                        </span>
                                    </td>
                                    <td class="py-2.5 pr-3 text-slate-600 truncate max-w-[160px]">{{ $cert->curso->nombre }}</td>
                                    <td class="py-2.5 pr-3 whitespace-nowrap">
                                        <a href="{{ route('admin.certificados.show', $cert) }}" class="codigo-admin hover:bg-amber-100">{{ $cert->codigo_unico }}</a>
                                    </td>
                                    <td class="py-2.5 text-slate-600 whitespace-nowrap tabular-nums">{{ $cert->fecha_emision->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="acento-cursos tarjeta-admin p-6">
            <h2 class="text-base font-semibold text-slate-900 mb-4">Cursos más certificados</h2>
            @if($cursosMasUsados->isEmpty())
                <p class="text-sm text-slate-500">Sin datos aún.</p>
            @else
                @php $max = $cursosMasUsados->first()->certificados_count ?: 1; @endphp
                <ul class="space-y-4">
                    @foreach($cursosMasUsados as $curso)
                        <li>
                            <div class="flex justify-between gap-3 text-sm mb-1.5">
                                <span class="text-slate-700">{{ $curso->nombre }}</span>
                                <span class="font-bold text-slate-900 tabular-nums">{{ $curso->certificados_count }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2" role="presentation">
                                <div class="h-2 rounded-full acento-fondo" style="width: {{ round(($curso->certificados_count / $max) * 100) }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Mensajes recientes --}}
    @if($mensajesRecientes->isNotEmpty())
        <div class="tarjeta-admin p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-slate-900">Mensajes recientes</h2>
                <a href="{{ route('admin.mensajes.index') }}" class="text-sm font-semibold text-blue-800 hover:underline">Ver todos</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach($mensajesRecientes as $mensaje)
                    <li class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                        <span class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-slate-700 font-semibold text-sm" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr($mensaje->nombre, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-slate-800">{{ $mensaje->nombre }}</p>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if($mensaje->estado === 'nuevo')
                                        <span class="chip-peligro">Nuevo</span>
                                    @endif
                                    <span class="text-xs text-slate-500 whitespace-nowrap">{{ $mensaje->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 truncate">{{ $mensaje->mensaje }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script nonce="{{ $cspNonce }}">
    const ctx = document.getElementById('chartCertificados').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($certificadosPorMes['labels']),
            datasets: [{
                label: 'Certificados',
                data: @json($certificadosPorMes['data']),
                backgroundColor: '#B45309',      // dorado = certificados
                hoverBackgroundColor: '#92400E',
                borderWidth: 0,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? false : { duration: 400 },
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#E2E8F0' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
