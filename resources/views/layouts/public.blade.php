<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0B1E4A">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
    <title>@yield('titulo', 'Instituto EDCSST') - Instituto EDCSST</title>
    <meta name="description" content="@yield('descripcion', 'Instituto EDCSST - Capacitación y certificación profesional en seguridad y salud en el trabajo.')">

    {{-- Vista previa al compartir (WhatsApp, Facebook, X, LinkedIn…) --}}
    @php
        $ogTitulo = trim($__env->yieldContent('titulo', 'Instituto EDCSST')) . ' - Instituto EDCSST';
        $ogDescripcion = trim($__env->yieldContent('descripcion', 'Instituto EDCSST - Capacitación y certificación profesional en seguridad y salud en el trabajo.'));
        $ogImagen = asset('img/og-edcsst.jpg');
    @endphp
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Instituto EDCSST">
    <meta property="og:locale" content="es_CO">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $ogTitulo }}">
    <meta property="og:description" content="{{ $ogDescripcion }}">
    <meta property="og:image" content="{{ $ogImagen }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Instituto EDCSST - Capacitación en seguridad y salud en el trabajo">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitulo }}">
    <meta name="twitter:description" content="{{ $ogDescripcion }}">
    <meta name="twitter:image" content="{{ $ogImagen }}">

    {{-- Tipografía: Lexend (títulos) + Source Sans 3 (texto). Carga no bloqueante. --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link id="fonts-publico" rel="preload" as="style" href="https://fonts.bunny.net/css?family=lexend:500,600,700|source-sans-3:400,500,600,700&display=swap">
    <noscript><link href="https://fonts.bunny.net/css?family=lexend:500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet"></noscript>
    <script nonce="{{ $cspNonce }}">
        document.getElementById('fonts-publico').addEventListener('load', function () {
            this.rel = 'stylesheet';
        }, { once: true });
    </script>

    @stack('preload')

    {{-- Tailwind compilado por Vite --}}
    <x-app-assets />

    <style>
        :root {
            --navy:        #0B1E4A;
            --navy-claro:  #16306E;
            --gold:        #D4A017;
            --gold-dark:   #8A6408;
            --gold-light:  #FEF3C7;
            --gold-soft:   #F59E0B;
            --ease-out:    cubic-bezier(0.22, 1, 0.36, 1);
        }

        html { scroll-behavior: smooth; }
        h1, h2, h3, .font-titulo { font-family: 'Lexend', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.01em; }
        h1, h2 { text-wrap: balance; }

        /* ===== FOCO VISIBLE (teclado) ===== */
        :where(a, button, input, select, textarea, summary, [tabindex]):focus-visible {
            outline: 3px solid var(--gold-soft);
            outline-offset: 2px;
            border-radius: 0.375rem;
        }

        /* ===== EYEBROW (etiqueta sobre títulos) ===== */
        .eyebrow {
            display: inline-flex; align-items: center; gap: 0.5rem;
            font-size: 0.8125rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--gold-dark);
        }
        .eyebrow::before { content: ''; width: 1.5rem; height: 2px; background: var(--gold); border-radius: 2px; }
        .eyebrow-claro { color: #FCD34D; }
        .eyebrow-claro::before { background: #FCD34D; }

        /* ===== TÍTULOS CON SUBRAYADO DORADO ===== */
        .section-title::after,
        .section-title-center::after {
            content: ''; display: block;
            width: 3.5rem; height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--gold-soft));
            border-radius: 2px; margin-top: 0.75rem;
            transform: scaleX(0); transform-origin: left;
            transition: transform 0.5s var(--ease-out) 0.2s;
        }
        .section-title-center::after { margin-left: auto; margin-right: auto; transform-origin: center; }
        .section-title.visible::after,
        .section-title-center.visible::after { transform: scaleX(1); }

        /* ===== TARJETAS ===== */
        .card-gold-hover { transition: border-color 0.15s; }
        .card-gold-hover:hover { border-color: #94A3B8; }

        /* ===== BOTÓN DORADO (texto navy: contraste ≥ 6.8:1) ===== */
        .btn-gold {
            background: linear-gradient(135deg, #FBBF24, var(--gold-soft));
            color: var(--navy); font-weight: 600; border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(212,160,23,0.35);
            transition: background-color 0.2s, box-shadow 0.2s, transform 0.15s;
        }
        .btn-gold:hover { box-shadow: 0 6px 18px rgba(212,160,23,0.45); background: linear-gradient(135deg, #FCD34D, #FBBF24); }
        .btn-gold:active { transform: scale(0.98); }

        /* ===== BADGE DORADO (estático) ===== */
        .badge-gold {
            display: inline-block;
            background: var(--gold-light); color: var(--gold-dark);
            border: 1px solid rgba(212,160,23,0.4);
            font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
            font-size: 0.75rem; padding: 0.3rem 0.85rem; border-radius: 9999px;
        }
        .badge-gold-oscuro { background: rgba(252,211,77,0.12); color: #FCD34D; border-color: rgba(252,211,77,0.35); }

        /* ===== ÍCONO DORADO ===== */
        .icon-gold { background: var(--gold-light); color: var(--gold-dark); }

        /* ===== SCROLL REVEAL ===== */
        .reveal, .reveal-left, .reveal-right {
            opacity: 0;
            transition: opacity 0.5s var(--ease-out), transform 0.5s var(--ease-out);
        }
        .reveal       { transform: translateY(16px); }
        .reveal-left  { transform: translateX(-16px); }
        .reveal-right { transform: translateX(16px); }
        .reveal.visible, .reveal-left.visible, .reveal-right.visible { opacity: 1; transform: none; }
        .delay-1 { transition-delay: 0.04s; }
        .delay-2 { transition-delay: 0.08s; }
        .delay-3 { transition-delay: 0.12s; }
        .delay-4 { transition-delay: 0.16s; }
        .delay-5 { transition-delay: 0.20s; }
        .delay-6 { transition-delay: 0.24s; }

        /* ===== ENTRADA DEL HERO ===== */
        @keyframes heroUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .hero-1 { animation: heroUp 0.6s var(--ease-out) 0.05s both; }
        .hero-2 { animation: heroUp 0.6s var(--ease-out) 0.15s both; }
        .hero-3 { animation: heroUp 0.6s var(--ease-out) 0.25s both; }
        .hero-4 { animation: heroUp 0.6s var(--ease-out) 0.35s both; }

        /* ===== WHATSAPP: pulso limitado (3 ciclos) ===== */
        @keyframes wa-pulse {
            0%, 100% { box-shadow: 0 4px 14px rgba(22,163,74,0.45), 0 0 0 0 rgba(22,163,74,0.4); }
            60%      { box-shadow: 0 4px 14px rgba(22,163,74,0.45), 0 0 0 12px rgba(22,163,74,0); }
        }
        .wa-pulse { animation: wa-pulse 2.4s ease-in-out 1s 3; }

        [x-cloak] { display: none !important; }

        /* ===== MOVIMIENTO REDUCIDO ===== */
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                transition-delay: 0s !important;
            }
            .reveal, .reveal-left, .reveal-right { opacity: 1; transform: none; }
            .section-title::after, .section-title-center::after { transform: scaleX(1); }
            .card-gold-hover:hover { transform: none; }
        }
    </style>

    {{-- Estilos adicionales por página --}}
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 font-body text-[17px] leading-relaxed antialiased">

    <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:px-4 focus:py-2 focus:bg-white focus:text-marca-navy focus:font-semibold focus:rounded-md focus:shadow-lg">
        Saltar al contenido
    </a>

    @php
        $navItems = [
            // Íconos de apoyo (decorativos): el texto siempre es visible
            ['route' => 'home',      'label' => 'Inicio',
             'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'nosotros',  'label' => 'Nosotros',
             'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['route' => 'catalogo',  'label' => 'Cursos',
             'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 15c0 3.314-2.686 6-6 6s-6-2.686-6-6a12.083 12.083 0 01-.16-4.422L12 14z'],
            ['route' => 'consulta',  'label' => 'Mis certificados',
             'icon' => 'M12 10v6m0 0l-3-3m3 3l3-3M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
            ['route' => 'verificar', 'label' => 'Verificar',
             'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ['route' => 'contacto',  'label' => 'Contacto',
             'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ];
    @endphp

    {{-- ============ NAVBAR PÚBLICO ============ --}}
    <header x-data="{ abierto: false }" @keydown.escape.window="abierto = false" class="bg-marca-navy sticky top-0 z-40 shadow-lg">
        <div class="h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400" aria-hidden="true"></div>
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Navegación principal">
            <div class="flex justify-between items-center h-16 lg:h-[72px]">

                {{-- Logo / Nombre del instituto --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                    <span class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1 shadow-md">
                        <x-application-logo class="w-full h-full" />
                    </span>
                    <span class="leading-tight">
                        <span class="block text-white font-titulo font-semibold text-base">Instituto EDCSST</span>
                        <span class="hidden sm:block text-amber-300 text-xs font-medium tracking-wide">Certificación verificable</span>
                    </span>
                </a>

                {{-- Menú desktop: texto siempre visible; ícono de apoyo desde xl (1280px) --}}
                <div class="hidden lg:flex items-center gap-0.5 xl:gap-1">
                    @foreach($navItems as $item)
                        @php $activo = request()->routeIs($item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                           @if($activo) aria-current="page" @endif
                           class="group relative inline-flex items-center gap-2 px-2.5 xl:px-3 py-2 rounded-md text-[15px] font-medium transition-colors duration-150 {{ $activo ? 'text-white' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                            <svg class="hidden xl:block w-[18px] h-[18px] shrink-0 transition-colors {{ $activo ? 'text-amber-300' : 'text-blue-300 group-hover:text-amber-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                            {{ $item['label'] }}
                            @if($activo)
                                <span class="absolute left-3 right-3 -bottom-0.5 h-0.5 rounded-full bg-amber-400" aria-hidden="true"></span>
                            @endif
                        </a>
                    @endforeach

                    <span class="w-px h-6 bg-white/15 mx-2" aria-hidden="true"></span>

                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm btn-gold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold border border-amber-400/70 text-amber-300 rounded-lg hover:bg-amber-400 hover:text-marca-navy transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Acceso
                        </a>
                    @endauth
                </div>

                {{-- Botón menú móvil --}}
                <button type="button" @click="abierto = !abierto"
                        :aria-expanded="abierto.toString()" aria-controls="menu-movil" :aria-label="abierto ? 'Cerrar menú' : 'Abrir menú'" aria-label="Abrir menú"
                        class="lg:hidden -mr-2 p-2.5 text-blue-100 hover:text-white hover:bg-white/10 rounded-md transition-colors">
                    <svg x-show="!abierto" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="abierto" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Menú móvil --}}
            <div id="menu-movil" x-show="abierto" x-cloak
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="lg:hidden pb-4 pt-2 border-t border-white/10">
                <ul class="space-y-1">
                    @foreach($navItems as $item)
                        @php $activo = request()->routeIs($item['route']); @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @if($activo) aria-current="page" @endif
                               class="flex items-center gap-3 px-4 py-3 rounded-md text-base font-medium transition-colors {{ $activo ? 'bg-white/10 text-white border-l-4 border-amber-400' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                                <svg class="w-5 h-5 shrink-0 {{ $activo ? 'text-amber-300' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                <span class="flex-1">{{ $item['label'] }}</span>
                                <svg class="w-4 h-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="pt-3 mt-3 px-4 border-t border-white/10">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="block btn-gold text-center py-3 text-base">Panel administrativo</a>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 py-3 border border-amber-400/70 text-amber-300 rounded-lg text-base font-semibold hover:bg-amber-400 hover:text-marca-navy transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Acceso administrativo
                        </a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    {{-- ============ CONTENIDO ============ --}}
    <main id="contenido" tabindex="-1" class="min-h-[60vh] focus:outline-none">
        {{-- Mensajes flash --}}
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6" role="status">
                <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-900 p-4 rounded-lg">
                    <svg class="w-5 h-5 mt-0.5 shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6" role="alert">
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-900 p-4 rounded-lg">
                    <svg class="w-5 h-5 mt-0.5 shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('contenido')
    </main>

    {{-- ============ FOOTER ============ --}}
    <footer id="site-footer" class="bg-marca-navy text-white mt-20">
        <div class="h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-400" aria-hidden="true"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10">

                {{-- Sobre el instituto --}}
                <div class="lg:col-span-5">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-11 h-11 bg-white rounded-lg flex items-center justify-center p-1 shadow-md">
                            <x-application-logo class="w-full h-full" />
                        </span>
                        <span class="font-titulo font-semibold text-lg">Instituto EDCSST</span>
                    </div>
                    <p class="text-blue-200 text-[15px] leading-relaxed max-w-sm">
                        Educación para el Desarrollo y la Calidad en Seguridad y Salud en el Trabajo.
                        Formación profesional con certificación verificable en línea.
                    </p>
                    <a href="{{ route('verificar') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-amber-300 hover:text-amber-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Verificar un certificado
                    </a>
                </div>

                {{-- Enlaces rápidos --}}
                <div class="lg:col-span-3">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-amber-300 mb-4">Enlaces</h2>
                    <ul class="space-y-2.5 text-[15px] text-blue-200">
                        <li><a href="{{ route('home') }}"      class="hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="{{ route('nosotros') }}"  class="hover:text-white transition-colors">Sobre nosotros</a></li>
                        <li><a href="{{ route('catalogo') }}"  class="hover:text-white transition-colors">Catálogo de cursos</a></li>
                        <li><a href="{{ route('consulta') }}"  class="hover:text-white transition-colors">Consultar mis certificados</a></li>
                        <li><a href="{{ route('verificar') }}" class="hover:text-white transition-colors">Verificar certificado</a></li>
                        <li><a href="{{ route('contacto') }}"  class="hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>

                {{-- Contacto y redes --}}
                <div class="lg:col-span-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-amber-300 mb-4">Contacto</h2>
                    <ul class="space-y-3 text-[15px] text-blue-200">
                        @if($configSitio->direccion)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $configSitio->direccion }}
                        </li>
                        @endif
                        @if($configSitio->telefono)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $configSitio->telefono) }}" class="hover:text-white transition-colors">{{ $configSitio->telefono }}</a>
                        </li>
                        @endif
                        @if($configSitio->correo_contacto)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:{{ $configSitio->correo_contacto }}" class="hover:text-white transition-colors [overflow-wrap:anywhere]">{{ $configSitio->correo_contacto }}</a>
                        </li>
                        @endif
                    </ul>

                    <div class="flex gap-3 mt-6">
                        @if($configSitio->facebook)
                        <a href="{{ $configSitio->facebook }}" target="_blank" rel="noopener" class="w-11 h-11 bg-white/5 hover:bg-amber-400 hover:text-marca-navy border border-white/15 hover:border-amber-400 rounded-full flex items-center justify-center transition-colors duration-200" aria-label="Facebook (abre en una pestaña nueva)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if($configSitio->instagram)
                        <a href="{{ $configSitio->instagram }}" target="_blank" rel="noopener" class="w-11 h-11 bg-white/5 hover:bg-amber-400 hover:text-marca-navy border border-white/15 hover:border-amber-400 rounded-full flex items-center justify-center transition-colors duration-200" aria-label="Instagram (abre en una pestaña nueva)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        @endif
                        @if($configSitio->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $configSitio->whatsapp) }}" target="_blank" rel="noopener" class="w-11 h-11 bg-white/5 hover:bg-green-500 border border-white/15 hover:border-green-500 rounded-full flex items-center justify-center transition-colors duration-200" aria-label="WhatsApp (abre en una pestaña nueva)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between text-sm text-blue-300 gap-2">
                <p>&copy; {{ date('Y') }} Instituto EDCSST. Todos los derechos reservados.</p>
                <p>Villavicencio, Meta · Colombia</p>
            </div>
        </div>
    </footer>

    {{-- Botón flotante de WhatsApp --}}
    @if($configSitio->whatsapp)
    <a href="https://wa.me/{{ preg_replace('/\D/', '', $configSitio->whatsapp) }}" target="_blank" rel="noopener"
       class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 bg-green-600 hover:bg-green-700 text-white p-4 rounded-full shadow-lg transition-colors z-30 wa-pulse"
       aria-label="Contactar por WhatsApp (abre en una pestaña nueva)">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>
    @endif

    {{-- Scripts --}}
    <script nonce="{{ $cspNonce }}">
        const _sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // ===== Scroll Reveal =====
        const _revelables = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .section-title, .section-title-center');
        if (_sinMovimiento || !('IntersectionObserver' in window)) {
            _revelables.forEach(el => el.classList.add('visible'));
        } else {
            const _revealObs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) { e.target.classList.add('visible'); _revealObs.unobserve(e.target); }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            _revelables.forEach(el => _revealObs.observe(el));
        }

        // ===== Contadores animados =====
        function _animCounter(el) {
            const target = parseFloat(el.dataset.target);
            const suffix = el.dataset.suffix || '';
            if (_sinMovimiento) { el.textContent = target + suffix; return; }
            const t0 = performance.now(), dur = 1200;
            (function tick(now) {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))) + suffix;
                if (p < 1) requestAnimationFrame(tick);
            })(performance.now());
        }
        const _counterObs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting && !e.target.dataset.counted) {
                    e.target.dataset.counted = '1';
                    _animCounter(e.target);
                }
            });
        }, { threshold: 0.6 });
        document.querySelectorAll('[data-target]').forEach(el => _counterObs.observe(el));
    </script>

    @stack('scripts')
</body>
</html>
