<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Persona que recibe capacitaciones y certificados del instituto.
 *
 * El campo `horas_capacitadas` es un total acumulado que se recalcula
 * automáticamente cada vez que se crea, actualiza o elimina uno de
 * sus certificados (ver Certificado::booted()).
 */
class Capacitado extends Model
{
    use HasFactory;

    protected $table = 'capacitados';

    /** Tipos de documento de identidad válidos, con su etiqueta legible. */
    public const TIPOS_DOCUMENTO = [
        'CC'  => 'Cédula de ciudadanía',
        'TI'  => 'Tarjeta de identidad',
        'CE'  => 'Cédula de extranjería',
        'PP'  => 'Pasaporte',
        'PPT' => 'Permiso por protección temporal',
    ];

    /** Abreviatura con puntos usada para imprimir el tipo de documento en certificados (ej. "C.C."). */
    public const TIPOS_DOCUMENTO_ABREVIADO = [
        'CC'  => 'C.C.',
        'TI'  => 'T.I.',
        'CE'  => 'C.E.',
        'PP'  => 'P.P.',
        'PPT' => 'P.P.T.',
    ];

    /** Abreviatura del tipo de documento para imprimir en certificados (ej. "C.C."). */
    public function tipoDocumentoAbreviado(): string
    {
        return self::TIPOS_DOCUMENTO_ABREVIADO[$this->tipo_documento] ?? 'C.C.';
    }

    protected $fillable = [
        'nombre_completo',
        'tipo_documento',
        'documento',
        'correo',
        'telefono',
        'rh',
        'horas_capacitadas',
    ];

    protected $casts = [
        'horas_capacitadas' => 'integer',
    ];

    /** Todos los certificados emitidos a este capacitado. */
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
