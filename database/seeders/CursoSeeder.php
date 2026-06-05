<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Curso;
use Illuminate\Database\Seeder;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        $salud = Categoria::where('nombre', 'Salud y Seguridad')->first();
        $sistemas = Categoria::where('nombre', 'Sistemas')->first();
        $idiomas = Categoria::where('nombre', 'Idiomas')->first();

        $cursos = [
            [
                'categoria_id' => $salud->id,
                'nombre' => 'Curso de SST nivel básico',
                'descripcion_corta' => 'Fundamentos de seguridad y salud en el trabajo para todos los trabajadores.',
                'duracion' => '50 horas',
                'intensidad_horaria' => 50,
                'destacado' => true,
                'activo' => true,
            ],
            [
                'categoria_id' => $salud->id,
                'nombre' => 'Trabajo en alturas',
                'descripcion_corta' => 'Curso certificado para trabajo seguro en alturas según resolución vigente.',
                'duracion' => '40 horas',
                'intensidad_horaria' => 40,
                'destacado' => true,
                'activo' => true,
            ],
            [
                'categoria_id' => $sistemas->id,
                'nombre' => 'Excel Intermedio',
                'descripcion_corta' => 'Domina funciones, tablas dinámicas y herramientas avanzadas de Excel.',
                'duracion' => '30 horas',
                'intensidad_horaria' => 30,
                'destacado' => true,
                'activo' => true,
            ],
            [
                'categoria_id' => $idiomas->id,
                'nombre' => 'Inglés conversacional A1-A2',
                'descripcion_corta' => 'Bases del inglés conversacional para nivel principiante.',
                'duracion' => '60 horas',
                'intensidad_horaria' => 60,
                'destacado' => false,
                'activo' => true,
            ],
        ];

        foreach ($cursos as $curso) {
            Curso::firstOrCreate(
                ['nombre' => $curso['nombre']],
                $curso
            );
        }
    }
}
