<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Material de estudio de cada módulo: presentaciones, talleres,
     * documentos o videos. `archivo` para material subido, `url` para
     * videos externos (ej. YouTube).
     */
    public function up(): void
    {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete();
            $table->string('titulo');
            $table->enum('tipo', ['presentacion', 'taller', 'documento', 'video']);
            $table->string('archivo')->nullable();
            $table->string('url')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index(['modulo_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
