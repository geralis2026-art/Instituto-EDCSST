<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Marca que un capacitado completó un módulo dentro de su matrícula.
 */
class ProgresoModulo extends Model
{
    use HasFactory;

    protected $table = 'progreso_modulos';

    protected $fillable = [
        'matricula_id',
        'modulo_id',
        'completado_en',
    ];

    protected $casts = [
        'completado_en' => 'datetime',
    ];

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }
}
