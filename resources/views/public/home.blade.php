@extends('layouts.public')

@section('titulo', 'Inicio')
@section('descripcion', 'Instituto EDCSST - Capacitación y certificación profesional en seguridad y salud en el trabajo.')

@section('contenido')

{{-- ============ HERO / BANNER PRINCIPAL ============ --}}
<section class="relative bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800 text-white overflow-hidden">

    {{-- Elementos decorativos dorados flotantes --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full pointer-events-none float-a"
         style="background: radial-gradient(circle, rgba(245,158,11,0.18) 0%, transparent 70%)"></div>
    <div class="absolute bottom-0 -left-16 w-72 h-72 rounded-full pointer-events-none float-b"
         style="background: radial-gradient(circle, rgba(212,160,23,0.12) 0%, transparent 70%)"></div>
    <div class="absolute top-1/4 left-1/4 w-48 h-48 rounded-full pointer-events-none float-c"
         style="background: radial-gradient(circle, rgba(245,158,11,0.07) 0%, transparent 70%)"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-1 opacity-10 pointer-events-none"
         style="background: linear-gradient(90deg, transparent, #F59E0B, transparent)"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Texto del hero --}}
            <div>
                <span class="badge-gold mb-5 inline-block hero-1">
                    Capacitación profesional
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6 hero-2">
                    Forma tu futuro con<br>
                    <span style="background: linear-gradient(90deg, #F59E0B, #D4A017); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        certificaciones reales
                    </span>
                </h1>
                <p class="text-lg sm:text-xl text-blue-100 mb-8 leading-relaxed hero-3">
                    En el Instituto EDCSST capacitamos profesionales con certificados verificables digitalmente. Educación práctica para el mundo laboral actual.
                </p>
                <div class="flex flex-wrap gap-4 hero-4">
                    <a href="{{ route('consulta') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-900 font-semibold rounded-lg hover:bg-amber-50 transition shadow-lg">
                        Consultar mi certificado
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('verificar') }}" class="inline-flex items-center px-6 py-3 btn-gold rounded-lg">
                        Verificar certificado
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Card decorativo de certificado --}}
            <div class="hidden lg:block hero-card">
                <div class="relative">
                    {{-- Sombra decorativa dorada --}}
                    <div class="absolute inset-0 rounded-3xl transform rotate-2"
                         style="background: linear-gradient(135deg, #F59E0B22, #D4A01744); margin: -4px;"></div>
                    <div class="relative bg-white text-gray-800 rounded-3xl shadow-2xl p-8 border-t-4 border-amber-400">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Certificado oficial</p>
                                <p class="text-lg font-bold text-blue-900">Instituto EDCSST</p>
                            </div>
                            <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                 style="background: linear-gradient(135deg, #FEF3C7, #FDE68A)">
                                <svg class="w-9 h-9" style="color: #D4A017" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Otorgado a:</p>
                            <p class="text-xl font-bold text-gray-800 mb-3">Tu nombre aquí</p>
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Por completar el curso de:</p>
                            <p class="text-base font-semibold text-blue-900">Seguridad y Salud en el Trabajo</p>
                        </div>
                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100 text-xs">
                            <span class="text-gray-400">Cód: EDCSST-2026-00001</span>
                            <span class="font-semibold px-2 py-0.5 rounded-full text-white" style="background: #D4A017">✓ Verificable</span>
                        </div>
                    </div>
                </div>
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
                                <img src="{{ asset('storage/' . $curso->imagen) }}" alt="{{ $curso->nombre }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            @endif
                            <span class="absolute top-3 left-3 bg-white/95 px-3 py-1 rounded-full text-xs font-semibold text-blue-900">
                                {{ $curso->categoria->nombre }}
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
