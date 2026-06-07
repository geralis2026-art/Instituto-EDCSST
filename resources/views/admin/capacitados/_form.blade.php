{{-- Formulario compartido para crear/editar capacitados --}}
<form action="{{ isset($capacitado) ? route('admin.capacitados.update', $capacitado) : route('admin.capacitados.store') }}"
      method="POST"
      class="space-y-6"
      x-data="{ modalidad: '{{ old('modalidad', $capacitado->modalidad ?? '') }}' }">
    @csrf
    @isset($capacitado)
        @method('PUT')
    @endisset

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Nombre Completo --}}
        <div class="md:col-span-2">
            <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-1">
                Nombre Completo *
            </label>
            <input type="text"
                   id="nombre_completo"
                   name="nombre_completo"
                   value="{{ old('nombre_completo', $capacitado->nombre_completo ?? '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre_completo') border-red-500 @enderror"
                   placeholder="Ej: Juan Pérez García"
                   required>
            @error('nombre_completo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Cédula --}}
        <div>
            <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">
                Cédula *
            </label>
            <input type="text"
                   id="documento"
                   name="documento"
                   value="{{ old('documento', $capacitado->documento ?? '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('documento') border-red-500 @enderror"
                   placeholder="Ej: 1234567890"
                   required>
            @error('documento')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Correo --}}
        <div>
            <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">
                Correo Electrónico
            </label>
            <input type="email"
                   id="correo"
                   name="correo"
                   value="{{ old('correo', $capacitado->correo ?? '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('correo') border-red-500 @enderror"
                   placeholder="juan@example.com">
            @error('correo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Teléfono --}}
        <div>
            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                Teléfono
            </label>
            <input type="text"
                   id="telefono"
                   name="telefono"
                   value="{{ old('telefono', $capacitado->telefono ?? '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('telefono') border-red-500 @enderror"
                   placeholder="Ej: +57 123 456 7890">
            @error('telefono')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Modalidad --}}
        <div>
            <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1">
                Modalidad
            </label>
            <select id="modalidad"
                    name="modalidad"
                    x-model="modalidad"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('modalidad') border-red-500 @enderror">
                <option value="">— Sin especificar —</option>
                <option value="virtual">Virtual</option>
                <option value="presencial">Presencial</option>
            </select>
            @error('modalidad')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- RH (solo visible cuando modalidad = presencial) --}}
        <div x-show="modalidad === 'presencial'" x-cloak>
            <label for="rh" class="block text-sm font-medium text-gray-700 mb-1">
                Grupo Sanguíneo (RH)
            </label>
            <input type="text"
                   id="rh"
                   name="rh"
                   value="{{ old('rh', $capacitado->rh ?? '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rh') border-red-500 @enderror"
                   placeholder="Ej: O+, A-, B+">
            @error('rh')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Nota de campos requeridos --}}
    <p class="text-sm text-gray-500 italic">* Campos requeridos</p>

    {{-- Botones de acción --}}
    <div class="flex gap-3 pt-4 border-t">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            {{ isset($capacitado) ? 'Guardar Cambios' : 'Crear Capacitado' }}
        </button>
        <a href="{{ route('admin.capacitados.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
            Cancelar
        </a>
    </div>
</form>
