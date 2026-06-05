<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    /**
     * Genera el slug automáticamente al guardar.
     */
    protected static function booted(): void
    {
        static::saving(function ($categoria) {
            if (empty($categoria->slug)) {
                $categoria->slug = Str::slug($categoria->nombre);
            }
        });
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }
}
