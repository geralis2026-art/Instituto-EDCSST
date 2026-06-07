<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega modalidad (virtual/presencial) y RH a la tabla de capacitados.
 * RH solo aplica para modalidad presencial.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->enum('modalidad', ['virtual', 'presencial'])->nullable()->after('telefono');
            $table->string('rh', 10)->nullable()->after('modalidad')
                  ->comment('Grupo sanguíneo, solo para modalidad presencial');
        });
    }

    public function down(): void
    {
        Schema::table('capacitados', function (Blueprint $table) {
            $table->dropColumn(['modalidad', 'rh']);
        });
    }
};
