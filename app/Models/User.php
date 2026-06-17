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
 *
 * Los usuarios nuevos se crean con `activo = false`; un admin debe
 * activarlos para que puedan iniciar sesión (ver EnsureUserIsActivo).
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROL_ADMIN       = 'admin';
    const ROL_CAPACITADOR = 'capacitador';

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

    public function isAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function isCapacitador(): bool
    {
        return $this->rol === self::ROL_CAPACITADOR;
    }
}
