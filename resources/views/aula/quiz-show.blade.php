@extends('layouts.aula')

@section('titulo', 'Quiz - ' . $matricula->curso->nombre)

@section('contenido')
<div class="space-y-6">
    <div>
        <a href="{{ route('aula.cursos.show', $matricula) }}" class="text-sm text-blue-700 hover:text-amber-600 transition inline-flex items-center gap-1">
            <span>&larr;</span> {{ $matricula->curso->nombre }}
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">Quiz de validación</h1>
        <p class="text-gray-600 mt-1">
            Nota mínima para aprobar: <span class="font-semibold">{{ $quiz->nota_minima }}%</span>.
            Intento <span class="font-semibold">{{ $matricula->intentos_usados + 1 }}</span> de {{ $quiz->intentos_maximos }}.
        </p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-md text-sm">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('aula.quiz.store', $matricula) }}" class="space-y-5">
        @csrf

        @foreach($preguntas as $i => $pregunta)
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-start gap-3">
                    <span class="w-7 h-7 rounded-full bg-blue-950 text-white text-sm font-semibold flex items-center justify-center flex-shrink-0">{{ $i + 1 }}</span>
                    <p class="font-semibold text-gray-900 pt-0.5">{{ $pregunta->enunciado }}</p>
                </div>

                <div class="mt-3 space-y-2 sm:ml-10">
                    @foreach($pregunta->opciones->shuffle() as $opcion)
                        <label class="flex items-center gap-3 text-sm p-3 rounded-md border border-gray-200 cursor-pointer transition hover:border-amber-400 hover:bg-amber-50 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $opcion->id }}" required
                                   class="w-4 h-4 text-blue-800 focus:ring-blue-700 flex-shrink-0">
                            <span>{{ $opcion->texto }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="w-full py-3 rounded-md font-semibold btn-gold"
                onclick="return confirm('¿Enviar tus respuestas? No podrás cambiarlas después.');">
            Enviar respuestas
        </button>
    </form>
</div>
@endsection
