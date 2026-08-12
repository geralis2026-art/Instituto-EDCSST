<?php

namespace App\Http\Controllers\Aula;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Cambio obligatorio de contraseña en el primer ingreso del capacitado al
 * aula virtual (ver EnsureCapacitadoNoDebeCambiarPassword). La contraseña
 * nueva no puede ser igual al número de documento del capacitado.
 */
class PasswordCambioController extends Controller
{
    public function edit(): View
    {
        return view('aula.password.cambiar');
    }

    public function update(Request $request): RedirectResponse
    {
        $capacitado = Auth::guard('capacitados')->user();

        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
                function ($attribute, $value, $fail) use ($capacitado) {
                    if ($value === $capacitado->documento) {
                        $fail('La contraseña no puede ser tu número de documento.');
                    }
                },
            ],
        ]);

        $capacitado->forceFill([
            'password' => Hash::make($request->string('password')),
            'debe_cambiar_password' => false,
        ])->save();

        return redirect()->route('aula.dashboard')->with('success', 'Contraseña actualizada correctamente.');
    }
}
