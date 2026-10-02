<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#0B1E4A">
    <link rel="icon" type="image/png" href="/img/logo-edcsst.png">
    <title>@yield('codigo') · @yield('titulo') — Instituto EDCSST</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet">
    @php
        // Si el build de assets no existe, la página igual debe poder mostrarse.
        $cssError = null;
        try {
            $manifest = json_decode((string) @file_get_contents(public_path('build/manifest.json')), true);
            $cssError = isset($manifest['resources/css/app.css']['file']) ? '/build/' . $manifest['resources/css/app.css']['file'] : null;
        } catch (\Throwable $e) {
        }
    @endphp
    @if($cssError)<link rel="stylesheet" href="{{ $cssError }}">@endif
    <style>
        body { margin: 0; font-family: 'Source Sans 3', system-ui, sans-serif; background: #f8fafc; color: #1e293b; }
        h1 { font-family: 'Lexend', system-ui, sans-serif; letter-spacing: -0.01em; }
        a:focus-visible { outline: 3px solid #F59E0B; outline-offset: 2px; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-800 antialiased">

    <header class="bg-marca-navy" style="background:#0B1E4A">
        <div style="height:4px;background:#F59E0B"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center">
            <a href="/" class="flex items-center gap-3 text-white" style="color:#fff;text-decoration:none">
                <span class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1" style="width:40px;height:40px;background:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center">
                    <img src="/img/logo-edcsst.png" alt="" width="32" height="32" style="width:32px;height:32px;object-fit:contain">
                </span>
                <span class="font-semibold" style="font-family:'Lexend',system-ui,sans-serif;font-weight:600">Instituto EDCSST</span>
            </a>
        </div>
    </header>

    <main class="flex-1 flex items-center">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-16 w-full">
            <p class="font-titulo text-6xl sm:text-7xl font-bold" style="color:#B45309;font-family:'Lexend',system-ui,sans-serif" aria-hidden="true">@yield('codigo')</p>
            <h1 class="mt-3 text-3xl sm:text-4xl font-bold text-slate-900">@yield('titulo')</h1>
            <p class="mt-4 text-lg text-slate-600 max-w-xl">@yield('mensaje')</p>

            @hasSection('extra')
                <div class="mt-4 max-w-xl text-slate-600">@yield('extra')</div>
            @endif

            <div class="mt-8 flex flex-wrap items-center gap-4">
                <a href="/" class="inline-flex items-center justify-center min-h-[48px] px-6 rounded-lg font-semibold text-white bg-marca-navy hover:bg-marca-navy-claro" style="background:#0B1E4A;color:#fff;text-decoration:none;padding:0 24px;min-height:48px;display:inline-flex;align-items:center;border-radius:8px">
                    Ir al inicio
                </a>
                <a href="/contacto" class="font-semibold text-blue-800 underline underline-offset-4">Contactarnos</a>
            </div>

            <nav aria-label="Enlaces útiles" class="mt-12 pt-6 border-t border-slate-200 max-w-xl">
                <p class="text-sm font-semibold text-slate-500">¿Buscabas esto?</p>
                <ul class="mt-3 flex flex-wrap gap-x-6 gap-y-2">
                    <li><a href="/consulta" class="text-blue-800 underline underline-offset-2">Consultar mis certificados</a></li>
                    <li><a href="/verificar" class="text-blue-800 underline underline-offset-2">Verificar un certificado</a></li>
                    <li><a href="/cursos" class="text-blue-800 underline underline-offset-2">Ver los cursos</a></li>
                </ul>
            </nav>
        </div>
    </main>
</body>
</html>
