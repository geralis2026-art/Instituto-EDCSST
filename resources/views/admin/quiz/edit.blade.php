@extends('layouts.admin')

@section('titulo', 'Quiz del Curso')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('admin.cursos.show', $curso) }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2">
        <span>&larr;</span> Volver a {{ $curso->nombre }}
    </a>

    <h1 class="text-2xl font-bold text-gray-900">Quiz — {{ $curso->nombre }}</h1>

    <form action="{{ route('admin.cursos.quiz.update', $curso) }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nota mínima para aprobar (%) *</label>
                <input type="number" step="0.01" min="0" max="100" name="nota_minima" value="{{ old('nota_minima', $quiz->nota_minima ?? 70) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nota_minima') border-red-500 @enderror">
                @error('nota_minima')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Intentos máximos *</label>
                <input type="number" min="1" max="10" name="intentos_maximos" value="{{ old('intentos_maximos', $quiz->intentos_maximos ?? 3) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('intentos_maximos') border-red-500 @enderror">
                @error('intentos_maximos')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="activo" value="0">
            <input type="checkbox" name="activo" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @checked(old('activo', $quiz->activo ?? true))>
            Activo
        </label>

        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Guardar configuración</button>
    </form>

    @if($quiz)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Preguntas</h2>
                <a href="{{ route('admin.cursos.quiz.preguntas.create', $curso) }}" class="px-3 py-1.5 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition font-medium">+ Pregunta</a>
            </div>

            <div class="space-y-3">
                @forelse($quiz->preguntas as $i => $pregunta)
                    <div class="border border-gray-200 rounded-lg px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <p class="font-medium text-gray-900">{{ $i + 1 }}. {{ $pregunta->enunciado }}</p>
                            <div class="flex items-center gap-3 whitespace-nowrap">
                                <a href="{{ route('admin.cursos.quiz.preguntas.edit', [$curso, $pregunta]) }}" class="text-sm text-blue-700 hover:text-amber-600 font-medium">Editar</a>
                                <form action="{{ route('admin.cursos.quiz.preguntas.destroy', [$curso, $pregunta]) }}" method="POST" onsubmit="return confirm('¿Eliminar esta pregunta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Eliminar</button>
                                </form>
                            </div>
                        </div>
                        <ul class="mt-2 text-sm text-gray-600 space-y-1">
                            @foreach($pregunta->opciones as $opcion)
                                <li class="flex items-center gap-2">
                                    @if($opcion->es_correcta)
                                        <span class="text-green-600 font-bold">&check;</span>
                                    @else
                                        <span class="text-gray-300">&middot;</span>
                                    @endif
                                    {{ $opcion->texto }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Este quiz todavía no tiene preguntas.</p>
                @endforelse
            </div>
        </div>
    @else
        <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 p-4 rounded-md text-sm">
            Guarda primero la configuración del quiz para poder agregar preguntas.
        </div>
    @endif
</div>
@endsection
