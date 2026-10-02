@extends('layouts.public')

@section('titulo', 'Verificar Certificado')
@section('descripcion', 'Verifica la autenticidad de un certificado emitido por el Instituto EDCSST.')

@section('contenido')

<x-public.encabezado
    titulo="Verificar autenticidad"
    subtitulo="Confirma que un certificado fue emitido oficialmente por el Instituto EDCSST."
    :imagen="asset('img/capacitacion-grupal-docencia.jpg')" />

<section class="relative -mt-6 sm:-mt-8 pb-4">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Formulario --}}
        <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-slate-200 mb-8">
            <div class="flex items-start gap-4 mb-6">
                <span class="w-12 h-12 icon-gold rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Ingresa el código del certificado</h2>
                    <p class="text-[15px] text-slate-600 mt-1">Está impreso en el documento, con el formato EDCSST-AÑO-NÚMERO.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('verificar.verificar') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="codigo" class="block text-sm font-semibold text-slate-700 mb-1.5">Código del certificado <span class="text-red-600" aria-hidden="true">*</span></label>
                    <input type="text" id="codigo" name="codigo" value="{{ old('codigo', $codigoBuscado ?? '') }}" required
                        autocomplete="off" autocapitalize="characters" spellcheck="false"
                        placeholder="EDCSST-2026-00001"
                        @error('codigo') aria-invalid="true" aria-describedby="codigo-error" @enderror
                        class="w-full min-h-[52px] px-4 py-3 text-lg font-mono uppercase tracking-wider border rounded-lg focus:ring-2 focus:ring-blue-700 focus:border-blue-700 transition @error('codigo') border-red-500 @else border-slate-300 @enderror">
                    @error('codigo')
                        <p id="codigo-error" class="text-red-700 text-sm mt-1.5" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 bg-marca-navy text-white font-semibold rounded-lg hover:bg-marca-navy-claro transition-colors">
                    Verificar certificado
                </button>
            </form>
        </div>

        {{-- ============ RESULTADO ============ --}}
        @if(isset($verificacionRealizada) && $verificacionRealizada)
            <div role="status" aria-live="polite">
            @if($certificado)
                @php
                    $estilo = $vencido
                        ? ['borde' => 'border-amber-300', 'cabecera' => 'bg-amber-50 text-amber-950', 'icono' => 'bg-amber-500 text-white', 'sub' => 'text-amber-900',
                           'titulo' => 'Certificado auténtico, pero vencido', 'texto' => 'Fue emitido por el Instituto EDCSST y su vigencia de un año ya expiró.']
                        : ['borde' => 'border-green-300', 'cabecera' => 'bg-green-50 text-green-950', 'icono' => 'bg-green-600 text-white', 'sub' => 'text-green-900',
                           'titulo' => 'Certificado válido y vigente', 'texto' => 'Emitido oficialmente por el Instituto EDCSST.'];
                    $fecha = fn ($f) => $f?->locale('es')->isoFormat('D [de] MMMM [de] YYYY') ?? '—';
                @endphp

                <article class="bg-white rounded-lg border-2 {{ $estilo['borde'] }} overflow-hidden shadow-sm">
                    <header class="flex items-center gap-4 px-6 py-5 {{ $estilo['cabecera'] }}">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 {{ $estilo['icono'] }}">
                            @if($vencido)
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </span>
                        <div>
                            <h3 class="text-xl sm:text-2xl font-bold">{{ $estilo['titulo'] }}</h3>
                            <p class="text-[15px] {{ $estilo['sub'] }}">{{ $estilo['texto'] }}</p>
                        </div>
                    </header>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5 p-6 sm:p-8">
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Otorgado a</dt>
                            <dd class="mt-1 text-lg font-semibold text-slate-900">{{ $certificado->capacitado->nombre_completo }}</dd>
                            <dd class="text-sm text-slate-600">{{ $certificado->capacitado->tipo_documento ?? 'CC' }} {{ $certificado->capacitado->documentoEnmascarado() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Código</dt>
                            <dd class="mt-1 text-lg font-semibold font-mono text-marca-navy [overflow-wrap:anywhere]">{{ $certificado->codigo_unico }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Curso</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $certificado->curso->nombre }}</dd>
                            <dd class="text-sm text-slate-600">{{ $certificado->curso->categoria?->nombre ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Intensidad horaria</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $certificado->intensidad_horaria }} horas</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha de emisión</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $fecha($certificado->fecha_emision) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $vencido ? 'Venció el' : 'Válido hasta' }}</dt>
                            <dd class="mt-1 font-semibold {{ $vencido ? 'text-red-700' : 'text-slate-900' }}">{{ $fecha($certificado->fecha_vencimiento) }}</dd>
                        </div>
                    </dl>

                    <p class="mx-6 sm:mx-8 mb-6 sm:mb-8 pt-5 border-t border-slate-200 text-[15px] text-slate-600">
                        @if($vencido)
                            Para renovarlo, el titular puede <a href="{{ route('contacto') }}" class="text-blue-800 font-semibold underline underline-offset-2">comunicarse con el instituto</a>.
                        @else
                            Verificado contra los registros oficiales del Instituto EDCSST.
                        @endif
                    </p>
                </article>
            @else
                {{-- NO VÁLIDO --}}
                <article class="bg-white rounded-lg border-2 border-red-300 overflow-hidden shadow-sm">
                    <header class="flex items-center gap-4 px-6 py-5 bg-red-50 text-red-950">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 bg-red-600 text-white">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <div>
                            <h3 class="text-xl sm:text-2xl font-bold">Certificado no encontrado</h3>
                            <p class="text-[15px] text-red-900">Este código no existe en nuestros registros.</p>
                        </div>
                    </header>
                    <div class="p-6 sm:p-8">
                        <p class="text-slate-700 mb-4">
                            El código <strong class="font-mono [overflow-wrap:anywhere]">{{ $codigoBuscado }}</strong> no corresponde a ningún certificado emitido por el Instituto EDCSST.
                        </p>
                        <p class="font-semibold text-slate-800 mb-2">Posibles razones:</p>
                        <ul class="list-disc pl-5 space-y-1 text-[15px] text-slate-700">
                            <li>El código fue digitado incorrectamente.</li>
                            <li>El certificado no fue emitido por nuestro instituto.</li>
                            <li>El certificado fue invalidado.</li>
                        </ul>
                        <p class="mt-4 text-[15px] text-slate-700">Si tienes dudas, <a href="{{ route('contacto') }}" class="text-blue-800 font-semibold underline underline-offset-2">contacta al instituto</a>.</p>
                    </div>
                </article>
            @endif
            </div>
        @endif

        {{-- Info --}}
        <aside class="mt-10 bg-white border border-slate-200 rounded-xl p-5">
            <h3 class="font-semibold text-slate-900">Sobre la verificación</h3>
            <p class="text-[15px] text-slate-600 mt-1">
                Empresas, empleadores y terceros pueden validar aquí cualquier certificado emitido por el instituto. Si eres el titular y quieres descargarlo, usa la <a href="{{ route('consulta') }}" class="text-blue-800 font-semibold underline underline-offset-2">página de consulta</a>.
            </p>
        </aside>
    </div>
</section>

@endsection
