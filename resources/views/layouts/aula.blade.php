<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
    <title>@yield('titulo', 'Aula Virtual') - Instituto EDCSST</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    <x-app-assets />
    <style>
        :root { --gold: #D4A017; --gold-soft: #F59E0B; --gold-light: #FEF3C7; }

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

        /* ===== CARD HOVER ===== */
        .card-gold-hover {
            border-top: 3px solid transparent;
            transition: border-color 0.2s, box-shadow 0.25s, transform 0.25s cubic-bezier(0.22,1,0.36,1);
        }
        .card-gold-hover:hover {
            border-top-color: var(--gold-soft);
            box-shadow: 0 14px 32px rgba(212,160,23,0.16);
            transform: translateY(-4px);
        }

        /* ===== ICON GOLD ===== */
        .icon-gold { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: var(--gold); }

        /* ===== PROGRESS BAR GOLD ===== */
        .progress-gold { background: linear-gradient(90deg, var(--gold-soft), var(--gold)); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col">

    <nav class="bg-blue-950 sticky top-0 z-40 shadow-xl border-b-4 border-amber-500">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('aula.dashboard') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center p-1 shadow-md flex-shrink-0">
                        <x-application-logo class="w-full h-full" />
                    </div>
                    <div class="leading-tight min-w-0">
                        <div class="text-white font-bold text-sm">Aula Virtual</div>
                        <div class="text-amber-400 text-xs font-medium tracking-wide">Instituto EDCSST</div>
                    </div>
                </a>

                <div class="flex items-center gap-2 sm:gap-4">
                    <span class="hidden sm:inline text-blue-100 text-sm truncate max-w-[12rem]">
                        {{ Auth::guard('capacitados')->user()->nombre_completo }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-sm font-medium text-blue-100 hover:text-amber-300 hover:bg-blue-900 rounded-md transition">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-md shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-md shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('contenido')
    </main>

    <footer class="text-center text-xs text-gray-400 py-6 px-4">
        Instituto EDCSST &middot; Aula Virtual
    </footer>

</body>
</html>
