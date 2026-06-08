<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar modalidad a certificados
        Schema::table('certificados', function (Blueprint $table) {
            $table->enum('modalidad', ['virtual', 'presencial'])->nullable()->after('intensidad_horaria');
        });

        // Todos los certificados existentes son virtuales
        DB::table('certificados')->update(['modalidad' => 'virtual']);

        // Quitar modalidad de capacitados (rh se queda)
        Schema::table('capacitados', function (Blueprint $table) {
            $table->dropColumn('modalidad');
        });
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropColumn('modalidad');
        });

        Schema::table('capacitados', function (Blueprint $table) {
            $table->enum('modalidad', ['virtual', 'presencial'])->nullable()->after('telefono');
        });
    }
};
