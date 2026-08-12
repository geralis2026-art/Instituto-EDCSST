<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "correo" se consulta en cada login/reset de contraseña del aula virtual
     * (Auth::guard('capacitados')->attempt(['correo' => ...]) y los password
     * brokers) y no tenía ningún índice — full table scan en cada intento.
     * Es un índice simple, no único: la app no garantiza aún que "correo"
     * sea irrepetible entre capacitados (no hay validación de unicidad en
     * CapacitadoRequest), así que forzar UNIQUE aquí podría romper el
     * `migrate` si ya existen duplicados en datos reales.
     */
    public function up(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->index('correo');
        });
    }

    public function down(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->dropIndex(['correo']);
        });
    }
};
