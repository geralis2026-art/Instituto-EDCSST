@extends('layouts.admin')

@section('titulo', 'Crear Curso')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.cursos.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
            <span>&larr;</span>
            Volver al listado
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Crear Curso</h1>
        <p class="text-gray-600 mt-2">Registra un curso para el catalogo y emision de certificados.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        @include('admin.cursos._form')
    </div>
</div>
@endsection
