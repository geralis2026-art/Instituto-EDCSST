<form action="{{ isset($certificado) ? route('admin.certificados.update', $certificado) : route('admin.certificados.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf
    @isset($certificado)
        @method('PUT')
    @endisset

    @if($capacitados->isEmpty() || $cursos->isEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-md">
            Para registrar certificados necesitas al menos un capacitado y un curso activo.
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="capacitado_id" class="block text-sm font-medium text-gray-700 mb-1">Capacitado *</label>
            <select id="capacitado_id" name="capacitado_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('capacitado_id') border-red-500 @enderror" required>
                <option value="">Seleccionar capacitado</option>
                @foreach($capacitados as $capacitado)
                    <option value="{{ $capacitado->id }}" @selected(old('capacitado_id', $certificado->capacitado_id ?? '') == $capacitado->id)>
                        {{ $capacitado->nombre_completo }} - {{ $capacitado->documento }}
                    </option>
                @endforeach
            </select>
            @error('capacitado_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="curso_id" class="block text-sm font-medium text-gray-700 mb-1">Curso *</label>
            <select id="curso_id" name="curso_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('curso_id') border-red-500 @enderror" required>
                <option value="">Seleccionar curso</option>
                @foreach($cursos as $curso)
                    <option value="{{ $curso->id }}" @selected(old('curso_id', $certificado->curso_id ?? '') == $curso->id)>
                        {{ $curso->nombre }}
                    </option>
                @endforeach
            </select>
            @error('curso_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="codigo_unico" class="block text-sm font-medium text-gray-700 mb-1">Codigo unico</label>
            <input type="text" id="codigo_unico" name="codigo_unico" value="{{ old('codigo_unico', $certificado->codigo_unico ?? '') }}" placeholder="Opcional, se genera si lo dejas vacio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('codigo_unico') border-red-500 @enderror">
            @error('codigo_unico')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="fecha_emision" class="block text-sm font-medium text-gray-700 mb-1">Fecha de emision *</label>
            <input type="date" id="fecha_emision" name="fecha_emision" value="{{ old('fecha_emision', isset($certificado) ? $certificado->fecha_emision->format('Y-m-d') : now()->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_emision') border-red-500 @enderror" required>
            @error('fecha_emision')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="intensidad_horaria" class="block text-sm font-medium text-gray-700 mb-1">Intensidad horaria *</label>
            <input type="number" id="intensidad_horaria" name="intensidad_horaria" min="1" value="{{ old('intensidad_horaria', $certificado->intensidad_horaria ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('intensidad_horaria') border-red-500 @enderror" required>
            @error('intensidad_horaria')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="archivo_pdf" class="block text-sm font-medium text-gray-700 mb-1">PDF {{ isset($certificado) ? '' : '*' }}</label>
            <input type="file" id="archivo_pdf" name="archivo_pdf" accept="application/pdf" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('archivo_pdf') border-red-500 @enderror" @required(!isset($certificado))>
            @isset($certificado)
                @if($certificado->archivo_pdf)
                    <p class="text-sm text-gray-500 mt-1">Si cargas un nuevo PDF, reemplazara el archivo actual.</p>
                @endif
            @endisset
            @error('archivo_pdf')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox" name="activo" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('activo', $certificado->activo ?? true))>
        Activo
    </label>

    <div class="flex gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium" @disabled($capacitados->isEmpty() || $cursos->isEmpty())>
            {{ isset($certificado) ? 'Guardar Cambios' : 'Registrar Certificado' }}
        </button>
        <a href="{{ route('admin.certificados.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">Cancelar</a>
    </div>
</form>
