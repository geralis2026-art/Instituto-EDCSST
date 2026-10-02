@extends('layouts.admin')

@section('titulo', 'Configuración del sitio')
@section('titulo_topbar', 'Configuración del sitio')

@section('contenido')
<div class="max-w-3xl mx-auto">

    <form method="POST" action="{{ route('admin.configuracion.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Datos del instituto --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 reveal">
            <h2 class="text-base font-semibold text-slate-800 mb-5 pb-3 border-b border-slate-100">Datos del instituto</h2>

            <div class="space-y-4">
                <div>
                    <label class="etiqueta-admin" for="campo-nombre_instituto">Nombre del instituto <span class="text-red-700">*</span></label>
                    <input id="campo-nombre_instituto" type="text" name="nombre_instituto" value="{{ old('nombre_instituto', $config->nombre_instituto) }}"
                        class="campo-admin @error('nombre_instituto') border-red-500 @enderror">
                    @error('nombre_instituto') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="etiqueta-admin" for="campo-descripcion">Descripción</label>
                    <textarea id="campo-descripcion" name="descripcion" rows="3"
                        class="campo-admin @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $config->descripcion) }}</textarea>
                    @error('descripcion') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="etiqueta-admin" for="campo-telefono">Teléfono</label>
                        <input id="campo-telefono" type="text" name="telefono" value="{{ old('telefono', $config->telefono) }}"
                            class="campo-admin @error('telefono') border-red-500 @enderror">
                        @error('telefono') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="etiqueta-admin" for="campo-correo_contacto">Correo de contacto</label>
                        <input id="campo-correo_contacto" type="email" name="correo_contacto" value="{{ old('correo_contacto', $config->correo_contacto) }}"
                            class="campo-admin @error('correo_contacto') border-red-500 @enderror">
                        @error('correo_contacto') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="etiqueta-admin" for="campo-direccion">Dirección</label>
                    <input id="campo-direccion" type="text" name="direccion" value="{{ old('direccion', $config->direccion) }}"
                        class="campo-admin @error('direccion') border-red-500 @enderror">
                    @error('direccion') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Redes sociales --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 reveal delay-1">
            <h2 class="text-base font-semibold text-slate-800 mb-5 pb-3 border-b border-slate-100">Redes sociales</h2>

            <div class="space-y-4">
                <div>
                    <label class="etiqueta-admin" for="campo-whatsapp">WhatsApp <span class="text-slate-500 font-normal">(número con código de país, ej: 573001234567)</span></label>
                    <input id="campo-whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp', $config->whatsapp) }}"
                        class="campo-admin @error('whatsapp') border-red-500 @enderror">
                    @error('whatsapp') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="etiqueta-admin" for="campo-facebook">Facebook <span class="text-slate-500 font-normal">(URL completa)</span></label>
                    <input id="campo-facebook" type="url" name="facebook" value="{{ old('facebook', $config->facebook) }}"
                        class="campo-admin @error('facebook') border-red-500 @enderror">
                    @error('facebook') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="etiqueta-admin" for="campo-instagram">Instagram <span class="text-slate-500 font-normal">(URL completa)</span></label>
                    <input id="campo-instagram" type="url" name="instagram" value="{{ old('instagram', $config->instagram) }}"
                        class="campo-admin @error('instagram') border-red-500 @enderror">
                    @error('instagram') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Plantilla de certificado --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 reveal delay-2">
            <h2 class="text-base font-semibold text-slate-800 mb-5 pb-3 border-b border-slate-100">Plantilla de certificado</h2>

            <div class="space-y-3">
                @if($config->plantilla_certificado)
                    <p class="text-sm text-slate-600">
                        Plantilla actual: <span class="font-medium text-slate-800">{{ basename($config->plantilla_certificado) }}</span>
                    </p>
                @else
                    <p class="text-sm text-amber-600 font-medium">No hay plantilla configurada. Los certificados se generarán con la plantilla por defecto.</p>
                @endif

                <div>
                    <label class="etiqueta-admin" for="campo-plantilla_certificado_archivo">
                        {{ $config->plantilla_certificado ? 'Reemplazar plantilla (PDF)' : 'Subir plantilla (PDF)' }}
                    </label>
                    <input id="campo-plantilla_certificado_archivo" type="file" name="plantilla_certificado_archivo" accept="application/pdf"
                        class="block text-sm text-slate-700 border border-slate-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-400 file:mr-3 file:py-2 file:px-4 file:border-0 file:bg-amber-400 file:text-marca-navy file:rounded-l-lg file:cursor-pointer hover:file:bg-amber-600 @error('plantilla_certificado_archivo') border-red-500 @enderror">
                    @error('plantilla_certificado_archivo') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-slate-500 mt-1">PDF, máx. 10 MB. Se guardará como <code>plantillas/certificado.pdf</code>.</p>
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
