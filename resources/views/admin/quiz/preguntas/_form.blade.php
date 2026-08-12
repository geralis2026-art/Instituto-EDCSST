@php
    $claves = ['a', 'b', 'c', 'd'];
    $opcionesExistentes = $pregunta ? $pregunta->opciones->values() : collect();
    $correctaKey = null;
    foreach ($claves as $i => $clave) {
        if (($opcionesExistentes[$i] ?? null)?->es_correcta) {
            $correctaKey = $clave;
        }
    }
@endphp

<form action="{{ $action }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
    @csrf
    @if($pregunta) @method('PUT') @endif

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Enunciado *</label>
        <textarea name="enunciado" rows="2" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('enunciado') border-red-500 @enderror">{{ old('enunciado', $pregunta->enunciado ?? '') }}</textarea>
        @error('enunciado')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
        <select name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach(\App\Models\QuizPregunta::TIPOS as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('tipo', $pregunta->tipo ?? 'seleccion_multiple') === $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Para "Verdadero / Falso" solo llena las 2 primeras opciones y deja las demás vacías.</p>
    </div>

    @error('opcion_correcta')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror

    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Opciones (marca cuál es la correcta) *</label>
        @foreach($claves as $i => $clave)
            <div class="flex items-center gap-3">
                <input type="radio" name="opcion_correcta" value="{{ $clave }}" required
                       class="text-blue-800 focus:ring-blue-700"
                       @checked(old('opcion_correcta', $correctaKey) === $clave)>
                <input type="text" name="opciones[{{ $clave }}]" placeholder="Opción {{ strtoupper($clave) }}"
                       value="{{ old('opciones.' . $clave, $opcionesExistentes[$i]->texto ?? '') }}"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        @endforeach
    </div>

    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        {{ $pregunta ? 'Guardar cambios' : 'Crear pregunta' }}
    </button>
</form>
