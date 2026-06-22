@extends('layouts.public')

@section('titulo', 'Sobre Nosotros')
@section('descripcion', 'Conoce el Instituto EDCSST: nuestra misión, visión, valores y compromiso con la formación profesional en seguridad y salud en el trabajo.')

@push('styles')
<style>
    #hero-nosotros { min-height: 45vh; display: flex; align-items: center; }
    @media (min-width: 1024px) { #hero-nosotros { min-height: 58vh; } }
    @keyframes nosotrosKenBurns {
        0%   { transform: scale(1) translate(0, 0); }
        100% { transform: scale(1.07) translate(1%, -1%); }
    }
    #hero-nosotros .bg-foto {
        animation: nosotrosKenBurns 14s ease-out forwards;
    }
    .stat-float-a { animation: floatA 4s ease-in-out infinite; }
    .stat-float-b { animation: floatB 4.5s ease-in-out 0.5s infinite; }
    @keyframes floatA {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-7px); }
    }
    @keyframes floatB {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-5px); }
    }
</style>
@endpush

@section('contenido')

{{-- HERO --}}
<section id="hero-nosotros" class="relative overflow-hidden bg-blue-950 text-white">

    {{-- Foto de fondo con Ken Burns --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/examen-medico-ocupacional.jpg') }}"
             alt="Instituto EDCSST"
             class="bg-foto w-full h-full object-cover"
             style="object-position: center 30%;">
        {{-- Overlay: oscuro en ambos lados, más transparente al centro-derecha --}}
        <div class="absolute inset-0"
             style="background: linear-gradient(to right, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.80) 30%, rgba(30,58,138,0.65) 60%, rgba(30,58,138,0.45) 100%);"></div>
        {{-- Línea dorada superior --}}
        <div class="absolute top-0 left-0 right-0 h-1"
             style="background: linear-gradient(90deg, transparent, #F59E0B 40%, #D4A017 60%, transparent);"></div>
    </div>

    {{-- Contenido --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-0">
        <div class="lg:grid lg:grid-cols-2 lg:gap-12 lg:items-center">

            {{-- Texto principal --}}
            <div class="hero-1">
                <span class="badge-gold mb-5 inline-block text-xs sm:text-sm">Quiénes somos</span>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-5">
                    Instituto<br>
                    <span style="background: linear-gradient(90deg, #F59E0B, #D4A017); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        EDCSST
                    </span>
                </h1>
                <p class="text-base sm:text-lg text-blue-100 leading-relaxed max-w-lg mb-8">
                    Educación para el Desarrollo y la Calidad en Seguridad y Salud en el Trabajo. Formamos profesionales con certificación verificable, respaldados por licencia SST de cobertura nacional.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('catalogo') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 btn-gold rounded-lg font-semibold text-sm">
                        Ver cursos
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('contacto') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 border border-blue-400 text-white rounded-lg font-semibold text-sm hover:bg-blue-900 transition">
                        Contáctanos
                    </a>
                </div>
            </div>

            {{-- Stats flotantes — solo desktop --}}
            <div class="hidden lg:flex flex-col gap-4 items-end hero-2">
                <div class="stat-float-a bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-5 w-52 text-center shadow-xl">
                    <div class="text-3xl font-bold mb-1"
                         style="background: linear-gradient(90deg, #F59E0B, #D4A017); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        100%
                    </div>
                    <div class="text-blue-100 text-xs leading-snug">Certificados digitales verificables</div>
                </div>
                <div class="stat-float-b bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-5 w-52 text-center shadow-xl" style="margin-right: 2.5rem;">
                    <div class="text-3xl font-bold text-white mb-1">24/7</div>
                    <div class="text-blue-100 text-xs leading-snug">Verificación en línea disponible</div>
                </div>
                <div class="stat-float-a bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-5 w-52 text-center shadow-xl" style="animation-delay: 1s;">
                    <div class="text-3xl font-bold text-white mb-1">SST</div>
                    <div class="text-blue-100 text-xs leading-snug">Especialización en seguridad laboral</div>
                </div>
            </div>

        </div>
    </div>

    {{-- Ola decorativa inferior --}}
    <div class="absolute bottom-0 left-0 right-0 pointer-events-none">
        <svg viewBox="0 0 1440 48" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display:block; width:100%; height:48px;">
            <path d="M0 48 C360 0 1080 0 1440 48 L1440 48 L0 48 Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- MISIÓN / VISIÓN / VALORES --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Misión: card dorado --}}
            <div class="text-center p-8 rounded-2xl shadow-md border-t-4 border-amber-400 reveal delay-1"
                 style="background: linear-gradient(160deg, #FFFBEB, #FEF3C7)">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-5 shadow-md"
                     style="background: linear-gradient(135deg, #F59E0B, #D4A017)">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-blue-900 mb-3">Misión</h2>
                <p class="text-gray-700 text-sm leading-relaxed">
                    Brindar formación profesional de alta calidad en seguridad y salud en el trabajo, emitiendo certificaciones verificables digitalmente que respalden el desarrollo laboral de nuestros capacitados.
                </p>
            </div>

            {{-- Visión: card azul --}}
            <div class="text-center p-8 rounded-2xl shadow-md border-t-4 border-blue-500 bg-blue-50 reveal delay-2">
                <div class="w-14 h-14 bg-blue-700 rounded-xl flex items-center justify-center mx-auto mb-5 shadow-md">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-blue-900 mb-3">Visión</h2>
                <p class="text-gray-700 text-sm leading-relaxed">
                    Ser referente nacional en capacitación y certificación en seguridad y salud en el trabajo, reconocidos por la calidad de nuestros programas y la confianza de empresas y profesionales en Colombia.
                </p>
            </div>

            {{-- Valores: card dorado --}}
            <div class="text-center p-8 rounded-2xl shadow-md border-t-4 border-amber-400 reveal delay-3"
                 style="background: linear-gradient(160deg, #FFFBEB, #FEF3C7)">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-5 shadow-md"
                     style="background: linear-gradient(135deg, #F59E0B, #D4A017)">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-blue-900 mb-3">Valores</h2>
                <p class="text-gray-700 text-sm leading-relaxed">
                    Calidad, transparencia, innovación y compromiso social. Cada certificado que emitimos representa nuestra responsabilidad con la seguridad laboral y el bienestar de los trabajadores colombianos.
                </p>
            </div>

        </div>
    </div>
</section>

{{-- QUIÉNES SOMOS --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <div class="reveal-left">
                <span class="text-amber-600 font-semibold text-sm uppercase tracking-wider">Nuestra historia</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2 mb-6">
                    Comprometidos con la formación profesional
                </h2>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    El Instituto EDCSST nació con el propósito de cerrar la brecha en la formación especializada en seguridad y salud en el trabajo en la región de los Llanos Orientales de Colombia.
                </p>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Capacitamos a trabajadores, empresas e instituciones en todo el territorio nacional que requieren cumplir con los estándares exigidos por la normativa colombiana en SST, con personal profesional licenciado y habilitado para prestar servicios de seguridad y salud en el trabajo.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Cada uno de nuestros programas está diseñado con un enfoque práctico, adaptado a las necesidades reales del entorno laboral, garantizando que el conocimiento adquirido sea aplicable de inmediato.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-5 reveal-right">
                <div class="bg-amber-50 p-6 rounded-xl shadow-sm border border-amber-100 text-center">
                    <div class="text-4xl font-bold text-amber-600 mb-1 counter" data-target="100" data-suffix="%">100%</div>
                    <div class="text-sm text-gray-600">Certificados digitales verificables</div>
                </div>
                <div class="bg-amber-50 p-6 rounded-xl shadow-sm border border-amber-100 text-center">
                    <div class="text-4xl font-bold text-amber-600 mb-1">24/7</div>
                    <div class="text-sm text-gray-600">Verificación en línea disponible</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="text-4xl font-bold text-blue-700 mb-1">SST</div>
                    <div class="text-sm text-gray-600">Especialización en seguridad laboral</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="text-4xl font-bold text-blue-700 mb-1">Col</div>
                    <div class="text-sm text-gray-600">Normativa colombiana vigente</div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- POR QUÉ ELEGIRNOS --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3 section-title-center">¿Por qué elegirnos?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Razones que nos diferencian y respaldan la confianza de nuestros capacitados y empresas aliadas.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'titulo' => 'Certificados verificables', 'texto' => 'Cada certificado cuenta con un código único que cualquier empresa puede validar en nuestra plataforma en segundos.', 'color' => 'amber'],
                ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'titulo' => 'Formación práctica', 'texto' => 'Programas diseñados con enfoque aplicado para que el conocimiento sea útil desde el primer día en el entorno laboral.', 'color' => 'blue'],
                ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'titulo' => 'Habilitados legalmente', 'texto' => 'Contamos con licencia vigente para prestación de servicios en SST, otorgada por la Secretaría de Salud, con cobertura en todo el territorio nacional.', 'color' => 'amber'],
                ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'titulo' => 'Atención personalizada', 'texto' => 'Acompañamos a cada capacitado durante su proceso de formación y estamos disponibles para resolver dudas.', 'color' => 'blue'],
                ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'titulo' => 'Seguridad de datos', 'texto' => 'La información de nuestros capacitados se gestiona con los más altos estándares de seguridad y privacidad.', 'color' => 'amber'],
                ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'titulo' => 'Soporte continuo', 'texto' => 'Contáctanos por correo, teléfono o WhatsApp. Respondemos a la brevedad para resolver cualquier inquietud.', 'color' => 'blue'],
            ] as $item)
            <div class="flex items-start bg-gray-50 p-6 rounded-xl border border-gray-100 hover:shadow-sm transition card-gold-hover reveal delay-{{ min(($loop->index % 3) + 1, 6) }}">
                <div class="w-11 h-11 {{ $item['color'] === 'amber' ? 'bg-amber-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $item['color'] === 'amber' ? 'text-amber-600' : 'text-blue-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $item['titulo'] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $item['texto'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- CTA --}}
<section class="py-16 bg-blue-950 text-white relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1"
         style="background: linear-gradient(90deg, transparent, #F59E0B, transparent)"></div>
    <div class="absolute inset-0 pointer-events-none"
         style="background: radial-gradient(ellipse at 80% 50%, rgba(245,158,11,0.08) 0%, transparent 60%)"></div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4">¿Listo para capacitarte?</h2>
        <p class="text-lg text-blue-200 mb-8">Revisa nuestro catálogo de cursos o contáctanos para más información.</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('catalogo') }}" class="px-8 py-4 btn-gold rounded-lg font-semibold inline-flex items-center gap-2">
                Ver cursos
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="{{ route('contacto') }}" class="px-8 py-4 border-2 border-blue-600 text-white font-semibold rounded-lg hover:bg-blue-900 transition">
                Contáctanos
            </a>
        </div>
    </div>
</section>

@endsection
