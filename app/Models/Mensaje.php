<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;

    protected $table = 'mensajes';

    protected $fillable = [
        'nombre',
        'correo',
        'mensaje',
        'estado',
        'notas_internas',
        'ip',
    ];

    // Constantes para los estados
    public const ESTADO_NUEVO = 'nuevo';
    public const ESTADO_LEIDO = 'leido';
    public const ESTADO_RESPONDIDO = 'respondido';

    public static array $estados = [
        self::ESTADO_NUEVO => 'Nuevo',
        self::ESTADO_LEIDO => 'Leído',
        self::ESTADO_RESPONDIDO => 'Respondido',
    ];

    /** Mensajes que aún no han sido leídos por el admin. */
    public function scopeNuevos(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('estado', self::ESTADO_NUEVO);
    }

    /** Cambia el estado a 'leido' solo si estaba en 'nuevo'. */
    public function marcarComoLeido(): void
    {
        if ($this->estado === self::ESTADO_NUEVO) {
            $this->update(['estado' => self::ESTADO_LEIDO]);
        }
    }

    /** Cambia el estado a 'respondido' sin importar el estado actual. */
    public function marcarComoRespondido(): void
    {
        $this->update(['estado' => self::ESTADO_RESPONDIDO]);
    }

    /** Retorna la etiqueta legible del estado actual (ej: "Leído"). */
    public function getEstadoFormateadoAttribute(): string
    {
        return self::$estados[$this->estado] ?? $this->estado;
    }
}
