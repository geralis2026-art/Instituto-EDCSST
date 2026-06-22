<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Agrupación temática de cursos (ej: "Alturas", "Espacios confinados").
 */
class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /** Genera el slug automáticamente al guardar si no viene uno explícito. */
    protected static function booted(): void
    {
        static::saving(function ($categoria) {
            if (empty($categoria->slug)) {
                $base = Str::slug($categoria->nombre);
                $slug = $base;
                $i    = 1;

                while (
                    static::where('slug', $slug)
                        ->when($categoria->exists, fn ($q) => $q->where('id', '!=', $categoria->id))
                        ->exists()
                ) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }

                $categoria->slug = $slug;
            }
        });
    }

    /** Cursos que pertenecen a esta categoría. */
    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }

    /** Categorías activas. */
    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
