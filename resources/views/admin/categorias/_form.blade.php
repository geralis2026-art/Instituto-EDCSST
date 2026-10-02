<form action="{{ isset($categoria) ? route('admin.categorias.update', $categoria) : route('admin.categorias.store') }}"
      method="POST"
      class="space-y-6">
    @csrf
    @isset($categoria)
        @method('PUT')
    @endisset

    <div>
        <label for="nombre" class="etiqueta-admin">Nombre *</label>
        <input type="text"
               id="nombre"
               name="nombre"
               value="{{ old('nombre', $categoria->nombre ?? '') }}"
               class="campo-admin @error('nombre') border-red-500 @enderror"
               required>
        @error('nombre')
            <p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="descripcion" class="etiqueta-admin">Descripcion</label>
        <textarea id="descripcion"
                  name="descripcion"
                  rows="4"
                  class="campo-admin @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox"
               name="activo"
               value="1"
               class="rounded border-slate-300 text-blue-800 focus:ring-blue-700"
               @checked(old('activo', $categoria->activo ?? true))>
        Activa
    </label>

    <div class="flex gap-3 pt-4 border-t">
        <button type="submit" class="btn-primario">
            {{ isset($categoria) ? 'Guardar Cambios' : 'Crear Categoria' }}
        </button>
        <a href="{{ route('admin.categorias.index') }}" class="btn-secundario">
            Cancelar
        </a>
    </div>
</form>
