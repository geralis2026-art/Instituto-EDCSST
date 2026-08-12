<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Matrícula: asigna un curso con aula virtual a un capacitado.
     * Determina quién tiene acceso a qué curso (el contenido en sí es
     * el mismo para todos los matriculados en ese curso).
     */
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->foreignId('capacitado_id')->constrained('capacitados')->cascadeOnDelete();
            $table->date('fecha_asignacion');
            $table->boolean('completado')->default(false);
            $table->date('fecha_completado')->nullable();
            $table->timestamps();

            $table->unique(['curso_id', 'capacitado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
