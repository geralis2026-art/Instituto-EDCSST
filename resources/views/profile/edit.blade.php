@extends('layouts.admin')

@section('titulo', 'Mi perfil')
@section('titulo_topbar', 'Mi perfil')

@section('contenido')
<div class="max-w-3xl space-y-6">
    <x-admin.encabezado titulo="Mi perfil" subtitulo="Actualiza tus datos de acceso y tu contraseña." />

    <div class="tarjeta-admin p-6 sm:p-8">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="tarjeta-admin p-6 sm:p-8">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="tarjeta-admin p-6 sm:p-8 border-red-200">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
