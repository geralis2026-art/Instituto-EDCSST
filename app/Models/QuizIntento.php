<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Un intento de un capacitado sobre el quiz de su matrícula. `respuestas`
 * guarda un snapshot en json (orden aleatorio de preguntas y lo que
 * respondió) para poder auditar el intento después.
 */
class QuizIntento extends Model
{
    use HasFactory;

    protected $table = 'quiz_intentos';

    protected $fillable = [
        'quiz_id',
        'matricula_id',
        'numero_intento',
        'nota_obtenida',
        'aprobado',
        'respuestas',
        'iniciado_en',
        'finalizado_en',
    ];

    protected $casts = [
        'numero_intento' => 'integer',
        'nota_obtenida'  => 'decimal:2',
        'aprobado'       => 'boolean',
        'respuestas'     => 'array',
        'iniciado_en'    => 'datetime',
        'finalizado_en'  => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    /** Certificado generado automáticamente a partir de este intento, si aplica. */
    public function certificado(): HasOne
    {
        return $this->hasOne(Certificado::class);
    }
}
