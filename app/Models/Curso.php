<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

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

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true)->where('activo', true);
    }

    /**
     * Accessor para la URL de la imagen.
     */
    public function getImagenUrlAttribute(): string
    {
        return $this->imagen
            ? asset('storage/' . $this->imagen)
            : asset('img/curso-default.png');
    }
}
