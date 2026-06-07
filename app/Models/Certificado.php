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
        'fecha_vencimiento',
        'intensidad_horaria',
        'modalidad',
        'archivo_pdf',
        'activo',
    ];

    protected $casts = [
        'fecha_emision'      => 'date',
        'fecha_vencimiento'  => 'date',
        'intensidad_horaria' => 'integer',
        'activo'             => 'boolean',
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
    public static function generarCodigoUnico(int $id): string
    {
        return sprintf('EDCSST-%d-%05d', now()->year, $id);
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
            ? route('admin.certificados.pdf', $this)
            : null;
    }

    /** Verdadero si la fecha de vencimiento ya pasó. */
    public function isVencido(): bool
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }

    /** Certificados marcados como activos (no invalidados). */
    public function scopeActivos(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('activo', true);
    }

    /** Certificados activos y cuya fecha de vencimiento no ha llegado. */
    public function scopeVigentes(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('activo', true)
                     ->where('fecha_vencimiento', '>=', now()->toDateString());
    }

    /** Certificados cuya fecha de vencimiento ya pasó (independientemente de si están activos). */
    public function scopeVencidos(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('fecha_vencimiento', '<', now()->toDateString());
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
