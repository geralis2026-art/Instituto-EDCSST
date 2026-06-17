<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

/** Validación para crear un nuevo usuario (empleado). Solo admin (ver authorize()). */
class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol'      => ['required', 'in:admin,capacitador'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre es requerido.',
            'email.required'     => 'El correo electrónico es requerido.',
            'email.unique'       => 'Ya existe un usuario con este correo electrónico.',
            'password.required'  => 'La contraseña es requerida.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol.required'       => 'El rol es requerido.',
            'rol.in'             => 'El rol debe ser admin o capacitador.',
        ];
    }
}
