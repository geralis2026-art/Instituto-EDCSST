@extends('layouts.admin')

@section('titulo', 'Nueva categoría')
@section('titulo_topbar', 'Categorías')

@section('contenido')
<div class="max-w-2xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Nueva categoría" subtitulo="Agrupa los cursos para organizar el catálogo." :volver="route('admin.categorias.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        @include('admin.categorias._form')
    </div>
</div>
@endsection
