<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos de acceso al aula virtual. correo sigue siendo nullable a nivel
     * de BD; se exige a nivel de aplicación solo cuando se crea la primera
     * matrícula del capacitado (ahí se genera su contraseña inicial).
     */
    public function up(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->string('password')->nullable()->after('correo');
            $table->rememberToken()->after('password');
            $table->boolean('debe_cambiar_password')->default(false)->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token', 'debe_cambiar_password']);
        });
    }
};
