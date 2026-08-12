<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Filtra automáticamente por el empleado dueño del registro (multi-instructor).
 *
 * Si el empleado autenticado (guard "web") tiene rol `instructor`, solo ve
 * sus propios registros (`user_id` = su id). Admin, capacitador, o ninguna
 * sesión de empleado activa (catálogo público, consultas, comandos artisan)
 * no filtran: se ven todos los registros.
 */
class PropietarioScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();

        if ($user && $user->isInstructor()) {
            $builder->where($model->getTable() . '.user_id', $user->id);
        }
    }
}
