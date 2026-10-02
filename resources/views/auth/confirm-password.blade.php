<x-guest-layout titulo="Confirma tu contraseña" subtitulo="Esta es una zona segura. Confirma tu contraseña para continuar.">
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="mt-1" type="password" name="password" required autofocus autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="btn-primario w-full min-h-[48px] text-base">Confirmar</button>
    </form>
</x-guest-layout>
