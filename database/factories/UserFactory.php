<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'rol'               => User::ROL_ADMIN,
            'activo'            => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(['rol' => User::ROL_ADMIN, 'activo' => true]);
    }

    public function capacitador(): static
    {
        return $this->state(['rol' => User::ROL_CAPACITADOR, 'activo' => true]);
    }

    public function inactivo(): static
    {
        return $this->state(['activo' => false]);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}
