<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->date('fecha_vencimiento')->nullable()->after('fecha_emision');
        });

        // Poblar fecha_vencimiento en registros existentes
        DB::statement('UPDATE certificados SET fecha_vencimiento = DATE_ADD(fecha_emision, INTERVAL 1 YEAR) WHERE fecha_vencimiento IS NULL');
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropColumn('fecha_vencimiento');
        });
    }
};
