@extends('layouts.admin')

@section('titulo', 'Matrículas')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.cursos.show', $curso) }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2">
        <span>&larr;</span> Volver a {{ $curso->nombre }}
    </a>

    <h1 class="text-2xl font-bold text-gray-900">Matrículas — {{ $curso->nombre }}</h1>

    @if(auth()->user()->isGestor())
    <div class="bg-white rounded-lg shadow p-6" x-data="buscadorCapacitado('', '', '')">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Matricular capacitado</h2>

        @error('capacitado_id')<p class="text-red-500 text-sm mb-2">{{ $message }}</p>@enderror

        <form action="{{ route('admin.cursos.matriculas.store', $curso) }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="capacitado_id" :value="seleccionado.id">

            {{-- Capacitado ya seleccionado --}}
            <div x-show="seleccionado.id" style="display:none" class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex-1">
                    <p class="font-medium text-gray-900" x-text="seleccionado.nombre_completo"></p>
                    <p class="text-sm text-gray-500" x-text="seleccionado.documento"></p>
                </div>
                <button type="button" @click="limpiar()" class="text-sm text-red-600 hover:text-red-800">Cambiar</button>
            </div>

            {{-- Buscador (visible cuando no hay seleccionado) --}}
            <div x-show="!seleccionado.id" class="flex gap-3">
                <div class="relative flex-1">
                    <input type="text"
                           x-model="query"
                           @input.debounce.300ms="buscar()"
                           @keydown.escape="cerrar()"
                           placeholder="Buscar por nombre o cédula..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div x-show="cargando" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </div>

                    <div x-show="resultados.length > 0" class="absolute z-10 w-full border border-gray-200 rounded-lg mt-1 divide-y divide-gray-100 shadow-lg bg-white">
                        <template x-for="item in resultados" :key="item.id">
                            <button type="button"
                                    @click="elegir(item)"
                                    class="w-full text-left px-4 py-2.5 hover:bg-blue-50 transition">
                                <span class="font-medium text-gray-900" x-text="item.nombre_completo"></span>
                                <span class="text-sm text-gray-500 ml-2" x-text="item.documento"></span>
                            </button>
                        </template>
                    </div>

                    <p x-show="sinResultados" style="display:none" class="text-sm text-gray-500 mt-1">No se encontró ningún capacitado.</p>
                </div>
            </div>

            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Matricular</button>
        </form>
        <p class="text-xs text-gray-500 mt-2">El capacitado debe existir en el sistema y tener correo registrado. Si es su primera vez en el aula virtual, su contraseña inicial será su número de documento y se le notifica por correo.</p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Capacitado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fecha asignación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                    @if(auth()->user()->isGestor())
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($matriculas as $matricula)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $matricula->capacitado->nombre_completo }}</p>
                            <p class="text-xs text-gray-500">{{ $matricula->capacitado->documento }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $matricula->fecha_asignacion->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $matricula->completado ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $matricula->completado ? 'Completado' : 'En curso' }}
                            </span>
                        </td>
                        @if(auth()->user()->isGestor())
                            <td class="px-6 py-4">
                                @unless($matricula->completado)
                                    <form action="{{ route('admin.cursos.matriculas.destroy', [$curso, $matricula]) }}" method="POST" onsubmit="return confirm('¿Quitar esta matrícula?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Quitar</button>
                                    </form>
                                @endunless
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isGestor() ? 4 : 3 }}" class="px-6 py-8 text-center text-gray-500">Todavía no hay capacitados matriculados en este curso.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce }}">
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
@endpush
@endsection
