@extends('layouts.public')

@section('titulo', 'Registro de Capacitado')
@section('descripcion', 'Formulario de registro para capacitados del Instituto EDCSST.')

@section('contenido')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Encabezado --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 mb-4">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Registro de Capacitado</h1>
        <p class="text-gray-500 mt-2 text-sm">Completa tus datos para registrarte en el Instituto EDCSST.</p>
    </div>

    {{-- Errores --}}
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg mb-6">
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('registro.guardar', ['token' => $token]) }}" class="space-y-6">
        @csrf

        {{-- Datos personales --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Datos personales</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre_completo" value="{{ old('nombre_completo') }}"
                           placeholder="Ej: Juan Carlos Pérez Gómez"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('nombre_completo') border-red-400 @enderror">
                    @error('nombre_completo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Número de documento <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="documento" value="{{ old('documento') }}"
                           placeholder="Cédula, pasaporte u otro documento"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('documento') border-red-400 @enderror">
                    @error('documento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                        <input type="email" name="correo" value="{{ old('correo') }}"
                               placeholder="ejemplo@correo.com"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('correo') border-red-400 @enderror">
                        @error('correo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono / Celular</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}"
                               placeholder="Ej: 3001234567"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('telefono') border-red-400 @enderror">
                        @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grupo sanguíneo (RH)</label>
                    <select name="rh" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('rh') border-red-400 @enderror">
                        <option value="">— Seleccionar —</option>
                        @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $tipo)
                            <option value="{{ $tipo }}" {{ old('rh') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                        @endforeach
                    </select>
                    @error('rh') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Cursos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-1 pb-2 border-b border-gray-100">
                Cursos a realizar <span class="text-red-500">*</span>
            </h2>
            <p class="text-xs text-gray-500 mb-4">Selecciona todos los cursos que vas a tomar.</p>

            @error('cursos') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror
            @error('cursos.*') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror
            @error('modalidades') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror
            @error('modalidades.*') <p class="text-red-500 text-sm mb-3">{{ $message }}</p> @enderror

            @if($cursos->isEmpty())
                <p class="text-gray-500 text-sm">No hay cursos disponibles en este momento.</p>
            @else
                <div class="space-y-5">
                    @foreach($cursos as $categoria => $listaCursos)
                        <div>
                            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-2">{{ $categoria }}</p>
                            <div class="space-y-2">
                                @foreach($listaCursos as $curso)
                                    @php $oldModal = old("modalidades.{$curso->id}"); @endphp
                                    <div x-data="{ marcado: {{ in_array($curso->id, old('cursos', [])) ? 'true' : 'false' }} }"
                                         class="rounded-lg border transition"
                                         :class="marcado ? 'border-amber-400 bg-amber-50' : 'border-gray-200'">
                                        <label class="flex items-start gap-3 p-3 cursor-pointer group">
                                            <input type="checkbox"
                                                   name="cursos[]"
                                                   value="{{ $curso->id }}"
                                                   x-model="marcado"
                                                   class="mt-0.5 w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-400">
                                            <div class="flex-1 min-w-0">
                                                <span class="text-sm font-medium text-gray-800 group-hover:text-amber-700 transition">{{ $curso->nombre }}</span>
                                                @if($curso->intensidad_horaria)
                                                    <span class="ml-2 text-xs text-gray-400">{{ $curso->intensidad_horaria }}h</span>
                                                @endif
                                            </div>
                                        </label>
                                        <div x-show="marcado" x-cloak class="px-3 pb-3">
                                            <label class="text-xs font-medium text-gray-600 mb-1 block">
                                                Modalidad <span class="text-red-500">*</span>
                                            </label>
                                            <select name="modalidades[{{ $curso->id }}]"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent">
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
        </div>

        {{-- Botón --}}
        <button type="submit"
                class="w-full btn-gold py-3 text-base font-semibold rounded-xl">
            Enviar registro
        </button>

        <p class="text-center text-xs text-gray-400">
            Al enviar confirmas que los datos ingresados son correctos.
        </p>
    </form>
</div>
@endsection
