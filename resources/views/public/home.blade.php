@extends('layouts.public')

@section('titulo', 'Inicio')
@section('descripcion', 'Instituto EDCSST - Capacitación y certificación profesional en seguridad y salud en el trabajo.')

@push('preload')
<link rel="preload" as="image" href="{{ asset('img/capacitacion-grupal-docencia.jpg') }}" fetchpriority="high">
@endpush

@push('styles')
<style>#site-footer { margin-top: 0; }</style>
@endpush

@section('contenido')

{{-- ============ HERO con foto del instituto ============ --}}
<section class="relative overflow-hidden bg-marca-navy text-white">
    <div class="absolute inset-0">
        <img src="{{ asset('img/capacitacion-grupal-docencia.jpg') }}"
             alt="" aria-hidden="true"
             class="w-full h-full object-cover"
             style="object-position: 35% 35%;"
             fetchpriority="high" decoding="async">
        {{-- Móvil: velo uniforme; desktop: navy a la izquierda para el texto, foto visible a la derecha --}}
        <div class="absolute inset-0 bg-marca-navy/85 lg:bg-transparent lg:bg-gradient-to-r lg:from-marca-navy lg:via-marca-navy/85 lg:to-marca-navy/10" aria-hidden="true"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32">
        <div class="max-w-2xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.08] hero-1">
                Capacitación en seguridad y salud en el trabajo
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-blue-100 max-w-xl hero-2">
                Formamos trabajadores y empresas desde Villavicencio para todo el país. Cada certificado que emitimos se puede comprobar en línea.
            </p>
            <div class="mt-9 flex flex-col sm:flex-row sm:items-center gap-x-6 gap-y-4 hero-3">
                <a href="{{ route('consulta') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 btn-gold text-base">
                    Descargar mis certificados
                </a>
                <a href="{{ route('catalogo') }}" class="inline-flex items-center gap-1.5 font-semibold text-white underline decoration-white/40 underline-offset-[6px] hover:decoration-amber-300">
                    Ver los cursos
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ============ SOBRE NOSOTROS (resumen) ============ --}}
<section class="py-16 sm:py-20 bg-white border-b border-slate-200" aria-labelledby="titulo-sobre-nosotros">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
        <div class="lg:col-span-6 order-2 lg:order-1 reveal-left">
            <h2 id="titulo-sobre-nosotros" class="text-3xl sm:text-4xl font-bold text-slate-900">Sobre nosotros</h2>
            <p class="mt-5 text-lg text-slate-700">
                El Instituto EDCSST —Educación para el Desarrollo y la Calidad en Seguridad y Salud en el Trabajo— nació en Villavicencio para cerrar la brecha de formación especializada en SST en los Llanos Orientales.
            </p>
            <p class="mt-4 text-slate-600">
                Hoy capacitamos a trabajadores, empresas e instituciones de todo el país que necesitan cumplir la normativa colombiana, con programas prácticos y personal profesional licenciado.
            </p>

            <blockquote class="mt-7 border-l-4 border-amber-400 pl-5">
                <p class="text-slate-800 italic">
                    «Brindar formación profesional de alta calidad en seguridad y salud en el trabajo, emitiendo certificaciones verificables digitalmente que respalden el desarrollo laboral de nuestros capacitados.»
                </p>
                <footer class="mt-2 text-sm font-semibold text-slate-500">Nuestra misión</footer>
            </blockquote>

            <dl class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-px bg-slate-200 border border-slate-200 rounded-md overflow-hidden text-[15px]">
                <div class="bg-slate-50 p-4">
                    <dt class="text-slate-500">Habilitación</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900">Licencia SST vigente</dd>
                </div>
                <div class="bg-slate-50 p-4">
                    <dt class="text-slate-500">Cobertura</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900">Nacional</dd>
                </div>
                <div class="bg-slate-50 p-4">
                    <dt class="text-slate-500">Sede</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900">Villavicencio, Meta</dd>
                </div>
            </dl>

            <a href="{{ route('nosotros') }}" class="mt-8 inline-flex items-center gap-2 px-5 py-3 rounded-md border border-slate-300 font-semibold text-marca-navy hover:bg-marca-navy hover:text-white hover:border-marca-navy transition-colors">
                Conoce más sobre nosotros
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <figure class="lg:col-span-6 order-1 lg:order-2 reveal-right">
            <img src="{{ asset('img/examen-medico-ocupacional.jpg') }}" alt="Instituto EDCSST"
                 loading="lazy" decoding="async" width="900" height="675"
                 class="w-full aspect-[4/3] object-cover rounded-md">
        </figure>
    </div>
</section>

{{-- ============ CURSOS ============ --}}
<section class="py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2 pb-5 mb-8 border-b border-slate-300">
            <h2 class="text-3xl sm:text-4xl font-bold text-slate-900">Cursos</h2>
            <a href="{{ route('catalogo') }}" class="font-semibold text-blue-800 hover:text-blue-950 underline decoration-blue-200 underline-offset-4 hover:decoration-blue-800">
                Catálogo completo
            </a>
        </div>

        @if($cursosDestacados->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-10">
                @foreach($cursosDestacados as $curso)
                    <article class="group reveal delay-{{ min($loop->iteration, 6) }}">
                        <div class="aspect-[4/3] bg-slate-200 rounded-md overflow-hidden">
                            @if($curso->imagen)
                                <img src="{{ $curso->imagen_url }}" alt="" loading="lazy" decoding="async" width="640" height="480"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-end p-5 bg-marca-navy border-b-4 border-amber-400" aria-hidden="true">
                                    <span class="font-titulo text-lg font-semibold text-white leading-snug line-clamp-3">{{ $curso->nombre }}</span>
                                </div>
                            @endif
                        </div>
                        <p class="mt-4 text-sm text-slate-500">{{ $curso->categoria?->nombre ?? 'Sin categoría' }} · {{ $curso->duracion }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900 leading-snug">{{ $curso->nombre }}</h3>
                        <p class="mt-2 text-[15px] text-slate-600 line-clamp-3">{{ $curso->descripcion_corta }}</p>
                    </article>
                @endforeach
            </div>
        @else
            <p class="text-slate-600">Estamos actualizando el catálogo. <a href="{{ route('contacto') }}" class="font-semibold text-blue-800 underline underline-offset-2">Escríbenos</a> y te contamos la oferta disponible.</p>
        @endif
    </div>
</section>

{{-- ============ CÓMO SE COMPRUEBA UN CERTIFICADO (con verificador) ============ --}}
<section class="py-16 sm:py-20 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">

        <div class="lg:col-span-7">
            <h2 class="text-3xl sm:text-4xl font-bold text-slate-900">Cómo se comprueba un certificado</h2>
            <p class="mt-4 text-lg text-slate-600 max-w-xl">Cada certificado que emitimos queda registrado con un código único. No hace falta llamarnos para confirmarlo.</p>

            <ol class="mt-10 space-y-8">
                <li class="grid grid-cols-[3rem_1fr] gap-4">
                    <span class="font-titulo text-3xl font-bold text-amber-500 leading-none" aria-hidden="true">1</span>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Ubica el código</h3>
                        <p class="mt-1 text-slate-600">Está impreso en el certificado, con el formato <span class="font-mono text-[15px] bg-slate-100 border border-slate-200 rounded px-1.5 py-0.5 text-slate-900 whitespace-nowrap">EDCSST-2026-00001</span>.</p>
                    </div>
                </li>
                <li class="grid grid-cols-[3rem_1fr] gap-4">
                    <span class="font-titulo text-3xl font-bold text-amber-500 leading-none" aria-hidden="true">2</span>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Escríbelo en el verificador</h3>
                        <p class="mt-1 text-slate-600">Aquí mismo o en la <a href="{{ route('verificar') }}" class="font-semibold text-blue-800 underline underline-offset-2">página de verificación</a>. Funciona a cualquier hora, sin registro.</p>
                    </div>
                </li>
                <li class="grid grid-cols-[3rem_1fr] gap-4">
                    <span class="font-titulo text-3xl font-bold text-amber-500 leading-none" aria-hidden="true">3</span>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Revisa el resultado</h3>
                        <p class="mt-1 text-slate-600">Verás a quién se otorgó, el curso, la intensidad horaria y si sigue vigente. Los certificados tienen vigencia de un año.</p>
                    </div>
                </li>
            </ol>
        </div>

        {{-- Verificador funcional --}}
        <div class="lg:col-span-5 lg:sticky lg:top-28">
            <form method="POST" action="{{ route('verificar.verificar') }}"
                  class="bg-slate-50 rounded-lg p-6 sm:p-7 border border-slate-200 border-t-[6px] border-t-amber-400">
                @csrf
                <h3 class="text-xl font-semibold text-slate-900">¿Te presentaron un certificado EDCSST?</h3>
                <p class="mt-1.5 text-[15px] text-slate-600">Escribe el código y confirma si es auténtico y está vigente.</p>

                <label for="codigo-inicio" class="block mt-5 text-sm font-semibold text-slate-700">Código del certificado</label>
                <input type="text" id="codigo-inicio" name="codigo" required
                       autocomplete="off" autocapitalize="characters" spellcheck="false"
                       placeholder="EDCSST-2026-00001"
                       class="mt-1.5 w-full min-h-[52px] px-4 font-mono text-lg tracking-wider uppercase bg-white border border-slate-300 rounded-md focus:ring-2 focus:ring-blue-700 focus:border-blue-700">

                <button type="submit" class="mt-4 w-full min-h-[48px] px-5 bg-marca-navy text-white font-semibold rounded-md hover:bg-marca-navy-claro transition-colors">
                    Verificar
                </button>
            </form>
        </div>
    </div>
</section>

@include('public.partials.contacto-franja')

@endsection
