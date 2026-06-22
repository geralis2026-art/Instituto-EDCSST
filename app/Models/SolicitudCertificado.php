<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Solicitud de certificación pendiente, generada por la importación
 * masiva de capacitados. Vincula un capacitado con el curso que está
 * tomando, a la espera de que se genere el certificado definitivo
 * (fecha de emisión + PDF) en la Fase 2b.
 */
class SolicitudCertificado extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_certificado';

    const ESTADO_PENDIENTE  = 'pendiente';
    const ESTADO_PROCESADA  = 'procesada';
    const ESTADO_DESCARTADA = 'descartada';

    const MODALIDAD_VIRTUAL    = 'virtual';
    const MODALIDAD_PRESENCIAL = 'presencial';

    protected $fillable = [
        'capacitado_id',
        'curso_id',
        'curso_texto',
        'modalidad',
        'estado',
        'origen',
        'certificado_id',
    ];

    /** Capacitado al que corresponde la solicitud. */
    public function capacitado(): BelongsTo
    {
        return $this->belongsTo(Capacitado::class);
    }

    /** Curso identificado para la solicitud (si hubo match). */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /** Certificado generado a partir de esta solicitud (si ya fue procesada). */
    public function certificado(): BelongsTo
    {
        return $this->belongsTo(Certificado::class);
    }

    /** Solicitudes a la espera de generar su certificado. */
    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    /** Solicitudes que ya tienen certificado generado. */
    public function scopeProcesadas(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PROCESADA);
    }
}
