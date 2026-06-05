<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Capacitados: las personas que reciben los certificados
     * No tienen login propio, se identifican por documento
     */
    public function up(): void
    {
        Schema::create('capacitados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('documento')->unique();
            $table->string('correo')->nullable();
            $table->string('telefono')->nullable();
            $table->integer('horas_capacitadas')->default(0); // acumulado total
            $table->timestamps();

            $table->index('documento');
            $table->index('nombre_completo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacitados');
    }
};
