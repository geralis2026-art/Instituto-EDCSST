<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
