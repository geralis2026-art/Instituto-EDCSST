<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de recuperación de contraseña para el guard "capacitados",
     * separada de password_reset_tokens (que es para users/empleados).
     */
    public function up(): void
    {
        Schema::create('password_reset_tokens_capacitados', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens_capacitados');
    }
};
