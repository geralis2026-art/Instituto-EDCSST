@extends('layouts.public')

@section('titulo', 'Contacto')
@section('descripcion', 'Contáctanos para más información sobre nuestros cursos y certificaciones.')

@section('contenido')

<section class="bg-blue-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-bold mb-3">Contáctanos</h1>
        <p class="text-blue-100 text-lg">Resolvemos tus dudas y te orientamos en tu proceso de capacitación</p>
    </div>
</section>

<section class="py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Información de contacto --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Información</h2>

                    <div class="space-y-5">
                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">Dirección</p>
                                <p class="text-sm text-gray-600">Villavicencio, Meta - Colombia</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">Teléfono</p>
                                <p class="text-sm text-gray-600">+57 321 2173463</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">Correo</p>
                                <p class="text-sm text-gray-600 break-all">academiasstcolombiana@gmail.com</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-700" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-gray-900">WhatsApp</p>
                                <a href="https://wa.me/573212173463" target="_blank" class="text-sm text-green-700 hover:text-green-800 font-semibold">Escríbenos</a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Horario de atención</h3>
                        <p class="text-sm text-gray-600">Lunes a Viernes</p>
                        <p class="text-sm text-gray-600">8:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Envíanos un mensaje</h2>
                    <p class="text-sm text-gray-600 mb-6">Completa el formulario y te responderemos lo antes posible.</p>

                    <form method="POST" action="{{ route('contacto.enviar') }}" class="space-y-5">
                        @csrf

                        {{-- Nombre --}}
                        <div>
                            <label for="nombre" class="block text-sm font-semibold text-gray-700 mb-1">Nombre completo *</label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="150"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('nombre') border-red-500 @enderror">
                            @error('nombre')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Correo --}}
                        <div>
                            <label for="correo" class="block text-sm font-semibold text-gray-700 mb-1">Correo electrónico *</label>
                            <input type="email" id="correo" name="correo" value="{{ old('correo') }}" required maxlength="150"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('correo') border-red-500 @enderror">
                            @error('correo')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mensaje --}}
                        <div>
                            <label for="mensaje" class="block text-sm font-semibold text-gray-700 mb-1">Mensaje *</label>
                            <textarea id="mensaje" name="mensaje" rows="6" required minlength="10" maxlength="2000"
                                placeholder="Cuéntanos en qué podemos ayudarte..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none @error('mensaje') border-red-500 @enderror">{{ old('mensaje') }}</textarea>
                            @error('mensaje')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Mínimo 10 caracteres</p>
                        </div>

                        {{-- reCAPTCHA --}}
                        <div>
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site') }}"></div>
                            @error('g-recaptcha-response')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Aviso de privacidad --}}
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-gray-600">
                            Al enviar este formulario aceptas que tus datos sean utilizados para responder tu consulta, conforme a la Ley 1581 de 2012 (Habeas Data).
                        </div>

                        {{-- Botón --}}
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Enviar mensaje
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
