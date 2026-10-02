<form action="{{ isset($curso) ? route('admin.cursos.update', $curso) : route('admin.cursos.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf
    @isset($curso)
        @method('PUT')
    @endisset

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label for="nombre" class="etiqueta-admin">Nombre *</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $curso->nombre ?? '') }}" class="campo-admin @error('nombre') border-red-500 @enderror" required>
            @error('nombre')<p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="categoria_id" class="etiqueta-admin">Categoria *</label>
            <select id="categoria_id" name="categoria_id" class="campo-admin @error('categoria_id') border-red-500 @enderror" required>
                <option value="">Seleccionar categoria</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected(old('categoria_id', $curso->categoria_id ?? '') == $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            @error('categoria_id')<p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="duracion" class="etiqueta-admin">Duracion *</label>
            <input type="text" id="duracion" name="duracion" value="{{ old('duracion', $curso->duracion ?? '') }}" placeholder="Ej: 40 horas" class="campo-admin @error('duracion') border-red-500 @enderror" required>
            @error('duracion')<p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="intensidad_horaria" class="etiqueta-admin">Intensidad horaria *</label>
            <input type="number" id="intensidad_horaria" name="intensidad_horaria" min="1" value="{{ old('intensidad_horaria', $curso->intensidad_horaria ?? '') }}" class="campo-admin @error('intensidad_horaria') border-red-500 @enderror" required>
            @error('intensidad_horaria')<p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="imagen" class="etiqueta-admin">Imagen</label>
            @isset($curso)
                @if($curso->imagen)
                    <div class="mb-2">
                        <img src="{{ $curso->imagen_url }}" alt="{{ $curso->nombre }}" class="h-24 w-auto rounded-lg border border-slate-200 object-cover">
                        <p class="text-sm text-slate-500 mt-1">Si cargas una nueva imagen, reemplazará la actual.</p>
                    </div>
                @endif
            @endisset
            <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp"
                   class="campo-admin @error('imagen') border-red-500 @enderror">
            <p class="text-sm text-slate-500 mt-1">JPG, PNG o WebP — máx. 2 MB. Opcional.</p>
            @error('imagen')<p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label for="descripcion_corta" class="etiqueta-admin">Descripcion corta *</label>
            <textarea id="descripcion_corta" name="descripcion_corta" rows="4" class="campo-admin @error('descripcion_corta') border-red-500 @enderror" required>{{ old('descripcion_corta', $curso->descripcion_corta ?? '') }}</textarea>
            @error('descripcion_corta')<p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex flex-wrap gap-6">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" class="rounded border-slate-300 text-blue-800 focus:ring-blue-700" @checked(old('activo', $curso->activo ?? true))>
            Activo
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="destacado" value="0">
            <input type="checkbox" name="destacado" value="1" class="rounded border-slate-300 text-blue-800 focus:ring-blue-700" @checked(old('destacado', $curso->destacado ?? false))>
            Destacado
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="tiene_aula_virtual" value="0">
            <input type="checkbox" name="tiene_aula_virtual" value="1" class="rounded border-slate-300 text-blue-800 focus:ring-blue-700" @checked(old('tiene_aula_virtual', $curso->tiene_aula_virtual ?? false))>
            Tiene aula virtual
        </label>
    </div>

    <div class="flex gap-3 pt-4 border-t">
        <button type="submit" class="btn-primario">
            {{ isset($curso) ? 'Guardar Cambios' : 'Crear Curso' }}
        </button>
        <a href="{{ route('admin.cursos.index') }}" class="btn-secundario">Cancelar</a>
    </div>
</form>
