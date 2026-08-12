<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registra qué módulos ha completado un capacitado dentro de su matrícula.
     * El % de avance del curso se calcula a partir de este registro, no se
     * guarda como columna aparte.
     */
    public function up(): void
    {
        Schema::create('progreso_modulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete();
            $table->timestamp('completado_en')->nullable();
            $table->timestamps();

            $table->unique(['matricula_id', 'modulo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progreso_modulos');
    }
};
