@extends('layouts.admin')

@section('titulo', 'Dashboard')
@section('titulo_topbar', 'Panel de control')

@section('contenido')
<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-gray-500">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        <div class="flex gap-3">
            <a href="{{ route('admin.capacitados.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Capacitado
            </a>
            <a href="{{ route('admin.certificados.create') }}" class="inline-flex items-center gap-2 px-4 py-2 btn-gold text-sm rounded-lg shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Certificado
            </a>
        </div>
    </div>

    {{-- Tarjetas de estadísticas --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-t-amber-500 card-hover reveal delay-1">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Capacitados</p>
            <p class="text-3xl font-bold text-gray-900 mt-1 counter" data-target="{{ $totalCapacitados }}" data-format="number">{{ number_format($totalCapacitados) }}</p>
            <p class="text-xs text-amber-600 mt-1">+{{ $capacitadosMesActual }} este mes</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-t-blue-600 card-hover reveal delay-2">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Certificados</p>
            <p class="text-3xl font-bold text-gray-900 mt-1 counter" data-target="{{ $totalCertificados }}" data-format="number">{{ number_format($totalCertificados) }}</p>
            <p class="text-xs text-amber-600 mt-1">+{{ $certificadosMesActual }} este mes</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-t-amber-500 card-hover reveal delay-3">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Horas capacitadas</p>
            <p class="text-3xl font-bold text-gray-900 mt-1 counter" data-target="{{ $horasCapacitadasTotal }}" data-format="number">{{ number_format($horasCapacitadasTotal) }}</p>
            <p class="text-xs text-gray-400 mt-1">acumuladas</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 border-t-4 border-t-blue-600 card-hover reveal delay-4">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Mensajes nuevos</p>
            <p class="text-3xl font-bold mt-1 {{ $mensajesNuevos > 0 ? 'text-red-600' : 'text-gray-900' }} counter" data-target="{{ $mensajesNuevos }}">{{ $mensajesNuevos }}</p>
            <a href="{{ route('admin.mensajes.index') }}" class="text-xs text-amber-600 hover:underline mt-1 inline-block">Ver bandeja</a>
        </div>
    </div>

    {{-- Gráfica + Top capacitados --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Gráfica certificados por mes --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6 card-hover reveal delay-1">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Certificados emitidos — últimos 12 meses</h2>
            <canvas id="chartCertificados" height="100"></canvas>
        </div>

        {{-- Top capacitados por horas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 card-hover reveal delay-2">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Top capacitados por horas</h2>
            @if($topCapacitados->isEmpty())
                <p class="text-sm text-gray-400">Sin datos aún.</p>
            @else
                <ul class="space-y-3">
                    @foreach($topCapacitados as $c)
                    <li class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $c->nombre_completo }}</p>
                            <p class="text-xs text-gray-400">{{ $c->documento }}</p>
                        </div>
                        <span class="ml-3 text-sm font-bold text-amber-600 whitespace-nowrap">{{ $c->horas_capacitadas }}h</span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Últimos certificados + Cursos más usados --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Últimos certificados --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6 card-hover reveal">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Últimos certificados</h2>
                <a href="{{ route('admin.certificados.index') }}" class="text-xs text-amber-600 hover:underline">Ver todos</a>
            </div>
            @if($ultimosCertificados->isEmpty())
                <p class="text-sm text-gray-400">Sin certificados aún.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                <th class="pb-2 font-medium">Capacitado</th>
                                <th class="pb-2 font-medium">Curso</th>
                                <th class="pb-2 font-medium">Código</th>
                                <th class="pb-2 font-medium">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($ultimosCertificados as $cert)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-2 pr-3 font-medium text-gray-800 truncate max-w-[140px]">{{ $cert->capacitado->nombre_completo }}</td>
                                <td class="py-2 pr-3 text-gray-600 truncate max-w-[140px]">{{ $cert->curso->nombre }}</td>
                                <td class="py-2 pr-3">
                                    <a href="{{ route('admin.certificados.show', $cert) }}" class="text-amber-600 hover:underline font-mono text-xs">{{ $cert->codigo_unico }}</a>
                                </td>
                                <td class="py-2 text-gray-500 whitespace-nowrap">{{ $cert->fecha_emision->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Cursos más usados --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 card-hover reveal delay-1">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Cursos más certificados</h2>
            @if($cursosMasUsados->isEmpty())
                <p class="text-sm text-gray-400">Sin datos aún.</p>
            @else
                <ul class="space-y-3">
                    @foreach($cursosMasUsados as $curso)
                    <li>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 truncate pr-2">{{ $curso->nombre }}</span>
                            <span class="font-bold text-gray-900 whitespace-nowrap">{{ $curso->certificados_count }}</span>
                        </div>
                        @php $max = $cursosMasUsados->first()->certificados_count ?: 1; @endphp
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full" style="width: {{ round(($curso->certificados_count / $max) * 100) }}%; background: linear-gradient(90deg, #F59E0B, #D4A017)"></div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Mensajes recientes --}}
    @if($mensajesRecientes->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 card-hover reveal">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700">Mensajes recientes</h2>
            <a href="{{ route('admin.mensajes.index') }}" class="text-xs text-amber-600 hover:underline">Ver todos</a>
        </div>
        <div class="space-y-3">
            @foreach($mensajesRecientes as $mensaje)
            <div class="flex items-start gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-700 font-semibold text-sm">
                    {{ strtoupper(substr($mensaje->nombre, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium text-gray-800">{{ $mensaje->nombre }}</p>
                        <div class="flex items-center gap-2">
                            @if($mensaje->estado === 'nuevo')
                                <span class="inline-block w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                            @endif
                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $mensaje->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 truncate">{{ $mensaje->mensaje }}</p>
                </div>
            </div>
            @endforeach
        </div>
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
                backgroundColor: 'rgba(245, 158, 11, 0.18)',
                borderColor: 'rgba(212, 160, 23, 0.9)',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
