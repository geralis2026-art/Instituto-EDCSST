<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    public static function obtener(): self
    {
        $attrs = Cache::rememberForever('configuracion_sitio', function () {
            return static::firstOrCreate(
                ['id' => 1],
                ['nombre_instituto' => 'Instituto EDCSST']
            )->getAttributes();
        });

        $model = (new self)->forceFill($attrs);
        $model->exists = true;

        return $model->syncOriginal();
    }

    public static function invalidarCache(): void
    {
        Cache::forget('configuracion_sitio');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::invalidarCache());
    }

    /** URL del logo; null si no hay logo configurado. */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo || !str_contains($this->logo, '/')) {
            return null;
        }

        [$type, $filename] = explode('/', $this->logo, 2);

        return route('uploads.serve', compact('type', 'filename'));
    }
}
