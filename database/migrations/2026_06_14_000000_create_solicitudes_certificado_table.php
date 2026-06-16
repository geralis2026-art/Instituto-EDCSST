<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Solicitudes de certificación generadas por la importación masiva
     * de capacitados. Quedan "pendientes" hasta que se generen los
     * certificados definitivos (fecha de emisión + PDF) en la Fase 2b.
     */
    public function up(): void
    {
        Schema::create('solicitudes_certificado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capacitado_id')->constrained('capacitados')->cascadeOnDelete();
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->nullOnDelete();
            $table->string('curso_texto')->nullable(); // texto original del Excel si no hubo match
            $table->enum('modalidad', ['virtual', 'presencial'])->nullable();
            $table->enum('estado', ['pendiente', 'procesada', 'descartada'])->default('pendiente');
            $table->string('origen')->default('importacion_excel');
            $table->foreignId('certificado_id')->nullable()->constrained('certificados')->nullOnDelete();

            $table->timestamps();

            $table->index(['estado', 'curso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_certificado');
    }
};
