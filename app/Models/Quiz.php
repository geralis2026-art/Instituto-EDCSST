<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Quiz de validación de un curso con aula virtual. Al aprobarlo (nota >=
 * nota_minima) se dispara la generación automática del certificado.
 */
class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';

    protected $fillable = [
        'curso_id',
        'nota_minima',
        'intentos_maximos',
        'activo',
    ];

    protected $casts = [
        'nota_minima'      => 'decimal:2',
        'intentos_maximos' => 'integer',
        'activo'           => 'boolean',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(QuizPregunta::class)->orderBy('orden');
    }

    public function intentos(): HasMany
    {
        return $this->hasMany(QuizIntento::class);
    }
}
