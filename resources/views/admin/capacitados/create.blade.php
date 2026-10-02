@extends('layouts.admin')

@section('titulo', 'Nuevo capacitado')
@section('titulo_topbar', 'Capacitados')

@section('contenido')
<div class="max-w-2xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Nuevo capacitado" subtitulo="Agrega una persona a la base de datos." :volver="route('admin.capacitados.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        @include('admin.capacitados._form')
    </div>
</div>
@endsection
