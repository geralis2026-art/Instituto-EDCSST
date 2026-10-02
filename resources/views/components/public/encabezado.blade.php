@props([
    'titulo',
    'subtitulo' => null,
    'etiqueta' => null,
    'imagen' => null,
])

{{-- Encabezado común de las páginas internas del sitio público --}}
<section class="relative overflow-hidden bg-marca-navy text-white">
    @if($imagen)
        <img src="{{ $imagen }}" alt="" aria-hidden="true" fetchpriority="high" decoding="async"
             class="absolute inset-0 w-full h-full object-cover opacity-30" style="object-position: center 30%;">
        <div class="absolute inset-0 bg-gradient-to-r from-marca-navy via-marca-navy/90 to-marca-navy/60" aria-hidden="true"></div>
    @endif

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="max-w-3xl">
            @if($etiqueta)
                <p class="eyebrow eyebrow-claro mb-3 hero-1">{{ $etiqueta }}</p>
            @endif
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight hero-2">{{ $titulo }}</h1>
            @if($subtitulo)
                <p class="mt-4 text-lg text-blue-100 max-w-2xl hero-3">{{ $subtitulo }}</p>
            @endif
            {{ $slot }}
        </div>
    </div>
</section>
