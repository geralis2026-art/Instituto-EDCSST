<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
    <title>@yield('titulo', 'Panel') - Admin Instituto EDCSST</title>

    <x-app-assets />
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">

    <div class="min-h-screen flex">

        {{-- ============ SIDEBAR ============ --}}
        <aside id="sidebar" class="bg-blue-900 text-white w-64 min-h-screen fixed lg:sticky top-0 left-0 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

            {{-- Logo --}}
            <div class="p-6 border-b border-blue-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-24 h-24 bg-white rounded-lg flex items-center justify-center p-1">
                        <x-application-logo class="w-full h-full" />
                    </div>
                    <div>
                        <div class="font-bold text-base">EDCSST Admin</div>
                        <div class="text-xs text-blue-300">Panel administrativo</div>
                    </div>
                </a>
            </div>

            {{-- Menú lateral --}}
            <nav class="p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.capacitados.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.capacitados.*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Capacitados
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.cursos.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.cursos.*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Cursos
                    </a>

                    <a href="{{ route('admin.categorias.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.categorias.*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Categorías
                    </a>
                @endif

                <a href="{{ route('admin.certificados.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.certificados.*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    Certificados
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.mensajes.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.mensajes.*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Mensajes
                        @if($mensajesNuevos > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $mensajesNuevos }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.usuarios.index') }}" class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.usuarios.*') ? 'bg-blue-700' : 'hover:bg-blue-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Usuarios
                    </a>
                @endif

                <div class="border-t border-blue-800 my-4"></div>

                <a href="{{ route('home') }}" target="_blank" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Ver sitio público
                </a>
            </nav>
        </aside>

        {{-- Overlay para móvil --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden"></div>

        {{-- ============ ÁREA PRINCIPAL ============ --}}
        <div class="flex-1 flex flex-col lg:ml-0">

            {{-- Topbar --}}
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
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
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-md shadow-sm mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-md shadow-sm mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 p-4 rounded-md shadow-sm mb-4">
                        {{ session('info') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-md shadow-sm mb-4">
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
    </script>

    @stack('scripts')
</body>
</html>
