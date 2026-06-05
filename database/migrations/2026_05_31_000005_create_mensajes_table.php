<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mensajes recibidos desde el formulario de contacto público
     * Tienen estados: nuevo, leído, respondido
     */
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo');
            $table->text('mensaje');
            $table->enum('estado', ['nuevo', 'leido', 'respondido'])->default('nuevo');
            $table->text('notas_internas')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
