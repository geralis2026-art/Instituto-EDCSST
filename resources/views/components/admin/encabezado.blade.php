@props([
    'titulo',
    'subtitulo' => null,
    'volver' => null,
])

{{-- Encabezado de página del panel: título + descripción a la izquierda, acciones (slot) a la derecha --}}
<div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div class="min-w-0 acento-barra pl-4">
        @if($volver)
            <a href="{{ $volver }}" class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-slate-800 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver
            </a>
        @endif
        <h1 class="text-2xl sm:text-[28px] font-bold text-slate-900 leading-tight">{{ $titulo }}</h1>
        @if($subtitulo)
            <p class="text-slate-600 mt-1">{{ $subtitulo }}</p>
        @endif
    </div>
    @if($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            {{ $slot }}
        </div>
    @endif
</div>
