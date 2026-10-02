@extends('layouts.admin')

@section('titulo', 'Nuevo Usuario')
@section('titulo_topbar', 'Usuarios')

@section('contenido')
<div class="max-w-xl mx-auto space-y-6">
    <x-admin.encabezado titulo="Nuevo usuario" subtitulo="El usuario quedará inactivo hasta que lo actives manualmente." :volver="route('admin.usuarios.index')" />

    <div class="tarjeta-admin p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.usuarios.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="etiqueta-admin" for="campo-name">Nombre</label>
                <input id="campo-name" type="text" name="name" value="{{ old('name') }}" required
                    class="campo-admin @error('name') border-red-500 @enderror">
                @error('name') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="etiqueta-admin" for="campo-email">Email</label>
                <input id="campo-email" type="email" name="email" value="{{ old('email') }}" required
                    class="campo-admin @error('email') border-red-500 @enderror">
                @error('email') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="etiqueta-admin" for="campo-password">Contraseña</label>
                <input id="campo-password" type="password" name="password" required
                    class="campo-admin @error('password') border-red-500 @enderror">
                @error('password') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="etiqueta-admin" for="campo-password_confirmation">Confirmar contraseña</label>
                <input id="campo-password_confirmation" type="password" name="password_confirmation" required
                    class="campo-admin">
            </div>

            <div>
                <label class="etiqueta-admin" for="campo-rol">Rol</label>
                <select id="campo-rol" name="rol" required
                    class="campo-admin @error('rol') border-red-500 @enderror">
                    <option value="capacitador" @selected(old('rol') === 'capacitador')>Capacitador — solo subir certificados</option>
                    <option value="instructor"  @selected(old('rol') === 'instructor')>Instructor — gestiona solo sus propios cursos, capacitados y certificados</option>
                    <option value="admin"       @selected(old('rol') === 'admin')>Administrador — acceso completo</option>
                </select>
                @error('rol') <p class="text-red-700 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('admin.usuarios.index') }}" class="btn-secundario">Cancelar</a>
                <button type="submit" class="btn-primario">Crear usuario</button>
            </div>
        </form>
    </div>
</div>
@endsection
