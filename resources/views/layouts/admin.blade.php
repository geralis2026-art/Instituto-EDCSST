<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
    <title>@yield('titulo', 'Panel') - Admin Instituto EDCSST</title>

    {{-- Misma tipografía del sitio público: Lexend (títulos) + Source Sans 3 (texto) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet">

    <x-app-assets />
    <style>
        :root { --gold: #D4A017; --gold-soft: #F59E0B; --navy: #0B1E4A; }

        h1, h2, h3, .font-titulo { font-family: 'Lexend', ui-sans-serif, system-ui, sans-serif; letter-spacing: -0.01em; }

        /* Foco visible para navegación con teclado */
        :where(a, button, input, select, textarea, summary, [tabindex]):focus-visible {
            outline: 3px solid var(--gold-soft);
            outline-offset: 2px;
        }

        /* Botón dorado: texto navy (contraste ≥ 6.8:1) */
        .btn-gold {
            background: linear-gradient(135deg, #FBBF24, var(--gold-soft));
            color: var(--navy); font-weight: 600; border-radius: 0.5rem;
            transition: background 0.15s, box-shadow 0.15s;
        }
        .btn-gold:hover { background: linear-gradient(135deg, #FCD34D, #FBBF24); box-shadow: 0 4px 12px rgba(212,160,23,0.35); }

        .badge-gold {
            display: inline-block; background: #FEF3C7; color: #8A6408; border: 1px solid rgba(212,160,23,0.4);
            font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; font-size: 0.7rem;
            padding: 0.2rem 0.7rem; border-radius: 9999px;
        }

        /* Navegación lateral */
        .nav-item { display: flex; align-items: center; gap: 0.75rem; min-height: 42px; padding: 0.5rem 0.75rem; border-radius: 0.5rem;
                    font-size: 0.9375rem; color: #DBEAFE; border-left: 3px solid transparent; transition: background-color .15s, color .15s; }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: #fff; }
        .nav-item[aria-current="page"] { background: rgba(255,255,255,0.1); background: color-mix(in srgb, var(--acento-claro, #FBBF24) 20%, transparent); color: #fff; font-weight: 600; border-left-color: var(--acento-claro, #FBBF24); }
        .nav-item svg { width: 1.25rem; height: 1.25rem; flex-shrink: 0; color: var(--acento-claro, #93C5FD); }

        /* Aparición sutil (compatibilidad con vistas que la usan) */
        .reveal, .reveal-left { opacity: 0; transform: translateY(8px); transition: opacity .3s ease-out, transform .3s ease-out; }
        .reveal.visible, .reveal-left.visible { opacity: 1; transform: none; }
        .delay-1 { transition-delay: .03s; } .delay-2 { transition-delay: .06s; } .delay-3 { transition-delay: .09s; } .delay-4 { transition-delay: .12s; }
        .card-hover { transition: box-shadow .2s; }
        .card-hover:hover { box-shadow: 0 6px 20px -6px rgba(15,23,42,0.12); }

        @keyframes slideDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }
        .flash-msg { animation: slideDown .25s ease-out both; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; transition-delay: 0s !important; }
            .reveal, .reveal-left { opacity: 1; transform: none; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 font-body text-[15px] antialiased">

    <a href="#contenido-admin" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[70] focus:px-4 focus:py-2 focus:bg-white focus:text-marca-navy focus:font-semibold focus:rounded-md focus:shadow-lg">
        Saltar al contenido
    </a>

    @php
        $usuario = auth()->user();
        // Color funcional por sección: capacitados = azul, certificados = dorado, cursos = verde azulado.
        $acentoDe = fn (string $patron) => match (true) {
            str_contains($patron, 'capacitados')  => 'acento-capacitados',
            str_contains($patron, 'certificados') => 'acento-certificados',
            str_contains($patron, 'cursos'), str_contains($patron, 'categorias') => 'acento-cursos',
            default => 'acento-neutro',
        };
        $acentoActual = collect(['admin.capacitados.*', 'admin.certificados.*', 'admin.cursos.*', 'admin.categorias.*'])
            ->first(fn ($patron) => request()->routeIs($patron));
        $claseAcento = $acentoActual ? $acentoDe($acentoActual) : 'acento-neutro';
        $grupos = [
            'Gestión' => array_filter([
                ['route' => 'admin.dashboard',          'activo' => 'admin.dashboard',      'label' => 'Inicio',
                 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'admin.capacitados.index',  'activo' => 'admin.capacitados.*',  'label' => 'Capacitados',
                 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                ['route' => 'admin.certificados.index', 'activo' => 'admin.certificados.*', 'label' => 'Certificados',
                 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ]),
            'Oferta académica' => array_filter([
                $usuario->isInstructor() ? null : ['route' => 'admin.cursos.index', 'activo' => 'admin.cursos.*', 'label' => 'Cursos',
                 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                $usuario->isAdmin() ? ['route' => 'admin.categorias.index', 'activo' => 'admin.categorias.*', 'label' => 'Categorías',
                 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'] : null,
            ]),
            'Administración' => $usuario->isAdmin() ? [
                ['route' => 'admin.mensajes.index',      'activo' => 'admin.mensajes.*',      'label' => 'Mensajes', 'contador' => $mensajesNuevos,
                 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['route' => 'admin.usuarios.index',      'activo' => 'admin.usuarios.*',      'label' => 'Usuarios',
                 'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['route' => 'admin.configuracion.edit',  'activo' => 'admin.configuracion.*', 'label' => 'Configuración',
                 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ] : [],
        ];
    @endphp

    <div x-data="{ menu: false }" @keydown.escape.window="menu = false" class="min-h-screen lg:flex">

        {{-- ============ SIDEBAR ============ --}}
        <aside id="sidebar"
               :class="{ '!translate-x-0': menu }"
               class="bg-marca-navy text-white w-64 h-screen fixed lg:sticky top-0 left-0 z-40 -translate-x-full lg:!translate-x-0 transition-transform duration-200 ease-out flex flex-col shrink-0"
               aria-label="Menú del panel">

            <div class="flex items-center justify-between gap-3 px-5 h-16 border-b border-white/10 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <span class="w-9 h-9 bg-white rounded-lg flex items-center justify-center p-1 shrink-0">
                        <x-application-logo class="w-full h-full" />
                    </span>
                    <span class="min-w-0 leading-tight">
                        <span class="block font-titulo font-semibold text-sm">Instituto EDCSST</span>
                        <span class="block text-xs text-amber-300">Panel administrativo</span>
                    </span>
                </a>
                <button type="button" @click="menu = false" class="lg:hidden p-2 -mr-2 rounded-md text-blue-200 hover:bg-white/10" aria-label="Cerrar menú">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6" aria-label="Secciones">
                @foreach($grupos as $nombreGrupo => $items)
                    @continue(empty($items))
                    <div>
                        <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-blue-300/80">{{ $nombreGrupo }}</p>
                        <ul class="space-y-0.5">
                            @foreach($items as $item)
                                <li>
                                    <a href="{{ route($item['route']) }}" class="nav-item {{ $acentoDe($item['activo']) }}"
                                       @if(request()->routeIs($item['activo'])) aria-current="page" @endif>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                        <span class="flex-1">{{ $item['label'] }}</span>
                                        @if(!empty($item['contador']))
                                            <span class="min-w-[1.5rem] text-center bg-amber-400 text-marca-navy text-xs font-bold px-1.5 py-0.5 rounded-full">
                                                {{ $item['contador'] }}<span class="sr-only"> sin leer</span>
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            <div class="px-3 py-3 border-t border-white/10 shrink-0">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="nav-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Ver sitio público
                    <span class="sr-only">(abre en una pestaña nueva)</span>
                </a>
            </div>
        </aside>

        {{-- Fondo oscuro del menú en móvil --}}
        <div x-show="menu" x-cloak @click="menu = false"
             x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 z-30 lg:hidden" aria-hidden="true"></div>

        {{-- ============ ÁREA PRINCIPAL ============ --}}
        <div class="{{ $claseAcento }} flex-1 min-w-0 flex flex-col">

            {{-- Topbar --}}
            <header class="bg-white/95 backdrop-blur border-b-[3px] sticky top-0 z-20" style="border-bottom-color: var(--acento, #CBD5E1)">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <button type="button" @click="menu = true" class="lg:hidden p-2 -ml-2 text-slate-700 hover:bg-slate-100 rounded-md"
                                aria-label="Abrir menú" aria-controls="sidebar" :aria-expanded="menu.toString()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <p class="font-titulo text-base sm:text-lg font-semibold text-slate-800 truncate">@yield('titulo_topbar', 'Panel administrativo')</p>
                    </div>

                    {{-- Menú de usuario --}}
                    <div class="relative" x-data="{ abierto: false }" @click.outside="abierto = false" @keydown.escape="abierto = false">
                        <button type="button" @click="abierto = !abierto" :aria-expanded="abierto.toString()" aria-haspopup="menu"
                                class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-full hover:bg-slate-100 transition-colors">
                            <span class="w-8 h-8 bg-marca-navy text-amber-300 rounded-full flex items-center justify-center text-sm font-semibold" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($usuario->name ?? 'A', 0, 1)) }}
                            </span>
                            <span class="hidden sm:block text-left leading-tight">
                                <span class="block text-sm font-semibold text-slate-800">{{ $usuario->name ?? 'Admin' }}</span>
                                <span class="block text-xs text-slate-500 capitalize">{{ $usuario->rol }}</span>
                            </span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            <span class="sr-only">Menú de usuario</span>
                        </button>

                        <div x-show="abierto" x-cloak
                             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="absolute right-0 mt-2 w-56 origin-top-right bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                                <p class="text-sm font-semibold text-slate-800">{{ $usuario->name }}</p>
                                <p class="text-xs text-slate-500 capitalize">{{ $usuario->rol }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mi perfil
                            </a>
                            <div class="my-1 border-t border-slate-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-700 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Mensajes flash --}}
            @if(session('success') || session('error') || session('info') || $errors->any())
                <div class="px-4 sm:px-6 lg:px-8 pt-6 space-y-3">
                    @if(session('success'))
                        <div class="flash-msg flex items-start gap-3 bg-green-50 border border-green-200 text-green-900 p-4 rounded-lg" role="status">
                            <svg class="w-5 h-5 mt-0.5 shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="flash-msg flex items-start gap-3 bg-red-50 border border-red-200 text-red-900 p-4 rounded-lg" role="alert">
                            <svg class="w-5 h-5 mt-0.5 shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="flash-msg flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-900 p-4 rounded-lg" role="status">
                            <svg class="w-5 h-5 mt-0.5 shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            <span>{{ session('info') }}</span>
                        </div>
                    @endif
                    @if($errors->any())
                        <div id="resumen-errores" tabindex="-1" class="flash-msg bg-red-50 border border-red-200 text-red-900 p-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400" role="alert">
                            <p class="font-semibold mb-1">Revisa {{ $errors->count() === 1 ? 'el siguiente campo' : 'los siguientes ' . $errors->count() . ' campos' }}:</p>
                            <ul class="list-disc pl-5 text-sm space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Contenido --}}
            <main id="contenido-admin" tabindex="-1" class="flex-1 min-w-0 px-4 sm:px-6 lg:px-8 py-6 lg:py-8 focus:outline-none">
                @yield('contenido')
            </main>
        </div>
    </div>

    {{-- ============ DIÁLOGO DE CONFIRMACIÓN ============
         Reemplaza los confirm() en línea (bloqueados por la CSP con nonce).
         Uso: <form data-confirmar="¿Eliminar…?"> o <button data-confirmar="…"> --}}
    <dialog id="dialogo-confirmar" aria-labelledby="dialogo-confirmar-titulo" aria-describedby="dialogo-confirmar-texto"
            class="w-[calc(100%-2rem)] max-w-md rounded-xl p-0 shadow-2xl backdrop:bg-slate-900/60">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <span id="dialogo-confirmar-icono" class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <div>
                    <h2 id="dialogo-confirmar-titulo" class="text-lg font-semibold text-slate-900">Confirmar acción</h2>
                    <p id="dialogo-confirmar-texto" class="mt-1 text-slate-600"></p>
                </div>
            </div>
        </div>
        <form method="dialog" class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 px-6 py-4 bg-slate-50 border-t border-slate-200 rounded-b-xl">
            <button value="cancelar" class="btn-secundario" autofocus>Cancelar</button>
            <button value="confirmar" id="dialogo-confirmar-ok" class="btn-admin bg-red-600 border-red-600 text-white hover:bg-red-700">Confirmar</button>
        </form>
    </dialog>

    <script nonce="{{ $cspNonce }}">
        (function () {
            const sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // ===== Confirmación de acciones =====
            const dialogo = document.getElementById('dialogo-confirmar');
            const texto   = document.getElementById('dialogo-confirmar-texto');
            const botonOk = document.getElementById('dialogo-confirmar-ok');
            const icono   = document.getElementById('dialogo-confirmar-icono');

            function pedirConfirmacion(mensaje, alConfirmar) {
                const peligrosa = /eliminar|quitar|desactivar/i.test(mensaje);
                texto.textContent = mensaje;
                botonOk.textContent = peligrosa ? (mensaje.match(/^¿?(\S+)/)?.[1] ?? 'Confirmar') : 'Confirmar';
                botonOk.textContent = botonOk.textContent.charAt(0).toUpperCase() + botonOk.textContent.slice(1);
                botonOk.className = peligrosa
                    ? 'btn-admin bg-red-600 border-red-600 text-white hover:bg-red-700'
                    : 'btn-primario';
                icono.className = 'w-10 h-10 rounded-full flex items-center justify-center shrink-0 ' +
                    (peligrosa ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700');

                if (typeof dialogo.showModal !== 'function') {        // navegadores sin <dialog>
                    if (window.confirm(mensaje)) alConfirmar();
                    return;
                }
                dialogo.returnValue = '';
                dialogo.showModal();
                dialogo.addEventListener('close', function cerrar() {
                    dialogo.removeEventListener('close', cerrar);
                    if (dialogo.returnValue === 'confirmar') alConfirmar();
                });
            }

            document.addEventListener('submit', function (e) {
                const form = e.target;
                const mensaje = e.submitter?.dataset.confirmar || form.dataset.confirmar;
                if (!mensaje || form.dataset.confirmado === '1') return;
                e.preventDefault();
                const submitter = e.submitter;
                pedirConfirmacion(mensaje, function () {
                    form.dataset.confirmado = '1';
                    form.requestSubmit ? form.requestSubmit(submitter && form.contains(submitter) ? submitter : undefined) : form.submit();
                    delete form.dataset.confirmado;
                });
            }, true);

            // ===== Envío automático de filtros (<select data-auto-submit>) =====
            document.addEventListener('change', function (e) {
                if (e.target.matches('[data-auto-submit]')) e.target.form?.requestSubmit();
            });

            // ===== Evita doble envío: deshabilita el botón y muestra "Procesando…" =====
            document.addEventListener('submit', function (e) {
                if (e.defaultPrevented) return;
                const form = e.target;
                if (form.method?.toLowerCase() === 'get' || form.target === '_blank' || form.hasAttribute('data-sin-bloqueo')) return;
                const boton = e.submitter;
                if (!boton || boton.dataset.sinBloqueo !== undefined) return;
                setTimeout(function () {
                    boton.disabled = true;
                    boton.setAttribute('aria-busy', 'true');
                    if (!boton.querySelector('[data-cargando]')) {
                        boton.insertAdjacentHTML('afterbegin',
                            '<svg data-cargando class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>');
                    }
                }, 0);
                // Las descargas de archivos no navegan: re-habilita el botón pasado un tiempo
                setTimeout(function () { restaurar(boton); }, 8000);
            });

            function restaurar(boton) {
                boton.disabled = false;
                boton.removeAttribute('aria-busy');
                boton.querySelector('[data-cargando]')?.remove();
            }
            // Al volver con "atrás" (bfcache) los botones no deben quedar bloqueados
            window.addEventListener('pageshow', function (e) {
                if (e.persisted) document.querySelectorAll('button[aria-busy="true"]').forEach(restaurar);
            });

            // ===== Lleva el foco al resumen de errores tras un envío fallido =====
            document.getElementById('resumen-errores')?.focus();

            // ===== Aparición sutil =====
            const revelables = document.querySelectorAll('.reveal, .reveal-left');
            if (sinMovimiento || !('IntersectionObserver' in window)) {
                revelables.forEach(el => el.classList.add('visible'));
            } else {
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
                }, { threshold: 0.05 });
                revelables.forEach(el => obs.observe(el));
            }

            // ===== Contadores (muestran el valor final de inmediato si se reduce el movimiento) =====
            document.querySelectorAll('[data-target]').forEach(function (el) {
                const objetivo = parseFloat(el.dataset.target);
                const sufijo = el.dataset.suffix || '';
                const formatear = v => (el.dataset.format === 'number' ? v.toLocaleString('es-CO') : v) + sufijo;
                if (sinMovimiento) { el.textContent = formatear(objetivo); return; }
                const t0 = performance.now(), dur = 800;
                (function paso(ahora) {
                    const p = Math.min((ahora - t0) / dur, 1);
                    el.textContent = formatear(Math.round(objetivo * (1 - Math.pow(1 - p, 3))));
                    if (p < 1) requestAnimationFrame(paso);
                })(t0);
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
