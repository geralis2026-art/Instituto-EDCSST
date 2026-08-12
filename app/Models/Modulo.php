<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Módulo en el que se organiza el contenido de un curso con aula virtual.
 */
class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';

    protected $fillable = [
        'curso_id',
        'titulo',
        'descripcion',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden'  => 'integer',
        'activo' => 'boolean',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(Material::class)->orderBy('orden');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
