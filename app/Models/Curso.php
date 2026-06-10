<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Programa de capacitación ofrecido por el instituto.
 *
 * `intensidad_horaria` es el valor oficial de horas del curso; se copia
 * a cada certificado emitido para preservar el dato aunque el curso
 * cambie después.
 */
class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'slug',
        'descripcion_corta',
        'duracion',
        'intensidad_horaria',
        'imagen',
        'destacado',
        'activo',
    ];

    protected $appends = ['imagen_url'];

    protected $casts = [
        'destacado' => 'boolean',
        'activo' => 'boolean',
        'intensidad_horaria' => 'integer',
    ];

    /**
     * Genera el slug automáticamente al guardar.
     */
    protected static function booted(): void
    {
        static::saving(function ($curso) {
            if (empty($curso->slug)) {
                $curso->slug = Str::slug($curso->nombre);
            }
        });
    }

    /** Categoría a la que pertenece el curso. */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /** Certificados emitidos para este curso. */
    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    /**
     * Capacitados que han recibido certificado de este curso.
     */
    public function capacitados(): BelongsToMany
    {
        return $this->belongsToMany(Capacitado::class, 'certificados')
                    ->withPivot('codigo_unico', 'fecha_emision', 'intensidad_horaria', 'activo')
                    ->withTimestamps();
    }

    /** Cursos activos. */
    public function scopeActivos(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('activo', true);
    }

    /** Cursos activos marcados como destacados (se muestran en el home). */
    public function scopeDestacados(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('destacado', true)->where('activo', true);
    }

    /**
     * Accessor para la URL de la imagen.
     */
    public function getImagenUrlAttribute(): string
    {
        if (!$this->imagen) {
            return asset('img/curso-default.svg');
        }

        [$type, $filename] = explode('/', $this->imagen, 2);

        return route('uploads.serve', compact('type', 'filename'));
    }
}
