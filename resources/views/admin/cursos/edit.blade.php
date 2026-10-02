@extends('layouts.admin')

@section('titulo', 'Editar curso')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-3xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Editar curso" :subtitulo="'Actualiza la información de ' . $curso->nombre" :volver="route('admin.cursos.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        @include('admin.cursos._form', ['curso' => $curso, 'categorias' => $categorias])
    </div>
</div>
@endsection
