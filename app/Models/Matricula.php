<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Asigna un curso con aula virtual a un capacitado. El contenido del curso
 * es el mismo para todos los matriculados; esta tabla solo determina quién
 * tiene acceso a qué curso y su avance individual.
 */
class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';

    protected $fillable = [
        'curso_id',
        'capacitado_id',
        'fecha_asignacion',
        'completado',
        'fecha_completado',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'completado'       => 'boolean',
        'fecha_completado' => 'date',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function capacitado(): BelongsTo
    {
        return $this->belongsTo(Capacitado::class);
    }

    public function progresoModulos(): HasMany
    {
        return $this->hasMany(ProgresoModulo::class);
    }

    public function quizIntentos(): HasMany
    {
        return $this->hasMany(QuizIntento::class);
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('completado', false);
    }

    /**
     * Porcentaje de módulos completados (0-100) sobre el total de módulos
     * activos del curso. Si `curso.modulos` y `progresoModulos` ya vienen
     * eager-loaded (ver AulaDashboardController), usa las colecciones en
     * memoria en vez de lanzar 2 consultas nuevas por cada matrícula.
     */
    public function getPorcentajeAvanceAttribute(): int
    {
        $totalModulos = $this->relationLoaded('curso') && $this->curso->relationLoaded('modulos')
            ? $this->curso->modulos->count()
            : $this->curso->modulos()->activos()->count();

        if ($totalModulos === 0) {
            return 0;
        }

        $completados = $this->relationLoaded('progresoModulos')
            ? $this->progresoModulos->whereNotNull('completado_en')->count()
            : $this->progresoModulos()->whereNotNull('completado_en')->count();

        return (int) round(($completados / $totalModulos) * 100);
    }

    /** Cuántos intentos de quiz ya ha usado el capacitado en esta matrícula. */
    public function getIntentosUsadosAttribute(): int
    {
        return $this->quizIntentos()->count();
    }
}
