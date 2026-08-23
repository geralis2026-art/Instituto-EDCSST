<?php

namespace App\Models;

use App\Models\Scopes\PropietarioScope;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Persona que recibe capacitaciones y certificados del instituto.
 *
 * El campo `horas_capacitadas` es un total acumulado que se recalcula
 * automáticamente cada vez que se crea, actualiza o elimina uno de
 * sus certificados (ver Certificado::booted()).
 *
 * También es el modelo de autenticación del guard "capacitados" (acceso
 * al aula virtual), separado del guard "web" (empleados/users). El login
 * es por `correo` + `password`; `debe_cambiar_password` fuerza el cambio
 * de contraseña solo en el primer ingreso.
 */
class Capacitado extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

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
        'user_id',
        'nombre_completo',
        'tipo_documento',
        'documento',
        'correo',
        'telefono',
        'rh',
        'horas_capacitadas',
        'password',
        'debe_cambiar_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'horas_capacitadas'     => 'integer',
        'password'              => 'hashed',
        'debe_cambiar_password' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new PropietarioScope);
    }

    /** Laravel usa "email" por defecto para el reset de contraseña; aquí el campo es "correo". */
    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }

    /** Notifiable usa "email" por defecto para el canal "mail"; aquí el campo es "correo". */
    public function routeNotificationForMail(): ?string
    {
        return $this->correo;
    }

    /**
     * NOTA: pendiente sobrescribir cuando se construyan las rutas de
     * autenticación del aula virtual — el aviso por defecto (CanResetPassword)
     * apunta a la ruta "password.reset" del guard "web" (empleados), no a la
     * del aula virtual. Se debe personalizar junto con esas rutas/controladores.
     */

    /** Empleado (admin/instructor) propietario de este capacitado. */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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

    /** Matrículas del capacitado en cursos con aula virtual. */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Recalcula las horas capacitadas totales sumando los certificados activos.
     */
    public function recalcularHorasCapacitadas(): void
    {
        // Sin scope de propietario: el total debe sumar TODOS los
        // certificados del capacitado, sin importar qué instructor
        // tenga la sesión activa al momento de recalcular.
        $total = $this->certificados()
            ->withoutGlobalScope(PropietarioScope::class)
            ->where('activo', true)
            ->sum('intensidad_horaria');

        $this->update(['horas_capacitadas' => $total]);
    }

    /**
     * Buscar capacitado por número de documento.
     */
    /**
     * Sin scope de propietario: usado por la consulta pública de
     * certificados, que debe encontrar al capacitado sin importar qué
     * empleado tenga sesión activa (guest o instructor/admin logueados en
     * el mismo navegador no deben afectar el resultado de una búsqueda
     * pública). El documento es único globalmente.
     */
    public static function porDocumento(string $documento): ?self
    {
        return static::withoutGlobalScope(PropietarioScope::class)
            ->where('documento', trim($documento))
            ->first();
    }
}
