@extends('layouts.admin')

@section('titulo', 'Generación Masiva de Certificados')
@section('titulo_topbar', 'Certificados masivos')

@section('contenido')
<div class="space-y-6" x-data="{
    fechaGlobal: '{{ now()->toDateString() }}',
    vigenciaGlobal: '1',
    activoGlobal: true,
    columnaCapacitado: '',
    columnaCurso: '',
    columnaFecha: '',
    columnaModalidad: '',
    columnaActivo: '',
    fechaRows: { @foreach($solicitudes as $s){{ $s->id }}: '{{ now()->toDateString() }}', @endforeach },
    activoRows: { @foreach($solicitudes as $s){{ $s->id }}: true, @endforeach },
    modalidadRows: { @foreach($solicitudes as $s){{ $s->id }}: '{{ $s->modalidad ?? '' }}', @endforeach },
    hayFiltros() {
        return this.columnaCapacitado || this.columnaCurso || this.columnaFecha || this.columnaModalidad || this.columnaActivo !== '';
    },
    limpiarFiltros() {
        this.columnaCapacitado = '';
        this.columnaCurso = '';
        this.columnaFecha = '';
        this.columnaModalidad = '';
        this.columnaActivo = '';
    },
    aplicarATodas() {
        document.querySelectorAll('tr[data-solicitud-id]').forEach(fila => {
            if (fila.style.display === 'none') return;
            const id = fila.dataset.solicitudId;
            const fecha = fila.querySelector('.fecha-emision');
            const vigencia = fila.querySelector('.anios-vigencia');
            const activo = fila.querySelector('.activo-cert');
            if (fecha)   { fecha.value   = this.fechaGlobal;   this.fechaRows[id]   = this.fechaGlobal; }
            if (vigencia)  vigencia.value  = this.vigenciaGlobal;
            if (activo)  { activo.checked = this.activoGlobal; this.activoRows[id]  = this.activoGlobal; }
        });
    },
    seleccionarTodos(valor) {
        document.querySelectorAll('tr[data-solicitud-id]').forEach(fila => {
            if (fila.style.display === 'none') return;
            const cb = fila.querySelector('.incluir-cert');
            if (cb) cb.checked = valor;
        });
    },
    filaVisible(el) {
        const id      = el.dataset.solicitudId;
        const nombre  = el.dataset.nombre   || '';
        const doc     = el.dataset.documento || '';
        const cursoId = el.dataset.cursoId  || '';
        if (this.columnaCapacitado) {
            const q = this.columnaCapacitado.toLowerCase();
            if (!nombre.includes(q) && !doc.includes(q)) return false;
        }
        if (this.columnaCurso && cursoId !== this.columnaCurso) return false;
        if (this.columnaFecha && this.fechaRows[id] !== this.columnaFecha) return false;
        if (this.columnaModalidad === '__vacio__') {
            if (this.modalidadRows[id] !== '') return false;
        } else if (this.columnaModalidad) {
            if (this.modalidadRows[id] !== this.columnaModalidad) return false;
        }
        if (this.columnaActivo !== '' && this.activoRows[id] !== (this.columnaActivo === 'si')) return false;
        return true;
    }
}">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Generación Masiva de Certificados</h1>
            <p class="text-gray-600 mt-1">Genera certificados a partir de las solicitudes pendientes (importación de capacitados)</p>
        </div>
        <a href="{{ route('admin.certificados.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            Volver a certificados
        </a>
    </div>

    @if($solicitudes->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            <p class="text-lg">No hay solicitudes de certificación pendientes.</p>
            <a href="{{ route('admin.capacitados.importar.form') }}" class="text-blue-600 hover:text-blue-900 mt-2 inline-block">
                Importar capacitados desde Excel →
            </a>
        </div>
    @else
        <form action="{{ route('admin.certificados.generar-masivos') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="bg-white rounded-lg shadow overflow-hidden">

                {{-- Panel azul: aplicar a filas visibles --}}
                <div class="bg-blue-50 border-b border-blue-200 p-4 flex flex-wrap items-end gap-4">
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider self-center mr-1">Aplicar a visibles</span>
                    <div>
                        <label class="block text-xs font-medium text-blue-700 mb-1">Fecha de emisión</label>
                        <input type="date" x-model="fechaGlobal" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-blue-700 mb-1">Vigencia</label>
                        <select x-model="vigenciaGlobal" class="w-28 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1">1 año</option>
                            <option value="2">2 años</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-medium text-blue-700">Activo</label>
                        <input type="checkbox" x-model="activoGlobal" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                    </div>
                    <button type="button" @click="aplicarATodas()"
                            class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                        Aplicar
                    </button>
                </div>

                {{-- Panel amarillo: filtros --}}
                <div class="bg-amber-50 border-b border-amber-200 p-4 flex flex-wrap items-end gap-4">
                    <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider self-center mr-1">Filtros</span>
                    <div>
                        <label class="block text-xs font-medium text-amber-700 mb-1">Capacitado</label>
                        <input type="text" x-model="columnaCapacitado" placeholder="Nombre o documento..."
                               class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-48">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-amber-700 mb-1">Curso</label>
                        <select x-model="columnaCurso"
                                class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 min-w-48">
                            <option value="">Todos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-amber-700 mb-1">Fecha</label>
                        <input type="date" x-model="columnaFecha"
                               class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-amber-700 mb-1">Modalidad</label>
                        <select x-model="columnaModalidad"
                                class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Todas</option>
                            <option value="virtual">Virtual</option>
                            <option value="presencial">Presencial</option>
                            <option value="__vacio__">Sin especificar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-amber-700 mb-1">Activo</label>
                        <select x-model="columnaActivo"
                                class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Todos</option>
                            <option value="si">Activo</option>
                            <option value="no">Inactivo</option>
                        </select>
                    </div>
                    <button type="button" @click="limpiarFiltros()" x-show="hayFiltros()"
                            class="px-3 py-1.5 text-amber-700 border border-amber-300 bg-white rounded-lg text-sm hover:bg-amber-100 transition self-end">
                        × Limpiar
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" checked
                                           @change="seleccionarTodos($event.target.checked)"
                                           title="Marcar / desmarcar filas visibles"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Capacitado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Curso</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha emisión</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Intensidad (h)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Modalidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Vigencia</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Activo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($solicitudes as $solicitud)
                                <tr class="hover:bg-gray-50 transition"
                                    x-show="filaVisible($el)"
                                    data-solicitud-id="{{ $solicitud->id }}"
                                    data-curso-id="{{ $solicitud->curso_id ?? '' }}"
                                    data-nombre="{{ mb_strtolower($solicitud->capacitado->nombre_completo) }}"
                                    data-documento="{{ $solicitud->capacitado->documento }}">
                                    <td class="px-4 py-3 align-top">
                                        <input type="checkbox" name="solicitudes[{{ $solicitud->id }}][incluir]" value="1"
                                               checked
                                               class="incluir-cert rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <span class="font-medium text-gray-900">{{ $solicitud->capacitado->nombre_completo }}</span>
                                        <p class="text-xs text-gray-500"><code class="bg-gray-100 px-1 rounded">{{ $solicitud->capacitado->documento }}</code></p>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        @if($solicitud->curso_id)
                                            <input type="hidden" name="solicitudes[{{ $solicitud->id }}][curso_id]" value="{{ $solicitud->curso_id }}">
                                            {{ $solicitud->curso->nombre }}
                                        @else
                                            <select name="solicitudes[{{ $solicitud->id }}][curso_id]"
                                                    class="px-2 py-1 border border-amber-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                                                <option value="">Selecciona un curso...</option>
                                                @foreach($cursos as $curso)
                                                    <option value="{{ $curso->id }}" data-horas="{{ $curso->intensidad_horaria }}">{{ $curso->nombre }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-xs text-amber-600 mt-1">Sin curso identificado: "{{ $solicitud->curso_texto }}"</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="date" name="solicitudes[{{ $solicitud->id }}][fecha_emision]"
                                               value="{{ now()->toDateString() }}"
                                               @change="fechaRows[{{ $solicitud->id }}] = $event.target.value"
                                               class="fecha-emision px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <input type="number" name="solicitudes[{{ $solicitud->id }}][intensidad_horaria]"
                                               value="{{ $solicitud->curso?->intensidad_horaria }}" min="1" max="500"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <select name="solicitudes[{{ $solicitud->id }}][modalidad]"
                                                @change="modalidadRows[{{ $solicitud->id }}] = $event.target.value"
                                                class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="" @selected(!$solicitud->modalidad)>Sin especificar</option>
                                            <option value="virtual" @selected($solicitud->modalidad === 'virtual')>Virtual</option>
                                            <option value="presencial" @selected($solicitud->modalidad === 'presencial')>Presencial</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <select name="solicitudes[{{ $solicitud->id }}][anios_vigencia]"
                                                class="anios-vigencia w-28 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="1">1 año</option>
                                            <option value="2">2 años</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 align-top text-center">
                                        <input type="hidden" name="solicitudes[{{ $solicitud->id }}][activo]" value="0">
                                        <input type="checkbox" name="solicitudes[{{ $solicitud->id }}][activo]" value="1"
                                               checked
                                               @change="activoRows[{{ $solicitud->id }}] = $event.target.checked"
                                               class="activo-cert rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Generar certificados seleccionados
                </button>
                <a href="{{ route('admin.certificados.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
            </div>
        </form>
    @endif
</div>
@endsection
