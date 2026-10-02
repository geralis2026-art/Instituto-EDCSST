@extends('layouts.public')

@section('titulo', 'Consultar Certificado')
@section('descripcion', 'Consulta y descarga tus certificados del Instituto EDCSST por número de documento o código.')

@section('contenido')

<x-public.encabezado
    titulo="Consulta tus certificados"
    subtitulo="Busca con tu número de documento o con el código del certificado y descárgalo en PDF."
    :imagen="asset('img/examen-medico-ocupacional.jpg')" />

<section class="relative -mt-6 sm:-mt-8 pb-4">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ============ FORMULARIO DE BÚSQUEDA ============ --}}
        <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-slate-200 mb-8">
            <h2 class="text-xl font-semibold text-slate-900">Buscar certificado</h2>
            <p class="text-[15px] text-slate-600 mt-1 mb-6">Elige cómo quieres buscar e ingresa el dato correspondiente.</p>

            <form method="POST" action="{{ route('consulta.buscar') }}" class="space-y-5">
                @csrf

                <fieldset>
                    <legend class="block text-sm font-semibold text-slate-700 mb-2">¿Cómo quieres buscar?</legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-4 border border-slate-300 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:bg-blue-50 has-[:checked]:border-blue-700 has-[:checked]:ring-1 has-[:checked]:ring-blue-700 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-amber-500">
                            <input type="radio" name="tipo_busqueda" value="documento" {{ old('tipo_busqueda', $tipoBusqueda ?? 'documento') === 'documento' ? 'checked' : '' }} class="w-5 h-5 text-blue-800 focus:ring-blue-700" required>
                            <span>
                                <span class="block font-semibold text-slate-900">Por documento</span>
                                <span class="block text-sm text-slate-600">Cédula del capacitado</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-slate-300 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:bg-blue-50 has-[:checked]:border-blue-700 has-[:checked]:ring-1 has-[:checked]:ring-blue-700 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-amber-500">
                            <input type="radio" name="tipo_busqueda" value="codigo" {{ old('tipo_busqueda', $tipoBusqueda ?? '') === 'codigo' ? 'checked' : '' }} class="w-5 h-5 text-blue-800 focus:ring-blue-700" required>
                            <span>
                                <span class="block font-semibold text-slate-900">Por código</span>
                                <span class="block text-sm text-slate-600">Ej: EDCSST-2026-00001</span>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <div>
                    <label for="valor" class="block text-sm font-semibold text-slate-700 mb-1.5">Documento o código <span class="text-red-600" aria-hidden="true">*</span></label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" id="valor" name="valor" value="{{ old('valor', $valorBuscado ?? '') }}" required
                            autocomplete="off" inputmode="text"
                            placeholder="Ej: 1121000000 o EDCSST-2026-00001"
                            @error('valor') aria-invalid="true" aria-describedby="valor-error" @enderror
                            class="flex-1 min-h-[48px] px-4 py-3 text-base border rounded-lg focus:ring-2 focus:ring-blue-700 focus:border-blue-700 transition @error('valor') border-red-500 @else border-slate-300 @enderror">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 bg-marca-navy text-white font-semibold rounded-lg hover:bg-marca-navy-claro transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Buscar
                        </button>
                    </div>
                    @error('valor')
                        <p id="valor-error" class="text-red-700 text-sm mt-1.5" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>

        {{-- ============ RESULTADOS ============ --}}
        @if(isset($busquedaRealizada) && $busquedaRealizada)
            <div role="status" aria-live="polite">
            {{-- No encontrado --}}
            @if(isset($mensajeError) && $mensajeError)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <h3 class="font-semibold text-amber-950">No encontramos certificados</h3>
                            <p class="text-[15px] text-amber-900 mt-1">{{ $mensajeError }}</p>
                            <ul class="list-disc pl-5 mt-3 space-y-1 text-[15px] text-amber-900">
                                <li>Verifica que el {{ $tipoBusqueda === 'documento' ? 'número de documento' : 'código' }} esté escrito correctamente.</li>
                                <li>Los certificados aparecen aquí una vez el instituto los emite.</li>
                                <li>Si fuiste capacitado y no apareces, <a href="{{ route('contacto') }}" class="font-semibold underline underline-offset-2">contacta al instituto</a>.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Encontrados --}}
            @if($certificados->count() > 0)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-green-50 border border-green-200 rounded-xl p-4 sm:p-5 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-green-700 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <p class="font-semibold text-green-950">
                            {{ $certificados->count() }} {{ Str::plural('certificado encontrado', $certificados->count()) }}
                            @if($capacitado)
                                <span class="font-normal">para</span> {{ $capacitado->nombre_completo }}
                            @endif
                        </p>
                    </div>
                    @if(!empty($urlDescargarTodos))
                        <a href="{{ $urlDescargarTodos }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-700 text-white font-semibold rounded-lg hover:bg-green-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Descargar todos en un PDF
                        </a>
                    @endif
                </div>

                <div x-data="{ seleccionados: [] }">
                    @if(!empty($urlDescargarSeleccionados))
                        <form method="POST" action="{{ $urlDescargarSeleccionados }}">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                <label class="inline-flex items-center gap-2 text-[15px] text-slate-700 cursor-pointer">
                                    <input type="checkbox"
                                           @change="seleccionados = $event.target.checked ? [{{ $certificados->filter(fn ($c) => !$c->isVencido() && $c->archivo_pdf)->pluck('id')->implode(',') }}] : []"
                                           class="w-5 h-5 text-blue-800 rounded focus:ring-blue-700">
                                    Seleccionar todos
                                </label>
                                <button type="submit"
                                        :disabled="seleccionados.length === 0"
                                        :class="seleccionados.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-marca-navy-claro'"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-marca-navy text-white font-semibold rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span x-text="seleccionados.length > 0 ? `Descargar seleccionados (${seleccionados.length})` : 'Descargar seleccionados'"></span>
                                </button>
                            </div>
                    @endif

                        <ul class="space-y-4">
                            @foreach($certificados as $certificado)
                                @php $vencidoCert = $certificado->isVencido(); @endphp
                                <li class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 hover:shadow-md transition-shadow">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                        <div class="flex items-start gap-4 flex-1 min-w-0">
                                            @if(!empty($urlDescargarSeleccionados) && !$vencidoCert && $certificado->archivo_pdf)
                                                <input type="checkbox" name="certificado_ids[]" value="{{ $certificado->id }}"
                                                       x-model="seleccionados"
                                                       aria-label="Seleccionar {{ $certificado->curso->nombre }}"
                                                       class="w-5 h-5 mt-1 text-blue-800 rounded focus:ring-blue-700 shrink-0">
                                            @endif
                                            <span class="hidden sm:flex w-12 h-12 rounded-lg items-center justify-center shrink-0 {{ $vencidoCert ? 'bg-slate-100 text-slate-500' : 'icon-gold' }}">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                            </span>
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="font-semibold text-lg text-slate-900">{{ $certificado->curso->nombre }}</h3>
                                                    @if($vencidoCert)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">Vencido</span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-800 border border-green-200">Vigente</span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-slate-500 mt-0.5">{{ $certificado->curso->categoria?->nombre ?? '—' }}</p>

                                                <dl class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-700">
                                                    <div class="flex gap-1"><dt class="text-slate-500">Emitido:</dt><dd class="font-medium tabular-nums">{{ $certificado->fecha_emision->format('d/m/Y') }}</dd></div>
                                                    <div class="flex gap-1"><dt class="text-slate-500">Vence:</dt><dd class="font-medium tabular-nums {{ $vencidoCert ? 'text-red-700' : '' }}">{{ $certificado->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</dd></div>
                                                    <div class="flex gap-1"><dt class="text-slate-500">Horas:</dt><dd class="font-medium tabular-nums">{{ $certificado->intensidad_horaria }}</dd></div>
                                                </dl>
                                                <p class="mt-2"><span class="inline-block bg-slate-100 text-slate-700 text-xs font-mono px-2 py-1 rounded [overflow-wrap:anywhere]">{{ $certificado->codigo_unico }}</span></p>
                                            </div>
                                        </div>

                                        <div class="shrink-0">
                                            @if($vencidoCert)
                                                <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 bg-slate-100 rounded-lg">
                                                    No disponible para descarga
                                                </span>
                                            @elseif($certificado->archivo_pdf)
                                                <a href="{{ $urlsDescarga[$certificado->id] }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-5 py-2.5 btn-gold">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    Descargar PDF
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 bg-slate-100 rounded-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    En procesamiento
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                    @if(!empty($urlDescargarSeleccionados))
                        </form>
                    @endif
                </div>
            @endif
            </div>
        @endif

        {{-- ============ AYUDA ============ --}}
        <aside class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-5">
                <h3 class="font-semibold text-slate-900">¿No encuentras tu certificado?</h3>
                <p class="text-[15px] text-slate-600 mt-1">Los certificados se emiten al completar y aprobar el curso. Si ya lo hiciste, <a href="{{ route('contacto') }}" class="text-blue-800 font-semibold underline underline-offset-2">escríbenos</a>.</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-5">
                <h3 class="font-semibold text-slate-900">¿Eres empresa o empleador?</h3>
                <p class="text-[15px] text-slate-600 mt-1">Para validar el certificado de un tercero usa la <a href="{{ route('verificar') }}" class="text-blue-800 font-semibold underline underline-offset-2">página de verificación</a>.</p>
            </div>
        </aside>
    </div>
</section>

@endsection
