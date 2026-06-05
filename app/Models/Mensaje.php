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

    public function scopeNuevos($query)
    {
        return $query->where('estado', self::ESTADO_NUEVO);
    }

    public function marcarComoLeido(): void
    {
        if ($this->estado === self::ESTADO_NUEVO) {
            $this->update(['estado' => self::ESTADO_LEIDO]);
        }
    }

    public function marcarComoRespondido(): void
    {
        $this->update(['estado' => self::ESTADO_RESPONDIDO]);
    }

    public function getEstadoFormateadoAttribute(): string
    {
        return self::$estados[$this->estado] ?? $this->estado;
    }
}
