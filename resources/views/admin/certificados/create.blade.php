@extends('layouts.admin')

@section('titulo', 'Registrar certificado')
@section('titulo_topbar', 'Certificados')

@section('contenido')
<div class="max-w-3xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Registrar certificado" subtitulo="Carga el PDF ya generado y asócialo a un capacitado y a un curso." :volver="route('admin.certificados.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        @include('admin.certificados._form')
    </div>
</div>
@endsection
