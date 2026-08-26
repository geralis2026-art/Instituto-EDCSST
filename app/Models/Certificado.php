<?php

namespace App\Models;

use App\Models\Scopes\PropietarioScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;

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
        'user_id',
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
        'origen',
        'quiz_intento_id',
    ];

    protected $casts = [
        'fecha_emision'      => 'date',
        'fecha_vencimiento'  => 'date',
        'intensidad_horaria' => 'integer',
        'activo'             => 'boolean',
    ];

    /**
     * Persona que recibió el certificado. Sin scope de propietario: el
     * capacitado puede pertenecer a otro empleado (ver CertificadoRequest),
     * así que el dueño del certificado siempre debe poder ver quién lo
     * recibió, aunque no tenga acceso a administrar su perfil completo.
     */
    public function capacitado(): BelongsTo
    {
        return $this->belongsTo(Capacitado::class)->withoutGlobalScope(PropietarioScope::class);
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

    /** Empleado (admin/instructor) propietario de este certificado. */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Intento de quiz que generó este certificado automáticamente (origen = 'virtual'). */
    public function quizIntento(): BelongsTo
    {
        return $this->belongsTo(QuizIntento::class);
    }

    /**
     * Genera un código único para el certificado.
     * Formato: EDCSST-{AÑO}-{NUMERO_5_DIGITOS}
     * Ej: EDCSST-2026-00001
     *
     * El número se calcula a partir del más alto ya usado en el año (incluyendo
     * códigos cargados manualmente), para evitar choques con códigos atrasados.
     * A propósito no reutiliza números de certificados eliminados: la
     * numeración debe quedar lineal, sin huecos que luego se rellenen con
     * códigos "fuera de orden" y generen confusión.
     */
    public static function generarCodigoUnico(): string
    {
        $anio    = now()->year;
        $prefijo = "EDCSST-{$anio}-";

        $offset = strlen($prefijo) + 1; // SUBSTRING en MySQL es 1-indexado

        // Sin scope de propietario: el contador es GLOBAL entre todos los
        // instructores para evitar colisiones de código_unico.
        $maximo = static::withoutGlobalScope(PropietarioScope::class)
            ->where('codigo_unico', 'like', "{$prefijo}%")
            ->selectRaw("MAX(CAST(SUBSTRING(codigo_unico, {$offset}) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $siguiente = ($maximo ?? 0) + 1;

        return sprintf('%s%05d', $prefijo, $siguiente);
    }

    /**
     * Genera un código único y lo persiste, reintentando si otra inserción
     * concurrente generó el mismo número antes de que esta terminara de
     * guardar (condición de carrera en generarCodigoUnico(): dos procesos
     * pueden calcular el mismo "siguiente número" antes de que el primero
     * confirme el suyo — más probable ahora con varios instructores emitiendo
     * certificados a la vez). El índice UNIQUE de la BD es quien detecta la
     * colisión; aquí solo se resuelve reintentando con un código nuevo.
     */
    public function guardarConCodigoUnico(int $intentosMaximos = 3): void
    {
        for ($intento = 1; $intento <= $intentosMaximos; $intento++) {
            $this->codigo_unico = static::generarCodigoUnico();

            try {
                $this->saveQuietly();

                return;
            } catch (QueryException $e) {
                $esColisionDeCodigo = ($e->errorInfo[1] ?? null) === 1062
                    && str_contains($e->getMessage(), 'codigo_unico');

                if (! $esColisionDeCodigo || $intento === $intentosMaximos) {
                    throw $e;
                }
            }
        }
    }

    /** Busca un certificado activo por su código único. */
    /**
     * Sin scope de propietario: usado por la verificación/consulta pública de
     * certificados, que debe encontrar el certificado sin importar qué
     * empleado tenga sesión activa en el mismo navegador. El código único
     * es global.
     */
    public static function porCodigo(string $codigo): ?self
    {
        return static::withoutGlobalScope(PropietarioScope::class)
            ->where('codigo_unico', trim(strtoupper($codigo)))
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
        static::addGlobalScope(new PropietarioScope);

        static::saved(function ($certificado) {
            $certificado->capacitado?->recalcularHorasCapacitadas();
            User::limpiarCacheDashboard();
        });

        static::deleted(function ($certificado) {
            $certificado->capacitado?->recalcularHorasCapacitadas();
            User::limpiarCacheDashboard();
        });
    }
}
