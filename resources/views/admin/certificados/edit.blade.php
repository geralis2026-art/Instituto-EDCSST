@extends('layouts.admin')

@section('titulo', 'Editar certificado')
@section('titulo_topbar', 'Certificados')

@section('contenido')
<div class="max-w-3xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Editar certificado" :subtitulo="'Actualiza el registro ' . $certificado->codigo_unico" :volver="route('admin.certificados.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        @include('admin.certificados._form', ['certificado' => $certificado])
    </div>
</div>
@endsection
