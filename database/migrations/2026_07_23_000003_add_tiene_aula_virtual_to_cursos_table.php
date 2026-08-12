<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingue cursos presenciales (emisión manual de certificado, flujo
     * actual) de cursos con aula virtual (módulos + quiz + certificación
     * automática).
     */
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->boolean('tiene_aula_virtual')->default(false)->after('intensidad_horaria');
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('tiene_aula_virtual');
        });
    }
};
