<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
    <title>@yield('titulo', 'Panel') - Admin Instituto EDCSST</title>

    <x-app-assets />
    <style>
        :root { --gold: #D4A017; --gold-soft: #F59E0B; }

        /* ===== BTN GOLD (shimmer) ===== */
        .btn-gold {
            position: relative; overflow: hidden;
            background: linear-gradient(135deg, var(--gold-soft), var(--gold));
            color: #fff; font-weight: 600; border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(212,160,23,0.35);
            transition: opacity 0.2s, box-shadow 0.2s;
        }
        .btn-gold:hover { opacity: 0.9; box-shadow: 0 4px 14px rgba(212,160,23,0.5); }
        .btn-gold::after {
            content: ''; position: absolute;
            top: 0; left: -80%; width: 55%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.28), transparent);
            transform: skewX(-20deg); transition: left 0.55s ease; pointer-events: none;
        }
        .btn-gold:hover::after { left: 130%; }

        /* ===== BADGE GOLD (shine) ===== */
        @keyframes badge-shine {
            0%   { background-position: 200% center; }
            100% { background-position: -200% center; }
        }
        .badge-gold {
            display: inline-block;
            background: linear-gradient(90deg, var(--gold-soft) 0%, var(--gold) 35%, #FCD34D 50%, var(--gold) 65%, var(--gold-soft) 100%);
            background-size: 200% auto;
            animation: badge-shine 3.5s linear infinite;
            color: #fff; font-weight: 700; letter-spacing: 0.06em;
            text-transform: uppercase; font-size: 0.68rem;
            padding: 0.25rem 0.75rem; border-radius: 9999px;
            box-shadow: 0 2px 6px rgba(212,160,23,0.4);
        }

        /* ===== NAV ===== */
        .nav-link-active {
            background-color: rgba(255,255,255,0.08);
            border-left: 4px solid #F59E0B;
            padding-left: 0.75rem; color: #FDE68A; font-weight: 600;
        }
        .nav-link-inactive { border-left: 4px solid transparent; padding-left: 0.75rem; }
        .nav-link-inactive:hover { background-color: rgba(255,255,255,0.06); color: #FDE68A; }
        aside nav a svg { transition: transform 0.2s; }
        aside nav a:hover svg { transform: scale(1.15); }

        /* ===== SCROLL REVEAL ===== */
        .reveal      { opacity: 0; transform: translateY(22px); transition: opacity 0.6s cubic-bezier(0.22,1,0.36,1), transform 0.6s cubic-bezier(0.22,1,0.36,1); }
        .reveal-left { opacity: 0; transform: translateX(-22px); transition: opacity 0.6s cubic-bezier(0.22,1,0.36,1), transform 0.6s cubic-bezier(0.22,1,0.36,1); }
        .reveal.visible, .reveal-left.visible { opacity: 1; transform: translate(0,0); }
        .delay-1 { transition-delay: 0.08s; }
        .delay-2 { transition-delay: 0.16s; }
        .delay-3 { transition-delay: 0.24s; }
        .delay-4 { transition-delay: 0.32s; }

        /* ===== CARD HOVER ===== */
        .card-hover { transition: box-shadow 0.25s, transform 0.25s cubic-bezier(0.22,1,0.36,1); }
        .card-hover:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.09); transform: translateY(-3px); }

        /* ===== FLASH MESSAGES SLIDE IN ===== */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .flash-msg { animation: slideDown 0.35s ease-out both; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <div class="min-h-screen flex">

        {{-- ============ SIDEBAR ============ --}}
        <aside id="sidebar" class="bg-blue-950 text-white w-64 min-h-screen fixed lg:sticky top-0 left-0 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 border-r-4 border-amber-500">

            {{-- Logo --}}
            <div class="p-5 border-b border-blue-900">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1 shadow">
                        <x-application-logo class="w-full h-full" />
                    </div>
                    <div>
                        <div class="font-bold text-sm text-white">EDCSST Admin</div>
                        <div class="text-xs text-amber-400">Panel administrativo</div>
                    </div>
                </a>
            </div>

            {{-- Menú lateral --}}
            <nav class="p-3 space-y-0.5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center pr-4 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'nav-link-inactive text-blue-100' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.capacitados.index') }}" class="flex items-center pr-4 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('admin.capacitados.*') ? 'nav-link-active' : 'nav-link-inactive text-blue-100' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Capacitados
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.cursos.index') }}" class="flex items-center pr-4 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('admin.cursos.*') ? 'nav-link-active' : 'nav-link-inactive text-blue-100' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Cursos
                    </a>

                    <a href="{{ route('admin.categorias.index') }}" class="flex items-center pr-4 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('admin.categorias.*') ? 'nav-link-active' : 'nav-link-inactive text-blue-100' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Categorías
                    </a>
                @endif

                <a href="{{ route('admin.certificados.index') }}" class="flex items-center pr-4 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('admin.certificados.*') ? 'nav-link-active' : 'nav-link-inactive text-blue-100' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Certificados
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.mensajes.index') }}" class="flex items-center pr-4 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('admin.mensajes.*') ? 'nav-link-active' : 'nav-link-inactive text-blue-100' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Mensajes</span>
                        @php $mensajesNuevos = \App\Models\Mensaje::nuevos()->count(); @endphp
                        @if($mensajesNuevos > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $mensajesNuevos }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.usuarios.index') }}" class="flex items-center pr-4 py-2.5 rounded-lg transition text-sm {{ request()->routeIs('admin.usuarios.*') ? 'nav-link-active' : 'nav-link-inactive text-blue-100' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Usuarios
                    </a>
                @endif

                <div class="border-t border-blue-900 my-3"></div>

                <a href="{{ route('home') }}" target="_blank" class="flex items-center pr-4 py-2.5 rounded-lg transition text-sm nav-link-inactive text-blue-200 hover:text-amber-300">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Ver sitio público
                </a>
            </nav>
        </aside>

        {{-- Overlay para móvil --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden"></div>

        {{-- ============ ÁREA PRINCIPAL ============ --}}
        <div class="flex-1 flex flex-col lg:ml-0">

            {{-- Topbar --}}
            <header class="bg-white shadow-sm border-b-2 border-amber-400 sticky top-0 z-10">
                <div class="px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
                    <div class="flex items-center">
                        {{-- Botón menú móvil --}}
                        <button id="sidebar-toggle" class="lg:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-md mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">@yield('titulo_topbar', 'Panel administrativo')</h1>
                    </div>

                    {{-- Usuario --}}
                    <div class="relative">
                        <button id="user-menu-btn" class="flex items-center space-x-2 px-3 py-2 rounded-md hover:bg-gray-100 transition">
                            <div class="w-8 h-8 bg-blue-700 text-white rounded-full flex items-center justify-center text-sm font-semibold">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 py-1 z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mi perfil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Mensajes flash --}}
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-md shadow-sm mb-4 flash-msg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-md shadow-sm mb-4 flash-msg">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-md shadow-sm mb-4 flash-msg">
                        {{ session('info') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-md shadow-sm mb-4 flash-msg">
                        <p class="font-semibold mb-2">Por favor corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Contenido --}}
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('contenido')
            </main>
        </div>
    </div>

    <script nonce="{{ $cspNonce }}">
        // Toggle sidebar móvil
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        sidebarToggle?.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });

        sidebarOverlay?.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        // Toggle menú de usuario
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userMenu = document.getElementById('user-menu');

        userMenuBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function() {
            userMenu?.classList.add('hidden');
        });

        // ===== Scroll Reveal =====
        const _rObs = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
        document.querySelectorAll('.reveal, .reveal-left').forEach(el => _rObs.observe(el));

        // ===== Counter Animation =====
        function _animCount(el) {
            const target = parseFloat(el.dataset.target);
            const suffix = el.dataset.suffix || '';
            const fmt = el.dataset.format === 'number';
            const dur = 1600, t0 = performance.now();
            (function tick(now) {
                const p = Math.min((now - t0) / dur, 1);
                const v = Math.round(target * (1 - Math.pow(1 - p, 3)));
                el.textContent = (fmt ? v.toLocaleString('es-CO') : v) + suffix;
                if (p < 1) requestAnimationFrame(tick);
            })(performance.now());
        }
        const _cObs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting && !e.target.dataset.counted) {
                    e.target.dataset.counted = '1';
                    _animCount(e.target);
                }
            });
        }, { threshold: 0.6 });
        document.querySelectorAll('[data-target]').forEach(el => _cObs.observe(el));
    </script>

    @stack('scripts')
</body>
</html>
