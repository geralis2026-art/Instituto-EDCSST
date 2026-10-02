{{-- Formulario compartido para crear/editar capacitados --}}
<form action="{{ isset($capacitado) ? route('admin.capacitados.update', $capacitado) : route('admin.capacitados.store') }}"
      method="POST"
      class="space-y-6">
    @csrf
    @isset($capacitado)
        @method('PUT')
    @endisset

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Nombre Completo --}}
        <div class="md:col-span-2">
            <label for="nombre_completo" class="etiqueta-admin">
                Nombre Completo *
            </label>
            <input type="text"
                   id="nombre_completo"
                   name="nombre_completo"
                   value="{{ old('nombre_completo', $capacitado->nombre_completo ?? '') }}"
                   class="campo-admin @error('nombre_completo') border-red-500 @enderror"
                   placeholder="Ej: Juan Pérez García"
                   required>
            @error('nombre_completo')
                <p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tipo de documento --}}
        <div>
            <label for="tipo_documento" class="etiqueta-admin">
                Tipo de documento *
            </label>
            <select id="tipo_documento"
                    name="tipo_documento"
                    class="campo-admin @error('tipo_documento') border-red-500 @enderror"
                    required>
                @foreach(\App\Models\Capacitado::TIPOS_DOCUMENTO as $codigo => $etiqueta)
                    <option value="{{ $codigo }}" {{ old('tipo_documento', $capacitado->tipo_documento ?? 'CC') === $codigo ? 'selected' : '' }}>
                        {{ $codigo }} — {{ $etiqueta }}
                    </option>
                @endforeach
            </select>
            @error('tipo_documento')
                <p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Número de documento --}}
        <div>
            <label for="documento" class="etiqueta-admin">
                Número de documento *
            </label>
            <input type="text"
                   id="documento"
                   name="documento"
                   value="{{ old('documento', $capacitado->documento ?? '') }}"
                   class="campo-admin @error('documento') border-red-500 @enderror"
                   placeholder="Ej: 1234567890"
                   required>
            @error('documento')
                <p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Correo --}}
        <div>
            <label for="correo" class="etiqueta-admin">
                Correo Electrónico
            </label>
            <input type="email"
                   id="correo"
                   name="correo"
                   value="{{ old('correo', $capacitado->correo ?? '') }}"
                   class="campo-admin @error('correo') border-red-500 @enderror"
                   placeholder="juan@example.com">
            @error('correo')
                <p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Teléfono --}}
        <div>
            <label for="telefono" class="etiqueta-admin">
                Teléfono
            </label>
            <input type="text"
                   id="telefono"
                   name="telefono"
                   value="{{ old('telefono', $capacitado->telefono ?? '') }}"
                   class="campo-admin @error('telefono') border-red-500 @enderror"
                   placeholder="Ej: +57 123 456 7890">
            @error('telefono')
                <p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Grupo Sanguíneo (RH) --}}
        <div>
            <label for="rh" class="etiqueta-admin">
                Grupo Sanguíneo (RH)
            </label>
            <input type="text"
                   id="rh"
                   name="rh"
                   value="{{ old('rh', $capacitado->rh ?? '') }}"
                   class="campo-admin @error('rh') border-red-500 @enderror"
                   placeholder="Ej: O+, A-, B+">
            @error('rh')
                <p class="text-red-700 text-sm mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Nota de campos requeridos --}}
    <p class="text-sm text-slate-500 italic">* Campos requeridos</p>

    {{-- Botones de acción --}}
    <div class="flex gap-3 pt-4 border-t">
        <button type="submit" class="btn-primario">
            {{ isset($capacitado) ? 'Guardar Cambios' : 'Crear Capacitado' }}
        </button>
        <a href="{{ route('admin.capacitados.index') }}" class="btn-secundario">
            Cancelar
        </a>
    </div>
</form>
