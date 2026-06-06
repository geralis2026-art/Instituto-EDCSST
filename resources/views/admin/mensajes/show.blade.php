@extends('layouts.admin')

@section('titulo', 'Ver mensaje')
@section('titulo_topbar', 'Mensajes de contacto')

@section('contenido')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.mensajes.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
            <span>&larr;</span> Volver a mensajes
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Mensaje de {{ $mensaje->nombre }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $mensaje->created_at->format('d/m/Y H:i') }} &middot; {{ $mensaje->ip }}</p>
    </div>

    {{-- Contenido del mensaje --}}
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nombre</p>
                <p class="text-gray-900 font-medium">{{ $mensaje->nombre }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Correo</p>
                <a href="mailto:{{ $mensaje->correo }}" class="text-blue-700 hover:underline font-medium">{{ $mensaje->correo }}</a>
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Mensaje</p>
            <div class="bg-gray-50 rounded-lg p-4 text-gray-800 whitespace-pre-wrap text-sm leading-relaxed border border-gray-200">{{ $mensaje->mensaje }}</div>
        </div>
    </div>

    {{-- Gestión --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Gestión</h2>

        <form action="{{ route('admin.mensajes.update', $mensaje) }}" method="POST" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full sm:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Models\Mensaje::$estados as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected($mensaje->estado === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Notas internas</label>
                <textarea name="notas_internas" rows="4" maxlength="2000"
                    placeholder="Anotaciones privadas sobre este mensaje..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none text-sm">{{ old('notas_internas', $mensaje->notas_internas) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-blue-700 text-white font-semibold rounded-lg hover:bg-blue-800 transition">
                    Guardar cambios
                </button>
                <a href="mailto:{{ $mensaje->correo }}" class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                    Responder por correo
                </a>
            </div>
        </form>
    </div>

    {{-- Eliminar --}}
    <div class="flex justify-end">
        <form action="{{ route('admin.mensajes.destroy', $mensaje) }}" method="POST" onsubmit="return confirm('¿Eliminar este mensaje definitivamente?');">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm font-medium transition">
                Eliminar mensaje
            </button>
        </form>
    </div>

</div>
@endsection
