@extends('layouts.admin')

@section('titulo', 'Ver mensaje')
@section('titulo_topbar', 'Mensajes de contacto')

@section('contenido')
<div class="max-w-3xl mx-auto space-y-6">

    <x-admin.encabezado :titulo="'Mensaje de ' . $mensaje->nombre"
                        :subtitulo="$mensaje->created_at->format('d/m/Y H:i') . ($mensaje->ip ? ' · IP ' . $mensaje->ip : '')"
                        :volver="route('admin.mensajes.index')" />

    {{-- Contenido del mensaje --}}
    <section class="tarjeta-admin p-6 sm:p-8" aria-labelledby="titulo-mensaje">
        <h2 id="titulo-mensaje" class="sr-only">Contenido del mensaje</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nombre</dt>
                <dd class="mt-1 font-semibold text-slate-900">{{ $mensaje->nombre }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Correo</dt>
                <dd class="mt-1"><a href="mailto:{{ $mensaje->correo }}" class="font-semibold text-blue-800 hover:underline [overflow-wrap:anywhere]">{{ $mensaje->correo }}</a></dd>
            </div>
        </dl>

        <div class="mt-6">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Mensaje</p>
            <div class="bg-slate-50 rounded-lg p-5 text-slate-800 whitespace-pre-wrap leading-relaxed border border-slate-200 border-l-4 border-l-slate-400">{{ $mensaje->mensaje }}</div>
        </div>
    </section>

    {{-- Gestión --}}
    <section class="tarjeta-admin p-6 sm:p-8" aria-labelledby="titulo-gestion">
        <h2 id="titulo-gestion" class="text-base font-semibold text-slate-900 mb-5">Gestión interna</h2>

        <form action="{{ route('admin.mensajes.update', $mensaje) }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')

            <div>
                <label for="estado" class="etiqueta-admin">Estado</label>
                <select id="estado" name="estado" class="campo-admin sm:w-56">
                    @foreach(\App\Models\Mensaje::$estados as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected($mensaje->estado === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="notas_internas" class="etiqueta-admin">Notas internas</label>
                <textarea id="notas_internas" name="notas_internas" rows="4" maxlength="2000"
                          aria-describedby="ayuda-notas"
                          placeholder="Anotaciones privadas sobre este mensaje…"
                          class="campo-admin resize-y">{{ old('notas_internas', $mensaje->notas_internas) }}</textarea>
                <p id="ayuda-notas" class="text-sm text-slate-500 mt-1.5">Solo las ve el personal administrativo.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="btn-primario">Guardar cambios</button>
                <a href="mailto:{{ $mensaje->correo }}?subject={{ rawurlencode('Re: tu mensaje al Instituto EDCSST') }}" class="btn-secundario">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Responder por correo
                </a>
            </div>
        </form>
    </section>

    <div class="flex justify-end">
        <form action="{{ route('admin.mensajes.destroy', $mensaje) }}" method="POST" data-confirmar="¿Eliminar el mensaje de {{ $mensaje->nombre }}? No se puede deshacer.">
            @csrf @method('DELETE')
            <button type="submit" class="btn-peligro"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Eliminar mensaje</button>
        </form>
    </div>
</div>
@endsection
