<?php

namespace Database\Factories;

use App\Models\Capacitado;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificadoFactory extends Factory
{
    protected $model = Certificado::class;

    public function definition(): array
    {
        $emision     = fake()->dateTimeBetween('-1 year', '-1 month');
        $vencimiento = (clone $emision)->modify('+1 year');

        return [
            'capacitado_id'      => Capacitado::factory(),
            'curso_id'           => Curso::factory(),
            'emitido_por'        => User::factory()->admin(),
            'codigo_unico'       => sprintf('EDCSST-%d-%05d', now()->year, fake()->unique()->numberBetween(1, 99999)),
            'fecha_emision'      => $emision->format('Y-m-d'),
            'fecha_vencimiento'  => $vencimiento->format('Y-m-d'),
            'intensidad_horaria' => fake()->numberBetween(8, 120),
            'modalidad'          => fake()->randomElement(['presencial', 'virtual']),
            'archivo_pdf'        => null,
            'activo'             => true,
        ];
    }

    public function vencido(): static
    {
        return $this->state(function () {
            $emision     = fake()->dateTimeBetween('-3 years', '-13 months');
            $vencimiento = (clone $emision)->modify('+1 year');

            return [
                'fecha_emision'     => $emision->format('Y-m-d'),
                'fecha_vencimiento' => $vencimiento->format('Y-m-d'),
            ];
        });
    }

    public function inactivo(): static
    {
        return $this->state(['activo' => false]);
    }
}
