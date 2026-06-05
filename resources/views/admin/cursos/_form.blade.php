<form action="{{ isset($curso) ? route('admin.cursos.update', $curso) : route('admin.cursos.store') }}"
      method="POST"
      class="space-y-6">
    @csrf
    @isset($curso)
        @method('PUT')
    @endisset

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $curso->nombre ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror" required>
            @error('nombre')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">Categoria *</label>
            <select id="categoria_id" name="categoria_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('categoria_id') border-red-500 @enderror" required>
                <option value="">Seleccionar categoria</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected(old('categoria_id', $curso->categoria_id ?? '') == $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            @error('categoria_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="duracion" class="block text-sm font-medium text-gray-700 mb-1">Duracion *</label>
            <input type="text" id="duracion" name="duracion" value="{{ old('duracion', $curso->duracion ?? '') }}" placeholder="Ej: 40 horas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('duracion') border-red-500 @enderror" required>
            @error('duracion')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="intensidad_horaria" class="block text-sm font-medium text-gray-700 mb-1">Intensidad horaria *</label>
            <input type="number" id="intensidad_horaria" name="intensidad_horaria" min="1" value="{{ old('intensidad_horaria', $curso->intensidad_horaria ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('intensidad_horaria') border-red-500 @enderror" required>
            @error('intensidad_horaria')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">Imagen</label>
            <input type="text" id="imagen" name="imagen" value="{{ old('imagen', $curso->imagen ?? '') }}" placeholder="Ruta en storage, opcional" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('imagen') border-red-500 @enderror">
            @error('imagen')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label for="descripcion_corta" class="block text-sm font-medium text-gray-700 mb-1">Descripcion corta *</label>
            <textarea id="descripcion_corta" name="descripcion_corta" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('descripcion_corta') border-red-500 @enderror" required>{{ old('descripcion_corta', $curso->descripcion_corta ?? '') }}</textarea>
            @error('descripcion_corta')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-wrap gap-6">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('activo', $curso->activo ?? true))>
            Activo
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="destacado" value="0">
            <input type="checkbox" name="destacado" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('destacado', $curso->destacado ?? false))>
            Destacado
        </label>
    </div>

    <div class="flex gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            {{ isset($curso) ? 'Guardar Cambios' : 'Crear Curso' }}
        </button>
        <a href="{{ route('admin.cursos.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">Cancelar</a>
    </div>
</form>
