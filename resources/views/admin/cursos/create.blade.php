@extends('layouts.admin')

@section('titulo', 'Nuevo curso')
@section('titulo_topbar', 'Cursos')

@section('contenido')
<div class="max-w-3xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Nuevo curso" subtitulo="Registra un curso para el catálogo y la emisión de certificados." :volver="route('admin.cursos.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        @include('admin.cursos._form')
    </div>
</div>
@endsection
