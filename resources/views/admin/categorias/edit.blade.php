@extends('layouts.admin')

@section('titulo', 'Editar categoría')
@section('titulo_topbar', 'Categorías')

@section('contenido')
<div class="max-w-2xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Editar categoría" :subtitulo="'Actualiza la información de ' . $categoria->nombre" :volver="route('admin.categorias.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        @include('admin.categorias._form', ['categoria' => $categoria])
    </div>
</div>
@endsection
