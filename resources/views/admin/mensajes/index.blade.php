@extends('layouts.admin')

@section('titulo', 'Mensajes')
@section('titulo_topbar', 'Mensajes de contacto')

@section('contenido')

{{-- MVP: Vista de mensajes comentada temporalmente --}}
{{--
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mensajes de contacto</h1>
            <p class="text-sm text-gray-500 mt-0.5">Mensajes recibidos desde el formulario público</p>
        </div>
    </div>
    ... (tabla de mensajes) ...
</div>
--}}

<div class="flex items-center justify-center min-h-[50vh]">
    <div class="text-center space-y-3">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-gray-500 text-sm">Módulo de mensajes en construcción</p>
    </div>
</div>

@endsection
