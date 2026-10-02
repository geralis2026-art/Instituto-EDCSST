@extends('layouts.public')

@section('titulo', 'Registro de Capacitado')
@section('descripcion', 'Formulario de registro para capacitados del Instituto EDCSST.')

@push('preload')
<meta name="robots" content="noindex, nofollow">
@endpush

@section('contenido')
@php
    $campo = 'w-full min-h-[48px] px-4 py-2.5 text-base bg-white border rounded-lg focus:ring-2 focus:ring-blue-700 focus:border-blue-700 transition';
    $err = fn (string $nombre) => $errors->has($nombre) ? 'border-red-500' : 'border-slate-300';
@endphp

<x-public.encabezado
    titulo="Registro de capacitado"
    subtitulo="Completa tus datos y elige los cursos que vas a tomar. Los campos con * son obligatorios." />

<div class="relative -mt-6 sm:-mt-8 pb-4">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Resumen de errores --}}
        @if($errors->any())
            <div id="resumen-errores" tabindex="-1" role="alert"
                 class="bg-red-50 border border-red-200 text-red-900 p-4 rounded-lg mb-6 focus:outline-none focus:ring-2 focus:ring-red-400">
                <p class="font-semibold mb-1">Revisa {{ $errors->count() === 1 ? 'el siguiente campo' : 'los siguientes ' . $errors->count() . ' campos' }}:</p>
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registro.guardar', ['token' => $token]) }}" class="space-y-6">
            @csrf

            {{-- Datos personales --}}
            <fieldset class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sm:p-8">
                <legend class="sr-only">Datos personales</legend>
                <h2 class="text-xl font-semibold text-slate-900 pb-3 mb-5 border-b border-slate-200">Datos personales</h2>

                <div class="space-y-5">
                    <div>
                        <label for="nombre_completo" class="block text-sm font-semibold text-slate-700 mb-1.5">Nombre completo <span class="text-red-600" aria-hidden="true">*</span></label>
                        <input type="text" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo') }}" required
                               autocomplete="name" placeholder="Ej: Juan Carlos Pérez Gómez"
                               @error('nombre_completo') aria-invalid="true" aria-describedby="error-nombre_completo" @enderror
                               class="{{ $campo }} {{ $err('nombre_completo') }}">
                        @error('nombre_completo') <p id="error-nombre_completo" class="text-red-700 text-sm mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label for="tipo_documento" class="block text-sm font-semibold text-slate-700 mb-1.5">Tipo <span class="text-red-600" aria-hidden="true">*</span></label>
                            <select id="tipo_documento" name="tipo_documento" required
                                    @error('tipo_documento') aria-invalid="true" aria-describedby="error-tipo_documento" @enderror
                                    class="{{ $campo }} {{ $err('tipo_documento') }}">
                                @foreach(\App\Models\Capacitado::TIPOS_DOCUMENTO as $codigo => $etiqueta)
                                    <option value="{{ $codigo }}" {{ old('tipo_documento', 'CC') === $codigo ? 'selected' : '' }}>{{ $codigo }} — {{ $etiqueta }}</option>
                                @endforeach
                            </select>
                            @error('tipo_documento') <p id="error-tipo_documento" class="text-red-700 text-sm mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="documento" class="block text-sm font-semibold text-slate-700 mb-1.5">Número de documento <span class="text-red-600" aria-hidden="true">*</span></label>
                            <input type="text" id="documento" name="documento" value="{{ old('documento') }}" required
                                   inputmode="numeric" autocomplete="off" placeholder="Ej: 1234567890"
                                   @error('documento') aria-invalid="true" aria-describedby="error-documento" @enderror
                                   class="{{ $campo }} tabular-nums {{ $err('documento') }}">
                            @error('documento') <p id="error-documento" class="text-red-700 text-sm mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="correo" class="block text-sm font-semibold text-slate-700 mb-1.5">Correo electrónico</label>
                            <input type="email" id="correo" name="correo" value="{{ old('correo') }}"
                                   autocomplete="email" inputmode="email" placeholder="ejemplo@correo.com"
                                   @error('correo') aria-invalid="true" aria-describedby="error-correo" @enderror
                                   class="{{ $campo }} {{ $err('correo') }}">
                            @error('correo') <p id="error-correo" class="text-red-700 text-sm mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-semibold text-slate-700 mb-1.5">Teléfono / celular</label>
                            <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}"
                                   autocomplete="tel" inputmode="tel" placeholder="Ej: 3001234567"
                                   @error('telefono') aria-invalid="true" aria-describedby="error-telefono" @enderror
                                   class="{{ $campo }} tabular-nums {{ $err('telefono') }}">
                            @error('telefono') <p id="error-telefono" class="text-red-700 text-sm mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="sm:max-w-xs">
                        <label for="rh" class="block text-sm font-semibold text-slate-700 mb-1.5">Grupo sanguíneo (RH)</label>
                        <select id="rh" name="rh"
                                @error('rh') aria-invalid="true" aria-describedby="error-rh" @enderror
                                class="{{ $campo }} {{ $err('rh') }}">
                            <option value="">— Seleccionar —</option>
                            @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $tipo)
                                <option value="{{ $tipo }}" {{ old('rh') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                            @endforeach
                        </select>
                        @error('rh') <p id="error-rh" class="text-red-700 text-sm mt-1.5">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            {{-- Cursos --}}
            <fieldset class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 sm:p-8">
                <legend class="sr-only">Cursos a realizar</legend>
                <h2 class="text-xl font-semibold text-slate-900">Cursos a realizar <span class="text-red-600" aria-hidden="true">*</span></h2>
                <p class="text-[15px] text-slate-600 mt-1 pb-3 mb-5 border-b border-slate-200">Selecciona todos los cursos que vas a tomar.</p>

                @foreach(['cursos', 'cursos.*', 'modalidades', 'modalidades.*'] as $campoCursos)
                    @error($campoCursos) <p class="text-red-700 text-sm mb-3">{{ $message }}</p> @enderror
                @endforeach

                @if($cursos->isEmpty())
                    <p class="text-slate-600">No hay cursos disponibles en este momento.</p>
                @else
                    <div class="space-y-7">
                        @foreach($cursos as $categoria => $listaCursos)
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color:#8A6408">{{ $categoria }}</h3>
                                <div class="space-y-3">
                                    @foreach($listaCursos as $curso)
                                        @php $oldModal = old("modalidades.{$curso->id}"); @endphp
                                        <div x-data="{ marcado: {{ in_array($curso->id, old('cursos', [])) ? 'true' : 'false' }} }"
                                             class="rounded-lg border-2 transition-colors"
                                             :class="marcado ? 'border-blue-700 bg-blue-50' : 'border-slate-200 hover:border-slate-300'">
                                            <label class="flex items-start gap-3 p-4 cursor-pointer min-h-[48px]">
                                                <input type="checkbox" name="cursos[]" value="{{ $curso->id }}" x-model="marcado"
                                                       class="mt-0.5 w-5 h-5 text-blue-800 border-slate-400 rounded focus:ring-blue-700">
                                                <span class="flex-1 min-w-0">
                                                    <span class="font-semibold text-slate-900">{{ $curso->nombre }}</span>
                                                    @if($curso->intensidad_horaria)
                                                        <span class="ml-2 text-sm text-slate-600 tabular-nums">{{ $curso->intensidad_horaria }} h</span>
                                                    @endif
                                                </span>
                                            </label>
                                            <div x-show="marcado" x-cloak class="px-4 pb-4 pl-12">
                                                <label for="modalidad-{{ $curso->id }}" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                                    Modalidad <span class="text-red-600" aria-hidden="true">*</span>
                                                </label>
                                                <select id="modalidad-{{ $curso->id }}" name="modalidades[{{ $curso->id }}]"
                                                        class="w-full sm:max-w-xs min-h-[44px] px-3 py-2 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-700 focus:border-blue-700">
                                                    <option value="presencial" {{ $oldModal === 'presencial' ? 'selected' : '' }}>Presencial</option>
                                                    <option value="virtual"    {{ $oldModal === 'virtual'    ? 'selected' : '' }}>Virtual</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </fieldset>

            <button type="submit" class="w-full min-h-[52px] btn-gold text-base rounded-lg">
                Enviar registro
            </button>

            <p class="text-center text-sm text-slate-500">
                Al enviar confirmas que los datos ingresados son correctos.
            </p>
        </form>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce }}">document.getElementById('resumen-errores')?.focus();</script>
@endpush
@endsection
