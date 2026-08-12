@extends('layouts.admin')

@section('titulo', 'Editar Pregunta')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('admin.cursos.quiz.edit', $curso) }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2">
        <span>&larr;</span> Volver al quiz de {{ $curso->nombre }}
    </a>

    <h1 class="text-2xl font-bold text-gray-900">Editar pregunta</h1>

    @include('admin.quiz.preguntas._form', [
        'action' => route('admin.cursos.quiz.preguntas.update', [$curso, $pregunta]),
        'pregunta' => $pregunta,
    ])
</div>
@endsection
