<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition(): array
    {
        $nombre = fake()->unique()->words(4, true);

        return [
            'categoria_id'       => Categoria::factory(),
            'nombre'             => ucfirst($nombre),
            'slug'               => Str::slug($nombre),
            'descripcion_corta'  => fake()->sentence(10),
            'duracion'           => fake()->randomElement(['8 horas', '16 horas', '40 horas', '80 horas']),
            'intensidad_horaria' => fake()->numberBetween(8, 120),
            'imagen'             => null,
            'destacado'          => false,
            'activo'             => true,
        ];
    }

    public function destacado(): static
    {
        return $this->state(['destacado' => true, 'activo' => true]);
    }

    public function inactivo(): static
    {
        return $this->state(['activo' => false]);
    }
}
