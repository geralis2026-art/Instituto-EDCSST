<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Prueba primero el guard "web" (empleados). Si no coincide, prueba el
     * guard "capacitados" (aula virtual) usando el mismo correo/contraseña
     * pero mapeado a la columna "correo" de ese modelo. Así queda una sola
     * página de login para ambos tipos de cuenta.
     *
     * El guard "capacitados" solo se intenta si el aula virtual está
     * habilitada (módulo de Fase 3 aún no pagado, ver config/features.php);
     * si no, ni siquiera se prueba, para no dejar entrar a un portal que de
     * todas formas tiene sus rutas bloqueadas.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $email = $this->string('email')->toString();
        $password = $this->string('password')->toString();
        $remember = $this->boolean('remember');

        $autenticado = Auth::guard('web')->attempt(['email' => $email, 'password' => $password, 'activo' => true], $remember)
            || (config('features.aula_virtual') && Auth::guard('capacitados')->attempt(['correo' => $email, 'password' => $password], $remember));

        if (! $autenticado) {
            RateLimiter::hit($this->throttleKey());
            RateLimiter::hit('login-email:'.Str::lower($this->string('email')), 60);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear('login-email:'.Str::lower($this->string('email')));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5) &&
            ! RateLimiter::tooManyAttempts('login-email:'.Str::lower($this->string('email')), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = max(
            RateLimiter::availableIn($this->throttleKey()),
            RateLimiter::availableIn('login-email:'.Str::lower($this->string('email')))
        );

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
