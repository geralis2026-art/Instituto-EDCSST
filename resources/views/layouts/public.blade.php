<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
    <title>@yield('titulo', 'Instituto EDCSST') - Instituto EDCSST</title>
    <meta name="description" content="@yield('descripcion', 'Instituto EDCSST - Capacitación y certificación profesional en seguridad y salud en el trabajo.')">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link id="fonts-figtree" rel="preload" as="style" href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap">
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"></noscript>
    <script nonce="{{ $cspNonce }}">
        document.getElementById('fonts-figtree').addEventListener('load', function () {
            this.rel = 'stylesheet';
        }, { once: true });
    </script>

    @stack('preload')

    {{-- Tailwind compilado por Vite --}}
    <x-app-assets />

    <style>
        :root {
            --gold:       #D4A017;
            --gold-dark:  #A87C0D;
            --gold-light: #FEF3C7;
            --gold-soft:  #F59E0B;
        }

        /* ===== TITLES ===== */
        .section-title::after {
            content: ''; display: block;
            width: 0; height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--gold-soft));
            border-radius: 2px; margin-top: 0.6rem;
            transition: width 0.55s ease 0.25s;
        }
        .section-title.visible::after { width: 4rem; }

        .section-title-center::after {
            content: ''; display: block;
            width: 0; height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--gold-soft));
            border-radius: 2px; margin: 0.6rem auto 0;
            transition: width 0.55s ease 0.25s;
        }
        .section-title-center.visible::after { width: 4rem; }

        /* ===== CARD HOVER ===== */
        .card-gold-hover {
            border-top: 3px solid transparent;
            transition: border-color 0.25s, box-shadow 0.25s, transform 0.28s cubic-bezier(0.22,1,0.36,1);
        }
        .card-gold-hover:hover {
            border-top-color: var(--gold-soft);
            box-shadow: 0 14px 36px rgba(212,160,23,0.16);
            transform: translateY(-6px);
        }

        /* ===== BTN GOLD (shimmer on hover) ===== */
        .btn-gold {
            position: relative; overflow: hidden;
            background: linear-gradient(135deg, var(--gold-soft), var(--gold));
            color: #fff; font-weight: 600; border-radius: 0.5rem;
            transition: opacity 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(212,160,23,0.35);
        }
        .btn-gold:hover { opacity: 0.92; box-shadow: 0 4px 18px rgba(212,160,23,0.52); }
        .btn-gold::after {
            content: ''; position: absolute;
            top: 0; left: -80%; width: 55%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: skewX(-20deg); transition: left 0.55s ease; pointer-events: none;
        }
        .btn-gold:hover::after { left: 130%; }

        /* ===== BADGE GOLD (shine animation) ===== */
        @keyframes badge-shine {
            0%   { background-position: 200% center; }
            100% { background-position: -200% center; }
        }
        .badge-gold {
            background: linear-gradient(90deg, var(--gold-soft) 0%, var(--gold) 35%, #FCD34D 50%, var(--gold) 65%, var(--gold-soft) 100%);
            background-size: 200% auto;
            animation: badge-shine 3.5s linear infinite;
            color: #fff; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; font-size: 0.7rem;
            padding: 0.3rem 0.85rem; border-radius: 9999px;
            box-shadow: 0 2px 6px rgba(212,160,23,0.4);
        }

        /* ===== ICON GOLD ===== */
        .icon-gold { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: var(--gold); }

        /* ===== FOOTER ===== */
        footer a:hover { color: var(--gold-soft) !important; }

        /* ===== SCROLL REVEAL ===== */
        .reveal        { opacity: 0; transform: translateY(28px); transition: opacity 0.65s cubic-bezier(0.22,1,0.36,1), transform 0.65s cubic-bezier(0.22,1,0.36,1); }
        .reveal-left   { opacity: 0; transform: translateX(-28px); transition: opacity 0.65s cubic-bezier(0.22,1,0.36,1), transform 0.65s cubic-bezier(0.22,1,0.36,1); }
        .reveal-right  { opacity: 0; transform: translateX(28px); transition: opacity 0.65s cubic-bezier(0.22,1,0.36,1), transform 0.65s cubic-bezier(0.22,1,0.36,1); }
        .reveal.visible, .reveal-left.visible, .reveal-right.visible { opacity: 1; transform: translate(0,0); }
        .delay-1 { transition-delay: 0.10s; }
        .delay-2 { transition-delay: 0.20s; }
        .delay-3 { transition-delay: 0.30s; }
        .delay-4 { transition-delay: 0.40s; }
        .delay-5 { transition-delay: 0.50s; }
        .delay-6 { transition-delay: 0.60s; }

        /* ===== HERO ENTRY ANIMATIONS (no JS needed) ===== */
        @keyframes heroUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes heroRight {
            from { opacity: 0; transform: translateX(32px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .hero-1 { animation: heroUp    0.7s ease-out 0.05s both; }
        .hero-2 { animation: heroUp    0.7s ease-out 0.18s both; }
        .hero-3 { animation: heroUp    0.7s ease-out 0.30s both; }
        .hero-4 { animation: heroUp    0.7s ease-out 0.44s both; }
        .hero-card { animation: heroRight 0.75s ease-out 0.35s both; }

        /* ===== FLOAT (hero decorative circles) ===== */
        @keyframes float1 {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-16px) scale(1.04); }
        }
        @keyframes float2 {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-10px) scale(1.02); }
        }
        .float-a { animation: float1  7s ease-in-out infinite; }
        .float-b { animation: float2  9s ease-in-out infinite 2s; }
        .float-c { animation: float1 11s ease-in-out infinite 1s; }

        /* Alpine x-cloak */
        [x-cloak] { display: none !important; }

        /* ===== WHATSAPP PULSE ===== */
        @keyframes wa-pulse {
            0%, 100% { box-shadow: 0 4px 14px rgba(22,163,74,0.5), 0 0 0 0 rgba(22,163,74,0.45); }
            60%       { box-shadow: 0 4px 14px rgba(22,163,74,0.5), 0 0 0 14px rgba(22,163,74,0); }
        }
        .wa-pulse { animation: wa-pulse 2.4s ease-in-out infinite; }
    </style>

    {{-- Estilos adicionales por página --}}
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    {{-- ============ NAVBAR PÚBLICO ============ --}}
    <nav class="bg-blue-950 sticky top-0 z-40 shadow-xl border-b-4 border-amber-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo / Nombre del instituto --}}
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1 shadow-md">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <div class="hidden sm:block leading-tight">
                            <div class="text-white font-bold text-base">Instituto EDCSST</div>
                            <div class="text-amber-400 text-xs font-medium tracking-wide">Certificación verificable</div>
                        </div>
                    </a>
                </div>

                {{-- Menú desktop --}}
                <div class="hidden md:flex items-center space-x-1">
                    @foreach([
                        ['route' => 'home',      'label' => 'Inicio'],
                        ['route' => 'nosotros',  'label' => 'Nosotros'],
                        ['route' => 'catalogo',  'label' => 'Cursos'],
                        ['route' => 'consulta',  'label' => 'Consultar Certificado'],
                        ['route' => 'verificar', 'label' => 'Verificar'],
                        ['route' => 'contacto',  'label' => 'Contacto'],
                    ] as $item)
                        <a href="{{ route($item['route']) }}"
                           class="px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs($item['route']) ? 'text-amber-400 border-b-2 border-amber-400 bg-blue-900' : 'text-blue-100 hover:text-amber-300 hover:bg-blue-900' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="ml-3 px-4 py-2 text-sm font-semibold btn-gold rounded-md">
                            Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="ml-3 px-4 py-2 text-sm font-semibold border-2 border-amber-500 text-amber-400 rounded-md hover:bg-amber-500 hover:text-white transition-all duration-200">
                            Acceso
                        </a>
                    @endauth
                </div>

                {{-- Botón menú móvil --}}
                <button id="mobile-menu-btn" class="md:hidden p-2 text-blue-200 hover:bg-blue-900 rounded-md transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            {{-- Menú móvil --}}
            <div id="mobile-menu" class="hidden md:hidden pb-4 space-y-1 border-t border-blue-800 pt-3 mt-1">
                <a href="{{ route('home') }}"     class="block px-4 py-2 text-sm font-medium text-blue-100 hover:bg-blue-900 hover:text-amber-300 rounded-md transition">Inicio</a>
                <a href="{{ route('nosotros') }}" class="block px-4 py-2 text-sm font-medium text-blue-100 hover:bg-blue-900 hover:text-amber-300 rounded-md transition">Nosotros</a>
                <a href="{{ route('catalogo') }}" class="block px-4 py-2 text-sm font-medium text-blue-100 hover:bg-blue-900 hover:text-amber-300 rounded-md transition">Cursos</a>
                <a href="{{ route('consulta') }}" class="block px-4 py-2 text-sm font-medium text-blue-100 hover:bg-blue-900 hover:text-amber-300 rounded-md transition">Consultar Certificado</a>
                <a href="{{ route('verificar') }}" class="block px-4 py-2 text-sm font-medium text-blue-100 hover:bg-blue-900 hover:text-amber-300 rounded-md transition">Verificar</a>
                <a href="{{ route('contacto') }}" class="block px-4 py-2 text-sm font-medium text-blue-100 hover:bg-blue-900 hover:text-amber-300 rounded-md transition">Contacto</a>
                <div class="pt-2 px-4">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="block btn-gold text-center py-2 rounded-md text-sm font-semibold">Panel administrativo</a>
                    @else
                        <a href="{{ route('login') }}" class="block text-center py-2 border-2 border-amber-500 text-amber-400 rounded-md text-sm font-semibold hover:bg-amber-500 hover:text-white transition">Acceso administrativo</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ============ CONTENIDO ============ --}}
    <main class="min-h-screen">
        {{-- Mensajes flash --}}
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-md shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-md shadow-sm">
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('contenido')
    </main>

    {{-- ============ FOOTER ============ --}}
    <footer id="site-footer" class="bg-blue-950 text-white mt-16 border-t-4 border-amber-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Sobre el instituto --}}
                <div>
                    <div class="flex items-center space-x-3 mb-5">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1 shadow-md">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <span class="text-amber-400 font-bold text-lg">Instituto EDCSST</span>
                    </div>
                    <p class="text-blue-200 text-sm leading-relaxed">
                        Educación para el Desarrollo y la Calidad en Seguridad y Salud en el Trabajo.
                        Formación profesional con certificación verificable.
                    </p>
                </div>

                {{-- Enlaces rápidos --}}
                <div>
                    <h3 class="text-amber-400 font-bold text-base mb-4 uppercase tracking-wider">
                        <span class="border-b-2 border-amber-500 pb-1">Enlaces rápidos</span>
                    </h3>
                    <ul class="space-y-2 text-sm text-blue-200">
                        <li><a href="{{ route('home') }}"     class="hover:text-amber-400 transition flex items-center gap-1"><span class="text-amber-600">›</span> Inicio</a></li>
                        <li><a href="{{ route('nosotros') }}" class="hover:text-amber-400 transition flex items-center gap-1"><span class="text-amber-600">›</span> Sobre nosotros</a></li>
                        <li><a href="{{ route('catalogo') }}" class="hover:text-amber-400 transition flex items-center gap-1"><span class="text-amber-600">›</span> Catálogo de cursos</a></li>
                        <li><a href="{{ route('consulta') }}" class="hover:text-amber-400 transition flex items-center gap-1"><span class="text-amber-600">›</span> Consultar certificado</a></li>
                        <li><a href="{{ route('verificar') }}" class="hover:text-amber-400 transition flex items-center gap-1"><span class="text-amber-600">›</span> Verificar certificado</a></li>
                        <li><a href="{{ route('contacto') }}" class="hover:text-amber-400 transition flex items-center gap-1"><span class="text-amber-600">›</span> Contacto</a></li>
                    </ul>
                </div>

                {{-- Contacto y redes --}}
                <div>
                    <h3 class="text-amber-400 font-bold text-base mb-4 uppercase tracking-wider">
                        <span class="border-b-2 border-amber-500 pb-1">Contacto</span>
                    </h3>
                    <ul class="space-y-3 text-sm text-blue-200">
                        @if($configSitio->direccion)
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $configSitio->direccion }}
                        </li>
                        @endif
                        @if($configSitio->telefono)
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $configSitio->telefono }}
                        </li>
                        @endif
                        @if($configSitio->correo_contacto)
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $configSitio->correo_contacto }}
                        </li>
                        @endif
                    </ul>

                    <div class="flex space-x-3 mt-5">
                        @if($configSitio->facebook)
                        <a href="{{ $configSitio->facebook }}" target="_blank" rel="noopener" class="w-9 h-9 bg-blue-900 hover:bg-amber-500 border border-blue-700 hover:border-amber-500 rounded-full flex items-center justify-center transition-all duration-200" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if($configSitio->instagram)
                        <a href="{{ $configSitio->instagram }}" target="_blank" rel="noopener" class="w-9 h-9 bg-blue-900 hover:bg-amber-500 border border-blue-700 hover:border-amber-500 rounded-full flex items-center justify-center transition-all duration-200" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        @endif
                        @if($configSitio->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $configSitio->whatsapp) }}" target="_blank" rel="noopener" class="w-9 h-9 bg-green-700 hover:bg-green-500 rounded-full flex items-center justify-center transition-all duration-200" aria-label="WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-blue-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between text-sm text-blue-400 gap-2">
                <p>&copy; {{ date('Y') }} Instituto EDCSST. Todos los derechos reservados.</p>
                <p class="text-amber-600 font-medium">Villavicencio, Meta - Colombia</p>
            </div>
        </div>
    </footer>

    {{-- Botón flotante de WhatsApp --}}
    @if($configSitio->whatsapp)
    <a href="https://wa.me/{{ preg_replace('/\D/', '', $configSitio->whatsapp) }}" target="_blank" rel="noopener" class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition z-50 wa-pulse" aria-label="Contactar por WhatsApp">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </a>
    @endif

    {{-- Scripts --}}
    <script nonce="{{ $cspNonce }}">
        // Toggle menú móvil
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // ===== Scroll Reveal =====
        const _revealObs = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => _revealObs.observe(el));

        // ===== Counter Animation =====
        function _animCounter(el) {
            const target = parseFloat(el.dataset.target);
            const suffix = el.dataset.suffix || '';
            const t0 = performance.now(), dur = 1800;
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
