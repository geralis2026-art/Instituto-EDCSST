<x-guest-layout titulo="Verifica tu correo" subtitulo="Antes de continuar, confirma tu dirección de correo con el enlace que te enviamos. Si no lo recibiste, podemos enviarte otro.">
    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mb-5" status="Te enviamos un nuevo enlace de verificación al correo registrado." />
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primario">Reenviar correo de verificación</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-900 underline underline-offset-2">Cerrar sesión</button>
        </form>
    </div>
</x-guest-layout>
