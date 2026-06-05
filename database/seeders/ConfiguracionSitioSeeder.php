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
                'telefono' => '+57 300 000 0000',
                'correo_contacto' => 'contacto@edcsst.com',
                'direccion' => 'Bogotá, Colombia',
                'whatsapp' => '+573000000000',
                'facebook' => 'https://facebook.com/edcsst',
                'instagram' => 'https://instagram.com/edcsst',
            ]
        );
    }
}
