<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un registro por cada intento de un capacitado sobre el quiz de su
     * matrícula (máx. `quizzes.intentos_maximos` por capacitado).
     * `respuestas` guarda un snapshot en json para auditar el orden
     * aleatorio de preguntas y lo que respondió en ese intento.
     */
    public function up(): void
    {
        Schema::create('quiz_intentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->integer('numero_intento');
            $table->decimal('nota_obtenida', 5, 2)->nullable();
            $table->boolean('aprobado')->default(false);
            $table->json('respuestas')->nullable();
            $table->timestamp('iniciado_en')->nullable();
            $table->timestamp('finalizado_en')->nullable();
            $table->timestamps();

            $table->index(['matricula_id', 'numero_intento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_intentos');
    }
};
