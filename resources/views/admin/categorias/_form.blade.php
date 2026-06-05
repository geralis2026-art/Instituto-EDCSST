<form action="{{ isset($categoria) ? route('admin.categorias.update', $categoria) : route('admin.categorias.store') }}"
      method="POST"
      class="space-y-6">
    @csrf
    @isset($categoria)
        @method('PUT')
    @endisset

    <div>
        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
        <input type="text"
               id="nombre"
               name="nombre"
               value="{{ old('nombre', $categoria->nombre ?? '') }}"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-500 @enderror"
               required>
        @error('nombre')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
        <textarea id="descripcion"
                  name="descripcion"
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox"
               name="activo"
               value="1"
               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
               @checked(old('activo', $categoria->activo ?? true))>
        Activa
    </label>

    <div class="flex gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            {{ isset($categoria) ? 'Guardar Cambios' : 'Crear Categoria' }}
        </button>
        <a href="{{ route('admin.categorias.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
            Cancelar
        </a>
    </div>
</form>
