@extends('layouts.admin')

@section('titulo', 'Crear Nuevo Capacitado')

@section('contenido')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.capacitados.index') }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al listado
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Registrar Nuevo Capacitado</h1>
        <p class="text-gray-600 mt-2">Complete el formulario para agregar una nueva persona a la base de datos.</p>
    </div>

    {{-- Tarjeta del Formulario --}}
    <div class="bg-white rounded-lg shadow p-8">
        @include('admin.capacitados._form')
    </div>
</div>
@endsection
