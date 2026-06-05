@extends('layouts.admin')

@section('titulo', 'Ver mensaje')
@section('titulo_topbar', 'Detalle del mensaje')

@section('contenido')

{{-- MVP: Vista de detalle de mensaje comentada temporalmente --}}
{{--
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('admin.mensajes.index') }}" ...>Volver a mensajes</a>
    ... (detalle y gestión del mensaje) ...
</div>
--}}

<div class="flex items-center justify-center min-h-[50vh]">
    <div class="text-center space-y-3">
        <p class="text-gray-500 text-sm">Módulo de mensajes en construcción</p>
        <a href="{{ route('admin.mensajes.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
            ← Volver
        </a>
    </div>
</div>

@endsection
