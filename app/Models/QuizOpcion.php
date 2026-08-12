<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOpcion extends Model
{
    use HasFactory;

    protected $table = 'quiz_opciones';

    protected $fillable = [
        'pregunta_id',
        'texto',
        'es_correcta',
        'orden',
    ];

    protected $casts = [
        'es_correcta' => 'boolean',
        'orden'       => 'integer',
    ];

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(QuizPregunta::class, 'pregunta_id');
    }
}
