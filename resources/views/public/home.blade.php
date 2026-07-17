@extends('layouts.public')

@section('titulo', 'Inicio')
@section('descripcion', 'Instituto EDCSST - Capacitación y certificación profesional en seguridad y salud en el trabajo.')

@push('preload')
<link rel="preload" as="image" href="{{ asset('images/capacitacion-grupal-docencia.jpg') }}" fetchpriority="high">
@endpush

@push('styles')
<style>
#hero-principal { min-height: 42vh; }
@media (min-width: 1024px) { #hero-principal { min-height: 60vh; } }
/* Móvil: gradiente vertical más fuerte para legibilidad */
@media (max-width: 1023px) {
    #hero-gradient { background: linear-gradient(to bottom, rgba(30,58,138,0.88) 0%, rgba(30,58,138,0.75) 50%, rgba(30,58,138,0.65) 100%) !important; }
}
</style>
@endpush

@section('contenido')

{{-- ============ HERO / BANNER PRINCIPAL ============ --}}
<section id="hero-principal" class="relative overflow-hidden bg-blue-900 text-white flex items-center">

    {{-- Imagen de fondo completa --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/capacitacion-grupal-docencia.jpg') }}"
            alt="Capacitación grupal EDCSST"
            class="w-full h-full object-cover"
            style="object-position: 35% 35%;"
            fetchpriority="high" decoding="async">
        {{-- Gradiente: azul sólido a la izquierda, transparente a la derecha --}}
        <div id="hero-gradient" class="absolute inset-0"
            style="background: linear-gradient(to right, #1e3a8a 0%, #1e3a8a 8%, rgba(30,58,138,0.98) 25%, rgba(30,58,138,0.92) 48%, rgba(30,58,138,0.40) 65%, transparent 100%);"></div>
    </div>

    {{-- Texto sobre el gradiente --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-14 lg:py-24">
        <div class="w-[90%] lg:w-[48%]">
            <span class="badge-gold mb-2 sm:mb-4 inline-block text-[10px] sm:text-xs">
                Capacitación profesional
            </span>
            <h1 class="text-xl sm:text-4xl lg:text-6xl font-bold leading-tight mb-3 sm:mb-6" style="text-shadow: 0 2px 12px rgba(0,0,0,0.45);">
                Forma tu futuro con<br>
                <span class="text-amber-300">certificaciones reales</span>
            </h1>
            <p class="hidden sm:block text-base sm:text-lg lg:text-xl text-blue-100 mb-6 sm:mb-8 leading-relaxed">
                En el Instituto EDCSST capacitamos profesionales con certificados verificables <br> digitalmente. Educación práctica para el mundo laboral actual.
            </p>
            <div class="flex flex-row flex-wrap gap-2 sm:gap-4">
                <a href="{{ route('consulta') }}" class="inline-flex items-center justify-center px-3 py-2 sm:px-6 sm:py-3 bg-white text-blue-900 font-semibold rounded-lg hover:bg-amber-50 transition shadow-lg text-xs sm:text-base">
                    Consultar mi certificado
                    <svg class="w-4 h-4 ml-1 sm:w-5 sm:h-5 sm:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="{{ route('verificar') }}" class="inline-flex items-center justify-center px-3 py-2 sm:px-6 sm:py-3 btn-gold rounded-lg text-xs sm:text-base">
                    Verificar certificado
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ============ CURSOS DESTACADOS ============ --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12 reveal">
            <span class="badge-gold mb-3 inline-block">Oferta académica</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3 section-title-center">Nuestros cursos destacados</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mt-4">
                Capacitaciones diseñadas para impulsar tu carrera profesional con certificación reconocida.
            </p>
        </div>

        @if($cursosDestacados->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($cursosDestacados as $curso)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border border-gray-100 group card-gold-hover reveal delay-{{ min($loop->index + 1, 6) }}">
                        {{-- Imagen del curso --}}
                        <div class="aspect-video bg-gradient-to-br from-blue-100 to-blue-200 relative overflow-hidden">
                            @if($curso->imagen)
                                <img src="{{ $curso->imagen_url }}" alt="{{ $curso->nombre }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif
                            <span class="absolute top-3 left-3 bg-white/95 px-3 py-1 rounded-full text-xs font-semibold text-blue-900">
                                {{ $curso->categoria?->nombre ?? 'Sin categoría' }}
                            </span>
                        </div>

                        {{-- Información --}}
                        <div class="p-5">
                            <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">{{ $curso->nombre }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $curso->descripcion_corta }}</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $curso->duracion }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-10 reveal">
                <a href="{{ route('catalogo') }}" class="inline-flex items-center px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition">
                    Ver todos los cursos
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @else
            <p class="text-center text-gray-500">Pronto publicaremos nuestros cursos. ¡Atento!</p>
        @endif
    </div>
</section>

{{-- ============ SOBRE NOSOTROS ============ --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="reveal-left">
                <span class="text-amber-600 font-semibold text-sm uppercase tracking-wider">Sobre nosotros</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2 mb-6">
                    Educación de calidad con<br>certificación verificable
                </h2>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Somos el Instituto EDCSST (Educación para el Desarrollo y la Calidad en Seguridad y Salud en el Trabajo), una institución dedicada a la formación profesional de calidad en Colombia.
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Cada uno de nuestros certificados puede ser verificado en línea mediante un código único, garantizando autenticidad y validez para empresas y empleadores.
                </p>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="p-5 rounded-xl shadow-md text-center" style="background: linear-gradient(135deg, #F59E0B, #D4A017)">
                        <div class="text-3xl font-bold text-white counter" data-target="100" data-suffix="%">100%</div>
                        <div class="text-sm text-amber-100 font-medium mt-1">Certificados digitales</div>
                    </div>
                    <div class="p-5 rounded-xl shadow-md text-center bg-blue-900">
                        <div class="text-3xl font-bold text-white">24/7</div>
                        <div class="text-sm text-blue-200 font-medium mt-1">Verificación online</div>
                    </div>
                </div>
            </div>

            <div class="space-y-4 reveal-right">
                <div class="flex items-start bg-amber-50 border border-amber-100 p-5 rounded-xl hover:shadow-sm transition">
                    <div class="w-12 h-12 icon-gold rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900 mb-1">Certificados verificables</h3>
                        <p class="text-sm text-gray-600">Cada certificado tiene un código único que cualquier empresa puede validar en línea.</p>
                    </div>
                </div>

                <div class="flex items-start bg-blue-50 border border-blue-100 p-5 rounded-xl hover:shadow-sm transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900 mb-1">Capacitación práctica</h3>
                        <p class="text-sm text-gray-600">Programas diseñados con enfoque práctico para aplicar en el entorno laboral real.</p>
                    </div>
                </div>

                <div class="flex items-start bg-amber-50 border border-amber-100 p-5 rounded-xl hover:shadow-sm transition">
                    <div class="w-12 h-12 icon-gold rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900 mb-1">Acceso 24/7</h3>
                        <p class="text-sm text-gray-600">Consulta y descarga tus certificados en cualquier momento desde nuestra plataforma.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-blue-950 text-white relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none"
         style="background: linear-gradient(135deg, transparent 60%, rgba(245,158,11,0.08))"></div>
    <div class="absolute top-0 left-0 right-0 h-1"
         style="background: linear-gradient(90deg, transparent, #F59E0B, transparent)"></div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
        <span class="badge-gold mb-4 inline-block">¿Tienes preguntas?</span>
        <h2 class="text-3xl sm:text-4xl font-bold mb-4">Estamos aquí para ayudarte</h2>
        <p class="text-lg text-blue-200 mb-8">
            Contáctanos para obtener información sobre nuestros cursos, fechas de inicio o procesos de inscripción.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('contacto') }}" class="inline-flex items-center px-8 py-4 btn-gold rounded-lg font-semibold">
                Contáctanos ahora
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </a>
            <a href="{{ route('catalogo') }}" class="inline-flex items-center px-8 py-4 border-2 border-blue-600 text-white rounded-lg font-semibold hover:bg-blue-900 transition">
                Ver cursos
            </a>
        </div>
    </div>
</section>

@endsection
