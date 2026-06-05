<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Certificados emitidos
     * Relaciona un capacitado con un curso
     * Cada certificado tiene un código único de verificación
     */
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capacitado_id')->constrained('capacitados')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->restrictOnDelete();
            $table->foreignId('emitido_por')->nullable()->constrained('users')->nullOnDelete();

            $table->string('codigo_unico')->unique();    // ej: "EDCSST-2026-00001"
            $table->date('fecha_emision');
            $table->integer('intensidad_horaria');       // copiada del curso al emitir
            $table->string('archivo_pdf')->nullable();   // ruta al PDF generado
            $table->boolean('activo')->default(true);    // por si hay que invalidar

            $table->timestamps();

            $table->index('codigo_unico');
            $table->index(['capacitado_id', 'curso_id']);
            $table->index(['activo', 'fecha_emision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
