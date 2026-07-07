<?php

namespace Database\Factories;

use App\Models\Capacitado;
use Illuminate\Database\Eloquent\Factories\Factory;

class CapacitadoFactory extends Factory
{
    protected $model = Capacitado::class;

    public function definition(): array
    {
        return [
            'nombre_completo'   => fake()->name(),
            'tipo_documento'    => fake()->randomElement(array_keys(Capacitado::TIPOS_DOCUMENTO)),
            'documento'         => fake()->unique()->numerify('##########'),
            'correo'            => fake()->unique()->safeEmail(),
            'telefono'          => fake()->numerify('3##########'),
            'rh'                => fake()->randomElement(['A+', 'B+', 'O+', 'AB+']),
            'horas_capacitadas' => 0,
        ];
    }
}
