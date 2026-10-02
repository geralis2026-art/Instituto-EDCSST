<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0B1E4A">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
    <title>{{ $attributes->get('titulo', 'Acceso') }} — Instituto EDCSST</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet">

    <x-app-assets />
    <style>
        h1, h2, h3 { font-family: 'Lexend', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.01em; }
        :where(a, button, input, select, textarea):focus-visible { outline: 3px solid #F59E0B; outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) { * { animation: none !important; transition-duration: .01ms !important; } }
    </style>
</head>
<body class="font-body text-[16px] text-slate-800 antialiased bg-slate-50">

    <a href="#formulario-acceso" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:font-semibold focus:rounded-md focus:shadow-lg">Saltar al formulario</a>

    <div class="relative min-h-screen lg:grid lg:grid-cols-2">

        {{-- Fondo de marca (solo desktop): la foto se disuelve hacia el fondo del formulario en vez de cortarse.
             El azul oscuro solo protege el texto (izquierda y esquinas); el resto de la foto se ve completa. --}}
        <div class="hidden lg:block absolute inset-y-0 left-0 w-[62%] bg-marca-navy overflow-hidden" aria-hidden="true"
             style="-webkit-mask-image: linear-gradient(to right, #000 68%, transparent 100%); mask-image: linear-gradient(to right, #000 68%, transparent 100%);">
            <img src="{{ asset('img/capacitacion-grupal-docencia.jpg') }}" alt="" width="1280" height="680" decoding="async"
                 class="absolute inset-0 w-full h-full object-cover" style="object-position: 40% center;">
            <div class="absolute inset-0 bg-gradient-to-r from-marca-navy/90 via-marca-navy/45 to-transparent"></div>
            <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-marca-navy/80 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-marca-navy/80 to-transparent"></div>
        </div>

        {{-- Texto de marca (solo desktop) --}}
        <aside class="hidden lg:flex relative flex-col justify-between text-white p-12" aria-label="Instituto EDCSST">
            <a href="{{ url('/') }}" class="relative flex items-center gap-4">
                <span class="w-14 h-14 bg-white rounded-xl flex items-center justify-center p-1.5 shrink-0">
                    <img src="{{ asset('img/logo-edcsst.png') }}" alt="" aria-hidden="true" class="w-full h-full object-contain">
                </span>
                <span class="font-titulo font-semibold text-xl leading-tight">Instituto EDCSST</span>
            </a>

            <div class="relative max-w-xs xl:max-w-md">
                <h2 class="text-3xl font-bold leading-tight">Panel administrativo</h2>
                <p class="mt-4 text-lg text-blue-100">Gestión de capacitados, certificados y cursos. Acceso solo para personal autorizado.</p>
            </div>

            <a href="{{ url('/') }}" class="relative inline-flex items-center gap-2 text-blue-100 hover:text-white font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al sitio público
            </a>
        </aside>

        {{-- Formulario --}}
        <main id="formulario-acceso" class="relative flex items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">

                {{-- Marca en móvil --}}
                <a href="{{ url('/') }}" class="lg:hidden flex items-center justify-center gap-3 mb-8">
                    <span class="w-12 h-12 bg-white rounded-xl border border-slate-200 flex items-center justify-center p-1.5">
                        <img src="{{ asset('img/logo-edcsst.png') }}" alt="" aria-hidden="true" class="w-full h-full object-contain">
                    </span>
                    <span class="font-titulo font-semibold text-lg text-marca-navy">Instituto EDCSST</span>
                </a>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-7 sm:p-9 border-t-[6px] border-t-amber-400">
                    <h1 class="text-2xl font-bold text-slate-900">{{ $attributes->get('titulo', 'Iniciar sesión') }}</h1>
                    @if($attributes->get('subtitulo', 'Ingresa tus credenciales para acceder al panel'))
                        <p class="mt-1.5 text-slate-600">{{ $attributes->get('subtitulo', 'Ingresa tus credenciales para acceder al panel') }}</p>
                    @endif

                    <div class="mt-7">
                        {{ $slot }}
                    </div>
                </div>

                <p class="mt-6 text-center text-sm text-slate-500 lg:hidden">
                    <a href="{{ url('/') }}" class="hover:text-slate-800 underline underline-offset-2">Volver al sitio público</a>
                </p>
            </div>
        </main>
    </div>
</body>
</html>
