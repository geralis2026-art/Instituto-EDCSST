@extends('layouts.public')

@section('titulo', 'Verificar Certificado')
@section('descripcion', 'Verifica la autenticidad de un certificado emitido por el Instituto EDCSST.')

@section('contenido')

<section class="bg-blue-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-bold mb-3">Verificar autenticidad</h1>
        <p class="text-blue-100 text-lg">Confirma que un certificado fue emitido oficialmente por el Instituto EDCSST</p>
    </div>
</section>

<section class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Formulario --}}
        <div class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-100 mb-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-3">
                    <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Ingresa el código del certificado</h2>
                <p class="text-sm text-gray-600 mt-1">Encontrarás el código impreso en el documento del certificado</p>
            </div>

            <form method="POST" action="{{ route('verificar.verificar') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="codigo" class="block text-sm font-semibold text-gray-700 mb-1">Código del certificado *</label>
                    <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $codigoBuscado ?? '') }}" required
                        placeholder="Ej: EDCSST-2026-00001"
                        class="w-full px-4 py-3 text-lg font-mono uppercase border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition tracking-wider @error('codigo') border-red-500 @enderror">
                    @error('codigo')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Verificar certificado
                </button>
            </form>
        </div>

        {{-- ============ RESULTADO DE VERIFICACIÓN ============ --}}
        @if(isset($verificacionRealizada) && $verificacionRealizada)

            @if($certificado && !$vencido)
                {{-- Certificado VÁLIDO Y VIGENTE --}}
                <div class="bg-white rounded-xl shadow-lg border-2 border-green-500 overflow-hidden">
                    <div class="bg-green-500 text-white px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4">
                                <svg class="w-7 h-7 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">✓ CERTIFICADO VÁLIDO</h3>
                                <p class="text-green-100 text-sm">Emitido oficialmente por el Instituto EDCSST</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Otorgado a</p>
                                <p class="text-lg font-bold text-gray-900">{{ $certificado->capacitado->nombre_completo }}</p>
                                <p class="text-sm text-gray-600">Documento: {{ $certificado->capacitado->documento }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Código del certificado</p>
                                <p class="text-lg font-bold font-mono text-blue-900">{{ $certificado->codigo_unico }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Curso aprobado</p>
                                <p class="text-base font-semibold text-gray-900">{{ $certificado->curso->nombre }}</p>
                                <p class="text-sm text-gray-600">Categoría: {{ $certificado->curso->categoria->nombre }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fecha de emisión</p>
                                <p class="text-base font-semibold text-gray-900">{{ $certificado->fecha_emision->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Válido hasta</p>
                                <p class="text-base font-semibold text-gray-900">{{ $certificado->fecha_vencimiento?->locale('es')->isoFormat('D [de] MMMM [de] YYYY') ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Intensidad horaria</p>
                                <p class="text-base font-semibold text-gray-900">{{ $certificado->intensidad_horaria }} horas</p>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                            <p>Este certificado ha sido verificado contra los registros oficiales del Instituto EDCSST y es auténtico.</p>
                        </div>
                    </div>
                </div>

            @elseif($certificado && $vencido)
                {{-- Certificado VENCIDO --}}
                <div class="bg-white rounded-xl shadow-lg border-2 border-orange-400 overflow-hidden">
                    <div class="bg-orange-400 text-white px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4">
                                <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">⚠ CERTIFICADO VENCIDO</h3>
                                <p class="text-orange-100 text-sm">El certificado existió pero su vigencia ha expirado</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Otorgado a</p>
                                <p class="text-lg font-bold text-gray-900">{{ $certificado->capacitado->nombre_completo }}</p>
                                <p class="text-sm text-gray-600">Documento: {{ $certificado->capacitado->documento }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Código del certificado</p>
                                <p class="text-lg font-bold font-mono text-blue-900">{{ $certificado->codigo_unico }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Curso</p>
                                <p class="text-base font-semibold text-gray-900">{{ $certificado->curso->nombre }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fecha de emisión</p>
                                <p class="text-base font-semibold text-gray-900">{{ $certificado->fecha_emision->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Venció el</p>
                                <p class="text-base font-semibold text-red-600">{{ $certificado->fecha_vencimiento->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-sm text-gray-700">
                                Este certificado fue emitido oficialmente por el Instituto EDCSST pero su vigencia de un (1) año ha expirado. Para renovarlo, comunícate con el instituto.
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Certificado NO VÁLIDO --}}
                <div class="bg-white rounded-xl shadow-lg border-2 border-red-500 overflow-hidden">
                    <div class="bg-red-500 text-white px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4">
                                <svg class="w-7 h-7 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">✗ CERTIFICADO NO VÁLIDO</h3>
                                <p class="text-red-100 text-sm">No encontramos este código en nuestros registros</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <p class="text-gray-700 mb-4">
                            El código <strong class="font-mono">{{ $codigoBuscado }}</strong> no corresponde a ningún certificado emitido por el Instituto EDCSST.
                        </p>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-gray-700">
                            <p class="font-semibold mb-2">Posibles razones:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>El código fue digitado incorrectamente</li>
                                <li>El certificado no fue emitido por nuestro instituto</li>
                                <li>El certificado puede haber sido invalidado</li>
                            </ul>
                            <p class="mt-3">Si tienes dudas, <a href="{{ route('contacto') }}" class="text-blue-700 font-semibold underline">contacta al instituto</a>.</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Info --}}
        <div class="mt-8 bg-blue-50 border border-blue-100 rounded-xl p-6">
            <h3 class="font-bold text-blue-900 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Sobre la verificación
            </h3>
            <p class="text-sm text-blue-900">
                Esta página permite a empresas, empleadores y terceros validar la autenticidad de cualquier certificado emitido por el Instituto EDCSST. Si eres el titular de un certificado y quieres consultarlo o descargarlo, utiliza la <a href="{{ route('consulta') }}" class="underline font-semibold">página de consulta</a>.
            </p>
        </div>
    </div>
</section>

@endsection
