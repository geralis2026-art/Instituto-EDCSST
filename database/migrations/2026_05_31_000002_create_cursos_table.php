<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cursos que ofrece el instituto
     * Cada curso pertenece a una categoría
     */
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion_corta');
            $table->string('duracion');                  // ej: "40 horas", "2 meses"
            $table->integer('intensidad_horaria');       // en horas (para el certificado)
            $table->string('imagen')->nullable();
            $table->boolean('destacado')->default(false); // ¿mostrar en home?
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['activo', 'destacado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
