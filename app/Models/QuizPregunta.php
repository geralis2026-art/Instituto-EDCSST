<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizPregunta extends Model
{
    use HasFactory;

    protected $table = 'quiz_preguntas';

    public const TIPOS = [
        'seleccion_multiple' => 'Selección múltiple',
        'verdadero_falso'    => 'Verdadero / Falso',
    ];

    protected $fillable = [
        'quiz_id',
        'enunciado',
        'tipo',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(QuizOpcion::class, 'pregunta_id')->orderBy('orden');
    }
}
