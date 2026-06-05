@extends('layouts.admin')

@section('titulo', 'Dashboard')
@section('titulo_topbar', 'Dashboard')

@section('contenido')

{{-- MVP: Dashboard comentado temporalmente --}}
{{--
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Panel de control</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.capacitados.create') }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm">
                Capacitado
            </a>
            <a href="{{ route('admin.certificados.create') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-sm">
                Certificado
            </a>
        </div>
    </div>

    ... (resto del dashboard) ...

</div>
--}}

<div class="flex items-center justify-center min-h-[50vh]">
    <div class="text-center space-y-4">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </div>
        <p class="text-gray-500 text-sm">Dashboard en construcción</p>
        <div class="flex gap-3 justify-center">
            <a href="{{ route('admin.capacitados.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                Capacitados
            </a>
            <a href="{{ route('admin.certificados.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                Certificados
            </a>
        </div>
    </div>
</div>

@endsection
