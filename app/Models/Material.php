<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Material de estudio de un módulo: presentación, taller, documento o video.
 * `archivo` para material subido al servidor, `url` para videos externos.
 */
class Material extends Model
{
    use HasFactory;

    protected $table = 'materiales';

    public const TIPOS = [
        'presentacion' => 'Presentación',
        'taller'       => 'Taller',
        'documento'    => 'Documento',
        'video'        => 'Video',
    ];

    protected $fillable = [
        'modulo_id',
        'titulo',
        'tipo',
        'archivo',
        'url',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }
}
