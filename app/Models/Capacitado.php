<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Capacitado extends Model
{
    use HasFactory;

    protected $table = 'capacitados';

    protected $fillable = [
        'nombre_completo',
        'documento',
        'correo',
        'telefono',
        'horas_capacitadas',
    ];

    protected $casts = [
        'horas_capacitadas' => 'integer',
    ];

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    /**
     * Cursos que ha tomado el capacitado (vía certificados).
     */
    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'certificados')
                    ->withPivot('codigo_unico', 'fecha_emision', 'intensidad_horaria', 'activo')
                    ->withTimestamps();
    }

    /**
     * Recalcula las horas capacitadas totales sumando los certificados activos.
     */
    public function recalcularHorasCapacitadas(): void
    {
        $total = $this->certificados()
            ->where('activo', true)
            ->sum('intensidad_horaria');

        $this->update(['horas_capacitadas' => $total]);
    }

    /**
     * Buscar capacitado por número de documento.
     */
    public static function porDocumento(string $documento): ?self
    {
        return static::where('documento', trim($documento))->first();
    }
}
