<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Certificado emitido a un capacitado por haber completado un curso.
 *
 * Al guardarse o eliminarse, recalcula automáticamente las horas
 * capacitadas acumuladas del capacitado asociado (ver booted()).
 */
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

    /** Persona que recibió el certificado. */
    public function capacitado(): BelongsTo
    {
        return $this->belongsTo(Capacitado::class);
    }

    /** Curso por el cual se emitió el certificado. */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /** Empleado (admin/capacitador) que emitió el certificado. */
    public function emitidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitido_por');
    }

    /**
     * Genera un código único para el certificado.
     * Formato: EDCSST-{AÑO}-{NUMERO_5_DIGITOS}
     * Ej: EDCSST-2026-00001
     *
     * El número se calcula a partir del más alto ya usado en el año (incluyendo
     * códigos cargados manualmente), para evitar choques con códigos atrasados.
     */
    public static function generarCodigoUnico(): string
    {
        $anio    = now()->year;
        $prefijo = "EDCSST-{$anio}-";

        $offset = strlen($prefijo) + 1; // SUBSTRING en MySQL es 1-indexado

        $maximo = static::where('codigo_unico', 'like', "{$prefijo}%")
            ->selectRaw("MAX(CAST(SUBSTRING(codigo_unico, {$offset}) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $siguiente = ($maximo ?? 0) + 1;

        return sprintf('%s%05d', $prefijo, $siguiente);
    }

    /** Busca un certificado activo por su código único. */
    public static function porCodigo(string $codigo): ?self
    {
        return static::where('codigo_unico', trim(strtoupper($codigo)))
            ->where('activo', true)
            ->first();
    }

    /** URL del PDF para descarga desde el panel admin. */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->archivo_pdf
            ? route('admin.certificados.pdf', $this)
            : null;
    }

    /**
     * Verdadero si la fecha de vencimiento ya pasó.
     * Se compara contra today() (sin hora) para que el certificado sea
     * válido durante todo el día de su fecha de vencimiento.
     */
    public function isVencido(): bool
    {
        return $this->fecha_vencimiento !== null
            && $this->fecha_vencimiento->lt(today());
    }

    /** Certificados marcados como activos (no invalidados). */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /** Certificados activos y cuya fecha de vencimiento no ha llegado. */
    public function scopeVigentes(Builder $query): Builder
    {
        return $query->where('activo', true)
                     ->where('fecha_vencimiento', '>=', today()->toDateString());
    }

    /** Certificados cuya fecha de vencimiento ya pasó (independientemente de si están activos). */
    public function scopeVencidos(Builder $query): Builder
    {
        return $query->where('fecha_vencimiento', '<', today()->toDateString());
    }

    /** Recalcula las horas del capacitado cuando se guarda o elimina un certificado. */
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
