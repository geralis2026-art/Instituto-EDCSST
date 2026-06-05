@extends('layouts.admin')

@section('titulo', 'Registrar Certificado')
@section('titulo_topbar', 'Certificados')

@section('contenido')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.certificados.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
            <span>&larr;</span>
            Volver al listado
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Registrar Certificado</h1>
        <p class="text-gray-600 mt-2">Carga el PDF ya generado y asocialo a un capacitado y curso.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        @include('admin.certificados._form')
    </div>
</div>
@endsection
