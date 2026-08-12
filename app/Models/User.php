<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Empleado del instituto con acceso al panel administrativo.
 *
 * Roles:
 * - admin: acceso total (CRUD de cursos, categorías, usuarios, mensajes, etc.)
 * - capacitador: solo lectura de capacitados y creación/consulta de certificados
 * - instructor: CRUD completo, pero limitado a sus propios cursos, capacitados y certificados
 *
 * Los usuarios nuevos se crean con `activo = false`; un admin debe
 * activarlos para que puedan iniciar sesión (ver EnsureUserIsActivo).
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROL_ADMIN       = 'admin';
    public const ROL_CAPACITADOR = 'capacitador';
    public const ROL_INSTRUCTOR  = 'instructor';

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**
     * Certificados que este usuario (empleado) ha emitido.
     */
    public function certificadosEmitidos(): HasMany
    {
        return $this->hasMany(Certificado::class, 'emitido_por');
    }

    /**
     * Scope para filtrar solo usuarios activos.
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * Empleados con rol admin o instructor (dueños posibles de cursos,
     * capacitados y certificados). Usado para el selector "Ver: Todos / X / Y"
     * que solo ve el admin.
     */
    public function scopeGestores(Builder $query): Builder
    {
        return $query->whereIn('rol', [self::ROL_ADMIN, self::ROL_INSTRUCTOR]);
    }

    public function isAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function isCapacitador(): bool
    {
        return $this->rol === self::ROL_CAPACITADOR;
    }

    public function isInstructor(): bool
    {
        return $this->rol === self::ROL_INSTRUCTOR;
    }

    /** Admin o instructor: roles con CRUD sobre cursos/capacitados/certificados (propios o todos). */
    public function isGestor(): bool
    {
        return $this->isAdmin() || $this->isInstructor();
    }
}
