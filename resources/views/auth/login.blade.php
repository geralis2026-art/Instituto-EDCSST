<x-guest-layout titulo="Iniciar sesión" subtitulo="Ingresa tus credenciales para acceder al panel">
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1" type="email" name="email"
                          :value="old('email')" required autofocus autocomplete="username"
                          inputmode="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="{ ver: false }">
            <x-input-label for="password" value="Contraseña" />
            <div class="relative mt-1">
                <x-text-input id="password" class="pr-12" ::type="ver ? 'text' : 'password'" type="password" name="password"
                              required autocomplete="current-password" />
                <button type="button" @click="ver = !ver" :aria-pressed="ver.toString()"
                        class="absolute inset-y-0 right-0 px-3.5 text-slate-500 hover:text-slate-800 rounded-r-lg"
                        :aria-label="ver ? 'Ocultar contraseña' : 'Mostrar contraseña'" aria-label="Mostrar contraseña">
                    <svg x-show="!ver" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="ver" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer min-h-[44px]">
                <input id="remember_me" type="checkbox" name="remember"
                       class="w-5 h-5 rounded border-slate-300 text-blue-800 focus:ring-blue-700">
                <span class="text-sm text-slate-700">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-blue-800 hover:text-blue-950 underline underline-offset-2"
                   href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            @endif
        </div>

        <button type="submit" class="btn-primario w-full min-h-[48px] text-base">
            Iniciar sesión
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </button>
    </form>
</x-guest-layout>
