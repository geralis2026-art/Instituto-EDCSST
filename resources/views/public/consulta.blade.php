@extends('layouts.public')

@section('titulo', 'Consultar Certificado')
@section('descripcion', 'Consulta y descarga tus certificados del Instituto EDCSST por número de documento o código.')

@section('contenido')

<section class="bg-blue-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-bold mb-3">Consulta tus certificados</h1>
        <p class="text-blue-100 text-lg">Busca y descarga tus certificados emitidos por el instituto</p>
    </div>
</section>

<section class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ============ FORMULARIO DE BÚSQUEDA ============ --}}
        <div class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-100 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Buscar certificado</h2>
            <p class="text-sm text-gray-600 mb-6">Puedes buscar por tu número de documento o por el código único del certificado.</p>

            <form method="POST" action="{{ route('consulta.buscar') }}" class="space-y-4">
                @csrf

                {{-- Selector de tipo --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">¿Cómo quieres buscar?</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                            <input type="radio" name="tipo_busqueda" value="documento" {{ old('tipo_busqueda', $tipoBusqueda ?? 'documento') === 'documento' ? 'checked' : '' }} class="w-4 h-4 text-blue-700 focus:ring-blue-500" required>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-gray-900">Por documento</div>
                                <div class="text-xs text-gray-500">Cédula del capacitado</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                            <input type="radio" name="tipo_busqueda" value="codigo" {{ old('tipo_busqueda', $tipoBusqueda ?? '') === 'codigo' ? 'checked' : '' }} class="w-4 h-4 text-blue-700 focus:ring-blue-500" required>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-gray-900">Por código</div>
                                <div class="text-xs text-gray-500">Ej: EDCSST-2026-00001</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Campo de búsqueda --}}
                <div>
                    <label for="valor" class="block text-sm font-semibold text-gray-700 mb-1">Valor de búsqueda *</label>
                    <input type="text" id="valor" name="valor" value="{{ old('valor', $valorBuscado ?? '') }}" required
                        placeholder="Ingresa tu documento o código"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('valor') border-red-500 @enderror">
                    @error('valor')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botón --}}
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Buscar certificado
                </button>
            </form>
        </div>

        {{-- ============ RESULTADOS ============ --}}
        @if(isset($busquedaRealizada) && $busquedaRealizada)
            {{-- Si hay error / no encontrado --}}
            @if(isset($mensajeError) && $mensajeError)
                <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div class="ml-3">
                            <h3 class="font-semibold text-yellow-900">No encontramos certificados</h3>
                            <p class="text-sm text-yellow-800 mt-1">{{ $mensajeError }}</p>
                            <div class="mt-4 text-sm text-yellow-800">
                                <strong>Sugerencias:</strong>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>Verifica que el {{ $tipoBusqueda === 'documento' ? 'número de documento' : 'código' }} esté escrito correctamente</li>
                                    <li>Si has sido capacitado pero no apareces, contacta al instituto</li>
                                    <li>Los certificados aparecen aquí una vez son emitidos por el instituto</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Si encontramos certificados --}}
            @if($certificados->count() > 0)
                <div class="bg-green-50 border-l-4 border-green-500 rounded-r-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <div class="ml-3">
                            <p class="font-semibold text-green-900">
                                {{ $certificados->count() }} {{ Str::plural('certificado encontrado', $certificados->count()) }}
                                @if($capacitado)
                                    para <strong>{{ $capacitado->nombre_completo }}</strong>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($certificados as $certificado)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                            <div class="p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start">
                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="font-bold text-lg text-gray-900">{{ $certificado->curso->nombre }}</h3>
                                                <p class="text-sm text-gray-500 mt-1">{{ $certificado->curso->categoria->nombre }}</p>

                                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-sm">
                                                    <div class="flex items-center text-gray-600">
                                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        <span><strong>Emitido:</strong> {{ $certificado->fecha_emision->format('d/m/Y') }}</span>
                                                    </div>
                                                    <div class="flex items-center text-gray-600">
                                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span><strong>Horas:</strong> {{ $certificado->intensidad_horaria }}</span>
                                                    </div>
                                                </div>

                                                <div class="mt-2">
                                                    <span class="inline-block bg-blue-50 text-blue-700 text-xs font-mono px-2 py-1 rounded">{{ $certificado->codigo_unico }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0">
                                        @if($certificado->archivo_pdf)
                                            <a href="{{ $urlsDescarga[$certificado->id] }}" class="inline-flex items-center px-5 py-2.5 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition shadow-sm">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Descargar PDF
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-5 py-2.5 bg-gray-100 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                En procesamiento
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        {{-- ============ INFO ÚTIL ============ --}}
        <div class="mt-12 bg-blue-50 border border-blue-100 rounded-xl p-6">
            <h3 class="font-bold text-blue-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                ¿Necesitas ayuda?
            </h3>
            <ul class="space-y-2 text-sm text-blue-900">
                <li>• Si no encuentras tu certificado, comunícate directamente con el instituto</li>
                <li>• Para verificar la autenticidad de un certificado de un tercero, utiliza la <a href="{{ route('verificar') }}" class="underline font-semibold">página de verificación</a></li>
                <li>• Los certificados se emiten una vez completes y apruebes el curso correspondiente</li>
            </ul>
        </div>
    </div>
</section>

@endsection
