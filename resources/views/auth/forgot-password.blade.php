<x-guest-layout titulo="Recuperar contraseña" subtitulo="Ingresa tu correo y te enviaremos un enlace para restablecerla.">
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" inputmode="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="btn-primario w-full min-h-[48px] text-base">Enviar enlace de recuperación</button>

        <p class="text-center text-sm">
            <a href="{{ route('login') }}" class="font-semibold text-blue-800 hover:text-blue-950 underline underline-offset-2">Volver a iniciar sesión</a>
        </p>
    </form>
</x-guest-layout>
