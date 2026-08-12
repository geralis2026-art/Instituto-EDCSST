<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Capacitado;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * Prueba primero el broker "users" (empleados); si el token/correo no
     * corresponde a un empleado, prueba el broker "capacitados" (aula
     * virtual). Al restablecer así (voluntariamente, vía "olvidé mi
     * contraseña"), se limpia `debe_cambiar_password` porque ya eligieron
     * su propia contraseña.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Si el correo pertenece a un capacitado, su nueva contraseña no puede
        // ser su número de documento (misma regla que el cambio obligatorio
        // del primer ingreso, ver PasswordCambioController). Se valida antes
        // de intentar el broker "users" porque un mismo correo nunca debería
        // coincidir con ambos, pero así queda garantizado en cualquier caso.
        $capacitadoPorCorreo = Capacitado::where('correo', $request->input('email'))->first();

        if ($capacitadoPorCorreo && $request->input('password') === $capacitadoPorCorreo->documento) {
            throw ValidationException::withMessages([
                'password' => 'La contraseña no puede ser tu número de documento.',
            ]);
        }

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            $status = Password::broker('capacitados')->reset(
                [
                    'correo' => $request->input('email'),
                    'password' => $request->input('password'),
                    'password_confirmation' => $request->input('password_confirmation'),
                    'token' => $request->input('token'),
                ],
                function (Capacitado $capacitado) use ($request) {
                    $capacitado->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                        'debe_cambiar_password' => false,
                    ])->save();

                    event(new PasswordReset($capacitado));
                }
            );
        }

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
