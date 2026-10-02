@extends('layouts.admin')

@section('titulo', 'Editar capacitado')
@section('titulo_topbar', 'Capacitados')

@section('contenido')
<div class="max-w-2xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Editar capacitado" :subtitulo="'Actualiza los datos de ' . $capacitado->nombre_completo" :volver="route('admin.capacitados.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        @include('admin.capacitados._form', ['capacitado' => $capacitado])
    </div>
</div>
@endsection
