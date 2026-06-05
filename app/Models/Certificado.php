<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificados';

    protected $fillable = [
        'capacitado_id',
        'curso_id',
        'emitido_por',
        'codigo_unico',
        'fecha_emision',
        'intensidad_horaria',
        'archivo_pdf',
        'activo',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'intensidad_horaria' => 'integer',
        'activo' => 'boolean',
    ];

    public function capacitado(): BelongsTo
    {
        return $this->belongsTo(Capacitado::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function emitidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitido_por');
    }

    /**
     * Genera un código único para el certificado.
     * Formato: EDCSST-{AÑO}-{ID_5_DIGITOS}
     * Ej: EDCSST-2026-00001
     */
    public static function generarCodigoUnico(): string
    {
        $anio = now()->year;
        $ultimo = static::whereYear('created_at', $anio)
            ->orderBy('id', 'desc')
            ->first();

        $siguiente = $ultimo ? $ultimo->id + 1 : 1;
        return sprintf('EDCSST-%d-%05d', $anio, $siguiente);
    }

    /**
     * Buscar certificado por su código único.
     */
    public static function porCodigo(string $codigo): ?self
    {
        return static::where('codigo_unico', trim(strtoupper($codigo)))
            ->where('activo', true)
            ->first();
    }

    /**
     * URL del PDF para descarga.
     */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->archivo_pdf
            ? asset('storage/' . $this->archivo_pdf)
            : null;
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Cuando se elimina o desactiva un certificado, recalcular las horas del capacitado.
     */
    protected static function booted(): void
    {
        static::saved(function ($certificado) {
            $certificado->capacitado?->recalcularHorasCapacitadas();
        });

        static::deleted(function ($certificado) {
            $certificado->capacitado?->recalcularHorasCapacitadas();
        });
    }
}
