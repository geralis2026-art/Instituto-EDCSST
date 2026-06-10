<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Configuración global del sitio (datos institucionales, redes sociales,
 * logo y plantilla de certificado). Es una tabla "singleton": siempre
 * existe una única fila con id = 1, accedida vía obtener().
 */
class ConfiguracionSitio extends Model
{
    protected $table = 'configuracion_sitio';

    protected $fillable = [
        'nombre_instituto',
        'descripcion',
        'telefono',
        'correo_contacto',
        'direccion',
        'whatsapp',
        'facebook',
        'instagram',
        'logo',
        'plantilla_certificado',
    ];

    /**
     * Obtiene la configuración del sitio (singleton).
     * Si no existe, la crea con valores por defecto.
     */
    public static function obtener(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            ['nombre_instituto' => 'Instituto EDCSST']
        );
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        [$type, $filename] = explode('/', $this->logo, 2);

        return route('uploads.serve', compact('type', 'filename'));
    }

    public function getPlantillaUrlAttribute(): ?string
    {
        return $this->plantilla_certificado
            ? storage_path('app/public/' . $this->plantilla_certificado)
            : null;
    }
}
