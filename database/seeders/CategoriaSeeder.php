<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Salud y Seguridad', 'descripcion' => 'Cursos relacionados con SST y salud ocupacional'],
            ['nombre' => 'Sistemas', 'descripcion' => 'Cursos de tecnología e informática'],
            ['nombre' => 'Idiomas', 'descripcion' => 'Cursos de idiomas extranjeros'],
            ['nombre' => 'Administración', 'descripcion' => 'Cursos administrativos y gerenciales'],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(
                ['nombre' => $cat['nombre']],
                $cat
            );
        }
    }
}
