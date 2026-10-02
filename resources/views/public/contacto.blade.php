@extends('layouts.public')

@section('titulo', 'Contacto')
@section('descripcion', 'Contáctanos para más información sobre nuestros cursos y certificaciones.')

@section('contenido')

@php
    $telefono = $configSitio->telefono ?? '+57 321 2173463';
    $correo   = $configSitio->correo_contacto ?? 'academiasstcolombiana@gmail.com';
    $whatsapp = preg_replace('/\D/', '', $configSitio->whatsapp ?? '573212173463');
    $campo    = 'w-full min-h-[48px] px-4 py-3 text-base border rounded-lg focus:ring-2 focus:ring-blue-700 focus:border-blue-700 transition';
@endphp

<x-public.encabezado
    titulo="Hablemos"
    subtitulo="Resolvemos tus dudas y te orientamos en tu proceso de capacitación." />

<section class="relative -mt-6 sm:-mt-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

            {{-- Información de contacto --}}
            <aside class="lg:col-span-1 order-2 lg:order-1">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900 mb-5">Información</h2>

                    <ul class="space-y-1">
                        @foreach([
                            ['titulo' => 'WhatsApp', 'valor' => 'Escríbenos ahora', 'href' => 'https://wa.me/' . $whatsapp, 'externo' => true, 'clase' => 'bg-green-100 text-green-700',
                             'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                            ['titulo' => 'Teléfono', 'valor' => $telefono, 'href' => 'tel:' . preg_replace('/[^\d+]/', '', $telefono), 'externo' => false, 'clase' => 'icon-gold',
                             'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                            ['titulo' => 'Correo', 'valor' => $correo, 'href' => 'mailto:' . $correo, 'externo' => false, 'clase' => 'icon-gold',
                             'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ] as $canal)
                            <li>
                                <a href="{{ $canal['href'] }}" @if($canal['externo']) target="_blank" rel="noopener" @endif
                                   class="flex items-start gap-3 -mx-2 p-2 rounded-lg hover:bg-slate-50 transition-colors">
                                    <span class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $canal['clase'] }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $canal['icon'] }}"/></svg>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold text-slate-900">{{ $canal['titulo'] }}</span>
                                        <span class="block text-[15px] text-blue-800 [overflow-wrap:anywhere]">{{ $canal['valor'] }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                        <li class="flex items-start gap-3 p-2 -mx-2">
                            <span class="w-10 h-10 icon-gold rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <span>
                                <span class="block text-sm font-semibold text-slate-900">Dirección</span>
                                <span class="block text-[15px] text-slate-600">Villavicencio, Meta - Colombia</span>
                            </span>
                        </li>
                    </ul>

                    <div class="mt-6 pt-5 border-t border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Horario de atención</h3>
                        <p class="text-[15px] text-slate-600">Lunes a viernes · 8:00 a. m. – 6:00 p. m.</p>
                    </div>
                </div>
            </aside>

            {{-- Formulario --}}
            <div class="lg:col-span-2 order-1 lg:order-2">
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Envíanos un mensaje</h2>
                    <p class="text-[15px] text-slate-600 mt-1 mb-6">Te responderemos lo antes posible. Los campos con <span class="text-red-600">*</span> son obligatorios.</p>

                    <form method="POST" action="{{ route('contacto.enviar') }}" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="nombre" class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre completo <span class="text-red-600" aria-hidden="true">*</span></label>
                                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="150" autocomplete="name"
                                    @error('nombre') aria-invalid="true" aria-describedby="nombre-error" @enderror
                                    class="{{ $campo }} @error('nombre') border-red-500 @else border-slate-300 @enderror">
                                @error('nombre')
                                    <p id="nombre-error" class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="correo" class="block text-sm font-semibold text-slate-700 mb-1.5">Correo electrónico <span class="text-red-600" aria-hidden="true">*</span></label>
                                <input type="email" id="correo" name="correo" value="{{ old('correo') }}" required maxlength="150" autocomplete="email" inputmode="email"
                                    @error('correo') aria-invalid="true" aria-describedby="correo-error" @enderror
                                    class="{{ $campo }} @error('correo') border-red-500 @else border-slate-300 @enderror">
                                @error('correo')
                                    <p id="correo-error" class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="mensaje" class="block text-sm font-semibold text-slate-700 mb-1.5">Mensaje <span class="text-red-600" aria-hidden="true">*</span></label>
                            <textarea id="mensaje" name="mensaje" rows="6" required minlength="10" maxlength="2000"
                                placeholder="Cuéntanos en qué podemos ayudarte..."
                                aria-describedby="mensaje-ayuda @error('mensaje') mensaje-error @enderror"
                                @error('mensaje') aria-invalid="true" @enderror
                                class="{{ $campo }} resize-y @error('mensaje') border-red-500 @else border-slate-300 @enderror">{{ old('mensaje') }}</textarea>
                            <p id="mensaje-ayuda" class="text-sm text-slate-500 mt-1.5">Mínimo 10 caracteres.</p>
                            @error('mensaje')
                                <p id="mensaje-error" class="text-red-700 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site') }}"></div>
                            @error('g-recaptcha-response')
                                <p class="text-red-700 text-sm mt-1.5" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <p class="text-sm text-slate-500">
                            Al enviar este formulario aceptas que tus datos sean utilizados para responder tu consulta, conforme a la Ley 1581 de 2012 (Habeas Data).
                        </p>

                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[48px] px-7 py-3 bg-marca-navy text-white font-semibold rounded-lg hover:bg-marca-navy-claro transition-colors">
                            Enviar mensaje
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@endsection
