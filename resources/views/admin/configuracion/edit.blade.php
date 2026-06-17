@extends('layouts.admin')

@section('titulo', 'Configuración del sitio')
@section('titulo_topbar', 'Configuración del sitio')

@section('contenido')
<div class="max-w-3xl mx-auto">

    <form method="POST" action="{{ route('admin.configuracion.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Datos del instituto --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 reveal">
            <h2 class="text-base font-semibold text-gray-800 mb-5 pb-3 border-b border-gray-100">Datos del instituto</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del instituto <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre_instituto" value="{{ old('nombre_instituto', $config->nombre_instituto) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('nombre_instituto') border-red-400 @enderror">
                    @error('nombre_instituto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea name="descripcion" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('descripcion') border-red-400 @enderror">{{ old('descripcion', $config->descripcion) }}</textarea>
                    @error('descripcion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $config->telefono) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('telefono') border-red-400 @enderror">
                        @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo de contacto</label>
                        <input type="email" name="correo_contacto" value="{{ old('correo_contacto', $config->correo_contacto) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('correo_contacto') border-red-400 @enderror">
                        @error('correo_contacto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $config->direccion) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('direccion') border-red-400 @enderror">
                    @error('direccion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Redes sociales --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 reveal delay-1">
            <h2 class="text-base font-semibold text-gray-800 mb-5 pb-3 border-b border-gray-100">Redes sociales</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp <span class="text-gray-400 font-normal">(número con código de país, ej: 573001234567)</span></label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $config->whatsapp) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('whatsapp') border-red-400 @enderror">
                    @error('whatsapp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook <span class="text-gray-400 font-normal">(URL completa)</span></label>
                    <input type="url" name="facebook" value="{{ old('facebook', $config->facebook) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('facebook') border-red-400 @enderror">
                    @error('facebook') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instagram <span class="text-gray-400 font-normal">(URL completa)</span></label>
                    <input type="url" name="instagram" value="{{ old('instagram', $config->instagram) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-transparent @error('instagram') border-red-400 @enderror">
                    @error('instagram') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Botón guardar --}}
        <div class="flex justify-end pb-6">
            <button type="submit" class="btn-gold px-6 py-2.5 text-sm">
                Guardar configuración
            </button>
        </div>

    </form>
</div>
@endsection
