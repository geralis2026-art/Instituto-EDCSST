<?php

namespace Database\Seeders;

use App\Models\ConfiguracionSitio;
use Illuminate\Database\Seeder;

class ConfiguracionSitioSeeder extends Seeder
{
    public function run(): void
    {
        ConfiguracionSitio::firstOrCreate(
            ['id' => 1],
            [
                'nombre_instituto' => 'Instituto EDCSST',
                'descripcion' => 'Instituto de certificaciones y capacitaciones profesionales',
                'telefono' => '+57 321 2173463',
                'correo_contacto' => 'academiasstcolombiana@gmail.com',
                'direccion' => 'Villavicencio, Meta - Colombia',
                'whatsapp' => '573212173463',
                'facebook' => 'https://facebook.com/edcsst',
                'instagram' => 'https://instagram.com/edcsst',
            ]
        );
    }
}
