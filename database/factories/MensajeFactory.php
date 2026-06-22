<?php

namespace Database\Factories;

use App\Models\Mensaje;
use Illuminate\Database\Eloquent\Factories\Factory;

class MensajeFactory extends Factory
{
    protected $model = Mensaje::class;

    public function definition(): array
    {
        return [
            'nombre'         => fake()->name(),
            'correo'         => fake()->safeEmail(),
            'mensaje'        => fake()->paragraph(),
            'estado'         => Mensaje::ESTADO_NUEVO,
            'notas_internas' => null,
            'ip'             => fake()->ipv4(),
        ];
    }

    public function leido(): static
    {
        return $this->state(['estado' => Mensaje::ESTADO_LEIDO]);
    }

    public function respondido(): static
    {
        return $this->state(['estado' => Mensaje::ESTADO_RESPONDIDO]);
    }
}
