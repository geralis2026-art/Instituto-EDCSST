<?php

namespace Database\Seeders;

use App\Models\Capacitado;
use Illuminate\Database\Seeder;

class CapacitadoSeeder extends Seeder
{
    public function run(): void
    {
        $capacitados = [
            [
                'nombre_completo' => 'Juan Pérez García',
                'tipo_documento' => 'CC',
                'documento' => '1000000001',
                'correo' => 'juan.perez@example.com',
                'telefono' => '3001234567',
            ],
            [
                'nombre_completo' => 'María Rodríguez López',
                'tipo_documento' => 'CC',
                'documento' => '1000000002',
                'correo' => 'maria.rodriguez@example.com',
                'telefono' => '3007654321',
            ],
            [
                'nombre_completo' => 'Carlos Gómez Martínez',
                'tipo_documento' => 'CC',
                'documento' => '1000000003',
                'correo' => 'carlos.gomez@example.com',
                'telefono' => '3009876543',
            ],
        ];

        foreach ($capacitados as $cap) {
            Capacitado::firstOrCreate(
                ['documento' => $cap['documento']],
                $cap
            );
        }
    }
}
