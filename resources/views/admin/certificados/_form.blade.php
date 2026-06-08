<form action="{{ isset($certificado) ? route('admin.certificados.update', $certificado) : route('admin.certificados.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="space-y-6"
      x-data="buscadorCapacitado('{{ old('capacitado_id', $certificado->capacitado_id ?? '') }}', '{{ old('capacitado_id') ? '' : ($certificado->capacitado->nombre_completo ?? '') }}', '{{ old('capacitado_id') ? '' : ($certificado->capacitado->documento ?? '') }}')">
    @csrf
    @isset($certificado)
        @method('PUT')
    @endisset

    @if($categorias->isEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-md">
            Para registrar certificados necesitas al menos una categoría y un curso activo.
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Búsqueda de capacitado --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Capacitado *</label>

            {{-- Campo oculto con el ID real --}}
            <input type="hidden" name="capacitado_id" :value="seleccionado.id">

            {{-- Capacitado ya seleccionado --}}
            <div x-show="seleccionado.id" class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2">
                <div class="flex-1">
                    <p class="font-medium text-gray-900" x-text="seleccionado.nombre_completo"></p>
                    <p class="text-sm text-gray-500" x-text="seleccionado.documento"></p>
                </div>
                <button type="button" @click="limpiar()" class="text-sm text-red-600 hover:text-red-800">Cambiar</button>
            </div>

            {{-- Buscador (visible cuando no hay seleccionado) --}}
            <div x-show="!seleccionado.id">
                <div class="relative">
                    <input type="text"
                           x-model="query"
                           @input.debounce.300ms="buscar()"
                           @keydown.escape="cerrar()"
                           placeholder="Buscar por cédula o nombre..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div x-show="cargando" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </div>
                </div>

                {{-- Resultados --}}
                <div x-show="resultados.length > 0" class="border border-gray-200 rounded-lg mt-1 divide-y divide-gray-100 shadow-sm bg-white">
                    <template x-for="item in resultados" :key="item.id">
                        <button type="button"
                                @click="elegir(item)"
                                class="w-full text-left px-4 py-2.5 hover:bg-blue-50 transition">
                            <span class="font-medium text-gray-900" x-text="item.nombre_completo"></span>
                            <span class="text-sm text-gray-500 ml-2" x-text="item.documento"></span>
                        </button>
                    </template>
                </div>

                <p x-show="sinResultados" class="text-sm text-gray-500 mt-1">No se encontró ningún capacitado.</p>
            </div>

            @error('capacitado_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Cursos agrupados por categoría --}}
        <div>
            <label for="curso_id" class="block text-sm font-medium text-gray-700 mb-1">Curso *</label>
            <select id="curso_id" name="curso_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('curso_id') border-red-500 @enderror"
                    required>
                <option value="">Seleccionar curso</option>
                @foreach($categorias as $categoria)
                    @if($categoria->cursos->isNotEmpty())
                        <optgroup label="{{ $categoria->nombre }}">
                            @foreach($categoria->cursos as $curso)
                                <option value="{{ $curso->id }}" @selected(old('curso_id', $certificado->curso_id ?? '') == $curso->id)>
                                    {{ $curso->nombre }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                @endforeach
            </select>
            @error('curso_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="codigo_unico" class="block text-sm font-medium text-gray-700 mb-1">Codigo unico</label>
            <input type="text" id="codigo_unico" name="codigo_unico"
                   value="{{ old('codigo_unico', $certificado->codigo_unico ?? '') }}"
                   placeholder="Opcional, se genera si lo dejas vacio"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('codigo_unico') border-red-500 @enderror">
            @error('codigo_unico')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="fecha_emision" class="block text-sm font-medium text-gray-700 mb-1">Fecha de emision *</label>
            <input type="date" id="fecha_emision" name="fecha_emision"
                   value="{{ old('fecha_emision', isset($certificado) ? $certificado->fecha_emision->format('Y-m-d') : now()->format('Y-m-d')) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fecha_emision') border-red-500 @enderror"
                   required>
            @error('fecha_emision')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="intensidad_horaria" class="block text-sm font-medium text-gray-700 mb-1">Intensidad horaria *</label>
            <input type="number" id="intensidad_horaria" name="intensidad_horaria" min="1"
                   value="{{ old('intensidad_horaria', $certificado->intensidad_horaria ?? '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('intensidad_horaria') border-red-500 @enderror"
                   required>
            @error('intensidad_horaria')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="modalidad" class="block text-sm font-medium text-gray-700 mb-1">Modalidad</label>
            <select id="modalidad" name="modalidad"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('modalidad') border-red-500 @enderror">
                <option value="">— Sin especificar —</option>
                <option value="virtual" @selected(old('modalidad', $certificado->modalidad ?? '') === 'virtual')>Virtual</option>
                <option value="presencial" @selected(old('modalidad', $certificado->modalidad ?? '') === 'presencial')>Presencial</option>
            </select>
            @error('modalidad')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="archivo_pdf" class="block text-sm font-medium text-gray-700 mb-1">PDF {{ isset($certificado) ? '' : '*' }}</label>
            <input type="file" id="archivo_pdf" name="archivo_pdf" accept="application/pdf"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('archivo_pdf') border-red-500 @enderror"
                   @required(!isset($certificado))>
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
        <input type="checkbox" name="activo" value="1"
               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
               @checked(old('activo', $certificado->activo ?? true))>
        Activo
    </label>

    <div class="flex gap-3 pt-4 border-t">
        <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                @disabled($categorias->isEmpty())>
            {{ isset($certificado) ? 'Guardar Cambios' : 'Registrar Certificado' }}
        </button>
        <a href="{{ route('admin.certificados.index') }}"
           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
            Cancelar
        </a>
    </div>
</form>

<script>
function buscadorCapacitado(idInicial, nombreInicial, documentoInicial) {
    return {
        query: '',
        resultados: [],
        cargando: false,
        sinResultados: false,
        seleccionado: idInicial
            ? { id: idInicial, nombre_completo: nombreInicial, documento: documentoInicial }
            : { id: null, nombre_completo: '', documento: '' },

        async buscar() {
            this.sinResultados = false;
            if (this.query.length < 2) {
                this.resultados = [];
                return;
            }
            this.cargando = true;
            try {
                const res = await fetch(`{{ route('admin.capacitados.buscar') }}?q=${encodeURIComponent(this.query)}`);
                this.resultados = await res.json();
                this.sinResultados = this.resultados.length === 0;
            } finally {
                this.cargando = false;
            }
        },

        elegir(item) {
            this.seleccionado = item;
            this.resultados = [];
            this.query = '';
            this.sinResultados = false;
        },

        limpiar() {
            this.seleccionado = { id: null, nombre_completo: '', documento: '' };
        },

        cerrar() {
            this.resultados = [];
        }
    };
}
</script>
