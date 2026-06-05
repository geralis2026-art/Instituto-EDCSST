<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta los seeders del proyecto.
     */
    public function run(): void
    {
        $this->call([
            ConfiguracionSitioSeeder::class,
            UserSeeder::class,
            CategoriaSeeder::class,
            CursoSeeder::class,
            CapacitadoSeeder::class,
        ]);
    }
}
