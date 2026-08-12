<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-edcsst.png') }}">
    <title>Cambiar contraseña - Instituto EDCSST</title>
    <x-app-assets />
    <style>
        :root { --gold: #D4A017; --gold-soft: #F59E0B; }
        .btn-gold {
            position: relative; overflow: hidden;
            background: linear-gradient(135deg, var(--gold-soft), var(--gold));
            color: #fff; font-weight: 600; border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(212,160,23,0.35);
            transition: opacity 0.2s, box-shadow 0.2s;
        }
        .btn-gold:hover { opacity: 0.9; box-shadow: 0 4px 14px rgba(212,160,23,0.5); }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-100 p-6 sm:p-8">
        <div class="text-center mb-6">
            <img src="{{ asset('img/logo-edcsst.png') }}" alt="Logo EDCSST" class="w-16 h-16 mx-auto mb-3">
            <h1 class="text-xl font-bold text-gray-900">Bienvenido al Aula Virtual</h1>
            <p class="text-gray-600 text-sm mt-1">Por seguridad, debes crear una nueva contraseña antes de continuar.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-800 p-3 rounded-md text-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('aula.password.cambiar.store') }}">
            @csrf

            <div>
                <x-input-label for="password" value="Nueva contraseña" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autofocus />
                <p class="text-xs text-gray-400 mt-1">No puede ser tu número de documento.</p>
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            </div>

            <button type="submit" class="mt-6 w-full btn-gold py-2.5 rounded-md font-semibold">
                Guardar y continuar
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 transition">Cerrar sesión</button>
        </form>
    </div>
</body>
</html>
