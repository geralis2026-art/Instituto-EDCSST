@extends('layouts.admin')

@section('titulo', 'Crear Categoria')
@section('titulo_topbar', 'Categorias')

@section('contenido')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.categorias.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
            <span>&larr;</span>
            Volver al listado
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Crear Categoria</h1>
        <p class="text-gray-600 mt-2">Agrupa los cursos para organizar el catalogo.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        @include('admin.categorias._form')
    </div>
</div>
@endsection
